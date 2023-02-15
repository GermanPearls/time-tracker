//Update the "End" Timer for a Time Entry to the Current Time

function update_end_timer() {
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

    document.getElementById('end-time').value = dstring;
}

jQuery(window).on("load", function() {
    var endtimer = document.getElementById('end-time');
	if (endtimer) {
		var autoupdate = setInterval(function() {
			update_end_timer();
		}, 60000);

        jQuery(endtimer).on("input", function() {
            clearInterval(autoupdate);
        });
	}
});
