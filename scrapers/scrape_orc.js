/*
This javascript code will extract all data.  Run on 
http://dartmouth.smartcatalogiq.com/current/orc.aspx.
Then right click save as the link in the h1 that says "Download CSV"
*/

var base = "http://dartmouth.smartcatalogiq.com";

function makeCSV(text) {
	window.URL = window.URL || window.webkitURL;

	var csvFile = new Blob([text], {type: 'text/csv'});
	var a = document.createElement('a');
	a.download = 'orc.csv';
	a.href = window.URL.createObjectURL(csvFile);
	a.textContent = 'Download CSV';
	a.dataset.downloadurl = ['text/csv', a.download, a.href].join(':');
	$("h1").append(a);
}

function clean(raw) {
	var allClasses = [];
	var x = $(raw).find("a")
	.filter(function() { // [A-Z]{3,4} [\d.-]*
        return this.text.match(/^[A-Z]{3,4} [\d.-]*$/);
    });
    x.each(function() {
    	console.log($(this).attr("href"));
    	$.get(base + $(this).attr("href"), function(data) {
    		var raw = $(data).find("#rightpanel").html();
			var titleRegex = /<h1>[\s\S]*?<span>(.*?)<\/span> (.*?)\n<\/h1>/g;
			var descripRegex = /<div class="desc">[\s\S]*?<p>(.*?)<\/p>/g;
			var instructorRegex = /id="instructor">.*?<\/h3>(.*?)<\/div>/g;
			var crossRegex = /Cross Listed Courses([\s\S]*?)<h3>/g;
			var crossRegex2 = /<a.*?href=".*?">(.*?)<\/a>/g;
			var prereqRegex1 = /Prerequisite([\s\S]*?)<h3>/g;
			var prereqRegex2 = /<a.*?href=".*?">(.*?)<\/a>/g;
			var offeredRegex = /Offered<\/h3>(.*?)<\/div>/g;
			
			var titleMatch = titleRegex.exec(raw);
			var code = titleMatch[1].split(" ");
			var num = code[1];
			code = code[0];
			var title = titleMatch[2].replace(/"/g,"'");

			var descripMatch = descripRegex.exec(raw);
			var description = (descripMatch) ? descripMatch[1] : "";
			description = description.replace(/<a.*?href=".*?">(.*?)<\/a>/g,"$1").replace(/&nbsp;/g,"").replace(/"/g,"'");

			var instructorMatch = instructorRegex.exec(raw);
			var instructor = (instructorMatch) ? instructorMatch[1] : "";

			var crossMatch = crossRegex.exec(raw);
			var crossListed = [];
			while (crossMatch != null && (result = crossRegex2.exec(crossMatch[1])) !== null) {
			    crossListed.push(result[1]);
			}

			var prereqMatch;
			var t;
			while (t = prereqRegex1.exec(raw)) { prereqMatch = t; }
			var prereqs = (prereqMatch == null) ? "" : prereqMatch[1];
			prereqs = prereqs.replace(/<a.*?href=".*?">(.*?)<\/a>/g,"$1").replace(/<.*?>/g,"").replace(/\n/g,"").replace(/"/g,"'");

			var offeredMatch = offeredRegex.exec(raw);
			var offeredText = (offeredMatch) ? offeredMatch[1] : "";
			offeredText = offeredText.split("; ");
			var offered = {};
			$.each(offeredText, function(index, value) {
				if (value.split(": ").length == 2) {
					offered[value.split(": ")[0]] = value.split(": ")[1];
				} else if (value.toLowerCase().indexOf("offered") > -1) {
					// eek
				}
			});
			console.log(JSON.stringify([code,num,title,description,instructor,prereqs]).slice(1,-1));
			allClasses.push(JSON.stringify([code,num,title,description,instructor,prereqs]).slice(1,-1));
    		if (allClasses.length == x.length) {
    			console.log("Done!");
    			makeCSV(allClasses.join('\n') + '\n');
    		}
    	});
    });
}

function expand(raw) {
	setTimeout(function () {
		if ($(raw).find("span.expandable:not(.collapsible)").length) {
			$(raw).find("span.expandable:not(.collapsible)").click();     
			expand(raw);
		} else {
			clean(raw);
		}
	}, 500)
}

var head = $("a[href$='Undergraduate']");
head.prev().click();
setTimeout(function (){
	var raw = $("a[href$='Undergraduate'] + ul");
	expand(raw);
}, 500);