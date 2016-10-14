function studenthubAdminParseElectives() {
	jQuery("#output>tbody").remove();
	var input = jQuery.parseHTML(jQuery("#input").val());
	
	jQuery(input).find("li").each(function() {
		var poster = jQuery(jQuery(this).find("a")[0]).attr("href");
		
		var fileName = poster.substring(poster.lastIndexOf("/") + 1, poster.length - 4);
		
		var student = fileName.substring(0, fileName.indexOf("_")).replace("-", " ");
		if (student == "") {
			student = fileName.substring(0, fileName.indexOf("-"));
			fileName = fileName.substring(fileName.indexOf("-") + 1);
			student += " " + fileName.substring(0, fileName.indexOf("-"));
			fileName = fileName.substring(fileName.indexOf("-") + 1);
		}
		
		var posterName = fileName.substring(fileName.lastIndexOf("_") + 1).toLowerCase();
		posterName = posterName.replace(/eposter/, "");
		posterName = posterName.replace(/poster/, "");
		posterName = posterName.replace(/summary/, "");
		posterName = posterName.replace(/elective/, "");
		posterName = posterName.replace(/[0-9]/g, "");
		posterName = posterName.replace(".", "");
		
		posterName = posterName.replace("respiratory-medicine", "respiratory medicine");
		posterName = posterName.replace("accident-and-emergency", "emergency medicine");
		posterName = posterName.replace("a-and-e", "emergency medicine");
		posterName = posterName.replace("emergency-medicine", "emergency medicine");
		posterName = posterName.replace("internal-medicine", "internal medicine");
		posterName = posterName.replace("primary-health-care", "primary care");
		posterName = posterName.replace("intensive-care", "intensive care");
		posterName = posterName.replace("indigineous", "indigenous");
		posterName = posterName.replace("indigenous-healthcare", "indigenous health");
		posterName = posterName.replace("indigenous-health", "indigenous health");
		posterName = posterName.replace("indigenous-community", "indigenous health");
		posterName = posterName.replace("icu", "intensive care");
		posterName = posterName.replace("paeds", "paediatrics");
		posterName = posterName.replace("peadiatrics", "paediatrics");
		posterName = posterName.replace("sports-medicine", "sports medicine");
		posterName = posterName.replace("gen-med", "general medicine");
		posterName = posterName.replace("general-medicine", "general medicine");
		posterName = posterName.replace("accident-emergency", "emergency medicine");
		posterName = posterName.replace("infectious-diseases", "infectious diseases");
		posterName = posterName.replace("obsgynae", "obs and gynae");
		posterName = posterName.replace("obstetrics-and-gynaecology", "obs and gynae");
		posterName = posterName.replace("west-indies", "west indies");
		posterName = posterName.replace("cayman-islands", "cayman islands");
		posterName = posterName.replace("st-vincent-and-the-grenadines", "st vincents and the grenadines");
		posterName = posterName.replace("st-vincents-and-the-grenadines", "st vincent and the grenadines");
		posterName = posterName.replace("st-lucia", "st lucia");
		posterName = posterName.replace("sri-lanka", "sri lanka");
		posterName = posterName.replace("acute-care", "acute care");
		posterName = posterName.replace("new-zealand", "new zealand");
		posterName = posterName.replace("newzealand", "new zealand");
		posterName = posterName.replace("the-philippines", "the philippines");
		
		while (posterName.startsWith("-")) {
			posterName = posterName.substring(1);
		}
		while (posterName.endsWith("-")) {
			posterName = posterName.substring(0, posterName.length - 1);
		}
		parts = posterName.split("-");
		var result = "<tr>";
		result += "<td>" + student + "</td>";
		result += "<td><ul>";
		for (var i = 0; i<parts.length;i++) {
			if (parts[i].trim().length != 0 && parts[i] != "and") {
				result += "<li>" + parts[i] + "</li>";
			}
		}
		result += "</ul></td>";
		result += "<td>" + poster + "</td>";
		
		result += "<td>";
		var summary = jQuery(this).find("a")[1];
		if (summary === undefined) {
			
		}
		else {
			summary = jQuery(summary).attr("href"); 
			result += summary;
		}
		result += "</td>";
		
		result += "</tr>";
		jQuery("#output").append(result);
		jQuery("#input").remove();
	});
}

function studenthub_electives_remove(event) {
	jQuery(event.currentTarget).parent().remove();
}

function studentHubAdminUpload() {
	jQuery("tbody>tr").each(function() {
		var student = jQuery(this.childNodes[0]).text();
		var keywords = [];
		jQuery(this.childNodes[1]).find("li").each(function() {
			keywords.push(jQuery(this).text());
		});
		var poster = jQuery(this.childNodes[2]).text();
		var summary = jQuery(this.childNodes[3]).text();
		jQuery.post(ajaxurl, {action: 'studenthub_admin_import_elective', student: student, keywords: keywords, poster: poster, summary: summary});
	});
}