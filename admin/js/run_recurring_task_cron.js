function run_recurring_task_cron(button_type) {
	var send = {
		'security': wp_ajax_object_tt_run_recurring_task_cron.security,
		'action': 'tt_run_recurring_task_cron',
		'type': +button_type	
	};
    jQuery.ajax({
        url: wp_ajax_object_tt_run_recurring_task_cron.ajax_url,
        type: "POST",
        data: send,
        success: function(results){
        	if (results.success) {
				//success
        	    window.alert(results.data.msg);
       	 	} else {
         	   window.alert('There was an error manually checking for missing recurring tasks. Please check the logs or contact support.');
          	  console.log(results.data.msg);
        	}
		}
	});
}