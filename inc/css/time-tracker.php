<?php
    header("Content-type: text/css; charset: UTF-8");

    //default fonts
    $font_family = 'Arial, sans-serif';
	$font_weight = '400';
	$font_size = '12px';
	$line_height = '1em';
    
	//default colors
    $brand_color = '#01375d';  //base blue
    $brand_color_minor = '#809bae';  //lighter grayish blue

    $neutral_background = '#d3d3d3';  //light gray
    $neutral_background_light = '#e4e4e4';  //lighter grayish
    $neutral_background_dark = '';
    
    $text_color_light = '#ffffff';  //white
    $text_color_dark = $brand_color;
    
    $alert_color = 'red';
?>


/****************************************/
/********** Time Tracker Menu **********/
/***************************************/
.tt-menu-heading {
    margin-top: 20px;
    margin-bottom: -10px;
}

/***********************************/
/********** Page Template **********/
/***********************************/

/*** contains sidebar and page content ***/
.tt-page-template div#content.site-content {
    margin-bottom: 10px;
}

/*** page content to the side of sidebar ***/
div#primary.content-area.tt-content {
  float: left;
  width: 70%;
}

div#primary.content-area.tt-content:after, div.tt-sidebar:after {
    clear:both;
}

/*header.page-header.tt-page-header {
    margin-top: 0;
    margin-bottom: 0;
}*/

.full-width-content .container.grid-container {
    padding: 20px 40px;
}

/**********************************/
/********** Page Header **********/
/*********************************/
header.page-header.tt-page-header {
  padding: 20px 0;
  overflow: hidden;
  margin-bottom: 0;
}

h1.page-title.tt-page-title {
  padding: 20px;
  float: left;
  margin-block-start: 0px;
  margin-block-end: 0px;
}

.tt-header-button {
  margin-top: 20px;
  margin-right: 20px;
  float: right;
}

.page-title.tt-page-title:after, .tt-header-button:after {
  clear: both;
}

header.page-header.tt-page-header::after {
  content: "";
  clear:both;
}


/*** content below the page title ***/
#main.site-main.tt-main {
    margin-left: 20px;
    margin-right: 20px;
}

/*****************************/
/********** Sidebar **********/
/*****************************/
div.tt-sidebar {
  float:left;
  width: 20%;
  background-color: <?php echo $neutral_background; ?>;
  padding: 20px;
  margin-top: 40px;
  margin-right: 20px;
  max-width: 300px;
}

.tt-sidebar-header {
  font-size: 1.3em;
  display: block;
  padding: 5px 10px;
}

.tt-sidebar-hr {
  margin: 10px;
}

.tt-mobile-menu, .tt-mobile-menu a.tt-sidebar-button, .tt-mobile-menu .tt-sidebar-header, .tt-mobile-menu .tt-sidebar-hr {
	text-align: center;
}

.tt-mobile-menu-button {
    -moz-appearance: button;
    -webkit-appearance: button;
    appearance: button;
    padding: 5px 20px;
    background-color: <?php echo $brand_color; ?>;
    box-shadow: 5px 5px 8px #888888;
    color: <?php echo $text_color_light; ?>;
    font-size: 1.2em;
    line-height: 1.2em;
    text-decoration: none;
	text-align: center;
}

.tt-mobile-menu-button:after {
  clear:both;
}

div #tt-nav-links {
  display: none;
  margin-top: 20px;
  background-color: lightgray;
}

@media only screen and (min-width: 768px) {
	.tt-mobile-menu {
		display: none;
	}
}

@media only screen and (max-width: 768px) {
	div.tt-sidebar {
		display: none;
  	}

	.content-area {
		width: 100%;
		margin: 0px;
		border: 0px;
		padding: 0px;
	}

	div#primary.content-area.tt-content {
		width: 100%;
	}

	.content-area .site {
		margin:0px;
	}

	.full-width-content .container.grid-container {
		padding: 10px;
	}

	h1.page-title.tt-page-title {
		padding-left: 0px;
	}

    header.page-header.tt-page-header {
      padding-top: 0px;
      padding-bottom: 0px;
	}
}

/*****************************/
/********** Buttons **********/
/****************************/
.tt-buton a {
    margin: 10px 10px 10px 20px;
    display: block;
    text-decoration: none;
}

.tt-button:after, a.tt-sidebar-button:after {
    clear:both;
}

a.tt-sidebar-button, a.tt-header-button, button.end-work-timer {
    -moz-appearance: button;
    -webkit-appearance: button;
    appearance: button;
    padding: 5px 10px;
    background-color: <?php echo $brand_color; ?>;
    box-shadow: 5px 5px 8px #888888;
    color: <?php echo $text_color_light; ?>;
    font-size: 1.2em;
    line-height: 1.2em;
    text-decoration: none;
}

