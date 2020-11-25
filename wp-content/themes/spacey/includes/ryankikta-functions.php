<?php

function atlas() {
    	
	$loc = get_template_directory();
	$fpth = "includes/atlasShrugged.txt";	
        $lines = file($loc ."/". $fpth);
	Shuffle($lines);
	Echo "<p>";
	// Toggle to help find bad text
	//Echo "<p style='color:red;'>";
	Echo implode(" ", array_slice($lines, 0, 50));
	Echo "</p>";
}

function clean_user_url($url)
{
    if (!preg_match('/^https*:\/\//i',$url)) {
        $url = 'http://' . $url;
    }
    $url = get_end_redirect_url($url);
    return $url;
}


function get_end_redirect_url($url)
{
    $base_url = get_bloginfo('wpurl');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_REFERER, $base_url);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_5) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.64 Safari/537.11');
    $return = curl_exec($ch);
    $last_url = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    if ($last_url)
        $url = $last_url;
    return $url;
}


add_action('wp_ajax_uploadimages', 'uploadimages');
function uploadimages(){
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    // File details 
    $temp_file = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_size = $_FILES['file']['name'];

    // Get the cdn alias so we know where to put the files
    $cdn_alias = get_user_meta($user_id, 'cdn_alias', true);

    // If the user dosen't have a cdn_alias then give them one
    if(empty($cdn_alias)){
        $cdn_alias = md5($user_id .time());
        //add_user_meta($user_id, 'cdn_alias', $cdn_alias, true);
    }

    // The path to upload the images to for the user
    //$temp_upload_path = '/var/www/html/wp-content/temp_user_uploads/' . $cdn_alias . '/images/';
    $upload_path = '/var/www/html/wp-content/pa-assets/users_uploads/' . $cdn_alias . '/images/';
    error_log($upload_path);
  
    if(!is_dir($upload_path)){
        error_log('not a dir');
    }
    
    if(move_uploaded_file($temp_file, $upload_path . $file_name)){
        error_log('file moved successfully');   
    }
}

add_action('wp_ajax_set_jumbo', 'set_jumbo');
function set_jumbo(){
    global $wpdb;    
        
    $return_array = array();
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $fileID = $_POST['fileID'];
    $jumbo = $_POST['isJumbo'];

    $query = "SELECT * from wp_userfiles where userID = $user_id and fileID = $fileID";// Does the image belong to the user
    $isUsersImage = $wpdb->get_row($wpdb->prepare($query));
    
    if(empty($isUsersImage)){
        $return_array['status'] = false;
        $return_array['error'] = 'Image does not belong to user';
    } else {
        // The image belomgs to the user so lets update the DB entry
        $update_result = $wpdb->update('wp_userfiles', array('is_jumbo' => $jumbo), array('userID' => $user_id, 'fileID' => $fileID));
        if($update_result === false){
            $return_array['status'] = false;
            $return_aray['error'] = 'There was an error updating the image';
        } else {
            $return_array['status'] = true;
        }
    }
    
   
    echo json_encode($return_array); 
    wp_die();
}

add_action('wp_ajax_set_underbase', 'set_underbase');
function set_underbase(){
    global $wpdb;

    $return_array = array();
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $fileID = $_POST['fileID'];
    $underbase = $_POST['underbase'];

    $query = "SELECT * from wp_userfiles WHERE userID = $user_id and fileID = $fileID"; // Does the image belong to the user
    $isUsersImage = $wpdb->get_row($wpdb->prepare($query));

    if(empty($isUsersImage)){
        $return_array['status'] = false;
        $return_array['error'] = 'Images does not belong to user';
    } else {
        // The image belongs to the user so lets update the DB
        $update_result = $wpdb->update('wp_userfiles', array('nounderbase' => $underbase), array('userID' => $user_id, 'fileID' => $fileID));
        if($update_result === false){
            $return_array['status'] = false;
            $return_array['error'] = 'There was an error updating the image';
        } else {
            $return_array['status'] = true;
        }
    }
    
    echo json_encode($return_array);
    wp_die();
}

add_action('wp_ajax_delete_image', 'delete_image');
function delete_image(){
    
    wp_die();
}

add_action('wp_ajax_get_images', 'get_images');
function get_images(){
    global $wpdb;    
    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;   
    $current_user_id = 615;
    $limit = 10;
    $page = ((isset($_POST['pagenum'])) && (is_numeric($_POST['pagenum']))) ? (int)$_POST['pagenum'] :  1;
    $offset = ($page - 1) * $limit;

    $html = '';

    $query = $_POST['search'];

    $cdn_alias = get_user_meta($current_user_id, 'cdn_alias', true);
    $query = "select * from wp_userfiles where userID = $current_user_id and fileSize > 0 and deleted = 0 and fileTitle like '%$query%' order by fileID desc limit $limit offset $offset";
    $images = $wpdb->get_results($query, ARRAY_A);
    $rowcount = $wpdb->num_rows;
    foreach($images as $image){
        $is_jumbo = ($image['is_jumbo'] == 1) ? 'checked' : '';
        $no_underbase = ($image['nounderbase'] == 1) ? 'checked' : '';
        $always_underbase = ($image['underbase'] == 1) ? 'checked' : '';  
         $html .= '
            <div class="col-12 col-md-6 col-lg-4 my-3">
                <div class="image_wrapper card" id="' . $image['fileID']  . '">
                    <div class="d-flex justify-content-between mb-2">
                        <a href="#"><i class="far fa-edit fa-2x"></i></a>
                        <a href="#"><i class="far fa-trash-alt fa-2x"></i></a>
                    </div>
                    <img class="img-fluid card-img-top"  src="https://storage.googleapis.com/pa-uploads/users_uploads/' . $cdn_alias . '/images/' . $image['fileName']  . '" alt="...">
                    <div class="card-body">
                        <h4 class="card-title fs3">' . $image['fileTitle'] . '</h4>
                        <h5>File Type: ' . $image['fileType']  . ' - Size: ' . $image['fileSize'] . '</h5>
                        <div class="d-flex justify-content-between">
                            <ul>
                                <li><div class="input_checkbox"><input class="setJumbo"  type="checkbox"' . $is_jumbo . '> <label>JUMBO PRINT</label></div></li>
                                <li><div class="input_checkbox"><input class="setUnderbase"  type="checkbox"' . $no_underbase  . '> <label>NO UNDERBASE</label></div></li>
                                <!-- li><div class="input_checkbox"><input id="alwaysUnderbase"  type="checkbox"' . $always_underbase  .  '> <label>ALWAYS UNDERBASE</label</label></div></li -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>';
    }
    
    
    $pagination = getPaginationString($page, $rowcount, 30, 4, '/', '?pagenum=');     
    echo json_encode(array('html' => $html, 'pagination' => $pagination));

    wp_die();
}

?>
