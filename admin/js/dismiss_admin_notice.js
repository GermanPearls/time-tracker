function dismiss_admin_notice(button_type) {
	var send = {
		'security': wp_ajax_object_tt_dismiss_admin_notice.security,
		'action': 'tt_dismiss_admin_notice',
		'type': +button_type	
	};
    jQuery.ajax({
        url: wp_ajax_object_tt_dismiss_admin_notice.ajax_url,
        type: "POST",
        data: send,
        success: function(results){
        	if (results.success) {
				//success
        	    jQuery(document.getElementById('tt-admin-notice-review')).hide();
       	 	} else {
          	    console.log('tt error delaying the admin notice');
                console.log(results.data.msg);
        	}
		}
	});
}