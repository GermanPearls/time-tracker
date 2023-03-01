//Set default values for date pickers, if they exist in get value, filter time form
document.addEventListener('DOMContentLoaded', function () {  //make sure doc is done loading before looking for element  
  var startDateField = document.getElementById('first-date');
  if (!startDateField) {
    var id = jQuery("label:contains(First Date)").prop("for");
    if (id) {
      startDateField = document.getElementById(id);
    }
  }

  var endDateField = document.getElementById('last-date');  
  if (!endDateField) {
    var id = jQuery("label:contains(End Date)").prop("for");
    if (id) {
      endDateField = document.getElementById(id);
    }
  } 

  if (startDateField || endDateField ) {
    //https://stackoverflow.com/a/901144/7303640
    const params = new Proxy(new URLSearchParams(window.location.search), {
      get: (searchParams, prop) => searchParams.get(prop),
    });
    let start = params['first-date'];
    let end = params['last-date'];

    if (startDateField && start) {
      startDateField.setAttribute('value', start);
    }

    if (endDateField && end ) {
      endDateField.setAttribute('value', end);
    }
  }
});