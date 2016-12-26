function addUser() {
	var ajax = jQuery.post(ajaxurl, {action: 'studenthub_add_user_to_committee', sh_society: jQuery('#sh-society').val(), sh_user: jQuery('#sh-userid').val(), sh_role: jQuery('#sh-role').val()});
	ajax.done(function(html) {
		jQuery('#sh-found-user').empty();
		
		var parent = jQuery("#committee-metabox").parent();
		jQuery("#committee-metabox").remove();
		parent.append(html);
	});
}
