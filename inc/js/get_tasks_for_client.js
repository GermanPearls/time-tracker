function tt_update_task_dropdown() {
  var taskField = jQuery(".tt-form").find("[name='task-name']")[0];
  //cf7
  if (!taskField) {
    //wp forms
    taskField = document.getElementById(jQuery(".tt-form").find("label:contains(Task)").prop("for"));
    if (!taskField) {
      //edit pages
      taskField = jQuery("span.editable").find("select[title*='task']")[0];
    }
  }

  var clientName = "";
  var clientField = jQuery(".tt-form").find("[name='client-name']")[0];
  //cf7 forms
  if (clientField) {
    clientName =  encodeURIComponent(clientField.value);
  } else {
    //wpforms
    clientField = document.getElementById(jQuery(".tt-form").find("label:contains(Client)").prop("for"));
    if (clientField) {
      clientName = encodeURIComponent(clientField.value);
    } else {
      //edit pages
      clientField = jQuery("span.editable").find("select[title*='client']");
      if (clientField) {
        clientName = clientField.children("option:selected").text();
      }
    }
  }   

  if (clientName && taskField) {
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
  } else {
    if (!clientName) {
      if (!taskField) {
        console.log("Could not update task dropdown based on client choice. - Could not locate client or task field.");
      } else {
        console.log("Could not update task dropdown based on client choice. - Could not locate client field.");
      }
    } else if (!taskField) {
      console.log("Could not update task dropdown based on client choice. - Could not locate task field to update.");
    }
  }
}