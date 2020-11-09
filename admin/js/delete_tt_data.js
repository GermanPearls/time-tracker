function delete_tt_data(btn) {
    if (btn == 'first') {
        document.getElementById('delete-confirm').hidden = false;
    } else if (btn == 'second') {
        jQuery.ajax({
            url: getDirectory.pluginURL + "/admin/function-tt-export-tables.php",
            type: "POST",
            data: "type=confirmed",
            success: function(data){
                //success
                window.alert('All of your time tracker data has been deleted.');
            },
            error:function(error){
                window.alert('There was an error when attempting to delete your time tracker data. Please check the logs or contact support.');
                console.log(error);
            }
        });
    }
}