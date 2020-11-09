function export_tt_data(button_type) {
    jQuery.ajax({
        url: getDirectory.pluginURL + "/admin/function-tt-export-button.php",
        type: "POST",
        data: 'type='+button_type,
        success: function(data){
            //success
            window.alert('Your time tracker data has been backed up to a file in your home user directory in a folder called tt_logs.');
        },
        error:function(error){
            window.alert('There has been an error backing up your time tracker data. Please check the logs or contact support.');
            console.log(error);
        }
    });
}