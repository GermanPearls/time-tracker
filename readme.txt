=== Time Tracker ===
Contributors: germanpearls
Donate link: https://www.paypal.com/paypalme/germanpearls
Tags: time tracker, time management, freelancer tools,  to do list, billing hours
Requires at least: 5.3
Tested up to: 6.6.1
Requires PHP: 7.0
Stable tag: 3.0.13
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Time Tracker enables freelancers to clients, projects, tasks (including recurring), time, billing info and more on private pages of their website.

== Description ==
Time Tracker enables you to manage your to do lists, time worked, billable time, recurring tasks, and projects. Track your clients, projects, tasks, time, and billing information on private pages of your own website.  Don't worry about us looking at or seeling your data as you'll maintain your data within your own WordPress database.

= Requirements =
This plugin is an add-on and **requires either Contact Form 7 or WP Forms** plugin to work properly.

**NEW:** Based on user request, this plugin now works with EITHER **Contact Form 7** OR **WP Forms**.

One of these form plugins must be installed and activated before installing Time Tracker.

Note that Time Tracker is in no way affiliated with Contact Form 7 or WP Forms.

= Time Tracker Features =
Time Tracker is a freelancer's time management tool. It keeps track of:

* Clients including contact information, separate bill-to information, and how the client found you
* Projects, which can have several related tasks
* Recurring weekly, monthly, or yearly tasks; Tasks will automatically be added to your to do list based on your chosen frequence
* To do list with open items, due dates, time budget, and task status
* Time worked including work notes and time billed

Time Tracker helps to:

* manage your open to do list, prioritizing items by due date
* track time spent on each task, as compared to budgeted time
* track which time has been billed
* monitor time billed vs time worked
* keep a log of work notes related to each task
* manage third party (or white label) work by organizing work by "bill to"
* monitor weekly and monthly time to compare against goals

== Installation ==
 
**Please Note: This plugin requires a form plugin to function properly. (Contact Form 7 or WP Forms are currently supported prerequisites. One of these must be installed and activated prior to activating Time Tracker.)**

1. Install and activate the Contact Form 7 or WP Forms plugin, if you don't have either already installed
2. Install and activate the Time Tracker plugin
3. Go to the Time Tracker Options menu page and add your business specific information, then save all changes
4. To begin using your new task management system, open a browser and navigate to your website /time-tracker. (NOTE: You will need to be logged in as an admin for the time tracker pages to be accessible.)
 
== Frequently Asked Questions ==
 
= Where is my information stored? =
 
All of the information you enter on a Time Tracker screen is stored in your WordPress database with your website host.
 
= How can I backup my Time Tracker information? =
 
