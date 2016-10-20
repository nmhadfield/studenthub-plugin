function sh_expandCollapse(event, id) {
	var button = jQuery(event.currentTarget);
	var div = jQuery('#' + id + '-widget-content');
	if (button.hasClass("collapsed")) {
		button.removeClass("collapsed");
		div.removeClass("collapsed");
	}
	else {
		button.addClass("collapsed");
		div.addClass("collapsed");
	}
	event.preventDefault();
}