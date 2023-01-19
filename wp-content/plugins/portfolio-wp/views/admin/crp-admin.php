<div class="crp-background">
</div>
<div id="crp-wrap" class="crp-wrap crp-glazzed-wrap">

<?php include_once( CRP_ADMIN_VIEWS_DIR_PATH.'/crp-header-banner.php'); ?>

<div class="crp-wrap-main">

    <script>
        CRP_AJAX_URL = '<?php echo esc_url(admin_url( 'admin-ajax.php', 'relative' )); ?>';
        CRP_IMAGES_URL = '<?php echo esc_url(CRP_IMAGES_URL) ?>';
        GKIT_NONCE = '<?php echo esc_attr(wp_create_nonce( 'gkit_nonce' )); ?>';
    </script>

    <?php

    abstract class CRPTabType{
        const Dashboard = 'dashboard';
        const Settings = 'settings';
        const Help = 'help';
        const Terms = 'terms';
    }

    $crp_tabs = array(
        CRPTabType::Dashboard => 'All Portfolios',
        CRPTabType::Settings => 'General Settings',
        CRPTabType::Help => 'User Manual',
    );

    $crp_adminPage = isset( $_GET['page']) ? sanitize_key($_GET['page']) : null;
    $crp_currentTab = isset ( $_GET['tab'] ) ? sanitize_key($_GET['tab']) : CRPTabType::Dashboard;
    $crp_action = isset ( $_GET['action'] ) ? sanitize_key($_GET['action']) : null;
    $crp_gridType = isset ( $_GET['type'] ) ? sanitize_key($_GET['type']) : null;

    include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-modal-spinner.php");
    include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-header.php");

    if($crp_action == 'create' || $crp_action == 'edit'){
        if($crp_gridType == CRPGridType::GALLERY) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-gallery.php");
        } elseif($crp_gridType == CRPGridType::TEAM) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-team.php");
        } elseif($crp_gridType == CRPGridType::CLIENT_LOGOS) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-client_logos.php");
        } elseif($crp_gridType == CRPGridType::CATALOG) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-catalog.php");
        } elseif($crp_gridType == CRPGridType::PORTFOLIO) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-portfolio.php");
        } else if($crp_gridType == CRPGridType::SLIDER) {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-slider.php");
        } else {
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-album.php");
        }
    }else if ($crp_action == 'options'){
        include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-portfolio-options.php");
    }else{
        if($crp_currentTab == CRPTabType::Dashboard){
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-dashboard.php");
        }else if($crp_currentTab == CRPTabType::Settings){
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-settings.php");
        }else if($crp_currentTab == CRPTabType::Help){
            include_once(CRP_ADMIN_VIEWS_DIR_PATH."/crp-admin-help.php");
        }
    }

    function crp_renderAdminTabs( $current, $page, $tabs = array()){
        echo '<h2 class="nav-tab-wrapper crp-admin-nav-tab-wrapper" style="border: 0px">';

        foreach ($tabs as $tab => $name) {
            $class = ($tab == $current) ? 'nav-tab-active' : '';
            echo "<a class='nav-tab " . esc_attr($class) . "' href='" . esc_url("?page=" . $page . "&tab=" . $tab) . "'>" . esc_html($name) . "</a>";
        }
        echo '</h2>';
    }

    ?>
    <div style="clear:both;"></div>
</div>
</div>
