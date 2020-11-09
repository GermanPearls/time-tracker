function tt_clear_sql_error() {
    jQuery.ajax({
        url: getDirectory.pluginURL + "/function-tt-clear-sql-error.php",
        type: "POST",
        data: { update: "clear" } ,
        success: function(data){
            //success
            //window.alert('Your time tracker data has been backed up to a file in your home user directory in a folder called tt_logs.');
            document.getElementById("sql-error-alert").innerHTML = "";
        },
        error:function(error){
            //window.alert('There has been an error backing up your time tracker data. Please check the logs or contact support.');
            console.log(error);
        }
    });
}