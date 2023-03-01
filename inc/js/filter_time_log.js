//Initiates when filter button pressed on page showing all time entries
function tt_filter_time_log(event) {

    var first_date = "";
    var last_date = "";
    var client = "";
    var notes = "";
    var ticket = "";
    var ticketname = "";
    var task = "";
    var project = "";

    if (event.detail !== undefined && event.detail !== null && event.detail !== false) {
        //cf7
        var inputs = event.detail.inputs;    
        for (var i = 0; i < inputs.length; i++) {
            var input = inputs[i];
            if (input.name == 'first-date') {
                first_date = input.value;
            } else if (input.name == 'last-date') {
                last_date = input.value;
            } else if (input.name == 'client-name' && input.value != "null") {
                client = input.value;
            } else if (input.name == 'notes' && input.value != "null") {
                notes = input.value;
            } else if (input.name == 'project-name' && input.value != "null") {
                project = input.value;
            } else if (input.name == 'task-name' && input.value != "null") {
                //pull out task number, to the left of the hyphen  
                task = input.value;
                if (input.value.includes("-")) {
                    ticket = task.split("-", 1);
                    ticketname = task.split("-", 2);
                }
                //ticket = inputs[i].value;
            } //end if
        }  //end for loop
    } else {
        if (event.target !== undefined && event.target !== null && event.target !== false) {
            //wpf
            var targets = event.target;
            for (var i = 0; i < targets.length; i++) {
                var ttfieldname = jQuery(event.target[i]).attr('data-tt-field');
                if (ttfieldname !== undefined && ttfieldname !== false && event.target[i].value !== "" && event.target[i].value !== null) {
                    if (ttfieldname == 'client') {
                        client = event.target[i].value;
                    } else if (ttfieldname == 'project') {
                        project = event.target[i].value;
                    } else if (ttfieldname == 'task') {
                        task = event.target[i].value;
                        if (task.includes("-")) {
                            ticket = task.split("-", 1);
                            ticketname = task.split("-", 2);
                        }
                    } else if (ttfieldname == 'notes') {
                        notes = event.target[i].value;
                    } else if (ttfieldname == "first-date") {
                        first_date = event.target[i].value;
                    } else if (ttfieldname == "last-date") {
                        last_date = event.target[i].value;
                    }
                }
            }
        }
    }

    client = encodeURIComponent(client);
    notes = encodeURIComponent(notes);
    ticket = encodeURIComponent(ticket);
    project = encodeURIComponent(project);

    window.location.href = scriptDetails.tthomeurl + '/time-log/?client-name=' + client + '&notes=' + notes + '&task-number=' + ticket + '&task-name=' + ticketname + '&project-name=' + project + '&first-date=' + first_date + '&last-date=' + last_date;

}