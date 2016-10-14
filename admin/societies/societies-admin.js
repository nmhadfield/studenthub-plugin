jQuery(document).ready(function($) {
    $("#sh-search").click(function(event) {
    	findUserByEmail();
    });
});

function findUserByEmail() {
	var ajax = jQuery.get(ajaxurl, {action: 'studenthub_find_user', sh_email: jQuery('#sh-email').val()});
	ajax.done(function(html) {
		jQuery('#sh-found-user').empty();
		jQuery('#sh-found-user').append(html);
		jQuery('#sh-add').click(function(event) {
			addUser();
		});
	});
}

function addUser() {
	var ajax = jQuery.post(ajaxurl, {action: 'studenthub_add_user_to_committee', sh_society: jQuery('#sh-society').val(), sh_user: jQuery('#sh-userid').val(), sh_role: jQuery('#sh-role').val()});
	ajax.done(function(html) {
		jQuery('#sh-found-user').empty();
		
		var parent = jQuery("#committee-metabox").parent();
		jQuery("#committee-metabox").remove();
		parent.append(html);
	});
}
