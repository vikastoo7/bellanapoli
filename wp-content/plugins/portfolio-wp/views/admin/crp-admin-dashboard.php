<?php

require_once( CRP_CLASSES_DIR_PATH.'/CRPDashboardListTable.php');

//Create an instance of our package class...
$listTable = new CRPDashboardListTable();
$listTable->prepare_items();

function featuresListToopltip(){
    $tooltip = "";
    $tooltip .= "<div class=\"crp-tooltip-content\">";
    $tooltip .= "<ul>";
    $tooltip .= "<li>* Full Design Adjustments</li>";
    $tooltip .= "<li>* Multiple Grids Per Pages</li>";
    $tooltip .= "<li>* 12+ Layouts Styles</li>";
    $tooltip .= "<li>* YouTube, Vimeo & Native Videos</li>";
    $tooltip .= "<li>* iFrame & Google Maps in Popups</li>";
    $tooltip .= "<li>* 8+ Popup Styles</li>";
    $tooltip .= "<li>* 100+ Hover Styles & Animations</li>";
    $tooltip .= "<li>* Category Filtration & Pagination</li>";
    $tooltip .= "<li>* Social Sharing</li>";
    $tooltip .= "<li>* Ajax/Lazy Loading</li>";
    $tooltip .= "<li>* Product Enquiries</li>";
    $tooltip .= "</ul>";
    $tooltip .= "</div>";

    return $tooltip;
}
?>

<div id="crp-dashboard-wrapper">
    <div id="crp-dashboard-add-new-wrapper">
        <div class="crp-upgrade-note">You’re running Free version of Grid Kit. You can <a href="<?php echo esc_url(CRP_PRO_URL); ?>" class="gkit-tooltip" title='<?php echo esc_html(featuresListToopltip()); ?>'>upgrade</a> your license to unlock all available features.</div>
        <div>
            <?php if ($crp_adminPageType == CRPGridType::PORTFOLIO) { ?><a id="add-portfolio-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create&type=" . CRPGridType::PORTFOLIO); ?>" title='Add new portfolio'>+ Portfolio</a><?php }
            elseif ($crp_adminPageType == CRPGridType::GALLERY) { ?><a id="add-gallery-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage ."&action=create&type=" . CRPGridType::GALLERY); ?>" title='Add new gallery'>+ Gallery</a><?php }
            elseif ($crp_adminPageType == CRPGridType::CLIENT_LOGOS) { ?><a id="add-client-logos-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create&type=" . CRPGridType::CLIENT_LOGOS); ?>" title='Add new gallery'>+ Client Logos</a><?php }
            elseif ($crp_adminPageType == CRPGridType::TEAM) { ?><a id="add-team-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create&type=" . CRPGridType::TEAM); ?>" title='Add new gallery'>+ Team</a><?php }
            elseif ($crp_adminPageType == CRPGridType::CATALOG) { ?><a id="add-catalog-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create&type=" . CRPGridType::CATALOG); ?>" title='Add new product catalog'>+ Product Catalog</a><?php }
            elseif ($crp_adminPageType == CRPGridType::SLIDER) { ?><a id="add-team-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create&type=" . CRPGridType::SLIDER); ?>" title='Add new slider'>+ Slider</a><?php }
            else { ?><a id="add-album-button" class='button-secondary add-portfolio-button crp-glazzed-btn crp-glazzed-btn-green' href="<?php echo esc_url("?page=" . $crp_adminPage . "&action=create"); ?>" title='Add new album'>+ Album</a><?php } ?>
        </div>
    </div>

    <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
    <form id="" method="get">
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo esc_attr($crp_adminPage) ?>" />
        <input type="hidden" name="gkit_nonce" value="<?php echo esc_attr(wp_create_nonce( 'gkit_nonce' )) ?>" />

        <!-- Now we can render the completed list table -->
        <?php $listTable->display() ?>
    </form>

</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".tablenav.top", jQuery(".wp-list-table .no-items").closest("#crp-dashboard-wrapper")).hide();
    });
</script>
