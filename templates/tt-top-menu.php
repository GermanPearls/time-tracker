<?php
/**
 * Top Menu Template for Time Tracker Pages
 *
 * 
 * @since 3.1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Templates;

 ?>
<div id="tt-top-menu-bar" class="tt-top-menu-bar">
    <ul>
        <li id="tt-home-button" class="tt-top-menu-header"><a href=<?php echo TT_HOME ?> class="tt-top-menu-button">Dashboard</a></li>
        <li id="tt-favorite-functions" class="tt-top-menu-header">Quick Links
            <ul>
                <li><a href=<?php echo TT_HOME . "open-task-list" ?> class="tt-top-menu-button">Open Tasks</a></li>
                <li><a href=<?php echo TT_HOME . "new-time-entry" ?> class="tt-top-menu-button">Log Time</a></li>
                <li><a href=<?php echo TT_HOME . "new-task" ?> class="tt-top-menu-button">New Task</a></li>
                <li><a href=<?php echo TT_HOME . "pending-time" ?> class="tt-top-menu-button">Pending Time</a></li>
            </ul>
        </li>
        <li id="tt-time-functions" class="tt-top-menu-header">Time
            <ul>
                <li><a href=<?php echo TT_HOME . "new-time-entry" ?> class="tt-top-menu-button">Log Time</a></li>
                <li><a href=<?php echo TT_HOME . "time-log" ?> class="tt-top-menu-button">All Time Entries</a></li>
                <li><a href=<?php echo TT_HOME . "pending-time" ?> class="tt-top-menu-button">Pending Time</a></li>
            </ul>
        </li>
        <li id="tt-task-functions" class="tt-top-menu-header">Tasks
            <ul>
                <li><a href=<?php echo TT_HOME . "new-task" ?> class="tt-top-menu-button">New Task</a></li>
                <li><a href=<?php echo TT_HOME . "new-recurring-task" ?> class="tt-top-menu-button">New Recurring Task</a></li>
                <li><a href=<?php echo TT_HOME . "task-list" ?> class="tt-top-menu-button">All Tasks</a></li>
                <li><a href=<?php echo TT_HOME . "open-task-list" ?> class="tt-top-menu-button">Open Tasks</a></li>
                <li><a href=<?php echo TT_HOME . "recurring-task-list" ?> class="tt-top-menu-button">All Recurring Tasks</a></li>
            </ul>
        </li>
        <li id="tt-project-functions" class="tt-top-menu-header">Projects
            <ul>
                <li><a href=<?php echo TT_HOME . "new-project" ?> class="tt-top-menu-button">New Project</a></li>
                <li><a href=<?php echo TT_HOME . "projects" ?> class="tt-top-menu-button">All Projects</a></li>
            </ul>
        </li>
        <li id="tt-client-functions" class="tt-top-menu-header">Clients
            <ul>
                <li><a href=<?php echo TT_HOME . "new-client" ?> class="tt-top-menu-button">New Client</a></li>
                <li><a href=<?php echo TT_HOME . "clients" ?> class="tt-top-menu-button">All Clients</a></li>
            </ul>
        </li>
    </ul>
</div>