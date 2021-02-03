<?php
    header("Content-type: text/css; charset: UTF-8");

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
}

.tt-sidebar-hr {
  margin: 10px;
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

/****************************/
/********** Tables **********/
/****************************/
.tt-table {
    text-align: left;
    position: relative;
    border-collapse: collapse; 
    /* max-width: fit-content; */
}

/*.tt-table.monthly-summary-table, .tt-table.yearly-summary-table {
    max-width: 50%;
}*/

.tt-align-right {
    text-align: right;
}

.tt-align-center {
    text-align: center;
}

.bold-font {
    font-weight: bold;
}

td.not-editable {
    background-color: <?php echo $neutral_background_light; ?>;
}

.divider-row {
    height: 10px;
    background-color:  <?php echo $brand_color; ?>;
}

.tt-total-row, .tt-header-row {
    font-weight: bold;
}

.tt-table th {
    font-weight: bold;
    position: sticky;
    background-color: <?php echo $brand_color; ?>; 
    color: <?php echo $text_color_light; ?>;
}

.pending-time-table #client {
    width: 10%;
    padding: 5px;
}

.pending-time-table #task-description {
    width: 15%;
    padding: 5px;
}

.pending-time-table #task-notes {
    width: 30%;
    padding: 5px;
}

.pending-time-table #task, .pending-time-table #start-time, .pending-time-table #end-time, .pending-time-table #time-logged, .pending-time-table #invoiced, 
.pending-time-table #invoice-number, .pending-time-table #invoiced-time, .pending-time-table #invoice-notes, .pending-time-table #status {
    width: 5%;
    padding: 5px;
}

/********** Time Log Table **********/
.time-log-table td, .time-log-table th, .task-list-table td, .task-list-table th, .pending-time-table td, .pending-time-table th {
    padding: 2px;
    /*font-family: 'Times New Roman', Times, serif;*/
    font-size: 12px;
    max-width: 300px;
}

/********** Project List Table **********/
.project-list-table td#time-details {
    text-align: right;
}

.project-list-table td#status-header-row {
    background-color: <?php echo $neutral_background_light; ?>;
    font-weight: bold;
}

/********** Monthly Summary Table **********/
.monthly-summary-table td, .yearly-summary-table td {
  padding: 10px;
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

/********** Open To Do List Table **********/
.task-list-table td#due-date, .task-list-table td#date-added, .task-list-table td#task-status, td#time-worked {
    text-align: center;
}

td#time-worked div {
    margin: 0 auto;
}

tr.over-time-estimate td#time-worked {
    color: red;
    font-weight: bold;
}

tr.late-date td#due-date {
    background-color: rgba(255, 51, 0, 0.5);  //transparent red
    color: #000000;
}

tr.soon-date td#due-date {
    background-color: rgba(255, 153, 51,0.5);  //transparent orange
    color: #000000;
}

tr.on-hold-date {
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
.filter-time-form form, .filter-time-form p {
	display: inline-block;
	padding-left: 10px;
	padding-right: 10px;
	margin-bottom: 0px;
}

.filter-time-form .wpcf7-date {
	padding-top: 5px;
	padding-bottom: 5px;
}

.filter-time-form #client-name, .filter-time-form #time-notes {
    width: 300px;
}

@media only screen and (max-width: 768px) {
    .filter-time-form #task-name {
        width: 100%;
        margin-bottom: 20px;
    }
}
@media only screen and (min-width: 768px) {
    .filter-time-form #task-name {
        width: 700px;
        margin-bottom: 20px;
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

#delete-confirm > button {
    background-color: <?php echo $alert_color; ?>;
    border-color: <?php echo $alert_color; ?>;
}