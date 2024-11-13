//Update Project List When Client Name is Updated
function tt_update_project_dropdown() {
  var clientName = "";
  //cf7 forms
  var clientField = jQuery(".tt-form").find("[name='client-name']")[0];
  if (clientField) {
    clientName =  encodeURIComponent(clientField.value);
  } else {
    //wp forms
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

  //cf7 forms
  var projectField = jQuery(".tt-form").find("[name='project-name']")[0];
  if (!projectField) {
    //wpforms
    projectField = document.getElementById(jQuery(".tt-form").find("label:contains(Project)").prop("for"));
    if (!projectField) {
      //edit pages
      projectField = jQuery("span.editable").find("select[title*='project']")[0];
    }
  }   

  if (clientName && projectField) {
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
  } else {
    if (!clientName) {
      console.log("Could not find client chosen.");
    } else if (!projectField) {
      console.log("Could not find project field.");
    }
  }
}