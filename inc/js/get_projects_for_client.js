//Update Project List When Client Name is Updated
function tt_update_project_dropdown() {
  var clientField = document.getElementsByName('client-name');
  var projectField = document.getElementsByName('project-name');
  if (clientField.length > 0 && projectField.length > 0) {
    var clientName =  encodeURIComponent(clientField[0].value);
    var send = {
        'security': wp_ajax_object_tt_update_project_list.security,
        'action': 'tt_update_project_list',
        'client': clientName
    };
    jQuery.ajax({
      action: 'tt_update_project_list',
      url: wp_ajax_object_tt_update_project_list.ajax_url,
      type: 'POST',
      data: send,
      success: function(response) {
        if (response.success) {
          //success
          //console.log(response.data.details);
          projectField[0].innerHTML = response.data.details;
        } else {
          //failed
          console.log('Get projects for client function failed' + response.data.details + '. Error: ' + response.data.message);
        }
      }
    });
  }
}