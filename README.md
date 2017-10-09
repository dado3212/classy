# <img src="/images/favicons/android-chrome-192x192.png?raw=true" width="30" alt="Logo"/> Classy
Classy is a way of searching for classes based on departments, distribs, periods, and medians. It allows you to find the best fits for you based on your priorities in a course.

### Why
I kept having difficulty figuring out what classes to take, and alternating between sites for looking up medians, the classes offered, the the class descriptions.  I built out a solution that I was using about a year ago, but I finally finalized it and prettied it up.

### Services
* Search by median
* Search by department
* Search by distribs
* Search by periods
* Leverage powerful points system for searching
* View all information in helpful format
* Filter out previous classes

### Setup
First, you'll need to set up the MySQL database.  The `Create_Tables.sql` file in the `scrapers` folder contains the SQL code to create the tables.  Then, you'll need to create a file called `secret.php` in the `php` folder.  All that folder has is a function called "createConnection" which will need to create a PDO connection to the database with the tables you previously created.

Then you'll need to populate the tables with the scrapers.  Information on running the scrapers can be found under the 'scrapers' subsection.  You can then upload the generated CSV files using the `Upload.sql` file, which contains the SQL code to truncate or update the existing tables from a local CSV.  Finally, add the new term to the util.php file as the last option, which will be the default.

### Scrapers
The project relies on various scrapers to create a MySQL database that is queried against.  These each have some oddities.

<ul>
  <li>
    <b>scrape_orc.js</b> - This JS code runs in your browser to download the current ORC information for all undergraduate courses.  Navigate to http://dartmouth.smartcatalogiq.com/current/orc.aspx, and run the code in the command line.  When it's finished processing, it will make a link called 'Download CSV' in the main header, which you can Right Click > Save As to save a local .csv file of the ORC data for uploading.
  </li>
  <li>
    <b>scrape_timetable.py</b> - This Python script needs some minor modifications to be run.  Simply edit the last line to have the function call the proper term identifiers.  "201703", "17S" is the 2017 Spring term, "201706, "17X" will be the summer, etc.  This will generate a file 'scrapeClass_201701.csv'.
  </li>
  <li>
  <b>scrape_medians.py</b> - This Python script was adapted from <a href="https://github.com/mattgmarcus/Median-Town/blob/master/scripts/medians.py">mattgmarcus</a>'s file, who developed Median-Town.  It can just be run with no parameters, and will generate a file 'medians.csv' which contains the averaged median for each class that it found from the data from 09W up to the present (it calculates the current year).
  </li>
</ul>

---

* HTML
* PHP
* CSS (Bootstrap Grid)
* JS (jQuery, Chosen)
* MySQL
* Python

**Created by Alex Beals © 2017**
