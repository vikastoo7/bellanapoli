<?php

function wp_ajax_crp_get_portfolio(){
    if(!current_user_can('administrator')) die("Unauthorized action! ");
    if (!isset($_GET['gkit_nonce'])) die("Hmm... looks like you didn't send any credentials... No CSRF for you! ");
    if (!wp_verify_nonce(sanitize_key($_GET['gkit_nonce']), 'gkit_nonce')) die("Hmm... looks like you sent invalid credentials... No CSRF for you! ");

    global $wpdb;
    $response = new stdClass();

    if(!isset($_GET['id'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid portfolio identifier!';
        crp_ajax_return($response);
    }

    $pid = (int)$_GET['id'];
    $query = $wpdb->prepare("SELECT * FROM ".CRP_TABLE_PORTFOLIOS." WHERE id = %d", $pid);
    $res = $wpdb->get_results( $query , OBJECT );

    if(count($res)){
        $portfolio = $res[0];

        $query = $wpdb->prepare("SELECT * FROM ".CRP_TABLE_PROJECTS." WHERE pid = %d", $pid);
        $res = $wpdb->get_results( $query , OBJECT );

        $projects = array();
        foreach($res as $project) {
            if (!empty($project->categories)) {
                $project->categories = explode(',', $project->categories);
            } else {
                $project->categories = array();
            }

            if(!empty($project->details)) {
                $project->details = json_decode($project->details, true);
            }

            $projects[$project->id] = $project;

            $picJson = json_decode(base64_decode($project->cover));
            $picId = $picJson ? $picJson->id : '';
            $picInfo = $picId ? CRPHelper::getAttachementMeta($picId, "medium") : '';
            $pic = array(
                "id" => $picId,
                "src" => $picInfo ? $picInfo["src"] : '',
            );
            $project->cover = base64_encode(json_encode($pic));

            $pics = array();
            if ($project->pics && !empty($project->pics)) {
                $exp = explode(",", $project->pics);
                foreach ($exp as $item) {
                    $picJson = json_decode(base64_decode($item));
                    $picId = $picJson ? $picJson->id : '';
                    $picInfo = $picId ? CRPHelper::getAttachementMeta($picId, "medium") : '';
                    $pic = array(
                        "id" => $picId,
                        "src" => $picInfo ? $picInfo["src"] : '',
                    );

                    $pics[] = base64_encode(json_encode($pic));
                }
            }
            $project->pics = implode(",", $pics);
        }

        $portfolio->projects = $projects;
        $portfolio->corder = explode(',',$portfolio->corder);
        $portfolio->options = json_decode( str_replace('\"', '"', $portfolio->options), true);

        $response->status = 'success';
        $response->portfolio = $portfolio;
    }else{
        $response->status = 'error';
        $response->errormsg = 'Unknown portfolio identifier!';
    }

    crp_ajax_return($response);
}

function wp_ajax_crp_save_portfolio() {
    if(!current_user_can('administrator')) die("Unauthorized action! ");
    if (!isset($_POST['gkit_nonce'])) die("Hmm... looks like you didn't send any credentials... No CSRF for you! ");
    if (!wp_verify_nonce(sanitize_key($_POST['gkit_nonce']), 'gkit_nonce')) die("Hmm... looks like you sent invalid credentials... No CSRF for you! ");

    global $wpdb;
    $response = new stdClass();

    if(!isset($_POST['portfolio'])){
        $response->status = 'error';
        $response->errormsg = 'Invalid portfolio passed!';
        crp_ajax_return($response);
    }

    //Convert to stdClass object
    $portfolio = json_decode( stripslashes($_POST['portfolio']), true);
    $pid = isset($portfolio['id']) ? (int)$portfolio['id'] : 0;

    $corder = "";
    if (isset($portfolio['corder'])) {
      $corder = array_map('intval', $portfolio['corder']);
      $corder = implode(',',$portfolio['corder']);
    }

    $type = ((isset($portfolio['extoptions']) && isset($portfolio['extoptions']['type'])) ? $portfolio['extoptions']['type'] : CRPGridType::ALBUM);
    $type =  sanitize_key($type);
    $extOptions = array(
        'type' => $type
    );

    //Insert if portfolio is draft yet
    if(isset($portfolio['isDraft']) && (int)$portfolio['isDraft']){
        $title = isset($portfolio['title']) ? sanitize_text_field($portfolio['title']) : "";

        $wpdb->insert(
            CRP_TABLE_PORTFOLIOS,
            array(
                'title' => $title,
            ),
            array(
                '%s',
            )
        );

        //Get real identifier and use it instead of draft identifier for tmp usage
        $pid = $wpdb->insert_id;
    }

    $projects = isset($portfolio['projects']) ? $portfolio['projects'] : array();
    foreach($projects as $id => $project){
        $cover = isset($project['cover']) ? $project['cover'] : "";
        if (empty($cover)) {
            continue;
        }

        if (empty(CRPHelper::validatedBase64String($cover))) {
          continue;
        } else {
          $coverInfo = CRPHelper::decode2Obj(CRPHelper::decode2Str($cover));
          if (empty($coverInfo) || (!empty($coverInfo) && isset($coverInfo->id ) && (int)$coverInfo->id == 0)) {
              continue;
          }
        }

        //Sanitize title, desc & url. Title & desc could be either text or html
        $title = isset($project['title']) ? sanitize_text_field($project['title']) : "";
        $description = isset($project['description']) ? sanitize_text_field($project['description']) : "";
        $url = isset($project['url']) ? sanitize_url($project['url']) : "";

        //Sanitize pictures
        $explodedPics = isset($project['pics']) ? explode("," , $project['pics']) : array();
        $sanitizedPics = array();
        foreach ($explodedPics as $pic) {
          $pic = json_decode(base64_decode($pic), true);

          if ($pic && isset($pic["id"]) && (int)$pic["id"] != 0 ) {
            $pic["id"]  = (int)$pic["id"];
            $pic["src"] = isset($pic["src"]) ? sanitize_url($pic["src"]) : "";
            $pic["type"] = isset($pic["type"]) ? sanitize_key($pic["type"]) : "";

            $sanitizedPics[] = base64_encode(json_encode($pic));
          }
        }

        $pics = "";
        if (count($sanitizedPics)) {
          $pics = implode(",", $sanitizedPics);;
        }

        //Caretories are not supported in Free version
        $cats = "";

        //Sanitize extra details
        $details = isset($project['details']) ? $project['details'] : '';
        if(!empty($details)) {
          $details["price"] = isset($details["price"]) ? (int)$details["price"] : 0;
          $details["sale"] = isset($details["sale"]) ? (int)$details["sale"] : 0;
          $details["status"] = isset($details["status"]) ? sanitize_key($details["status"]) : CRPProductStatus::Invisible;

          $details = json_encode($details);
        }

        if(isset($project['isDraft']) && $project['isDraft']){
            $wpdb->insert(
                CRP_TABLE_PROJECTS,
                array(
                    'title' => $title,
                    'pid' => $pid,
                    'cover' => $cover,
                    'description' => $description,
                    'url' => $url,
                    'pics' => $pics,
                    'categories' => $cats,
                    'details' => $details
                ),
                array(
                    '%s',
                    '%d',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                )
            );

            $realProjId = $wpdb->insert_id;
            $corder = str_replace($id,$realProjId,$corder);
        }else{
            $wpdb->update(
                CRP_TABLE_PROJECTS,
                array(
                    'title' => $title,
                    'cover' => $cover,
                    'description' => $description,
                    'url' => $url,
                    'pics' => $pics,
                    'categories' => $cats,
                    'details' => $details
                ),
                array( 'id' => $id ),
                array(
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s',
                    '%s'
                ),
                array( '%d' )
            );
        }
    }


    $deletions = isset($portfolio['deletions']) ? $portfolio['deletions'] : array();
    $deletions = array_map('intval', $deletions);

    foreach($deletions as $deletedProjectId) {
        // Default usage.
        $wpdb->delete( CRP_TABLE_PROJECTS, array( 'id' => (int)$deletedProjectId ) );
    }

    $title = isset($portfolio['title']) ? sanitize_text_field($portfolio['title']) : "";
    $extOptions = json_encode($extOptions);

    $wpdb->update(
        CRP_TABLE_PORTFOLIOS,
        array(
            'title' => $title,
            'corder' => $corder,
            'extoptions' => $extOptions
        ),
        array( 'id' => $pid ),
        array(
            '%s',
            '%s',
            '%s'
        ),
        array( '%d' )
    );

    $response->status = 'success';
    $response->pid = $pid;

    crp_ajax_return($response);
}


//Helper functions
function crp_ajax_return( $response ){
    echo  json_encode( $response );
    die();
}
