//Update Project List When Client Name is Updated
var clientField = document.getElementsByName('client-name');
var projectField = document.getElementsByName('project-name');

if (clientField.length > 0 && projectField.length > 0) {
  clientField[0].addEventListener('change', update_project_list);
  //window.getElementsByName("client-name").addEventListener("change", updateTaskList);
}

function update_project_list() {
  var clientName =  encodeURIComponent(document.getElementsByName('client-name')[0].value);
  
  //query db for projects associated with client selected
  //has to be an ajax call to get info from server side
  //https://www.w3schools.com/js/js_ajax_database.asp
  xmlhttp = new XMLHttpRequest();
  
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementsByName('project-name')[0].innerHTML = this.responseText;
    }
  };

  xmlhttp.open("GET", getDirectory.pluginURL + "/function-tt-dynamic-project-dropdown.php?client="+clientName, true);
  xmlhttp.send();
}