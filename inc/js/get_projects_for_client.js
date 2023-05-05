//Update Project List When Client Name is Updated
function tt_update_project_dropdown() {
  var clientField = jQuery(".tt-form").find("[name='client-name']")[0];
  if (!clientField) {
    clientField = document.getElementById(jQuery(".tt-form").find("label:contains(Client)").prop("for"));
  }   

  var projectField = jQuery(".tt-form").find("[name='project-name']")[0];
  if (!projectField) {
    projectField = document.getElementById(jQuery(".tt-form").find("label:contains(Project)").prop("for"));
  }   

  if (clientField && projectField) {
    var clientName =  encodeURIComponent(clientField.value);
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
          projectField.innerHTML = response.data.details;
        } else {
          //failed
          console.log("Get projects for client function failed, details: " + response.data.details + ". Error: " + response.data.message);
        }
      }
    });
  }
}