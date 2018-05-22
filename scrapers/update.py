import requests, re, MySQLdb, csv
from scrape_timetable import *
from secrets import mysql_connect

# Read in the text file
with open('terms.txt', 'r') as f:
	databaseTerms = [x.strip() for x in f.readlines()]

print(databaseTerms)

matchingYears = {
	'01': 'W',
	'03': 'S',
	'06': 'X',
	'09': 'F'
}

# Check the website
with requests.Session() as c:
	subjects = c.post('http://oracle-www.dartmouth.edu/dart/groucho/timetable.subject_search', data={
		"distribradio": "alldistribs",
		"subjectradio": "allsubjects",
		"termradio": "selectterms",
		"hoursradio": "allhours",
		"terms": "no_value",
		"depts": "no_value",
		"periods": "no_value",
		"distribs": "no_value",
		"distribs_i": "no_value",
		"distribs_wc": "no_value",
		"sortorder": "dept",
		"pmode": "public",
		"term": "",
		"levl": "",
		"fys": "n",
		"wrt": "n",
		"pe": "n",
		"review": "n",
		"crnl": "no_value",
		"classyear": "2008",
		"searchtype": "Subject Area(s)"
	}, verify=False)

	# Get the current terms
	currentTerms = re.findall('<input type = "checkbox" id=term.*value=(.*?)>', subjects.text, flags=re.IGNORECASE)

# Figure out if there are any terms that aren't in the database
for termCode in currentTerms:
	termShortcut = termCode[2:4] + matchingYears[termCode[-2:]]
	# If it's not in there, then it's time to go to work
	if termShortcut not in databaseTerms:
		# termCode = 201801
		# termShortcut = 18W

		print 'Scraping classes for ' + termShortcut + '...'
		# Scrape the current classes, creating a .csv file
		scrapeCurrentClasses(termCode, termShortcut + 'L')

		print 'Done.  Uploading to SQL database...'
		# Upload the .csv file to the SQL database
		mydb = mysql_connect()
		cursor = mydb.cursor()

		cursor.execute('''\
			LOAD DATA LOCAL INFILE 'scrapeClasses_''' + termCode + '''.csv'
			INTO TABLE `classes`.`timetable`
			FIELDS TERMINATED BY ',' ENCLOSED BY '"'
			LINES TERMINATED BY '\r\n'
			(department, `number`, fys, title, description, crosslisted, period, room, building, teacher, culture, distrib, `limit`, term);
		''')

		mydb.commit()
		cursor.close()
		print "Done."


'''
2) Run scrape_timetable.py with the new year, generating a new .csv file.
3) Upload the .csv file to the SQL database.
4) Delete the new .csv file.
5) Run the scrape_medians.py.
6) Truncate the SQL table, and uploade from the new .csv file.
7) Delete the new .csv file.
8) ORC UPDATES????
'''