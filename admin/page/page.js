jQuery(document).ready(function($) {
    $('#sh-add-another').click(function() {
    	sh_admin_page_add_forum();
    });
});


function sh_admin_page_add_forum() {
	var count = jQuery('#sh-forum-list li').length;
	
	var source = jQuery(jQuery('#sh-forum-list li')[0]); 
	var copy = source.clone();
	
	copy.find('select').val('');
	copy.find('select').attr('name', source.find('select').attr('name').replace('[0][', '[' + count + ']['));
	copy.find('input').removeAttr('checked');
	copy.find('input').attr('name', source.find('input').attr('name').replace('[0][', '[' + count + ']['));
	jQuery('#sh-forum-list').append(copy);
}