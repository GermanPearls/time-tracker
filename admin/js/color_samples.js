document.addEventListener('DOMContentLoaded', function() {
	if (document.getElementById('tt-css-button-override')) {
		var bk = document.getElementById('tt-css-buttons-background-normal');
		if (bk) {
			bk.addEventListener('input', function() {
				document.getElementById('tt-color-display-buttons-background-normal').style.backgroundColor = this.value;    
			});
		}

		var txt = document.getElementById('tt-css-buttons-text-normal');
		if (txt) {
			txt.addEventListener('input', function() {
				document.getElementById('tt-color-display-buttons-text-normal').style.backgroundColor = this.value;    
			});
		}

		var bkhover = document.getElementById('tt-css-buttons-background-hover');
		if (bkhover) {
			bkhover.addEventListener('input', function() {
				document.getElementById('tt-color-display-buttons-background-hover').style.backgroundColor = this.value;    
			});
		}

		var txthover = document.getElementById('tt-css-buttons-text-hover');
		if (txthover) {
			txthover.addEventListener('input', function() {
				document.getElementById('tt-color-display-buttons-text-hover').style.backgroundColor = this.value;    
			});
		}
	}
});