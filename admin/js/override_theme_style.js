document.addEventListener('DOMContentLoaded',function() {
    var chk = document.getElementById('tt-css-button-override');
    if (chk) {
        chk.addEventListener('change', function() {
            if (chk.checked == true) {
                document.getElementById('tt-css-buttons-background-normal').disabled = false;
                document.getElementById('tt-css-buttons-text-normal').disabled = false;
                document.getElementById('tt-css-buttons-background-hover').disabled = false;
                document.getElementById('tt-css-buttons-text-hover').disabled = false;
            } else {
                document.getElementById('tt-css-buttons-background-normal').disabled = true;
                document.getElementById('tt-css-buttons-text-normal').disabled = true;
                document.getElementById('tt-css-buttons-background-hover').disabled = true;
                document.getElementById('tt-css-buttons-text-hover').disabled = true;
            }
        });
    }
});