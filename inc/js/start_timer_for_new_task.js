function tt_start_timer_for_new_task() {
    var send = {
		'security': wp_ajax_object_tt_start_timer_for_new_task.security,
		'action': 'tt_start_timer_for_new_task'
	};
	jQuery.ajax({
		action: 'tt_start_timer_for_new_task',
        url: wp_ajax_object_tt_start_timer_for_new_task.ajax_url,
        type: "POST",
        data: send ,
        success: function(results){
			if (results.success) {
            	//success
            	start_timer_for_task(results.data.client, results.data.ticket);
			} else {
            	//failure
				window.alert('There was an error opening the time entry for the last task. Please check the logs or contact support.');
            	console.log(results.msg);				
			}
        }
    });
}