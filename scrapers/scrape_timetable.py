import requests, re, urllib, json
import unicodecsv as csv
requests.packages.urllib3.disable_warnings()

searchPage = "http://oracle-www.dartmouth.edu/dart/groucho/timetable.display_courses"
masterPage = "http://dartmouth.smartcatalogiq.com/en/2015/orc/Departments-Programs-Undergraduate"
punctuation = { 0x2018:0x27, 0x2019:0x27, 0x201C:0x22, 0x201D:0x22, 0x0A:0x20 }

def write_text(text):
	with open("output.html", "w") as f:
		f.write(text)

def fix_string(string):
	return string.translate(punctuation).encode('utf-8', 'ignore').rstrip().lstrip()

def scrapeCurrentClasses(yearCode, term):
	allSubjects = {
		"distribradio": "alldistribs", 
		"depts": "no_value",
		"periods": "no_value",
		"distribs": "no_value",
		"distribs_i": "no_value",
		"distribs_wc": "no_value",
		"pmode": "public",
		"term": "",
		"levl": "",
		"fys": "n",
		"wrt": "n",
		"pe": "n",
		"review": "n",
		"crnl": "no_value",
		"classyear": "2008",
		"searchtype": "Subject Area(s)",
		"termradio": "selectterms",
		"terms": ["no_value", yearCode],
		"subjectradio": "allsubjects",
		"hoursradio": "allhours",
		"sortorder": "dept"
	}

	with requests.Session() as c:
		subjects = c.post(searchPage, data=allSubjects, verify=False)

		matches = re.findall(
			'<td>'+yearCode+'</td>\n' +
			# '<td>.*?</td>\n' + 
			'<td>.*?"(.*?)">(.*?)</a></td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'[\s\S]*?' + 
			'<td><a .*?\'(.*?)\'\)">(.*?)</a></td>\n' + 
			'<td><a .*?\'(.*?)\'[\s\S]*?' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n' + 
			'<td>(.*?)</td>\n'
		, subjects.text, flags=re.IGNORECASE)

		''' Alternative sample row
		<td>201806</td>
		<td><A HREF= "http://dartmouth.smartcatalogiq.com/en/current/orc/Departments-Programs-Undergraduate/Spanish-and-Portuguese-Languages-and-Literatures "> SPAN</A></td>
		<td>003</td>
		<td>01</td>
		<td>N</td>
		<html>
		<head>
		<script language="JAVASCRIPT" type="TEXT/JAVASCRIPT">
		function reqmat_window(thisURL) {
		            theHeight=screen.height*.3
		            theWidth=screen.width*.4
		            theLeft=screen.width*.4
		            theWindow =
		                window.open(thisURL,'reqmatWindow','height='+theHeight+',width='+theWidth+',left='+theLeft+',directory=no,resizeable=yes, toolbar=no, scrollbars=yes')
		                }
		</script>
		</head>
		<body>
		<td><A HREF="javascript:reqmat_window('http://oracle-www.dartmouth.edu/dart/groucho/course_desc.display_course_desc?term=201806&subj=SPAN&numb=003')">Spanish III</A></td>
		<td><A HREF="javascript:reqmat_window('http://oracle-www.dartmouth.edu/dart/groucho/course_desc.display_non_fys_req_mat?p_term=201806&p_crn=60357')"><IMG SRC="https://banner.dartmouth.edu/wtlgifs/twgginfo.gif" BORDER=0></A></td>
		</body>
		</html>
		<td>&nbsp</td>
		<td>9S</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		<td>Irasema Saucedo</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		<td>18</td>
		<td>10</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		</tr>
		'''
		
		''' Sample row
		<td>201703</td>
		<td>36027</td>
		<td><A HREF= "http://dartmouth.smartcatalogiq.com/en/current/orc/Departments-Programs-Graduate/Genetics "> GENE</A></td>
		<td>198</td>
		<td>01</td>
		<td>N</td>
		<html>
		<head>
		<script language="JAVASCRIPT" type="TEXT/JAVASCRIPT">
		function reqmat_window(thisURL) {
		            theHeight=screen.height*.3
		            theWidth=screen.width*.4
		            theLeft=screen.width*.4
		            theWindow =
		                window.open(thisURL,'reqmatWindow','height='+theHeight+',width='+theWidth+',left='+theLeft+',directory=no,resizeable=yes, toolbar=no, scrollbars=yes')
		                }
		</script>
		</head>
		<body>
		<td><A HREF="javascript:reqmat_window('http://oracle-www.dartmouth.edu/dart/groucho/course_desc.display_course_desc?term=201703&subj=GENE&numb=198')">Grad Research I:  Level B</A></td>
		<td><A HREF="javascript:reqmat_window('http://oracle-www.dartmouth.edu/dart/groucho/course_desc.display_non_fys_req_mat?p_term=201703&p_crn=36027')"><IMG SRC="https://banner.dartmouth.edu/wtlgifs/twgginfo.gif" BORDER=0></A></td>
		</body>
		</html>
		<td>&nbsp</td>
		<td>ARR</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		<td>Jay Dunlap</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		<td>0</td>
		<td>&nbsp</td>
		<td>&nbsp</td>
		'''

		allData = []

		for match in matches:
			# extract all data
			program_url = match[0].strip()
			program = match[1].strip()
			num = match[2].strip().lstrip("0")
			sec = match[3].strip()
			fys = match[4].strip()
			course_url = match[5].strip()
			title = match[6].strip()
			textbooks = match[7].strip()
			xlist = match[8].strip()
			period = match[9].strip()
			room = match[10].strip()
			building = match[11].strip()
			instructor = match[12].strip()
			culture = match[13].strip()
			distrib = match[14].strip()
			lim = match[15].strip()
			enrl = match[16].strip()
			status = match[17].strip()

			get_descrip = c.get(course_url)
			try:
				if (fys == "Y"):
					sub = course_url[course_url.index("#"):]

					descrip = fix_string(re.sub("</?p>", "", re.search("<A NAME=\"" + sub + "\">[\s\S]*?<TD><p><p>[\s\S]*?<p>([\s\S]*?)</p></TD>", get_descrip.text).group(1)).replace('\n',''))
				else:
					descrip = re.search("<p><p>(.*?)</p>",get_descrip.text).group(1)
			except:
				descrip = ""

			if distrib == "&nbsp":
				distrib = "[]"
			else:
				distrib = json.dumps(distrib.split(" or "))

			if xlist == "&nbsp":
				xlist = "[]"
			else:
				t = xlist.split(", ")
				xlist = []
				for i in t:
					i = i[:-3].split(" ")
					i[1] = i[1].lstrip("0")
					xlist.append(i[0] + " " + i[1])
				xlist = json.dumps(xlist)

			arr = [program, num, fys, title, descrip, xlist, period, room, building, instructor, culture, distrib, lim, term]
			for i in range(0,len(arr)):
				if (arr[i] == "&nbsp" or "<BR>" in arr[i]):
					arr[i] = ""
			allData.append(arr)

		with open('scrapeClasses_' + yearCode + '.csv', 'wb') as fp:
		    a = csv.writer(fp, delimiter=',', encoding="utf-8", quoting=csv.QUOTE_ALL)
		    a.writerows(allData)

# 201801 - 18W
# 201803 - 18S
# 201806 - 18X
# 201809 - 18F
scrapeCurrentClasses("201809", "18F")