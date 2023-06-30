function tt_update_task_dropdown() {
  var taskField = jQuery(".tt-form").find("[name='task-name']")[0];
  if (!taskField) {
    taskField = document.getElementById(jQuery(".tt-form").find("label:contains(Task)").prop("for"));
  }

  var clientField = jQuery(".tt-form").find("[name='client-name']")[0];
  if (!clientField) {
    clientField = document.getElementById(jQuery(".tt-form").find("label:contains(Client)").prop("for"));
  }   

  if (clientField && taskField) {
    var clientName =  encodeURIComponent(clientField.value);
    var send = {
        'security': wp_ajax_object_tt_update_task_list.security,
        'action': 'tt_update_task_list',
        'client': clientName
    };
    jQuery.ajax({
      action: 'tt_update_task_list',
      url: wp_ajax_object_tt_update_task_list.ajax_url,
      type: 'POST',
      data: send,
      success: function(response) {
        if (response.success) {
          //success
          taskField.innerHTML = response.data.details;
        } else {
          //failed
          console.log("Get tasks for client function failed, details: " + response.data.details + ". Error: " + response.data.message);
        }
      }
    });
  }
}