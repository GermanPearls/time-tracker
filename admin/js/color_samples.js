document.addEventListener('DOMContentLoaded', function() {
    var bk = document.getElementById('tt-css-buttons-background-normal');
    bk.addEventListener('input', function() {
        document.getElementById('tt-color-display-buttons-background-normal').style.backgroundColor = this.value;    
    });

    var txt = document.getElementById('tt-css-buttons-text-normal');
    txt.addEventListener('input', function() {
        document.getElementById('tt-color-display-buttons-text-normal').style.backgroundColor = this.value;    
    });

    var bkhover = document.getElementById('tt-css-buttons-background-hover');
    bkhover.addEventListener('input', function() {
        document.getElementById('tt-color-display-buttons-background-hover').style.backgroundColor = this.value;    
    });

    var txthover = document.getElementById('tt-css-buttons-text-hover');
    txthover.addEventListener('input', function() {
        document.getElementById('tt-color-display-buttons-text-hover').style.backgroundColor = this.value;    
    });
});