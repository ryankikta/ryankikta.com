<?php
/*
Template Name: Seller API
*/

global $wpdb;
get_header(); ?>

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="120px" height="120px" viewBox="0 0 120 120" style="overflow:visible;enable-background:new 0 0 120 120;" xml:space="preserve"> <style type="text/css"> .st0{opacity:0.5;} .st1{fill:#19CBC5;} .st2{fill:none;stroke:#361181;stroke-width:2.5;} </style> <defs> </defs> <g id="Group_11119_1_" transform="translate(-468 -282)"> <g id="Group_11118_1_" transform="translate(468 282)"> <g id="Group_7763_1_" transform="translate(0)" class="st0"> <ellipse id="Ellipse_179_1_" class="st1" cx="60" cy="60" rx="60" ry="60"/> </g> <g id="Group_7764_1_" transform="translate(14.374 14.374)"> <ellipse id="Ellipse_180_1_" class="st1" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/> </g> </g> <path id="Path_10757_1_" class="st2" d="M556.2,357.7c-0.9-1.4-4.9-7.1-4.9-7.1v-25.9c0-1.5-1.7-3.2-3.8-3.2h-39 c-2.1,0-3.8,1.7-3.8,3.2v25.9c0,0-4,5.7-4.9,7.1c-1.7,2.7-0.6,5.3,2.6,5.3h51.2C556.8,363.1,557.9,360.4,556.2,357.7z M530.8,359.9 h-6.4c-0.6,0-1-0.5-1-1c0-0.6,0.5-1,1-1h6.4c0.6,0,1,0.5,1,1C531.8,359.5,531.3,359.9,530.8,359.9z M547.3,348.9 c0,0.9-0.8,1.7-1.7,1.7l0,0h-35.6c-0.9,0-1.7-0.8-1.7-1.7V327c0-0.9,0.8-1.7,1.7-1.7h35.6c0.9,0,1.7,0.8,1.7,1.7c0,0,0,0,0,0 L547.3,348.9z"/> </g> </svg>Integrations</h1>
                    <h2 class="fs1">E-COMMERCE</h2>
                    <h3 class="fs2">Get Started</h3>
                    <p>Nullam faucibus ut lectus vitae posuere. Nullam sollicitudin nunc ipsum, quis malesuada orci rutrum sit amet. Maecenas nulla justo, rutrum id iaculis at, elementum non lacus. Proin non molestie erat, vitae mattis elit. Donec ac euismod metus, ut efficitur nibh. Suspendisse magna est, pharetra sed hendrerit sit amet, blandit nec enim. Donec porttitor neque nec consectetur faucibus. Aliquam pellentesque leo eleifend, dictum turpis vitae, mattis mi. Curabitur luctus tortor ligula, vitae ultricies diam viverra id. Maecenas vitae sem quis nulla mollis euismod. Ut et cursus risus. Morbi pretium mi sit amet consectetur porta. Donec mi urna, accumsan eu augue sed, laoreet maximus eros. Maecenas sodales mi justo, vitae luctus purus semper vitae.</p>
                </div>
			</div>
			<div class="col-12 col-lg-6 p-0 text-center">
				<h2 class="fs3">ENABLE API</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
        </div>
    </div>
</div>
<!--//this is where sidebar was-->
<div class="container-fluid py-40">
    <div class="row align-items-center justify-content-center justify-content-lg-between">
    <div class="col-lg-8 col-xl-7 py-40">
        <?php
        while (have_posts()) {
            the_post();
            $content = apply_filters('the_content', get_the_content());
            echo $content;
            }
                // See if API is enabled or not?
                wp_get_current_user();
                $currentuserid = $current_user->ID;
                // Form submitted?
                if (isset($_POST['submitted'])) {
                    $submitted = intval(esc_sql($_POST['submitted']));
                    if ($submitted == 1) {
                        // Generate Key & Hash
                        $apikey = CreateSecureHash(32, 12, 12, 0);
                        $apihash = CreateSecureHash(64, 20, 20, 0);
                        // Enable it
                        $mysqlselect = $wpdb->query("UPDATE `wp_users` 
                                                    SET `apiactive` = 1 , `apikey` = '$apikey' , `apihash` = '$apihash' 
                                                    WHERE `id` = $currentuserid");
                    } elseif ($submitted == 2) {
                        // Disable it
                        $mysqlselect = $wpdb->query("UPDATE `wp_users` 
                                                    SET `apiactive` = 0 , `apikey` = '' , `apihash` = '' 
                                                    WHERE `id` = $currentuserid");
                    }
                }
                $mysqlselect = $wpdb->query("SELECT `apiactive`,`apikey`,`apihash` 
                                            FROM `wp_users` 
                                            WHERE `id` = $currentuserid");
                $row = $wpdb->get_row($mysqlselect);
                $apiactive = $row[0];
                $apikey = $row[1];
                $apihash = $row[2];
                if (intval($apiactive) == 0){
                // API is NOT Active. Show button to enable
                ?>
                <p> API is currently <strong> not enabled </strong> for your account, You may enable it below. Please follow
                our <a href="https://www.ryankikta.com/api/"> Documentation </a> to implement RyanKikta's API into your
                own Website or Application! </p>
                <p>
                <form method="POST">
                <input type="hidden" name="submitted" value="1">
                <input class="btn btn-primary" type="submit" name="enable" value="Enable API">
                </form>
                </p>
            <?php
            } else {
                // API is Active , Show simply show key and hash and button to disable
                ?>
                <p> API is currently <strong> enabled </strong> for your account, You may disable it below. Please
                follow our <a href="https://www.ryankikta.com/api/"> Documentation </a> to implement RyanKikta's API
                into your own Website or Application! </p>
                <form method="POST">
                    <input type="hidden" name="submitted" value="2">
                    <p><strong> API Key: </strong></p>
                    <p><input type="text" value="<?php echo $apikey; ?>" style="width: 525px;" name="key"></p>
                    <p><strong> API Hash: </strong></p>
                    <p><input type="text" value="<?php echo $apihash; ?>" style="width: 525px;" name="hash"></p>
                    <br/><br/>
                    <p><input class="btn btn-primary" type="submit" name="enable" value="Disable API"></p>
                </form>
                <?php
            }
            ?>
            </p>
            <br class="clear">
        </div>
        <div class="col-md-6 col-lg-4">
            <img class="img-fluid" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/manual_api_lady.svg">
        </div>
    </div> <!-- content-wrapper -->
</div>
<?php get_footer();?>

