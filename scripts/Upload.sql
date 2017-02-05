-- Load Timetable information resulting from scraper.py 
LOAD DATA LOCAL INFILE 'D:\\Alex\\Desktop\\Projects\\Academic Timetable Redesign\\scrapeClasses_201703.csv'
INTO TABLE `classes`.`timetable`
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\r\n'
(department, `number`, fys, title, description, crosslisted, period, room, building, teacher, culture, distrib, `limit`, term);

-- Check that it worked :D
SELECT * FROM classes.timetable WHERE term = "S2017";



-- Truncate the current ORC data
truncate table `classes`.`orc`;

-- Load in ORC data from scrape_orc.js running in browser
LOAD DATA LOCAL INFILE 'D:\\Alex\\Desktop\\Projects\\Academic Timetable Redesign\\orc_201703.csv'
INTO TABLE `classes`.`orc`
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
(department, `number`, title, description, teachers, prereqs);
