//Update Task List When Client Name Changed
var clientField = document.getElementsByName('client-name');
var taskField = document.getElementsByName('task-name');
if (clientField.length > 0 && taskField.length > 0) {
  clientField[0].addEventListener('change', update_task_list);
  //window.getElementsByName("client-name").addEventListener("change", updateProjectList);
}

function update_task_list() {
  var clientName =  encodeURIComponent(document.getElementsByName('client-name')[0].value);

  //query db for tasks associated with client selected (that aren't closed)
  //has to be an ajax call to get info from server side
  //https://www.w3schools.com/js/js_ajax_database.asp
  xmlhttp = new XMLHttpRequest();
  
  xmlhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
      document.getElementsByName('task-name')[0].innerHTML = this.responseText;
    }
  };

  xmlhttp.open("GET", getDirectory.pluginURL + "/function-tt-dynamic-task-dropdown.php?client="+clientName, true);
  xmlhttp.send();
}