To manually backup your Time Tracker client, project, task, time, etc. data, go to the Time Tracker Tools page in your WordPress admin area and click the backup button.
This will create a backup of your information and place it on your server. (The file will be dated and located in your user's directory in a folder named 'tt_logs'.)
Note: If you use a plugin or service to backup your WordPress database regularly, this will also backup your Time Tracker information.

= Will this work if I perform white label services or bill to third parties? =
 
Yes, Time Tracker keeps track of time by both client (end user) and bill to company, helping you to bill appropriately.

= I perform work under different business names, would Time Tracker work for me? =
 
Yes, by using the bill to field for your different businesses, Time Tracker can help you track time for your different companies.

= Can I sort work into different categories? =
 
Yes, Time Tracker lets you define your own work categories in the options screen.

= Does this take a lot of work to set up? =
 
No, to set up Time Tracker simply install it and setup your options like work categories and bill to names. The activation process creates everything else necessary including screens, menus, work summary tables, etc.

= What if I make a mistake when I enter a task, can I correct it? =
 
Yes, the screens of Time Tracker display your information in easy to read tables where you can easily edit information. All items can also be deleted from the user interface.

= Can I use Time Tracker on a multi-site installation? =
 
Time Tracker hasn't yet been tested on a multi-site application.

= WordPress is installed in a subfolder / subdirectory, will Time Tracker still work? =
 
Recent updates have improved the capability of this plugin to work in a subfolder/subdirectory installation. We welcome you to test it in your
installation and provide detailed feedback if you find features that don't work so we can work to improve this capability.

== Screenshots ==
 
1. The homepage of Time Tracker with quicklinks to important pages. Note: All front facing pages are private by default.
2. Entering time for a given client and task, with ample room for work notes.
3. Open to do list ordered by target due date. Includes time worked and progress bars for tasks with time projections.
4. Easily view time that hasn't been invoiced yet. Time is sorted by billable party with quick links to each section.
5. Admin Section - Create your own work categories, client referral names, and billable parties.
6. Admin Options - Backup your time data or delete all your data at will.

 
== Changelog ==

= 3.0.13 =
New Feature: Add button on client table to view all tasks for the client
New Feature: Added Time Tracker links to admin header bar
Improvement: Adjust display of client on front end to allow user to change client for a task via dropdown
Improvement: Adjust display of project on front end to allow user to change project for a task via dropdown
Improvement: Adjust time display to view Invoice details more compact
Improvement: Consolidate table field definitions into class for consistency and brevity.
Improvement: Moved sidebar menu to top menu
Improvement: Clean up Time Tracker menu (sidebar and top menu)
Improvement: Add button for exporting pending time in IIF format, ready for importing into QuickBooks for automated Invoice creation.
Improvement: Updated project listing to display projects with statuses not in predefined list as 'other' status
Improvement: Misc code cleanup
Fix: Removed deprecated php input filter
Fix: Update database to null if empty string passed

= 3.0.12 =
Fix: Fix fatal activation error related to admin notice looking for option before it was set
Fix: Include form subclass dependency in deletor class to fix fatal deletion error
Fix: Replaced obsolete? functions causing fatal activation error

= 3.0.11 =
Improvement: Modified installation / activation functions to streamline and avoid potential problems

= 3.0.10 =
Improvement: Delete TT pages on plugin deactivation / Create brand new pages on re-activation
Improvement: Changed method for getting user settings to avoid errors when missing or not yet created
Fix: Added notice and prevented plugin activation on block themes as block header/footer required and not yet established in code
Fix: Fix error related to deprecated wp function

= 3.0.9 =
Fix: Fix 404 error on sites with non-standard homepage urls

= 3.0.8 =
* Fix: Rewrite function as get_page_by_title deprecated in WordPress 6.2

= 3.0.7 =
* Fix: Fix permalink for time tracker sub pages

= 3.0.6 =
* Improvement: Update time tracker links to allow for sites with modified permalinks

= 3.0.5 =
* New Feature: Allowed for yearly recurring tasks
* New Feature: Add dropdowns within front end tables for fields which only have a few options
* Improvement: General code cleanup throughout
* Fix: Pagination when filtering time log - correct final page count
* Fix: Adjusted for single quotation marks in client name or project name

= 3.0.4 =
* New Feature: Time Tracker HOME link in admin menu, called 'Track Time'
* Improvement: Adjusted menu / toolbar breakpoint from 768 to 992
* Improvement: Update buttons on WP Forms forms to read Save instead of Enter.
* Fix: When client dropdown changed, project dropdown now updates properly to show only projects for selected client.
* Fix: Modify WP Forms creation to avoid restriction of 1 word limit on some text fields.
* Fix: Update new task start working button based on WP Forms template change in version 1.8.1.1.
* Fix: Fix error on admin page if WP Forms installed instead of CF7.
* Fix: Fix error creating dropdown of client, project, task names when using WPF.

= 3.0.3 =
* Fix: Fix activation problems when using WPForms

= 3.0.2 =
* Improvement: Extended plugin requirement to EITHER Contact Forms 7 OR WP Forms as per their preference
* Improvement: Moved 'Time Tracker Home' button to top of Time Tracker menu to clean up layout
* Improvement: Mobile layout and usability tweaks

= 2.4.7 =
* New Feature: Notice to users - looking for beta testers for next major release!

= 2.4.6 =
* Improvement: Changes for older versions of MariaDB and MySql (Project date started, Task date added, Time start and end, defaul and null values handled differently)

= 2.4.5 =
* Fix: Bug fixes

= 2.4.4 =
* Fix: Bug fixes and code cleanup

= 2.4.3 =
* New Feature: Added billed value (ie: $) capability with default billing rate and currency as well as ability to set billing rate by client. Monthly total shows billed and pending estimates.
* New Feature: Added 'View Detail' button to time log and pending time tables to allow user to view complete details of a task
* Improvement: Remove recaptcha from Time Track forms if using 3rd party 'Advanced Google Recaptcha' plugin
* Improvement: Streamline code and eliminate some unnecessary js calls on document load
* Fix: Remove recaptcha from Time Tracker forms, if enabled

= 2.4.2 =
* Fix: Fix error in month summary when no data exists for curent month
* Fix: Misc bug fixes

= 2.4.1 =
* Improvement: Provide user ability to edit more recurring task and project details in front end forms
* Fix: Fix buttons on admin notice(s)

= 2.4.0 =
* New Feature: Added 'start working' button to task detail page
* New Feature: Added default client and task to be used when none entered by user
* New Feature: Added function to enable user to check for any missing recurring tasks and add them; Useful as automated check only runs once daily
* New Feature: Added feedback request information to improve plugin usability for all, gave user ability to snooze request
* Improvement: Stop updating work end timer if user adjusts manually
* Improvement: Disabled recaptcha (if enabled in CF7) on Time Tracker tables to avoid false spam errors
* Improvement: General code cleanup
* Improvement: General database option naming cleanup
* Fix: Filter time entries by ticket #
* Fix: Recurring tasks cron

= 2.3.3 =
* Fix: Typo

= 2.3.2 =
* New Feature: End timer on time entry form stays in sync with clock (removed 'Set End Timer' button as no longer necessary)
* Fix: Resolve console error related to watching for color changes on all pages instead of just style admin
* Fix: Adjust height/overflow of main content to correct css issue with some themes
* Fix: Adjust accordion panel display so all toggles work properly

= 2.3.0 =
* New Feature: Give user ability to create recurring tasks on demand (currently run once per day, now user can manually run check if a task isn't created automatically)
* New Feature: Give user ability to override theme css for button colors, new page in admin (Time Tracker > Style)
* New Feature: New styling to show / hide different features
* New Feature: Display monthly summary on time entry page - for all data or filtered data
* New Feature: Homepage now displays summary by month history for all years
* Improvement: Retain filter criteria in form when filtering time entries
* Improvement: Misc styling improvements

= 2.2.3 =
* Fix: Resolve styling issue

= 2.2.2 =
* Improvement: Clean up front end styling

= 2.2.1 =
* Improvement: Reduce frequency of updates to end time on time entry form so user can adjust and submit end time if they want to manually change it
* Fix: Fix critical error in plugin update

= 2.2.0 =
* New Feature: Add ability to download pending time as a csv file
* New Feature: Added capability to delete clients, projects, recurring tasks, tasks, and time entries
* Improvement: Clean up formatting of forms to make them more compact
* Improvement: Cleaned up filter time log form to take up less space
* Improvement: Update script redirects to improve handling wordpress installed in subfolder
* Improvement: Work toward adding filter capability for each item type
* Improvement: Allow for page and form updates via plugin updates
* Fix: Broken home button in admin menu
* Fix: Php error on pagination null value
* Fix: Made time log filter by date more robust

= 2.1.0 = 
* Improvement: Added summary table to top of time log page

= 2.0.0 =
* Tested up to WordPress 5.8
* Improvement: Clarify required fields in forms
* Improvement: Add capability to add tool tips
* Improvement: Begin adding tool tips to guide users
* Improvement: Add capability to handle revisions
* Improvement: Begin adding page content through shortcodes to help with updates and revisions
* Fix: Home button in admin menu

= 1.5.0 =
* Improvement: Added default categories to help new users get started
* Improvement: Added alert notifications to help new users getting started -> Client needs to be added first, then task, before time can be added
* Improvement: Clean up coding, removed old coding
* Improvement: Updated to allow for WordPress installation on sub-directory
* Fix: Resolved 404 errors when WordPress not in root directory

= 1.4.0 =
* New Feature: 'All Tasks' and 'All Time Entries' are now paginated results
* New Feature: Added recurring task icon to task lists
* New Feature: Added progress bar to time worked cells
* Improvement: Improvements to responsiveness to make time tracking on-the-go easier
* Improvement: Clean up front end display of various dates
* Improvement: Sort client names alphabetically
* Fix: Resolved problem preventing recurring tasks from getting entered automatically
* Fix: Change to clean up front end display and data output

= 1.3.0 =
* Improvement: Continued styling improvements throughout
* Improvement: Cleaner way to create tables for new activations
* Fix: Resolved error displaying on user setting form

= 1.2.2 =
* Improvement: More consistent styling throughout
* Improvement: Improved method for verifying dependent plugin (CF7) is loaded
* Fix: Recurring tasks not respecting end date

= 1.2.1 =
* New Feature: Added page and table listing all recurring tasks and allowing user to edit some details
* Improvement: Clarified required fields in forms on front end
* Improvement: Updated table designs to enter some default values for fields in which null is not acceptable
* Improvement: Created display table class for coding ease and consistency
* Improvement: Improved method for verifying pages are private and alerting user if not
* Fix: Plugin option not initialized at activation
* Fix: Added missing due date field in new project form

= 1.1.1 =
* Fix: Correct error resulting from plugin option not added during activation

= 1.1.0 =
* Misc Bug Fixes
* Improvement: When recording time, after selecting client, the tasks will now appear in reverse chronological order, with newest tasks first
* Improvement: Recurring task icons in tables to identify recurring tasks (included new field in task table)
* New Feature: Filter time entries by project
* New Feature: Button in project table to view all time entries for projections
* New Feature: Button in client table to view all time entries for a client

= 1.0.0 =
* Plugin release
 
== Upgrade Notice ==
 
= 1.3.0 =
New update includes bug fixes and improved styling.

= 1.2.2 =
New update includes fixes and improved styling.

= 1.2.1 =
New update includes fixes, improved features, and new features.

= 1.1.1 =
Upgrade to correct errors.

= 1.1 =
New features to streamline searches, misc improvements, and small bug fixes.

= 1.0 =
This is the first publicly available version of the plugin.
