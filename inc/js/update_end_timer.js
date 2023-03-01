//Update the "End" Timer for a Time Entry to the Current Time
function tt_update_timer(timer) {
    var d = new Date();
    
    var month = d.getMonth() + 1;
    var day = d.getDate();
    var year = d.getFullYear().toString().slice(2,4);
    var hour = d.getHours();
    var minutes = d.getMinutes();
    if (minutes <10) {
        minutes = "0" + minutes;
    }

    if (hour >= 12) {
        var ampm = "PM";
        if (hour > 12) {
            hour = hour - 12;
        }
    } else {
        var ampm = "AM";
    }

    var dstring = month + "/" + day + "/" + year + " " + hour + ":" + minutes + " " + ampm;

    //document.getElementById('end-time').value = dstring;
    timer.value = dstring;
}

jQuery(window).on('load', function() {
    //look for cf7 end timer
    var endtimer = jQuery('.tt-form').find('#end-time')[0];
    //if not found look for wpf end timer
    if (!endtimer) {
        endtimer = document.getElementById(jQuery('.tt-form').find('label:contains(End Time)').prop('for'));
    }    
    if (endtimer) {
        //update every 600 milliseconds
        var autoupdate = setInterval(function() {
            tt_update_timer(endtimer);
        }, 600);

        //if user enters data stop updating
        jQuery(endtimer).on('input', function() {
            clearInterval(autoupdate);
        });
    }

    //wpf only
    var starttimer = document.getElementById(jQuery('.tt-form').find('label:contains(Start Time)').prop('for'));
    if (starttimer) {
        if (starttimer.value == '' || starttimer.value == null) {
            tt_update_timer(starttimer);
        }
    }
});