a.tt-sidebar-button:visited, a.tt-header-button:visited {
    color: <?php echo $text_color_light; ?>;
}

button.end-work-timer:hover, a.tt-sidebar-button:hover, a.tt-header-button:hover {
    background-color: <?php echo $brand_color_minor; ?>;
    color: <?php echo $text_color_dark; ?>;
    text-decoration: none;
    font-weight: bold;
}

a.tt-sidebar-button {
    width: 75%; /*of sidebar*/
    margin: 10px auto;
}

button.clear-error {
    background-color: <?php echo $neutral_background; ?>;
    color: <?php echo $text_color_dark; ?>;
    padding: 5px 10px;
    font-size: 0.8em;
    border-radius: 0;
    margin-left: 10px;
    box-shadow: 5px 5px 8px #888888;
}

button.clear-error:hover {
    background-color: <?php echo $brand_color; ?>;
    color: <?php echo $text_color_light; ?>;
    font-weight: bold;
}

/********** Button to Start Work Timer **********/
/********** Button to View Task Detail **********/
.start-work-timer, .open-task-detail, .chart-button {
    padding: 1px 2.5px;
    background-color: <?php echo $brand_color; ?>;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 12px;
    font-weight: normal;
    border-radius: 0;
}

.start-work-timer:hover, .open-task-detail:hover, .chart-button:hover  {
    padding: 1px 2.5px;
    background-color: <?php echo $brand_color_minor; ?>;
    color: <?php echo $text_color_dark; ?>;
    margin-right: 5px;
    margin-bottom: 5px;
    font-size: 12px;
    border-radius: 0;
    text-decoration: none;
}

/********** Button to End Timer **********/
.end-work-timer {
    color: <?php echo $text_color_light; ?>;
    background-color: <?php echo $brand_color_minor; ?>;
    margin-bottom: 10px;
}

.end-work-timer:hover {
    color: <?php echo $text_color_light; ?>;
    background-color: <?php echo $brand_color; ?>;
    text-decoration: none;
}

/********** Page Navigation Buttons **********/
.tt-pagination-nav a, .tt-pagination-nav .current {
    -moz-appearance: button;
    -webkit-appearance: button;
    appearance: button;
    padding: 5px 10px;
	margin: 5px;
    box-shadow: 5px 5px 8px #888888;
    font-size: 1.1em;
    line-height: 1.1em;
    text-decoration: none;
}

.tt-pagination-nav a {
    background-color: <?php echo $brand_color; ?>;
    color: <?php echo $text_color_light; ?>;
}

.tt-pagination-nav a:hover {
	color: <?php echo $brand_color_minor; ?>;
}

.tt-pagination-nav .current {
    background-color: <?php echo $brand_color_minor; ?>;
    color: <?php echo $text_color_dark; ?>;
}

@media only screen and (max-width: 768px) {
  a.tt-sidebar-button, a.tt-header-button, button.end-work-timer, button.clear-error {
    padding: 5px;
  }

  .tt-buton a {
	border-radius: 0;
  }
} 

/****************************/
/********** Tables **********/
/****************************/
.tt-table {
  	display: table;  
  	max-width: 100%;
}

.tt-table td {
    position: inherit;
	padding: 4px;
    border-collapse: collapse; 
	font-family: <?php echo $font_family; ?>;
	font-weight: <?php echo $font_weight; ?>;
	font-size: <?php echo $font_size; ?>;
	line-height: <?php echo $line_height; ?>;
	vertical-align: initial;
}

.tt-table th {
    font-weight: bold;
    position: sticky;
    background-color: <?php echo $brand_color; ?>; 
    color: <?php echo $text_color_light; ?>;
    vertical-align: bottom;
}

.tt-table td.not-editable {
    background-color: <?php echo $neutral_background_light; ?>;
}

.divider-row {
    height: 10px;
    background-color:  <?php echo $brand_color; ?>;
}

.tt-total-row, .tt-header-row {
    font-weight: bold;
}

/********** Table Column Widths **********/
.tt-col-width-five-pct {
	width: 5%;
}

.tt-col-width-ten-pct {
	width: 10%;
}

.tt-col-width-fifteen-pct {
	width: 15%;
}

.tt-col-width-thirty-pct {
	width: 30%;
}

.tt-even-columns-2 td {
    width: 50%;
}

.tt-even-columns-3 td {
    width: 33%;
}

.tt-even-columns-4 td {
    width: 25%;
}

.tt-even-columns-5 td {
    width: 20%;
}

.tt-even-columns-6 td {
    width: 16%;
}

