<?php
// Only show if logged in
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}

get_header();
?>

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>My Brand</h1>

                    <h2 class="fs1">Ready to launch</h2>
                    <h3 class="fs2">No brand left behind</h3>
                    <p>Nullam faucibus ut lectus vitae posuere. Nullam sollicitudin nunc ipsum, quis malesuada orci rutrum sit amet. Maecenas nulla justo, rutrum id iaculis at, elementum non lacus. Proin non molestie erat, vitae mattis elit. Donec ac euismod metus, ut efficitur nibh. Suspendisse magna est, pharetra sed hendrerit sit amet, blandit nec enim. Donec porttitor neque nec consectetur faucibus. Aliquam pellentesque leo eleifend, dictum turpis vitae, mattis mi. Curabitur luctus tortor ligula, vitae ultricies diam viverra id. Maecenas vitae sem quis nulla mollis euismod. Ut et cursus risus. Morbi pretium mi sit amet consectetur porta. Donec mi urna, accumsan eu augue sed, laoreet maximus eros. Maecenas sodales mi justo, vitae luctus purus semper vitae.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row tab_trigger_mobile">
        <div class="col-12">
    		<div class="tab_trigger_wrapper">

    		  	<button type="button" class="tab_trigger active">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_orderdefaults.svg">
                    Order<br>Defaults
                </button>

    		  	<button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_packingslips.svg">
                    Packing<br>Slips
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_returnlabels.svg">
                    Return<br>Labels
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_necklabel.svg">
                    Neck<br>Label
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_heatpress.svg">
                    Heat<br>Press
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_packins.svg">
                    Pack<br>Ins
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_hangtag.svg">
                    Hang<br>Tag
    		  	</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_individualbagging.svg">
                    Individual<br>Bagging
    			</button>

                <button type="button" class="tab_trigger">
                    <img class="tab_trigger_icon" src="<?php echo get_template_directory_uri(); ?>/images/branding_custompackaging.svg">
                    Custom<br>Packaging
                </button>
    		</div>
        </div>
    </div>

	
    <form method="POST" name="form" enctype="multipart/form-data" id="form_brand">
       <?php if ($message_saved) { ?>
            <div class="message message_saved text-center my-20">
                <p class="fs3 mb-0">Settings Updated Successfully.</p>
            </div>
        <?php }
        if ($message_error) { ?>
            <div class="message message_error text-center my-20">
                <p class="fs3 mb-0"><?php echo $message; ?></p>
            </div>
        <?php } ?>
        <div class="tab_content_wrapper">
        	<div class="tab_content active">
                <div class="row">
                    <div class="col-12">
                        <h2 class="fs1">Order Defaults</h2>
                    </div>
            		<div class="col-lg-6">
            			<p>The following settings are how you set the default for all orders received from our <a href="/integrations/">integrations</a>.</p>
            			<h3 class="fs2">Disable Plus Sizes</h3>
            			<p>Plus sized garments carry extra charges. Some clients want to simplify their pricing by not offering these sizes to keep the prices low. If you do not want to sell garments that are plus sized (i.e. 2XL, 3XL, 4XL, 5XL) you can disable it here.</p>

                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                            <input type='radio' id="plussize_yes" name="sizes_plus_disable" value="1" <?php if($plus_size_disabled){echo 'checked';} ?>><label for="plussize_yes"> Yes</label>

                            <input type='radio' id="plussize_no" name="sizes_plus_disable" value="0" <?php if(!$plus_size_disabled){echo 'checked';} ?>><label for="plussize_no"> No</label>
                        </div>
            			
            			<h3 class="fs2">Custom Price Per Size</h3>

                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                			<input type='radio' id="customsize_yes" name="custom_price_per_size" value="1" <?php if($custom_price_per_size){echo 'checked';} ?>><label for="customsize_yes"> Yes</label>

                			<input type='radio' id="customsize_no" name="custom_price_per_size" value="0" <?php if(!$custom_price_per_size){echo 'checked';} ?>><label for="customsize_no"> No</label>
                        </div>

                        <h3 class="fs2">Currency</h3>
                        <p>This controls what currency prices are listed at in the catalog and which currency gateways will take payments in.</p>

                        <select name="currency" class="mb-20">
                            <option value="USD">US Dollar ($)</option>
                            <option value="EUR">Euro (€)</option>
                            <option value="GBP">British Pound Sterling (£)</option>
                            <option value="CAD">Canadian Dollar (CA$)</option>
                            <option value="AUD">Australian Dollar (A$)</option>
                        </select>

            		</div>

            		<div class="col-lg-6">
            			<h3 class="fs2">Rush Orders</h3>
            			<p>We offer a rush processing of 2 business days. This is an additional <strong>$2</strong> fee per item. We do our best to make this happen but it is not a guarantee. We acquire blanks daily and some products may not be eligible for this type of turnaround time.</p>

                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                			<input type='radio' id="rush_yes" name="rush_order" value="1" <?php if($rush_order){echo 'checked';} ?>><label for="rush_yes"> Yes</label>

                			<input type='radio' id="rush_no" name="rush_order" value="0" <?php if(!$rush_order){echo 'checked';} ?>><label for="rush_no"> No</label>
                        </div>

            			<h3 class="fs2">Always use underbase</h3>
            			<p>Always use a white underbase on all images printed. <strong>Note: This does not include white shirts</strong></p>

                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                			<input type='radio' id="underbase_yes" name="use_underbase" value="1" <?php if($use_underbase){echo 'checked';} ?>><label  for="underbase_yes"> Yes</label>

                			<input type='radio' id="underbase_no" name="use_underbase" value="0" <?php if(!$use_underbase){echo 'checked';} ?>><label for="underbase_no"> No</label>
                        </div>

            		</div>
                </div>
        	</div>	

        	<div class="tab_content">
        	   <div class="row">
                    <div class="col-12">
                        <h2 class="fs1">Packing slips</h2>
                    </div>

                    <div class="col-lg-6">
            				<p>Packing slips are 8.5x11" white paper printed in black and white that will be included in your packages. No pricing information is included on these sheets. You can add your own logo to display in the packing slip as well as a special note generic to all your customers. It will appear at the bottom of the packing slip. Only jpeg, png and gif are allowed for logos. <a href="/packing-slips/">More information about packing slips.</a></p>

            			<div class="input_outline">
                            <label for="special_note">Special Note:</label>
            				<textarea id="special_note" name="special_note"><?php echo $special_note; ?></textarea>
            				<button id="save_specialnote" class="btn-primary" type="button">Save Note</button>
            			</div>

            		</div>
            		<div class="col-lg-6">
                        <div class="row">
                            <div class="col">
                    			<label for="auto_invoice">Use Our Generated Packing Slips</label>
                                <div class="input_radio_wrapper input_radio_wrapper_inline">
                    				<input type='radio' id="yes" name="auto_invoice" value="1" <?php if($auto_invoice){echo 'checked';} ?>><label for="yes"> Yes</label>

                    				<input type='radio' id="no" name="auto_invoice" value="0" <?php if(!$auto_invoice){echo 'checked';} ?>><label for="no"> No</label>
                                </div>
                            </div>
                            <div class="col">
                                <label>Packing Slip Logo:</label>   
                                <?php if($logo_url){ ?>

                                    <span class="packing_logo">
                                        <img src='<?php echo $logo_url; ?>' height="120"/>
                                    </span>
                                    
                                    <span id="file_upload" style="display:none">
                                        <input id="sui_image_file" type="file" name="sui_image_file" size="60"  />
                                    </span>
                                    
                                    <span id="remove_button" >
                                        <input id="remove_logo" type="button" size="60" value="Remove" />
                                    </span>

                                <?php } else { ?>

                                    <input id="sui_image_file" type="file" name="sui_image_file" size="60" />

                                <?php } ?>
                            </div>
                        </div>

                        <div class="input_outline">
                			<label>Customer Service Email (optional):</label>
                			<input name="email" type="text" placeholder="JOHNDOE@GMAIL.COM" value="<?php echo $email; ?>" />
                        </div>

                        <div class="input_outline">
                			<label>Customer Service Phone (optional):</label>
                			<input id="cust-service-phone"  name="phone" type="text" placeholder="888-888-8888" value="<?php echo $phone; ?>"/>
                        </div>
            		</div>
                </div>
        	</div>
        	
        	<div class="tab_content">
        	    <div class="row">
                    <div class="col-12">
                    </div>
                    <div class="col-lg-6">
                        <h2 class="fs1">Return Label</h2>
            			<h3 class="fs2"> Where Is Your Package Being Sent From? </h3>
            			<p class="mb-20">Fill out the address that you want your customers to see. It will by default include your business NAME, but you need to fill in your desired address. Please know that we can only use US addresses for a return label. If you do not include a US address we will use your Business Name and our fulfillment center address <b>2 Wurz Avenue Yorkville, NY 13495</b>. The address used below will ALSO be used in the packing slip return address. <a href="/return-labels">More information about return labels</a></p>

                        <div class="input_outline">
                            <label>Address 1 (required):</label>
                            <input name="address1" type="text" value="<?php echo $address1; ?>" />
                        </div>
                        <div class="input_outline">
                            <label>Address 2:</label>
                            <input name="address2" type="text" value="<?php echo $address2; ?>" />
                        </div>
                        <div class="input_outline">
                            <label>City (required):</label>
                            <input name="city" type="text" value="<?php echo $city; ?>" />
                        </div>

                        <div class="row">
                            <div class="col-5">

                                <div class="input_outline">
                                    <label>State (required):</label>
                                    <input name="state" type="text" value="<?php echo $state; ?>" />
                                </div>
                            </div>
                            <div class="col-5">
                                <div class="input_outline">
                                    <label>Zip (required):</label>
                                    <input name="zip" type="text" value="<?php echo $zip; ?>" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
            </div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Neck Label Removal</h2>
            			<p>We can remove the manufacturers' label or tag for <strong>$0.50</strong> per piece. The following setting applies to all products on all orders. <a href="/neck-label-removal">More information on neck label removal</a></p>
                        <label for="removetag">Remove Neck Label</label>
                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                            <input type='radio' id="removetag_yes" name="removetag" value="1" <?php if($removetag){echo 'checked';} ?>><label for="removetag_yes"> Yes</label>

                            <input type='radio' id="removetag_no" name="removetag" value="0" <?php if(!$removetag){echo 'checked';} ?>><label for="removetag_no"> No</label>
                        </div>
            		</div>
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
        	</div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Heat Press Labels</h2>
        				<p>If you provide us transfers that can be applied via heat press we will add them to your shirts on individual orders.  Once we have received your tags to apply, we can enable this feature in your account. <a href="/tagless-label-application">More information about heat press labels</a></p>

                        <?php // if($access_heat_press_tag == 1){ ?>

                            <label for="applytag">Apply Heat Press Tag</label>

                            <div class="input_radio_wrapper input_radio_wrapper_inline">
                                <input type="radio" id="applytag_yes" name="applytag" value="1" <?php if($applytag){echo 'checked'; }?>><label for="applytag_yes"> Yes</label>
                                <input type="radio" id="applytag_no" name="applytag" value="0" <?php if(!$applytag){echo 'checked'; }?>><label for="applytag_no"> No</label>
                            </div>

                            <label for="applytag_location">Location Options</label>
                            <select name="applytag_location">
                    
                                <?php foreach ($location_heat_press as $key => $loc) {
                                    $selected = ($applytag_location == $key) ? 'selected="selected"' : '';
                                    echo '<option value="' . $key . '" ' . $selected . '>' . $loc . '</option>';
                                } ?>
            
                            </select>

                        <?php // } else { ?>

                            <p class="fs3"> This account currently does not have heat press labels enabled. Please contact us at support@ryankikta.com to have heat press labels enabled.</p>

                        <?php // } ?>
        			</div>
                   
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
        	</div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Pack ins</h2>
                        <p>We can include extras such as stickers or handouts that you provide upon request. Once we have received your pack-ins, we can enable this feature in your account. <a href="/pack-ins">More information about pack-ins.</a></p>

                        <?php // if($access_pack_in == 1){ ?>

                            <label for="applytag">Add Pack Ins</label>

                            <div class="input_radio_wrapper input_radio_wrapper_inline">
                                <input type="radio" id="packin_yes" name="material" value="1" <?php if($material){echo 'checked'; }?>><label for="packin_yes"> Yes</label>

                                <input type="radio" id="packin_no" name="material" value="0" <?php if(!$material){echo 'checked'; }?> ><label for="packin_no"> No</label>
                            </div>

                            <div class="input_outline">
                                <label>Pack In Description</label>
                                <textarea class="brand_textarea" name="material_desc"><?php echo $material_desc; ?></textarea>
                            </div>

                        <?php // } else { ?>

                            <p class="fs3">This account currently does not have pack ins enabled. Please contact us at support@ryankikta.com to have pack ins enabled.<p>

                        <?php // } ?>
                    </div>
                   
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
            </div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Hang Tag Attachment</h2>
                        <p>We can attach a tag that hangs from the shirt on the outside. Once we have received your hang tags, we can enable this feature in your account. <a href="/hang-tags">More information about hang tags</a></p>
                
                        <?php // if($access_attach_hang_tag == 1) { ?>

                            <label for="attach_hang_tag">Attach Hang Tag</label>

                            <div class="input_radio_wrapper input_radio_wrapper_inline">
                                <input type="radio" id="hangtag_yes" name="attach_hang_tag" value="1" <?php if($attach_hang_tag){echo 'checked'; }?>><label for="hangtag_yes"> Yes</label>

                                <input type="radio" id="hangtag_no" name="attach_hang_tag" value="0" <?php if(!$attach_hang_tag){echo 'checked'; }?>><label for="hangtag_no"> No</label>
                            </div>

                            <label>Location Option</label>

                            <select name="hang_tag_location">
                            
                                <?php foreach ($location_hang_tag as $key => $loc) {
                                    $selected = ($hang_tag_location == $key) ? 'selected="selected"' : '';
                                    echo '<option value="' . $key . '" ' . $selected . '>' . $loc . '</option>';
                                } ?>

                            </select>
                        

                        <?php // } else { ?>

                            <p class="fs3">This account currently does not have hang tags enabled. Please contact us at support@ryankikta.com to have hang tags enabled.</p>

                        <?php // } ?>
                    </div>
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
        	</div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Individual Bagging</h2>
                        <p>We can put each t-shirt in its own poly bag and apply a simple size sticker on the outside. <a href="/individual-bagging">More information about individual bagging</a></p>
                        <label for="individual_bagging">Individual Bagging</label>

                        <div class="input_radio_wrapper input_radio_wrapper_inline">
                            <input type='radio' id="bagging_yes" name="individual_bagging" value="1" <?php if($individual_bagging){echo 'checked';} ?>><label for="bagging_yes"> Yes</label>

                            <input type='radio' id="bagging_no" name="individual_bagging" value="0" <?php if(!$individual_bagging){echo 'checked';} ?>><label for="bagging_no"> No</label>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
        	</div>

        	<div class="tab_content">
                <div class="row">
                    <div class="col-lg-6">
                        <h2 class="fs1">Custom Packaging</h2>
                        <p>Do you have your own shipping bags you want us to use? We can usually use them for you. Please send us a photo example for us to approve your packaging. Once we have received your bags we can enable this feature in your account. <a href="/shipping-bags">More information about custom packaging</a></p>
                        <?php // if($access_custom_packaging == 1){ ?>

                            <label for="custom_packaging">Custom Packaging</label>
                            <div class="input_radio_wrapper input_radio_wrapper_inline">
                                <input type="radio" id="custompackage_yes" name="custom_packaging" value="1" <?php if($custom_packaging){echo 'checked';} ?>><label for="custompackage_yes"> Yes</label>

                                <input type="radio" id="custompackage_no" name="custom_packaging" value="0" <?php if(!$custom_packaging){echo 'checked';} ?>><label for="custompackage_no"> No</label>
                            </div>
                            
                            <div class="input_outline">
                                <label>Custom Packaging Description</label>
                                <textarea name="custom_packaging_desc"><?php echo $custom_packaging_desc; ?></textarea>
                            </div>

                        <?php // } else { ?> 

                            <p class="fs3"> This account currently does not have custom packaging enabled. Please contact us at support@ryankikta.com to have custom packaging enabled.</p>

                        <?php // } ?>
                    </div>
                    <div class="offset-lg-1 col-lg-4 d-none d-lg-block">
                        <img class="img-fluid" src="<?php echo get_template_directory_uri(); ?>/images/mybrand_graphic.svg">
                    </div>
                </div>
        	</div>
        </div>

        <div class="text-center mb-40">
            <input type="hidden" name="submitted" value="1">
            <button type="submit" id="submit" class="btn-primary">Update Settings</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function () {
		//auto add dashes to the phone number
		jQuery('#cust-service-phone').keyup(function(){
			jQuery(this).val(jQuery(this).val().replace(/(\d{3})\-?(\d{3})\-?(\d{4})/,'$1-$2-$3'))

		});

        jQuery("#remove_logo").on('click', function () {
           jQuery(".packing_logo").hide();
            jQuery("#remove_logo").hide();
            jQuery('#rm_packlogo').val('1');
            jQuery("#file_upload").show();
            jQuery("#remove_button").hide();
        });

        jQuery("#save_specialnote").on("click", function () {
            jQuery.ajax({
                type: "POST",
                url: ajaxurl,
                data: {
                    action: "save_specialnote",
                    uid: '<?php echo base64_encode($current_user->ID) ?>',
                    note: jQuery("#special_note").val(),
                },
                success: function (response) {
                    alert('Special Note saved');
                }
            });

        });

    });
</script> 

<?php get_footer(); ?>
