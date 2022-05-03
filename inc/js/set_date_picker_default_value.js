//Set default values for date pickers, if they exist in get value
document.addEventListener('DOMContentLoaded', function () {  //make sure doc is done loading before looking for element  
  var startDateField = document.getElementsByName('start-date');
  var endDateField = document.getElementsByName('end-date');  

  if (startDateField || endDateField ) {
    //https://stackoverflow.com/a/901144/7303640
    const params = new Proxy(new URLSearchParams(window.location.search), {
      get: (searchParams, prop) => searchParams.get(prop),
    });
    let start = params['start-date'];
    let end = params['end-date'];

    if (startDateField && start) {
      startDateField.value = start;
    }

    if (endDateField && end ) {
      endDateField.value = end;
    }
  }
});