.tt-even-columns-7 td {
    width: 14%;
}

.tt-even-columns-8 td {
    width: 12%;
}

.tt-even-columns-9 td {
    width: 11%;
}

.tt-even-columns-10 td {
    width: 10%;
}

.tt-even-columns-11 td {
    width: 9%;
}

.tt-even-columns-12 td {
    width: 8%;
}

/********** Project List Table **********/
.project-list-table td#status-header-row {
    background-color: <?php echo $neutral_background_light; ?>;
    font-weight: bold;
}

/********** Monthly and Yearly Summary Tables **********/
.monthly-summary-table td, .yearly-summary-table td {
  padding: 10px;
}

/********** Open To Do List Table **********/
td#time-worked div {
    margin: 0 auto;
}

td#time-worked.over-time-estimate  {
    color: red;
    font-weight: bold;
}

td#due-date.late-date {
    background-color: rgba(255, 51, 0, 0.5);  //transparent red
    color: #000000;
}

td#due-date.soon-date {
    background-color: rgba(255, 153, 51,0.5);  //transparent orange
    color: #000000;
}

td.on-hold-date {
    background-color: rgba(136, 132, 126,0.5);  //transparent gray
    color: #000000;
}

/***************************/
/********** Forms **********/
/***************************/

.tt-form input[type="submit"], .tt-form button {
    padding: 1.2rem;
    background-color: <?php echo $brand_color_minor; ?>;
    color: <?php echo $text_color_dark; ?>;
}

.tt-form input[type="submit"]:hover, .tt-form button:hover {
    padding: 1.2rem;
    background-color: <?php echo $brand_color; ?>;
    color: <?php echo $text_color_light; ?>;
    text-decoration: none;
}

.tt-form input[type="text"], .tt-form input[type="password"], .tt-form input[type="email"], .tt-form input[type="url"],
.tt-form input[type="date"], .tt-form input[type="month"], .tt-form input[type="time"], .tt-form input[type="datetime"],
.tt-form input[type="datetime-local"], .tt-form input[type="week"], .tt-form input[type="number"],
.tt-form input[type="search"], .tt-form input[type="tel"], .tt-form input[type="color"], .tt-form textarea, .tt-form select {
    padding: 1rem;
    border: 1px solid <?php echo $neutral_background; ?>;
}

/********** Form for Fitlering **********/
.filter-time-form {
	border: 1px solid lightgray;
	padding: 10px;
	margin: 20px 0;
}

.filter-time-form form, .filter-time-form p {
	display: inline-block;
	padding-left: 10px;
	padding-right: 10px;
	margin-bottom: 0px;
}

.filter-time-form .wpcf7-date {
	padding-top: 5px;
	padding-bottom: 5px;
    margin-bottom: 20px;
}

.filter-time-form #client-name, .filter-time-form #time-notes {
    max-width: 300px;
}

.filter-time-form #task-name {
    max-width: 700px;
    margin-bottom: 20px;
}

@media only screen and (max-width: 768px) {
    .filter-time-form #task-name, .filter-time-form #client-name, .filter-time-form #time-notes, .filter-time-form #project-name {
        width: 100%;
        margin-bottom: 20px;
    }

    .filter-time-form #first-date, .filter-time-form #last-date {
       width: calc(100% - 20px);
	}
}


/***************************/
/********** Misc**********/
/***************************/
.float-right {
    float:right;
}

.float-right::after {
    content: "";
    clear:both;
}

.tt-align-right {
    text-align: right;
}

.tt-align-center {
    text-align: center;
}

.bold-font {
    font-weight: bold;
}


/*********************************/
/********** Admin Area **********/
/********************************/
#tt-options > input[type="submit"] {
    -webkit-appearance: button;
    -moz-appearance: button;
    appearance: button;
    font-size: 1.2em;
    font-weight: bold;
    padding: 5px 10px;
}

#tt-options {
    margin-left: 20px;
    margin-top: 20px;
}

.tt-admin-section, .tt-indent {
    padding-left: 20px;
}

.tt-admin-to-front.button-primary {
    margin-left: 40px;
}


/*********************************/
/**********Error Messages**********/
/*********************************/
.error-message, .tt-important, .tt-warning {
    font-size: 1.2em;
    font-weight: bold;
    color: <?php echo $alert_color; ?>;
    padding-left: 20px;
    padding-top: 20px;
}

div#no-client-alert, div#no-task-alert {
    padding: 20px;
    border: 2px solid <?php echo $alert_color; ?>;
}

'#delete-confirm > button {
    background-color: <?php echo $alert_color; ?>;
    border-color: <?php echo $alert_color; ?>;
}