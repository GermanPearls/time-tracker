function tt_update_task_dropdown() {
  var taskField = document.getElementsByName('task-name');
  var clientField = document.getElementsByName('client-name');
  if (clientField.length > 0 && taskField.length > 0) {
    var clientName =  encodeURIComponent(clientField[0].value);
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
          //console.log(response.data.details);
          taskField[0].innerHTML = response.data.details;
        } else {
          //failed
          console.log('Get tasks for client function failed' + response.data.details + '. Error: ' + response.data.message);
        }
      }
    });
  }
}