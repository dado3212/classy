# Adapted from https://github.com/mattgmarcus/Median-Town/blob/master/scripts/medians.py to better suit my purposes

import requests, re, json, time
import unicodecsv as csv
import urllib.request as ur
from bs4 import BeautifulSoup
requests.packages.urllib3.disable_warnings()

grade_key_dict = {
  "A":      6,
  "A/A-":   5.5,
  "A-":     5,
  "A-/B+":  4.5,
  "B+":     4,
  "B+/B":   3.5,
  "B":      3,
  "B/B-":   2.5,
  "B-":     2,
  "B-/C+":  1.5,
  "C+":     1,
  "C+/C":   0.5,
  "C":      0
}

def isValidPage(title):
  return (title.find("Median Grades") != -1)

def getAvgMedian(medians):
  #Get rid of any spaces
  medians = ["".join(median.split()) for median in medians]

  avg_median = ""
  sum_grades = 0
  num_terms = 0

  for median in medians:
    # Some 14W classes randomly had E medians
    if "E" != median:
      sum_grades += grade_key_dict[median]

      num_terms += 1
    else:
      print("HOLY SHIT THERE'S AN E")

  sum_grades *= 1.0
  avg_grade = sum_grades / num_terms

  for pair in grade_key_dict.items():
    if pair[1] == avg_grade:
      avg_median = pair[0]

  if not avg_median:
    if 1 > avg_grade:
      avg_median = "C+/C"
    elif 2 > avg_grade:
      avg_median = "B-/C+"
    elif 3 > avg_grade:
      avg_median = "B/B-"
    elif 4 > avg_grade:
      avg_median = "B+/B"
    elif 5 > avg_grade:
      avg_median = "A-/B+"
    elif 6 > avg_grade:
      avg_median = "A/A-"
    else:
      print("Something weird happened in the median calculation")

  return avg_median

def normalizeCourseName(name):
  if name.p is not None:
    name = name.p
  elif name.div is not None:
    name = name.div

  name = name.string

  if name.find("-") != -1:
    #The variable parts splits the course name as it appears on the Dartmouth site, 
    #i.e. "ENGL-015-01", into an array that would be like ["ENGL", "015", "01"]
    parts = name.split("-")
    
    #Get rid of any whitespace
    parts = [part.strip() for part in parts]
  else:
    parts = [name[:4].strip(), name[4:7]]

  #Make sure the course number contains no extraneous zeros
  if parts[1][0] == "0":
    if parts[1][1] == "0":
      parts[1] = parts[1][2]
    else:
      parts[1] = parts[1][1:]

  #Some courses with multiple sections are expressed like EDUC-09.03
  #This will remove the end section
  if "." in parts[1]:
    parts[1] = parts[1].split(".")[0]

  return parts[0] + " " + parts[1]

def normalizeGrade(grade):
  if grade.p is not None:
    return grade.p
  elif grade.div is not None:
    return grade.div
  else:
    return grade

def normalizeSize(size):
  if size.p is not None:
    return size.p
  elif size.div is not None:
    return size.div
  else:
    return size

def hasCompleteInfo(course, size, grade):
  return (course.replace(u"\xa0", u" ") != " ") and (size.string.replace(u"\xa0", u" ") != " ") and (grade.string.replace(u"\xa0", u" ") != " ")

def getMedians(term):
  #The key is the course name, it contains a list of the enrollment and median grade
  median_dict = {}

  url = "http://www.dartmouth.edu/~reg/transcript/medians/" + term + ".html"

  try:
    page = BeautifulSoup(ur.urlopen(url), "html.parser")

    if isValidPage(page.title.string):
      raw_medians = page.table.find_all("tr")

      #Some tables have their first entry as the header, this gets rid of that if it does
      firstRow = raw_medians[0].contents[1]
      if firstRow.p is not None:
        firstEntry = firstRow.p.string
      else:
        firstEntry = firstRow.string

      if (firstEntry.lower() == "term"):
        raw_medians = raw_medians[1:]

      for median in raw_medians:
        contents = median.contents
        course_name = normalizeCourseName(contents[3])
        size = normalizeSize(contents[5])
        thisGrade = normalizeGrade(contents[7])
        if not hasCompleteInfo(course_name, size, thisGrade):
          continue

        if course_name in median_dict:
          total_enrolled = str(int(median_dict[course_name][0]) + int(size.string))
          grade = getAvgMedian([median_dict[course_name][1], thisGrade.string.strip()])
          median_dict[course_name] = [total_enrolled, grade]

        else:
          #Line below means: median_dict[course name] = [# enrolled, median]
          median_dict[course_name] = [size.string.strip(), "".join(thisGrade.string.split())]
  except ur.HTTPError:
    pass 

  return median_dict

def compileMedians():
  quarters = ["F", "W", "S", "X"]
  years = [str(x).zfill(2) for x in range(9, int(time.strftime("%Y")[2:])+1)] # years from 09 to current
  all_courses = []

  for year in years:
    for quarter in quarters:
      term = year + quarter

      term_courses = getMedians(term)

      for course in term_courses:
        class_size = term_courses[course][0]
        median = term_courses[course][1]

        all_courses.append([course.split(" ")[0], course.split(" ")[1], median, str(class_size), term])

  with open('medians.csv', 'wb') as fp:
    a = csv.writer(fp, delimiter=',', encoding="utf-8", quoting=csv.QUOTE_ALL)
    a.writerows(all_courses)

def getTrendData():
  json_data = open(course_data_file)
  course_data = json.load(json_data)

  trend_data = {}

  for course in course_data:
    medians = course_data[course]

    num_terms = num_enrolled = 0
    median_grades = []

    for median in medians:
      num_enrolled += int(median["enrollment"])
      median_grades.append(median["median"])
      num_terms += 1

    if (num_enrolled != 0):
      avg_enrolled = num_enrolled / num_terms
      avg_median = getAvgMedian(median_grades)
      trend_data[course] = {"median": avg_median, "enrollment": str(avg_enrolled)}
    else:
      print("No data for this course?!?")

  with open(trend_data_file, "w") as f:
    f.write(json.dumps(trend_data))  

compileMedians()