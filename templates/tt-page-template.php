<?php
/**
 * Template Name: Time Tracker Pages
 *
 * Page with Left Sidebar that Includes Links to Important Time Tracker Pages
 * 
 * @since 1.0
 * 
 */

namespace Logically_Tech\Time_Tracker\Templates;
use function Logically_Tech\Time_Tracker\Inc\check_page_status;
use function Logically_Tech\Time_Tracker\Inc\log_tt_misc;

get_header();
?>

<!----------begin sidebar section-----------><div class="sidebar tt-sidebar">
<?php
include(TT_PLUGIN_DIR . '/templates/tt-sidebar.php');
?>
</div><!----------end sidebar section----------->


<!----------begin primary section-----------><div id="primary" class="content-area tt-content">
    
        <!----------begin page header section-----------><header class="page-header tt-page-header">
        <h1 class="page-title tt-page-title">
        <?php
        $title = esc_html( get_the_title() );
        if ( ( strpos($title, 'Private:') !== false ) || ( strpos($title, 'Protected:') !== false ) ) {
            $title = substr($title, strpos($title, ": ") +2 );
        }
        echo $title;
        ?>
        </h1>
        <?php

        $status = check_page_status(get_the_ID());        
        if ($status !== 'private') {
            ?>
            <div class="tt-warning">NOTE: This page may be visible to the public. You may wish to change it to 'private' in the pages menu.</div>
            <?php
            log_tt_misc('Page Visibility: The page named: ' . get_the_title() . ', ID=' . get_the_ID() . ', returned a status of ' . $status);
        }

        ?>
        <a href="/time-tracker" class="tt-header-button">Home</a>
        </header><!----------end page header----------->
        <!----------error alert----------->
        <?php echo do_shortcode('[tt_error_alert]'); ?>  
        <!----------end error alert----------->
        <!----------begin main section-----------><main id="main" class="site-main tt-main" role="main">

            <!----------begin content section-----------><div class="page-content tt-page-content">
            <?php the_content(); ?>
            </div><!----------end content section----------->
    
        </main><!----------end main section----------->
    
</div><!----------end primary section----------->

<?php
get_footer();