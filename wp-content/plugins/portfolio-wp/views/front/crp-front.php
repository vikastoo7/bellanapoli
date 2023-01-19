<?php
    global $crp_portfolio;

    //Validation goes here
    if($crp_portfolio) {
        //Setup ordered projects array
        $crp_portfolio->projects = getOrderedProjects($crp_portfolio);

        if ($crp_portfolio->grid_type == CRPGridType::SLIDER) {
            require(CRP_FRONT_VIEWS_DIR_PATH . "/layouts/crp-front-slider.php");
        } else {
            require_once(CRP_FRONT_VIEWS_DIR_PATH . "/layouts/crp-front-tiled-layout-lightgallery.php");
        }

    }else{
        echo "Ooops.. Short-code related layout not found!";
    }


function getOrderedProjects($crp_portfolio){
    $orderedProjects = array();

    if(isset($crp_portfolio->projects) && isset($crp_portfolio->corder)){
        foreach($crp_portfolio->corder as $pid){
            $orderedProjects[] = $crp_portfolio->projects[$pid];
        }
    }

    return $orderedProjects;
}
