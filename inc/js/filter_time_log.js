//Initiates when filter button pressed on page showing all time entries

function tt_filter_time_log(event) {
    var inputs = event.detail.inputs;
    
    for (var i = 0; i < inputs.length; i++) {
        
        var input = inputs[i];

        if (input.name == 'first-date') {
            var first_date = input.value;
        } else if (input.name == 'last-date') {
            var last_date = input.value;
        } else if (input.name == 'client-name') {
            var client = input.value;
        } else if (input.name == 'time-notes') {
            var notes = input.value;
        } else if (input.name == 'task-name') {
            //pull out task number, to the left of the hyphen  
            var task = input.value;
            var ticket = task.split("-", 1);
            //ticket = inputs[i].value;
        } //end if
    }  //end for loop

    client = encodeURIComponent(client);
    notes = encodeURIComponent(notes);
    ticket = encodeURIComponent(ticket);

    window.location.href = '/time-tracker/time-log/?client=' + client + '&notes=' + notes + '&task=' + ticket + '&start=' + first_date + '&end=' + last_date;

}