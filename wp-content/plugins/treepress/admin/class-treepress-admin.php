<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/treepress
 * @since      1.0.0
 *
 * @package    Treepress
 * @subpackage Treepress/admin
 */
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Treepress
 * @subpackage Treepress/admin
 * @author     Md Kabir Uddin <bd.kabiruddin@gmail.com>
 */
class Treepress_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private  $plugin_name ;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private  $version ;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
    
    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Treepress_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Treepress_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'css/treepress-admin.css',
            array(),
            $this->version,
            'all'
        );
    }
    
    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Treepress_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Treepress_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url( __FILE__ ) . 'js/treepress-admin.js',
            array( 'jquery' ),
            $this->version,
            false
        );
    }
    
    public function options_export()
    {
        $treepress = new Treepress( '', '' );
        $families = get_terms( array(
            'taxonomy'   => 'family',
            'hide_empty' => false,
        ) );
        ?>		<div class="wrap">
			<h2>TreePress Export</h2>
			<p>This will export a Zip file for download, which contains a Gedcom (.ged) format (version 5.5) file of your data.  </p>
<form action="" method="post">

<?php 
        wp_nonce_field( 'export_ged' );
        ?>


	<table>
		<tr>
			<td><?php 
        _e( 'Select Family:', 'treepress' );
        ?></td>
			<td>
<select name="family">
	<?php 
        foreach ( $families as $key => $family ) {
            ?>
	<option value="<?php 
            echo  $family->term_id ;
            ?>"> <?php 
            echo  $family->name ;
            ?> </option>
	<?php 
        }
        ?>
</select>
			</td>
			<td><input type="submit" name="submit" value="<?php 
        _e( 'Export', 'treepress' );
        ?>"></td>
		</tr>
	</table>
</form>

</div>
<?php 
        
        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'export_ged' );
            
            if ( !current_user_can( 'administrator' ) ) {
                echo  esc_html( __( 'You are not authorized to do this.', 'treepress' ) ) ;
                die;
            }
            
            $the_family = array();
            
            if ( isset( $_POST['family'] ) ) {
                $family = sanitize_text_field( $_POST['family'] );
                $the_family = $treepress->tree->get_tree( $family );
            }
            
            $famstemp = array();
            $text = '
0 HEAD
1 SOUR TreePress
2 NAME TreePress - Family Trees on WordPress
2 VERS 1.0.0
2 CORP Black and White Digital Ltd
3 ADDR Unit F, 44-48 Shepherdess Walk
4 CONT London, N1 7JP
4 CONT UK
1 DATE 18 JAN 2019
2 TIME 20:59:40
1 FILE test.ged
1 SUBM @SUBM@
1 GEDC
2 VERS 5.5
2 FORM LINEAGE-LINKED
1 CHAR UTF-8
';
            foreach ( $the_family as $key => $member ) {
                $text .= '0 @I' . $member->post_id . '@ INDI' . "\n";
                $text .= '1 NAME /' . $member->name . "/\n";
                
                if ( $member->born ) {
                    $text .= '1 BIRT' . "\n";
                    $text .= '2 DATE ' . strtoupper( date( 'd M Y', strtotime( $member->born ) ) ) . "\n";
                }
                
                
                if ( $member->died ) {
                    $text .= '1 DEAT' . "\n";
                    $text .= '2 DATE ' . strtoupper( date( 'd M Y', strtotime( $member->died ) ) ) . "\n";
                }
                
                $text .= '1 SEX ' . strtoupper( $member->gender ) . "\n";
                if ( $member->spouse && $member->spouse !== '-' ) {
                    
                    if ( $member->gender === 'm' ) {
                        $text .= '1 FAMS @F' . $member->post_id . '_' . $member->spouse . '@' . "\n";
                        array_push( $famstemp, (object) array(
                            'FAM'  => $member->post_id . '_' . $member->spouse,
                            'HUSB' => $member->post_id,
                            'WIFE' => $member->spouse,
                            'CHIL' => $member->children,
                        ) );
                    } else {
                        $text .= '1 FAMS @F' . $member->spouse . '_' . $member->post_id . '@' . "\n";
                        array_push( $famstemp, (object) array(
                            'FAM'  => $member->spouse . '_' . $member->post_id,
                            'HUSB' => $member->spouse,
                            'WIFE' => $member->post_id,
                            'CHIL' => $member->children,
                        ) );
                    }
                
                }
                if ( $member->father && $member->mother ) {
                    $text .= '1 FAMC @F' . $member->father . '_' . $member->mother . '@' . "\n";
                }
                if ( !$member->spouse || $member->spouse === '-' ) {
                    if ( $member->children ) {
                        
                        if ( $member->gender === 'm' ) {
                            array_push( $famstemp, (object) array(
                                'FAM'  => $member->post_id,
                                'HUSB' => $member->post_id,
                                'WIFE' => null,
                                'CHIL' => $member->children,
                            ) );
                        } else {
                            array_push( $famstemp, (object) array(
                                'FAM'  => $member->post_id,
                                'HUSB' => null,
                                'WIFE' => $member->post_id,
                                'CHIL' => $member->children,
                            ) );
                        }
                    
                    }
                }
                if ( $member->father && !$member->mother ) {
                    $text .= '1 FAMC @F' . $member->father . '@' . "\n";
                }
                if ( !$member->father && $member->mother ) {
                    $text .= '1 FAMC @F' . $member->mother . '@' . "\n";
                }
            }
            $fams = array();
            foreach ( $famstemp as $key => $value ) {
                $fams[$value->FAM] = $value;
            }
            foreach ( $fams as $key => $fam ) {
                $text .= '0 @F' . $fam->FAM . '@ FAM' . "\n";
                
                if ( $fam->HUSB ) {
                    $text .= '1 HUSB @I' . $fam->HUSB . '@' . "\n";
                } else {
                    $text .= '1 HUSB ' . "\n";
                }
                
                
                if ( $fam->WIFE ) {
                    $text .= '1 WIFE @I' . $fam->WIFE . '@' . "\n";
                } else {
                    $text .= '1 WIFE ' . "\n";
                }
                
                $text .= '1 MARR' . "\n";
                if ( $fam->CHIL ) {
                    foreach ( $fam->CHIL as $key => $chil ) {
                        $text .= '1 CHIL @I' . $chil . '@' . "\n";
                    }
                }
            }
            $text .= '0 @SUBM@ SUBM
';
            $text .= '0 TRLR';
            
            if ( isset( $_POST['family'] ) ) {
                $family = sanitize_text_field( $_POST['family'] );
                if ( !file_exists( WP_CONTENT_DIR . '/treepress/temp' ) ) {
                    mkdir( WP_CONTENT_DIR . '/treepress/temp', 0777, true );
                }
                $myfile = fopen( WP_CONTENT_DIR . '/treepress/temp/' . $family . '.ged', 'w' ) or die( 'Unable to open file!' );
                fwrite( $myfile, $text );
                fclose( $myfile );
                $zip = new ZipArchive();
                if ( $zip->open( WP_CONTENT_DIR . '/treepress/temp/' . $family . '.zip', ZipArchive::CREATE ) != TRUE ) {
                    die( "Could not open archive" );
                }
                $zip->addFile( WP_CONTENT_DIR . '/treepress/temp/' . $family . '.ged', $family . '.ged' );
                $zip->close();
                echo  '<script> location.replace("' . WP_CONTENT_URL . '/treepress/temp/' . $family . '.zip") </script>' ;
            }
        
        }
    
    }
    
    public function options_import()
    {
        ?>
		<div class="wrap">
			<h2><?php 
        _e( 'TreePress Import', 'treepress' );
        ?> </h2>
			<p>  <?php 
        _e( 'Here you can import a Gedcom (.ged) file which is the standard format for importing and exporting family tree data.', 'treepress' );
        ?> </p>
			<h3><?php 
        _e( 'A Note on Dates:', 'treepress' );
        ?> </h3>
			<p><?php 
        _e( 'Please note that TreePress requires exact dates so if your family tree data exports with approximate date options (such as \'Before\', \'After\', \'Between\' etc) these dates will not import.  Dates must contain day, month and year (although the format does not matter yyyy-mm-dd or dd-mm-yyyy will import).  You cannot import just a year or month-year.', 'treepress' );
        ?></p>
			<p><?php 
        _e( 'If your tree data does contain people without a full birth date, this will mean you need to use a slightly different shortcode to display a tree - see the \'Shortcodes\' tab in the Options page for a full explanation.', 'treepress' );
        ?></p>
				<form action="" method="post" enctype="multipart/form-data">
				<?php 
        wp_nonce_field( 'import_ged' );
        ?>
					<table>
						<tr>
							<td><?php 
        _e( 'Family Name', 'treepress' );
        ?></td>
							<td>
								<input type="text" name="family_name">
							</td>
						</tr>
						<tr>
							<td><?php 
        _e( 'Select file (.ged)', 'treepress' );
        ?></td>
							<td>
								<input type="file" name="ged">
							</td>
						</tr>
						<tr>
							<td></td>
							<td><input type="submit" name="submit" value="<?php 
        _e( 'Import', 'treepress' );
        ?>"></td>
						</tr>
					</table>
				</form>
			</div>
		<?php 
        
        if ( isset( $_POST['submit'] ) ) {
            check_admin_referer( 'import_ged' );
            
            if ( !current_user_can( 'administrator' ) ) {
                echo  esc_html( __( 'You are not authorized to do this.', 'text_domain' ) ) ;
                die;
            }
            
            $family_id = null;
            $file = null;
            
            if ( isset( $_POST['family_name'] ) ) {
                $family_name = sanitize_text_field( $_POST['family_name'] );
                
                if ( $family_name ) {
                } else {
                    echo  esc_html( __( 'Family name required', 'text_domain' ) ) ;
                    die;
                }
            
            } else {
                echo  esc_html( __( 'Family name required', 'text_domain' ) ) ;
                die;
            }
            
            
            if ( isset( $_FILES['ged'] ) ) {
                $file = $_FILES['ged']['tmp_name'];
            } else {
                echo  esc_html( __( 'File required', 'text_domain' ) ) ;
                die;
            }
            
            $term = term_exists( $family_name, 'family' );
            
            if ( 0 !== $term && null !== $term ) {
                echo  esc_html( __( 'Exists! Try different family name', 'text_domain' ) ) ;
                die;
            }
            
            
            if ( current_user_can( 'import' ) ) {
                $family = wp_insert_term( $family_name, 'family' );
                $family_id = $family['term_id'];
            }
            
            
            if ( !$family_id ) {
                echo  esc_html( __( 'Family name required', 'text_domain' ) ) ;
                die;
            }
            
            $individuals = array();
            spl_autoload_register( function ( $class ) {
                $pathToPhpGedcom = plugin_dir_path( dirname( __FILE__ ) ) . 'includes/php-gedcom/library/';
                // TODO FIXME
                if ( !substr( ltrim( $class, '\\' ), 0, 7 ) == 'PhpGedcom\\' ) {
                    return;
                }
                $class = str_replace( '\\', DIRECTORY_SEPARATOR, $class ) . '.php';
                if ( file_exists( $pathToPhpGedcom . $class ) ) {
                    require_once $pathToPhpGedcom . $class;
                }
            } );
            $parser = new \PhpGedcom\Parser();
            $gedcom = $parser->parse( $file );
            foreach ( $gedcom->getIndi() as $individual ) {
                $individuals['persons'][$individual->getId()]['id'] = $individual->getId();
                $individuals['persons'][$individual->getId()]['name'] = ( current( $individual->getName() ) ? current( $individual->getName() )->getName() : '' );
                $individuals['persons'][$individual->getId()]['sex'] = $individual->getSex();
                foreach ( $individual->getEven() as $key => $event ) {
                    if ( $event->getType() === 'BIRT' ) {
                        $individuals['persons'][$individual->getId()]['birt'][] = $event->getDate();
                    }
                    if ( $event->getType() === 'DEAT' ) {
                        $individuals['persons'][$individual->getId()]['deat'][] = $event->getDate();
                    }
                }
                if ( $individual->getEven() ) {
                    foreach ( $individual->getEven() as $key => $event ) {
                        $type = str_replace( ' ', '_', strtolower( $event->getType() ) );
                        $individuals['persons'][$individual->getId()]['event'][$type][$key]['date'] = $event->getDate();
                        
                        if ( $event->getPlac() ) {
                            $individuals['persons'][$individual->getId()]['event'][$type][$key]['place'] = $event->getPlac()->getPlac();
                        } else {
                            $individuals['persons'][$individual->getId()]['event'][$type][$key]['place'] = '';
                        }
                    
                    }
                }
            }
            foreach ( $gedcom->getFam() as $fam ) {
                $individuals['families'][$fam->getId()]['id'] = $fam->getId();
                $individuals['families'][$fam->getId()]['husb'] = $fam->getHusb();
                $individuals['families'][$fam->getId()]['wife'] = $fam->getWife();
                foreach ( $fam->getChil() as $key => $chil ) {
                    $individuals['families'][$fam->getId()]['chil'][] = $chil;
                }
            }
            $rsult = $individuals;
            
            if ( $rsult['persons'] ) {
                $persons = $rsult['persons'];
                foreach ( $persons as $key => $person ) {
                    $my_post = array(
                        'post_title'   => wp_strip_all_tags( trim( str_replace( array( '/', '\\' ), array( ' ', '' ), $person['name'] ) ) ),
                        'post_content' => '',
                        'post_status'  => 'publish',
                        'post_author'  => 1,
                        'post_type'    => 'member',
                    );
                    $post_id = wp_insert_post( $my_post );
                    if ( $person['event'] ) {
                        update_post_meta( $post_id, 'event', $person['event'] );
                    }
                    if ( isset( $person['id'] ) ) {
                        update_post_meta( $post_id, 'source_id', strtolower( $person['id'] ) );
                    }
                    if ( isset( $person['sex'] ) ) {
                        update_post_meta( $post_id, 'gender', strtolower( $person['sex'] ) );
                    }
                    
                    if ( isset( $person['birt'][0] ) ) {
                        $birt = DateTime::createFromFormat( 'd M Y', $person['birt'][0] );
                        
                        if ( is_object( $birt ) ) {
                            $birt = $birt->format( 'Y-m-d' );
                        } else {
                            $birt = '';
                        }
                        
                        update_post_meta( $post_id, 'born', $birt );
                    }
                    
                    
                    if ( isset( $person['deat'][0] ) ) {
                        $deat = DateTime::createFromFormat( 'd M Y', $person['deat'][0] );
                        
                        if ( is_object( $deat ) ) {
                            $deat = $deat->format( 'Y-m-d' );
                        } else {
                            $deat = '';
                        }
                        
                        update_post_meta( $post_id, 'died', $deat );
                    }
                    
                    wp_set_object_terms( $post_id, $family_id, 'family' );
                    $person_map[$person['id']] = $post_id;
                }
            }
            
            
            if ( $rsult['families'] ) {
                $newfamilies = array();
                foreach ( $rsult['families'] as $key => $family ) {
                    if ( isset( $person_map[$family['husb']] ) ) {
                        $newfamilies[$key]['husb'] = $person_map[$family['husb']];
                    }
                    if ( isset( $person_map[$family['wife']] ) ) {
                        $newfamilies[$key]['wife'] = $person_map[$family['wife']];
                    }
                    if ( isset( $family['chil'] ) ) {
                        if ( !empty($family['chil']) ) {
                            foreach ( $family['chil'] as $chil ) {
                                if ( isset( $person_map[$chil] ) ) {
                                    $newfamilies[$key]['chil'][] = $person_map[$chil];
                                }
                            }
                        }
                    }
                }
                foreach ( $newfamilies as $key => $family ) {
                    
                    if ( isset( $family['husb'] ) && isset( $family['wife'] ) ) {
                        /*						$get_wife = get_post_meta($family['husb'], 'spouse', true);
                        $get_wife = $get_wife.','.$family['wife'];
                        $get_wife = explode(',', $get_wife);
                        $get_wife = array_unique($get_wife);
                        $get_wife = implode(',', $get_wife);
                         */
                        update_post_meta( $family['husb'], 'spouse', $family['wife'] );
                        /*						$get_husb = get_post_meta($family['wife'], 'spouse', true);
                        $get_husb = $get_husb.','.$family['husb'];
                        $get_husb = explode(',', $get_husb);
                        $get_husb = array_unique($get_husb);
                        $get_husb = implode(',', $get_husb);
                         */
                        update_post_meta( $family['wife'], 'spouse', $family['husb'] );
                        if ( isset( $family['chil'] ) ) {
                            foreach ( $family['chil'] as $chil ) {
                                update_post_meta( $chil, 'father', $family['husb'] );
                                update_post_meta( $chil, 'mother', $family['wife'] );
                            }
                        }
                    }
                    
                    if ( isset( $family['husb'] ) && !isset( $family['wife'] ) ) {
                        if ( isset( $family['chil'] ) ) {
                            foreach ( $family['chil'] as $chil ) {
                                update_post_meta( $chil, 'father', $family['husb'] );
                            }
                        }
                    }
                    if ( !isset( $family['husb'] ) && isset( $family['wife'] ) ) {
                        if ( isset( $family['chil'] ) ) {
                            foreach ( $family['chil'] as $chil ) {
                                update_post_meta( $chil, 'mother', $family['wife'] );
                            }
                        }
                    }
                }
            }
            
            echo  '<p>' . __( 'Imported successfully', 'treepress' ) . '<p>' ;
            $my_post = array(
                'post_title'   => wp_strip_all_tags( 'Family Tree - ' . $family_name ),
                'post_content' => '[family-tree family=\'' . $family_id . '\']',
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'page',
            );
            $post_id = wp_insert_post( $my_post );
            echo  '<p>' . __( 'Family Tree', 'treepress' ) . ' <a href="' . get_permalink( $post_id ) . '">' . __( ' View Page', 'treepress' ) . '</a><p>' ;
            update_term_meta( $family_id, 'family_tree_link', get_permalink( $post_id ) );
            $my_post = array(
                'post_title'   => wp_strip_all_tags( 'Family Members - ' . $family_name ),
                'post_content' => '[family-members family=\'' . $family_id . '\']',
                'post_status'  => 'publish',
                'post_author'  => 1,
                'post_type'    => 'page',
            );
            $post_id = wp_insert_post( $my_post );
            echo  '<p>' . __( 'Family Members', 'treepress' ) ;
            echo  '<a href="' . get_permalink( $post_id ) . '">' ;
            echo  __( ' View Page', 'treepress' ) ;
            echo  '</a><p>' ;
            update_term_meta( $family_id, 'family_members_link', get_permalink( $post_id ) );
        }
    
    }
    
    public function family_custom_fields( $term )
    {
        $family_tree_link = get_term_meta( $term->term_id, 'family_tree_link', true );
        $family_members_link = get_term_meta( $term->term_id, 'family_members_link', true );
        ?>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="presenter_id"><?php 
        _e( 'Family tree link', 'treepress' );
        ?></label>
			</th>
			<td>
				<input type="text" name="family_tree_link" id="family_tree_link" size="25" style="width:60%;" value="<?php 
        echo  $family_tree_link ;
        ?>"><br />
				<span class="description"><?php 
        _e( 'Family tree link', 'treepress' );
        ?></span>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row" valign="top">
				<label for="presenter_id"><?php 
        _e( 'Family members link', 'treepress' );
        ?></label>
			</th>
			<td>
				<input type="text" name="family_members_link" id="family_members_link" size="25" style="width:60%;" value="<?php 
        echo  $family_members_link ;
        ?>"><br />
				<span class="description"><?php 
        _e( 'Family members link', 'treepress' );
        ?></span>
			</td>
		</tr>

		<?php 
    }
    
    public function save_family_custom_fields( $term_id )
    {
        global  $wpdb ;
        
        if ( isset( $_POST['family_tree_link'] ) ) {
            $family_tree_link = sanitize_text_field( $_POST['family_tree_link'] );
            update_term_meta( $term_id, 'family_tree_link', $family_tree_link );
        }
        
        
        if ( isset( $_POST['family_members_link'] ) ) {
            $family_members_link = sanitize_text_field( $_POST['family_members_link'] );
            update_term_meta( $term_id, 'family_members_link', $family_members_link );
        }
        
        // if(isset($_POST['animal-name']))
        // {
        // 	$species_term_id=$_POST['animal-name'];
        // 	$species_name=$tax_term->name;
        // 	$prefix = $wpdb->prefix;
        // 	$species_name=stripcslashes(strip_tags($species_name));
        // 	update_term_meta($term_id, 'animal-species',$species_name);
        // 	//$total_post=array();
        // 	//	$total_post = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wp_postmeta WHERE meta_value='$species_name'");
        // 	// 	$total_post=$wpdb->get_results(
        // 	//     $wpdb->prepare(
        // 	//         "SELECT * FROM {$wpdb->prefix}postmeta WHERE meta_value=%s",$species_name,""
        // 	//     ),ARRAY_A
        // 	// );
        // $total_post = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta where meta_value='$species_name'");
        // 		if(!empty($total_post))
        // 		{
        // 			foreach ($total_post as $matavalue)
        // 			 {
        // 				$num_object_id=$matavalue->post_id;
        // 				$num_taxo_id=$species_term_id;
        // 						 $table = $prefix . 'term_relationships';
        // 						$wpdb->insert($table, array(
        // 						            'object_id' => $num_object_id,
        // 						            'term_taxonomy_id' => $num_taxo_id,
        // 						            'term_order' => 0
        // 						), array('%d', '%d', '%d'));
        // 			}
        // 		}
        //SELECT * FROM `wp_postmeta` WHERE meta_key LIKE '%pyre_%';
        //}
    }
    
    public function admin_menu()
    {
        add_menu_page(
            __( 'TreePress', 'treepress' ),
            __( 'TreePress', 'treepress' ),
            'manage_options',
            'treepress',
            array( $this, 'options_panel' ),
            'dashicons-networking',
            40
        );
        add_submenu_page(
            'treepress',
            __( 'Add New', 'treepress' ),
            __( 'Add New', 'treepress' ),
            'manage_options',
            'post-new.php?post_type=member'
        );
        add_submenu_page(
            'treepress',
            __( 'Family Groups', 'treepress' ),
            __( 'Family Groups', 'treepress' ),
            'manage_categories',
            'edit-tags.php?taxonomy=family&post_type=member'
        );
        do_action( 'tree_admin_menu' );
        add_submenu_page(
            'treepress',
            __( 'Share', 'treepress' ),
            __( 'Share', 'treepress' ),
            'manage_options',
            'treepress-share',
            array( $this, 'options_share' )
        );
        add_submenu_page(
            'treepress',
            __( 'Options', 'treepress' ),
            __( 'Options', 'treepress' ),
            'manage_options',
            'treepress-options',
            array( $this, 'options_panel' )
        );
        add_submenu_page(
            'treepress',
            __( 'Account Settings', 'treepress' ),
            __( 'Account Settings', 'treepress' ),
            'manage_options',
            'treepress-account-settings',
            array( $this, 'account_settings' )
        );
    }
    
    public function options_share()
    {
        $plugloc = treepress_plugin_dir_url;
        $s_key = ( isset( $_POST['s_key'] ) ? $_POST['s_key'] : '' );
        
        if ( $s_key ) {
            update_option( 'tree_site_key', $s_key );
        } else {
            delete_option( 'tree_site_key' );
        }
        
        if ( !get_option( 'tree_site_key' ) ) {
            update_option( 'tree_site_key', md5( uniqid() ) );
        }
        ?>
			<br>
			<form method="post">
				
			
			<table class="form-table" style="max-width: 100%;">
				<tbody>
					<tr>
						<td width="100"><strong>Site Key</strong> </td>
						<td><input type="text" name="s_key" style="width: 100%;" value="<?php 
        echo  get_option( 'tree_site_key' ) ;
        ?>"> </td>
					</tr>
					<tr>
						<td width="100" style="vertical-align:top;">
							<button class="button button-primary">Save Changes ></button>
						 </td>
						<td>
							<p>The TreePress ‘hub’ website adds extra services and tools to help our plugin users with their family history research.<br><br></p>

							<p>Join for free at <a href="www.treepress.net">www.treepress.net</a> </p>
							<p>
								<ul>
							<li>- Share your research and allow others with similar interests to find mutual family members and contact you</li>
							<li>- Syncronize with your TreePress website</li>
							<li>- Control access with comprehensive privacy controls</li>
								<ul>

							</p>
							<br>
							<br>

							<img src="<?php 
        echo  $plugloc ;
        ?>admin/imgs/Banner.png">
						 </td>
					</tr>
				</tbody>
			</table>
			</form>
			<?php 
    }
    
    public function account_settings()
    {
        ?>
		<div class="wrap">
		<h1 class="wp-heading-inline"><?php 
        _e( 'Account Settings', 'treepress' );
        ?> </h1>
		<p><strong><?php 
        _e( 'Thank you for using TreePress.', 'treepress' );
        ?> </strong></p>
		<p><?php 
        _e( 'You can find a comprehensive support guide here.', 'treepress' );
        ?>   https://www.treepress.net/docs/</p>
		<p><strong> <?php 
        _e( 'Help us Improve TreePress', 'treepress' );
        ?></strong></p>

		<p><?php 
        _e( 'If you would like to make a small donation to help us further develop the plugin, please check out our PayPal link here:', 'treepress' );
        ?> </p>

		<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
		<input type="hidden" name="cmd" value="_s-xclick" />
		<input type="hidden" name="hosted_button_id" value="RSXSRDQ7HANFQ" />
		<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
		<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
		</form>
		<br>
		<br>

		<?php 
        
        if ( tre_fs()->is_not_paying() ) {
            echo  '<section><h1>' . __( 'Awesome Premium Features', 'treepress' ) . '</h1>' ;
            echo  '<a href="' . tre_fs()->get_upgrade_url() . '">' . __( 'Upgrade Now!', 'treepress' ) . '</a>' ;
            echo  '
			</section>' ;
        }
        
        ?>


</div>
		<?php 
    }
    
    public function validate_option( $key )
    {
        
        if ( isset( $_POST[$key] ) ) {
            return 'true';
        } else {
            return 'false';
        }
    
    }
    
    public function create_family_free( $term_id )
    {
        
        if ( tre_fs()->is_not_paying() ) {
            $terms = get_terms( array(
                'taxonomy'   => 'family',
                'hide_empty' => false,
            ) );
            
            if ( count( $terms ) > 1 ) {
                wp_delete_term( $term_id, 'family' );
                echo  '<section><strong>' . __( 'You are not allowed to create more than one family tree on free version. ', 'treepress' ) . '</strong><br>' ;
                echo  '<a href="' . tre_fs()->get_upgrade_url() . '">' . __( 'Upgrade Now!', 'treepress' ) . '</a>' ;
                echo  '</section>' ;
                die;
            }
        
        }
    
    }
    
    public function options_panel()
    {
        global  $wp_version ;
        
        if ( isset( $_POST['update_options'] ) ) {
            $active_tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dyi' );
            if ( $active_tab == 'dyi' ) {
            }
            $bBirthDatePrefix = ( isset( $_POST['bBirthDatePrefix'] ) ? sanitize_text_field( $_POST['bBirthDatePrefix'] ) : 'b' );
            $bDeathDatePrefix = ( isset( $_POST['bDeathDatePrefix'] ) ? sanitize_text_field( $_POST['bDeathDatePrefix'] ) : 'd' );
            if ( function_exists( 'check_admin_referer' ) ) {
                check_admin_referer( 'family-tree-action_options' );
            }
            
            if ( $active_tab == 'to' ) {
                update_option( 'bOneNamePerLine', $this->validate_option( 'bOneNamePerLine' ) );
                update_option( 'bOnlyFirstName', $this->validate_option( 'bOnlyFirstName' ) );
                update_option( 'bBirthAndDeathDates', $this->validate_option( 'bBirthAndDeathDates' ) );
                update_option( 'bBirthAndDeathDatesOnlyYear', $this->validate_option( 'bBirthAndDeathDatesOnlyYear' ) );
                update_option( 'bBirthDatePrefix', $bBirthDatePrefix );
                update_option( 'bDeathDatePrefix', $bDeathDatePrefix );
                update_option( 'bConcealLivingDates', $this->validate_option( 'bConcealLivingDates' ) );
                update_option( 'bShowSpouse', $this->validate_option( 'bShowSpouse' ) );
                update_option( 'bShowOneSpouse', $this->validate_option( 'bShowOneSpouse' ) );
                update_option( 'bVerticalSpouses', $this->validate_option( 'bVerticalSpouses' ) );
                update_option( 'bMaidenName', $this->validate_option( 'bMaidenName' ) );
                update_option( 'bShowGender', $this->validate_option( 'bShowGender' ) );
                update_option( 'bDiagonalConnections', $this->validate_option( 'bDiagonalConnections' ) );
                update_option( 'bRefocusOnClick', $this->validate_option( 'bRefocusOnClick' ) );
            }
            
            
            if ( $active_tab == 'ap' ) {
                update_option( 'bShowToolbar', $this->validate_option( 'bShowToolbar' ) );
                update_option( 'treepress_toolbar_blogpage', $this->validate_option( 'treepress_toolbar_blogpage' ) );
                update_option( 'treepress_toolbar_treenav', $this->validate_option( 'treepress_toolbar_treenav' ) );
                if ( $_POST['canvasbgcol'] != "" ) {
                    update_option( 'canvasbgcol', stripslashes( strip_tags( $_POST['canvasbgcol'] ) ) );
                }
                if ( $_POST['nodeoutlinecol'] != "" ) {
                    update_option( 'nodeoutlinecol', stripslashes( strip_tags( $_POST['nodeoutlinecol'] ) ) );
                }
                if ( $_POST['nodefillcol'] != "" ) {
                    update_option( 'nodefillcol', stripslashes( strip_tags( $_POST['nodefillcol'] ) ) );
                }
                if ( $_POST['nodefillopacity'] != "" ) {
                    update_option( 'nodefillopacity', stripslashes( strip_tags( $_POST['nodefillopacity'] ) ) );
                }
                if ( $_POST['nodetextcolour'] != "" ) {
                    update_option( 'nodetextcolour', stripslashes( strip_tags( $_POST['nodetextcolour'] ) ) );
                }
                if ( $_POST['nodecornerradius'] != "" ) {
                    update_option( 'nodecornerradius', stripslashes( strip_tags( $_POST['nodecornerradius'] ) ) );
                }
                if ( $_POST['nodeminwidth'] != "" ) {
                    update_option( 'nodeminwidth', stripslashes( strip_tags( $_POST['nodeminwidth'] ) ) );
                }
                if ( $_POST['generationheight'] != "" ) {
                    update_option( 'generationheight', stripslashes( strip_tags( $_POST['generationheight'] ) ) );
                }
            }
            
            if ( $active_tab == 'to' ) {
                update_option( 'showcreditlink', $this->validate_option( 'showcreditlink' ) );
            }
            echo  '<div class="updated"><p>Options saved.</p></div>' ;
        }
        
        $treepress = new Treepress();
        $active_tab = ( isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'dyi' );
        ?>


<style type="text/css">
	.treepress-options.wrap .form-table th,
	.treepress-options.wrap .form-table td {
		padding: 15px 0;
	}
	.treepress-options.wrap .form-table th h3,
	.treepress-options.wrap .form-table td h3 {
		margin: 0;
	}


</style>

		<div class="wrap treepress-options">



			<h2><?php 
        _e( 'TreePress Options', 'treepress' );
        ?> </h2>
			<form name="ft_main" method="post">
				<?php 
        if ( function_exists( 'wp_nonce_field' ) ) {
            wp_nonce_field( 'family-tree-action_options' );
        }
        $plugloc = treepress_plugin_dir_url;
        ?>





			<h2 class="nav-tab-wrapper">
				<a href="?page=treepress-options&tab=dyi" class="nav-tab <?php 
        echo  ( $active_tab == 'dyi' ? 'nav-tab-active' : '' ) ;
        ?>"><?php 
        _e( 'Display Your Information', 'treepress' );
        ?>  </a>
				<a href="?page=treepress-options&tab=to" class="nav-tab <?php 
        echo  ( $active_tab == 'to' ? 'nav-tab-active' : '' ) ;
        ?>"><?php 
        _e( 'TreePress Options', 'treepress' );
        ?>  </a>
			   <?php 
        ?>

			</h2>

			<?php 
        
        if ( $active_tab == 'dyi' ) {
            ?>

			<br>

			<h3><?php 
            _e( 'Managing Your Tree Display', 'treepress' );
            ?></h3>

			<p><?php 
            _e( 'TreePress uses shortcodes to display family trees in pages of posts.  There are two formats.  Which one you use depends on your data and what you want to achieve:', 'treepress' );
            ?></p>

			<p><?php 
            echo  __( 'Shortcode format 1 = ', 'treepress' ) . ' <code>[family-tree family=\'{family_id}\']</code> ' ;
            ?></p>
			<p><?php 
            _e( 'If every member of your tree has a full date of birth this shortcode will display the whole family starting with the oldest member. ', 'treepress' );
            ?></p>

			<p><?php 
            echo  __( 'Shortcode format 2 = ', 'treepress' ) . '<code>[family-tree family=\'{family_id}\' root=\'{oldest_member_id_of_family}\'] </code>' ;
            ?></p>
			<p><?php 
            _e( 'If you do not have full birth dates for every family member or simply want to create a tree from a specific person \'root\' within a larger family, use this shortcode.   ', 'treepress' );
            ?></p>

			<h3><?php 
            _e( 'Managing Your Family List Display', 'treepress' );
            ?></h3>
			<p><?php 
            echo  __( 'To create a list of all family members, use the shortcode format', 'treepress' ) . ' <code>[family-members family=\'{family_id}\']</code>' ;
            ?></p>
			<p><?php 
            _e( 'You can control which family tree the [tree icon image] icon links to by adding a page or post link in the individual Family Tree page.', 'treepress' );
            ?></p>

			<h3><?php 
            _e( 'Finding IDs for your Shortcodes', 'treepress' );
            ?></h3>
			<p><?php 
            _e( 'You can find the family_ID on the Families page - when added, the code \'{family_id}\' will look something like \'42\'.  ', 'treepress' );
            ?></p>
			<p><?php 
            _e( 'You can find the member ID on the All Members page.', 'treepress' );
            ?></p>


			<?php 
        }
        
        ?>

			<?php 
        
        if ( $active_tab == 'to' ) {
            ?>
<br>



				<h3><?php 
            _e( 'Treepress options', 'treepress' );
            ?></h3>

<table width="800">
	<tr>
		<td valign="top">
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><h3>Privacy</h3>
						</th>
						<td></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bOnlyFirstName"><?php 
            _e( 'Only show first name', 'treepress' );
            ?></label></th>
						<td><input name="bOnlyFirstName" type="checkbox" id="bOnlyFirstName" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bOnlyFirstName' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bBirthAndDeathDates"><?php 
            _e( 'Show living dates', 'treepress' );
            ?></label></th>
						<td><input name="bBirthAndDeathDates" type="checkbox" id="bBirthAndDeathDates" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bBirthAndDeathDates' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bBirthAndDeathDatesOnlyYear"><?php 
            _e( 'Only show year', 'treepress' );
            ?></label></th>
						<td><input name="bBirthAndDeathDatesOnlyYear" type="checkbox" id="bBirthAndDeathDatesOnlyYear" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bBirthAndDeathDatesOnlyYear' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bConcealLivingDates"><?php 
            _e( 'Conceal living dates for those alive', 'treepress' );
            ?></label></th>
						<td><input name="bConcealLivingDates" type="checkbox" id="bConcealLivingDates" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bConcealLivingDates' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>


					<tr valign="top">
						<th scope="row"><label for="bShowSpouse"><?php 
            _e( 'Show spouse', 'treepress' );
            ?></label></th>
						<td><input name="bShowSpouse" type="checkbox" id="bShowSpouse" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bShowSpouse' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>



					<tr valign="top">
						<th scope="row"><label for="bShowOneSpouse"><?php 
            _e( 'Show only one spouse', 'treepress' );
            ?></label></th>
						<td><input name="bShowOneSpouse" type="checkbox" id="bShowOneSpouse" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bShowOneSpouse' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bShowGender"><?php 
            _e( 'Show gender', 'treepress' );
            ?></label></th>
						<td><input name="bShowGender" type="checkbox" id="bShowGender" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bShowGender' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>


				</table>
		</td>
		<td valign="top">




				<table class="form-table">
					<tr valign="top">
						<th scope="row"><h3>Formatting</h3>
						</th>
						<td></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bOneNamePerLine"><?php 
            _e( 'Wrap names', 'treepress' );
            ?></label></th>
						<td><input name="bOneNamePerLine" type="checkbox" id="bOneNamePerLine" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bOneNamePerLine' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>




					<tr valign="top">
						<th scope="row"><label for="bVerticalSpouses"><?php 
            _e( 'Display spouses vertically', 'treepress' );
            ?></label></th>
						<td><input name="bVerticalSpouses" type="checkbox" id="bVerticalSpouses" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bVerticalSpouses' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bDiagonalConnections"><?php 
            _e( 'Diagonal connections', 'treepress' );
            ?></label></th>
						<td><input name="bDiagonalConnections" type="checkbox" id="bDiagonalConnections" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bDiagonalConnections' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="bRefocusOnClick"><?php 
            _e( 'Refocus on click', 'treepress' );
            ?></label></th>
						<td><input name="bRefocusOnClick" type="checkbox" id="bRefocusOnClick" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bRefocusOnClick' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bBirthDatePrefix"><?php 
            _e( 'Birth Date Prefix', 'treepress' );
            ?></label></th>
						<td><input name="bBirthDatePrefix" type="text" id="bBirthDatePrefix" value="<?php 
            echo  $treepress->options->get_option( 'bBirthDatePrefix' ) ;
            ?>" /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><label for="bDeathDatePrefix"><?php 
            _e( 'Death Date Prefix', 'treepress' );
            ?></label></th>
						<td><input name="bDeathDatePrefix" type="text" id="bDeathDatePrefix" value="<?php 
            echo  $treepress->options->get_option( 'bDeathDatePrefix' ) ;
            ?>" /></td>
					</tr>


					<tr valign="top">
						<th scope="row"><label for="showcreditlink"><?php 
            _e( 'Show Credit Link', 'treepress' );
            ?></label></th>
						<td><input name="showcreditlink" type="checkbox" id="showcreditlink" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'showcreditlink' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>


				</table>
		</td>
	</tr>
</table>


		<?php 
        }
        
        ?>
		<?php 
        
        if ( $active_tab == 'ap' ) {
            ?>
			<br>
				<h3><?php 
            _e( 'Node navigation toolbar', 'treepress' );
            ?></h3>
				<?php 
            _e( 'Each node in the family tree can have a toolbar which can show a number of additional options. Here you can define how the toolbar should work.', 'treepress' );
            ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="bShowToolbar"><?php 
            _e( 'Enable toolbar', 'treepress' );
            ?></label></th>
						<td><input name="bShowToolbar" type="checkbox" id="bShowToolbar" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'bShowToolbar' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="treepress_toolbar_blogpage"><?php 
            _e( 'Enable single page link', 'treepress' );
            ?> <img src="<?php 
            echo  $plugloc ;
            ?>admin/imgs/open-book.png"></label></th>
						<td><input name="treepress_toolbar_blogpage" type="checkbox" id="treepress_toolbar_blogpage" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'treepress_toolbar_blogpage' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="treepress_toolbar_treenav"><?php 
            _e( 'Enable tree nav link', 'treepress' );
            ?> <img src="<?php 
            echo  $plugloc ;
            ?>admin/imgs/tree.gif"></label></th>
						<td><input name="treepress_toolbar_treenav" type="checkbox" id="treepress_toolbar_treenav" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'treepress_toolbar_treenav' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
				</table>
				<h3><?php 
            _e( 'TreePress styling', 'treepress' );
            ?></h3>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="canvasbgcol"><?php 
            _e( 'Background colour (#rgb)', 'treepress' );
            ?></label></th>
						<td><input name="canvasbgcol" type="text" id="canvasbgcol" value="<?php 
            echo  $treepress->options->get_option( 'canvasbgcol' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodeoutlinecol"><?php 
            _e( 'Node outline colour (#rgb)', 'treepress' );
            ?></label></th>
						<td><input name="nodeoutlinecol" type="text" id="nodeoutlinecol" value="<?php 
            echo  $treepress->options->get_option( 'nodeoutlinecol' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodefillcol"><?php 
            _e( 'Node fill colour (#rgb)', 'treepress' );
            ?></label></th>
						<td><input name="nodefillcol" type="text" id="nodefillcol" value="<?php 
            echo  $treepress->options->get_option( 'nodefillcol' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodefillopacity"><?php 
            _e( 'Node opacity (0.0 to 1.0)', 'treepress' );
            ?></label></th>
						<td><input name="nodefillopacity" type="text" id="nodefillopacity" value="<?php 
            echo  $treepress->options->get_option( 'nodefillopacity' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodetextcolour"><?php 
            _e( 'Node text colour (#rgb)', 'treepress' );
            ?></label></th>
						<td><input name="nodetextcolour" type="text" id="nodetextcolour" value="<?php 
            echo  $treepress->options->get_option( 'nodetextcolour' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodetextcolour"><?php 
            _e( 'Node corner radius (pixels)', 'treepress' );
            ?></label></th>
						<td><input name="nodecornerradius" type="text" id="nodecornerradius" value="<?php 
            echo  $treepress->options->get_option( 'nodecornerradius' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="nodeminwidth"><?php 
            _e( 'Node minimum width (pixels)', 'treepress' );
            ?></label></th>
						<td><input name="nodeminwidth" type="text" id="nodeminwidth" value="<?php 
            echo  $treepress->options->get_option( 'nodeminwidth' ) ;
            ?>" size="40" /></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="generationheight"><?php 
            _e( 'Height of generations (pixels)', 'treepress' );
            ?></label></th>
						<td><input name="generationheight" type="text" id="generationheight" value="<?php 
            echo  $treepress->options->get_option( 'generationheight' ) ;
            ?>" size="40" /> <?php 
            _e( '* This parameter is useful if spouses are displayed vertically.', 'treepress' );
            ?></td>
					</tr>
				</table>
			<?php 
        }
        
        ?>
			<?php 
        
        if ( $active_tab == 'cr' ) {
            ?>
<br>
				<h3><?php 
            _e( 'Credit link', 'treepress' );
            ?></h3>
				<p>
					<?php 
            echo  __( 'If you use this plugin then we would be very grateful for some appreciation. ', 'treepress' ) . '
				<b>' . __( 'Appreciation makes us happy.', 'treepress' ) . '</b>' . __( '
					If you don\'t want to link to us from the bottom of the family tree then please consider these other options', 'treepress' ) . ' -
					<br><b>i)</b>' . __( ' send us an', 'treepress' ) . ' <a target="_blank" href="#">' . __( 'email', 'treepress' ) . '</a>' . __( 'and let us know about your family tree website (that would inspire us),', 'treepress' ) . '
					<br><b>ii)</b>' . __( ' include a link to', 'treepress' ) . ' <a target="_blank" href="http://www.treepress.net">www.treepress.net</a>' . __( 'from some other location of your site (that would help us feed our children)', 'treepress' ) . ',
					<br><b>iii)</b> ' . __( 'Give us a good rating at the ', 'treepress' ) . '<a target="_blank" href="#">' . __( 'Wordpress plugin site', 'treepress' ) . '</a>.</p>' ;
            ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row"><label for="showcreditlink"><?php 
            _e( 'Show powered-by link', 'treepress' );
            ?></label></th>
						<td><input name="showcreditlink" type="checkbox" id="showcreditlink" value="Y" <?php 
            echo  ( $treepress->options->get_option( 'showcreditlink' ) == 'true' ? ' checked' : '' ) ;
            ?> /></td>
					</tr>
				</table>
			<?php 
        }
        
        ?>
			<?php 
        ?>



				<p class="submit">
					<input type="hidden" name="action" value="update" />
					<input type="submit" name="update_options" class="button" value="<?php 
        _e( 'Save Changes', 'treepress' );
        ?> &raquo;" />
				</p>
			</form>
		</div>
		<?php 
    }
    
    public function init_post_type_member()
    {
        $labels = array(
            'name'               => _x( 'Members', 'post type general name', 'treepress' ),
            'singular_name'      => _x( 'Member', 'post type singular name', 'treepress' ),
            'menu_name'          => _x( 'Members', 'admin menu', 'treepress' ),
            'name_admin_bar'     => _x( 'Member', 'add new on admin bar', 'treepress' ),
            'add_new'            => _x( 'Add New', 'member', 'treepress' ),
            'add_new_item'       => __( 'Add New Member', 'treepress' ),
            'new_item'           => __( 'New Member', 'treepress' ),
            'edit_item'          => __( 'Edit Member', 'treepress' ),
            'view_item'          => __( 'View Member', 'treepress' ),
            'all_items'          => __( 'All Members', 'treepress' ),
            'search_items'       => __( 'Search Members', 'treepress' ),
            'parent_item_colon'  => __( 'Parent Members:', 'treepress' ),
            'not_found'          => __( 'No members found.', 'treepress' ),
            'not_found_in_trash' => __( 'No members found in Trash.', 'treepress' ),
        );
        $supports = array( 'title', 'custom-fields' );
        $args = array(
            'labels'             => $labels,
            'description'        => __( 'Description.', 'treepress' ),
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => 'treepress',
            'query_var'          => true,
            'rewrite'            => array(
            'slug' => 'member',
        ),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => $supports,
        );
        register_post_type( 'member', $args );
        //category taxonomy
        $labels = array(
            'name'                       => _x( 'Family Groups', 'taxonomy general name', 'treepress' ),
            'singular_name'              => _x( 'Family Group', 'taxonomy singular name', 'treepress' ),
            'search_items'               => __( 'Search Family Groups', 'treepress' ),
            'popular_items'              => __( 'Popular Family Groups', 'treepress' ),
            'all_items'                  => __( 'All Family Groups', 'treepress' ),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __( 'Edit Family Group', 'treepress' ),
            'update_item'                => __( 'Update Family Group', 'treepress' ),
            'add_new_item'               => __( 'Add New Group', 'treepress' ),
            'new_item_name'              => __( 'New Group Name', 'treepress' ),
            'separate_items_with_commas' => __( 'Separate family group with commas', 'treepress' ),
            'add_or_remove_items'        => __( 'Add or remove family group', 'treepress' ),
            'choose_from_most_used'      => __( 'Choose from the most used family group', 'treepress' ),
            'not_found'                  => __( 'No family group found.', 'treepress' ),
            'menu_name'                  => __( 'Family Groups', 'treepress' ),
        );
        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var'             => true,
            'rewrite'               => array(
            'slug' => 'family',
        ),
        );
        register_taxonomy( 'family', 'member', $args );
        //add_action( 'family_add_form_fields', 'add_new_meta_field', 10, 2 );
        // add_action( 'family_add_form_fields', array($this, 'add_new_meta_field'), 10, 2 );
        //  add_action( 'family__edit_form_fields', array($this,'pippin_taxonomy_edit_meta_field'), 10, 2 );
        //  add_action( 'edited_family', 'save_taxonomy_custom_meta', 10, 2 );
        //  add_action( 'create_family', 'save_taxonomy_custom_meta', 10, 2 );
    }
    
    /**
     * set current menu
     *
     */
    public function set_current_menu( $parent_file )
    {
        global  $submenu_file, $current_screen, $pagenow ;
        # Set the submenu as active/current while anywhere in your Custom Post Type (book)
        
        if ( $current_screen->post_type == 'member' ) {
            if ( $pagenow == 'edit-tags.php' ) {
                $submenu_file = 'edit-tags.php?taxonomy=family&post_type=member';
            }
            $parent_file = 'treepress';
        }
        
        return $parent_file;
    }
    
    public function add_meta_boxes_member( $post_type )
    {
        add_meta_box(
            'treepress-meta-box',
            __( 'Member info', 'treepress' ),
            array( $this, 'render_meta_box_member' ),
            'member',
            'normal',
            'high'
        );
        add_meta_box(
            'treepress-meta-box-events',
            __( 'Member Facts', 'treepress' ),
            array( $this, 'render_meta_box_member_events' ),
            'member',
            'normal',
            'high'
        );
        add_meta_box(
            'treepress-meta-box-pv-notes',
            __( 'Private Notes', 'treepress' ),
            array( $this, 'render_meta_box_member_pv_notes' ),
            'member',
            'normal',
            'high'
        );
        if ( class_exists( 'TreepressGallery' ) ) {
            add_meta_box(
                'custom_meta_box',
                __( 'Treepress Gallery Fields', 'treepress' ),
                array( $this, 'treepress_gallery_show_custom_meta_box' ),
                'member',
                'normal',
                'high'
            );
        }
    }
    
    /**
     * Add metabox to posts.
     */
    public function treepress_featured_box( $post )
    {
        wp_nonce_field( plugin_basename( __FILE__ ), $post->post_type . '_noncename' );
        $hide_featured = get_post_meta( $post->ID, '_hide_featured', true );
        ?>
	    <input type="radio" name="_hide_featured" value="1" <?php 
        checked( $hide_featured, 1 );
        ?>><?php 
        _e( 'Yes', 'hide-featured-image' );
        ?>&nbsp;&nbsp;
	    <input type="radio" name="_hide_featured" value="2" <?php 
        checked( $hide_featured, 2 );
        ?>><?php 
        _e( 'No', 'hide-featured-image' );
    }
    
    public function delete_fact_key()
    {
        //print_r($_POST);
        $type = sanitize_text_field( $_POST['type'] );
        $key = sanitize_text_field( $_POST['key'] );
        $post_id = sanitize_text_field( $_POST['post_id'] );
        $events = get_post_meta( $post_id, 'event', true );
        //print_r($events);
        if ( $events ) {
            foreach ( $events as $key_events => $event ) {
                if ( $event ) {
                    foreach ( $event as $key_e => $e ) {
                        if ( $type == $key_events ) {
                            if ( $key == $key_e ) {
                                unset( $events[$key_events][$key_e] );
                            }
                        }
                    }
                }
            }
        }
        //print_r($events);
        update_post_meta( $post_id, 'event', $events );
        die;
    }
    
    public function add_new_fact()
    {
        $type = sanitize_text_field( $_POST['type'] );
        $date = sanitize_text_field( $_POST['date'] );
        $post_id = sanitize_text_field( $_POST['post_id'] );
        $place = sanitize_text_field( $_POST['place'] );
        $events = get_post_meta( $post_id, 'event', true );
        $time_key = time();
        
        if ( $events ) {
            foreach ( $events as $key_events => $event ) {
                if ( $event ) {
                    foreach ( $event as $key_e => $e ) {
                        $events[$type][$time_key] = array(
                            'date'  => $date,
                            'place' => $place,
                        );
                    }
                }
            }
        } else {
            $events = array();
            $events[$type][$time_key] = array(
                'date'  => $date,
                'place' => $place,
            );
        }
        
        update_post_meta( $post_id, 'event', $events );
        echo  '
		<div class="ec_fact">
			<span data-id="' . $post_id . '" data-type="' . $type . '" data-key="' . $time_key . '" class="delete_fact_key" >&times;</span>
			<div>
				<strong>Date : </strong> ' . $date . '
			</div>
			<div>
				<strong>Place : </strong> ' . $place . '
			</div>
		</div>' ;
        die;
    }
    
    public function render_meta_box_member_events( $post )
    {
        $facts = array(
            'adop' => 'ADOPTION',
            'birt' => 'BIRTH',
            'bapm' => 'BAPTISM',
            'barm' => 'BAR_MITZVAH',
            'bles' => 'BLESSING',
            'buri' => 'BURIAL',
            'cens' => 'CENSUS',
            'chr'  => 'CHRISTENING',
            'crem' => 'CREMATION',
            'deat' => 'DEATH',
            'emig' => 'EMIGRATION',
            'grad' => 'GRADUATION',
            'immi' => 'IMMIGRATION',
            'natu' => 'NATURALIZATION',
            'reti' => 'RETIREMENT',
            'prob' => 'PROBATE',
            'will' => 'WILL',
        );
        $events = get_post_meta( $post->ID, 'event', true );
        if ( $events ) {
            foreach ( $events as $key_event => $event ) {
                
                if ( $event ) {
                    ?>
					<div class="cont-<?php 
                    echo  $key_event ;
                    ?>">
					<h4><?php 
                    echo  ( isset( $facts[$key_event] ) ? $facts[$key_event] : $key_event ) ;
                    ?></h4>
					<?php 
                    foreach ( $event as $key_e => $e ) {
                        ?>
						<div class="ec_fact">
							<span data-id="<?php 
                        echo  $post->ID ;
                        ?>" data-type="<?php 
                        echo  $key_event ;
                        ?>" data-key="<?php 
                        echo  $key_e ;
                        ?>" class="delete_fact_key" >&times;</span>
							<div>
								<strong>Date : </strong> <?php 
                        echo  $e['date'] ;
                        ?>
							</div>
							<div>
								<strong>Place : </strong> <?php 
                        echo  $e['place'] ;
                        ?>
							</div>
						</div>
						<?php 
                    }
                    ?>
					</div>		
					<?php 
                }
            
            }
        }
        ?>
		<style type="text/css">
			.fact_modal_bg {
				position: fixed;
				left: 0;
				right: 0;
				top: 0;
				bottom: 0;
				background: #fff;
				z-index: 500;
			}
			.fact_modal {
				position: absolute;
				left: 50%;
				top: 50%;
				transform: translate(-50%, -50%);
				border: 1px solid #d9d9d9;
				padding: 20px;
			}
			.fact_modal_header {
				position: relative;
				margin-left: -20px;
				margin-top: -20px;
				margin-right: -20px;
				margin-bottom: 10px;
				background: #d5d5d5;
			}
			.fact_modal_header h3 {
				margin: 0px;
				padding: 15px;
			}
			.fact_cnt td {
				padding: 4px;
			}
			.ec_fact {
				padding: 10px; border: 1px solid #d9d9d9; max-width: 300px; margin-bottom: 10px;
			}
			.delete_fact_key {
				float: right;display: inline-block;padding: 4px 8px;cursor: pointer;position: relative;top: -10px;right: -10px;
			}
		</style>

		<div class="fact_modal_bg" style="display: none;">
			<div class="fact_modal">
				<div class="fact_modal_header"><h3>Create or Edit a Fact <span style="float: right;display: block;padding: 4px;cursor: pointer;position: relative;top: -5px;">&times;</span></h3> </div>
				<div class="fact_cnt">
					<input type="hidden" class="event_post_id" value="<?php 
        echo  $post->ID ;
        ?>">
					<table>
						<tr>
							<td width="150"><strong>Fact Name</strong></td>
							<td width="200">
								<select class="event_type" name="event[type]">
									<option value="0">Select Fact Name</option>	
								<?php 
        foreach ( $facts as $key => $value ) {
            # code...
            ?>
									<option value="<?php 
            echo  $key ;
            ?>"><?php 
            echo  $value ;
            ?></option>	
									<?php 
        }
        ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><strong>Date</strong></td>
							<td><input type="date" class="event_date" name="event[date]"></td>
						</tr>
						<tr>
							<td><strong>Place</strong></td>
							<td><input type="text" class="event_place" name="event[place]"></td>
						</tr>
						<tr>
							<td><strong></strong></td>
							<td><button type="button" class="button Add_New_Fact_Save">Save</button></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
		<button class="button button-primary Add_New_Fact">Add New Fact</button>
		<?php 
    }
    
    public function treepress_gallery_show_custom_meta_box( $object )
    {
        
        if ( class_exists( 'TreepressGallery' ) ) {
            global  $post ;
            function treepress_gallery_get_image_id( $image_url )
            {
                global  $wpdb ;
                $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE guid='%s';", $image_url ) );
                return $attachment[0];
            }
            
            //       $custom_action = new Treepressgalleryfield\Gallerycustomfield;
            // $custom_action->galleryfield();
            $prefix = 'treepress_gallery_';
            $custom_meta_fields = array(
                'label'        => 'Gallery Images',
                'desc'         => 'This is the gallery images on the single item page.',
                'id'           => $prefix . 'gallery',
                'type'         => 'gallery',
                'imagecaption' => 'treepress_gallery_gallery_title',
            );
            global  $custom_meta_fields ;
            // Use nonce for verification
            //  echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
            // Begin the field table and loop
            echo  '<table class="form-table">' ;
            //   foreach ($custom_meta_fields as $field) {
            // get value of this field if it exists for this post
            $meta = get_post_meta( $post->ID, $custom_meta_fields['id'], true );
            $metacaption = get_post_meta( $post->ID, 'treepress_gallery_gallery_title', true );
            // print_r($metacaption);
            // begin a table row with
            echo  '<tr>
                <th><label for="' . $custom_meta_fields['id'] . '">' . $custom_meta_fields['label'] . '</label></th>
                <td>' ;
            //     switch($field['type']) {
            // case 'media':
            // $close_button = null;
            // if ($meta) {
            //         $close_button = '<span class="treepress_gallery_close"></span>';
            //      //   print_r($meta);
            // }
            // echo '<input id="treepress_gallery_image" type="hidden" name="treepress_gallery_image" value="' . esc_attr($meta) . '" />
            // <div class="treepress_gallery_image_container">' . $close_button . '<img id="treepress_gallery_image_src" src="' . wp_get_attachment_thumb_url(treepress_gallery_get_image_id($meta)) . '">
            // </div>
            // <input id="treepress_gallery_image_button" type="button" value="Add Image" />';
            // break;
            // case 'mobile':
            // $close_button = null;
            // if ($meta) {
            //         $close_button = '<span class="shift8_mobileportfolio_close"></span>';
            // }
            // echo '<input id="treepress_gallery_mobileimage" type="hidden" name="treepress_gallery_mobileimage" value="' . esc_attr($meta) . '" />
            // <div class="treepress_gallery_mobileimage_container">' . $close_button . '<img id="treepress_gallery_mobileimage_src" src="' . wp_get_attachment_thumb_url(treepress_gallery_get_image_id($meta)) . '"></div>
            // <input id="treepress_gallery_mobileimage_button" type="button" value="Add Image" />';
            // break;
            //   case 'gallery':
            $meta_html = null;
            //  if ($meta) {
            $meta_html .= '<ul class="treepress_gallery_gallery_list">';
            $meta_array = explode( ',', $meta );
            // $meta_array_caption=array();
            $meta_array_caption = explode( ',', $metacaption );
            $i = 0;
            foreach ( $meta_array as $meta_gall_item ) {
                $meta_html .= '';
                $meta_html .= '<li>
                                        <div class="treepress_gallery_gallery_container">
                                        <span class="treepress_gallery_gallery_close">
                                        <div class="treepress_gallery_gallery_caption">' . $meta_array_caption[$i] . '</div>
                                        <img id="' . esc_attr( $meta_gall_item ) . '" src="' . wp_get_attachment_thumb_url( $meta_gall_item ) . '">
                     
                                        </span>
                                        </div>
                                        </li>';
                $i++;
            }
            $meta_html .= '</ul>';
            //    }
            echo  '<input id="treepress_gallery_gallery" type="hidden" name="treepress_gallery_gallery" value="' . esc_attr( $meta ) . '" />
                        	<input id="treepress_gallery_gallery_title" type="hidden" name="treepress_gallery_gallery_title" value="' . esc_attr( $meta ) . '" />
                        <span id="treepress_gallery_gallery_src">' . $meta_html . '</span>
                        <div class="treepress_gallery_button_container"><input id="treepress_gallery_gallery_button" type="button" value="Add Gallery" /></div>' ;
            // break;
            //   } //end switch
            echo  '</td></tr>' ;
            //  } // end foreach
            echo  '</table>' ;
            // end table
        }
    
    }
    
    public function render_meta_box_member_pv_notes()
    {
        global  $post ;
        ?>
			<table  style="width: 100%">
				<tr>
					<td valign="top" width="160"><?php 
        _e( 'Notes:', 'treepress' );
        ?> </td>
					<td>
						<textarea style="resize: both;" name="pv_notes" ><?php 
        echo  esc_html( get_post_meta( $post->ID, 'pv_notes', true ), true ) ;
        ?></textarea>
					</td>
				</tr>
			</table>

<?php 
    }
    
    public function render_meta_box_member()
    {
        global  $post ;
        //$family = get_posts('post_type=member&numberposts=-1&orderby=title&order=asc');
        $family = get_posts( 'post_type=member&numberposts=-1' );
        // print_r('<pre>');
        // print_r(get_post_meta($post->ID));
        // print_r('</pre>');
        $males = array();
        $females = array();
        foreach ( $family as $f ) {
            
            if ( $f->ID != $post->ID ) {
                $postgender = get_post_meta( $f->ID, 'gender', true );
                
                if ( $postgender == "m" ) {
                    $males[] = $f;
                } else {
                    
                    if ( $postgender = "f" ) {
                        $females[] = $f;
                    } else {
                        $males[] = $f;
                        $females[] = $f;
                    }
                
                }
            
            }
        
        }
        /*
         *ALL HUMAN SPECIES MEMBER'S GENDER DIVISION
         *$human_male for human male member
         *$human_female for human female member
         *ALL ANIMAL SPECIES MEMBER'S GENDER DIVISION
         *$animal_male for Animal male member
         *$animal_female for Animal female member
         */
        $animal_male = array();
        $animal_female = array();
        $human_male = array();
        $human_female = array();
        foreach ( $family as $s ) {
            $rs = get_post_meta( $s->ID, 'speciesname', true );
            $pg = get_post_meta( $s->ID, 'gender', true );
            
            if ( $rs == 'animal' && $pg == 'm' ) {
                $animal_male[] = $s;
            } else {
                
                if ( $rs == 'animal' && $pg == 'f' ) {
                    $animal_female[] = $s;
                } else {
                    
                    if ( $rs == 'man' && $pg == 'm' ) {
                        $human_male[] = $s;
                    } else {
                        if ( $rs == 'man' && $pg == 'f' ) {
                            $human_female[] = $s;
                        }
                    }
                
                }
            
            }
            
            // else if($rs=='man')
            // {
            // 	$humanspecies[]=$rs;
            // }
        }
        //}
        $gender = get_post_meta( $post->ID, 'gender', true );
        $mother = get_post_meta( $post->ID, 'mother', true );
        $father = get_post_meta( $post->ID, 'father', true );
        $spouse = get_post_meta( $post->ID, 'spouse', true );
        $speciesame = get_post_meta( $post->ID, 'speciesname', true );
        $ifarray = array(
            array(
            'name'    => 'Address',
            'display' => __( 'Address', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Adoption',
            'display' => __( 'Adoption', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Baptism',
            'display' => __( 'Baptism', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Bar Mitzvah',
            'display' => __( 'Bar Mitzvah', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Bat Mitzvah',
            'display' => __( 'Bat Mitzvah', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Burial',
            'display' => __( 'Burial', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Circumcision',
            'display' => __( 'Circumcision', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Confirmation',
            'display' => __( 'Confirmation', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Confirmation (LDS)',
            'display' => __( 'Confirmation (LDS)', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Cremation',
            'display' => __( 'Cremation', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Divorced',
            'display' => __( 'Divorced', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Emigration',
            'display' => __( 'Emigration', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Employment',
            'display' => __( 'Employment', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Endowment (LDS)',
            'display' => __( 'Endowment (LDS)', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Funeral',
            'display' => __( 'Funeral', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Immigration',
            'display' => __( 'Immigration', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Known as',
            'display' => __( 'Known as', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Medical Condition',
            'display' => __( 'Medical Condition', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Military Serial Number',
            'display' => __( 'Military Serial Number', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Military Service',
            'display' => __( 'Military Service', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Mission (LDS)',
            'display' => __( 'Mission (LDS)', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Nationality',
            'display' => __( 'Nationality', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Occupation',
            'display' => __( 'Occupation', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Religion',
            'display' => __( 'Religion', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Sealed to Parents (LDS)',
            'display' => __( 'Sealed to Parents (LDS)', 'treepress' ),
            'type'    => 'date',
        ),
            array(
            'name'    => 'Social Security Number',
            'display' => __( 'Social Security Number', 'treepress' ),
            'type'    => 'text',
        ),
            array(
            'name'    => 'Title',
            'display' => __( 'Title', 'treepress' ),
            'type'    => 'text',
        )
        );
        $sfarray = array( array(
            'name'    => 'Banns',
            'display' => __( 'Banns', 'treepress' ),
            'type'    => 'date',
        ), array(
            'name'    => 'Divorce',
            'display' => __( 'Divorce', 'treepress' ),
            'type'    => 'date',
        ), array(
            'name'    => 'Sealed to Spouse (LDS)',
            'display' => __( 'Sealed to Spouse (LDS)', 'treepress' ),
            'type'    => 'date',
        ) );
        // $term_id1 = $terms1[0]->name;
        /*
         * Check if Animal Treepress plugin is activate or not
         * If it is activated then include the animal Treepress add-on file 
         */
        
        if ( is_plugin_active( 'TreePressAnimals-premium/treePress-animals.php' ) ) {
            include_once WP_PLUGIN_DIR . '/TreePressAnimals-premium/include/animal-file.php';
        } else {
        }
        
        ?>
		
		<div class="man-form">	
			<table>
				<tr>

					<td colspan="2"><h3><?php 
        _e( 'General Info', 'treepress' );
        ?></h3></td>
					<td><?php 
        ?></td>
				</tr>
				<tr>
					<td  width="160"><?php 
        _e( 'ID:', 'treepress' );
        ?> </td>
					<td><input type="text" readonly="readonly"  value="<?php 
        echo  esc_html( $post->ID ) ;
        ?>" id="id" /></td>
				</tr>
			<tr>
				<td><?php 
        _e( 'Gender:', 'treepress' );
        ?></td><td>
				<select name="gender" id="gender">
				<option value="" <?php 
        if ( empty($gender) ) {
            echo  "selected=\"selected\"" ;
        }
        ?>></option>
				<option value="m" <?php 
        if ( $gender == "m" ) {
            echo  "selected=\"selected\"" ;
        }
        ?>><?php 
        _e( 'Male', 'treepress' );
        ?></option>
				<option value="f" <?php 
        if ( $gender == "f" ) {
            echo  "selected=\"selected\"" ;
        }
        ?>><?php 
        _e( 'Female', 'treepress' );
        ?></option>
			</select></td>
			</tr>
				<tr><td><?php 
        _e( 'Born :', 'treepress' );
        ?></td><td><input type="text" name="born" value="<?php 
        echo  esc_html( get_post_meta( $post->ID, 'born', true ), true ) ;
        ?>" id="born" /> (YYYY-MM-DD)</td></tr>
				<tr><td><?php 
        _e( 'Died :', 'treepress' );
        ?></td><td><input type="text" name="died" value="<?php 
        echo  esc_html( get_post_meta( $post->ID, 'died', true ), true ) ;
        ?>" id="died"  /> (YYYY-MM-DD)</td> </tr>
				
				<tr><td><div class="mother"><?php 
        _e( 'Mother:', 'treepress' );
        ?>	</div></td><td>
				<select style="width:200px" name="mother" id="mother">
				<option value="" <?php 
        if ( empty($mother) ) {
            echo  "selected=\"selected\"" ;
        }
        ?>> </option>
				<?php 
        foreach ( $females as $f ) {
            echo  '<option value="' . $f->ID . '" ' ;
            if ( $f->ID == $mother ) {
                echo  "selected=\"selected\"" ;
            }
            echo  '>' . $f->ID . ' - ' . $f->post_title . '</option>' ;
        }
        ?>
			</select>
			</td></tr>			
					<tr>
						<td><div class="father"><?php 
        _e( 'Father:', 'treepress' );
        ?>	</div></td>
					
					<td>
				<select style="width:200px" name="father" id="father">
				<option value="" <?php 
        if ( empty($father) ) {
            echo  "selected=\"selected\"" ;
        }
        ?>> </option>
				<?php 
        foreach ( $males as $f ) {
            echo  '<option value="' . $f->ID . '" ' ;
            if ( $f->ID == $father ) {
                echo  "selected=\"selected\"" ;
            }
            echo  '>' . $f->ID . ' - ' . $f->post_title . '</option>' ;
        }
        ?>
			</select>
			</td></tr>

				<tr><td>
					<div class="spouse">
						<?php 
        _e( 'Spouse:', 'treepress' );
        ?>
						</div>
					</td>
					<td>
			<select style="width:200px" name="spouse" id="spouse">
				<option value="-" <?php 
        if ( empty($spouse) || $spouse == "-" ) {
            echo  "selected=\"selected\"" ;
        }
        ?>> </option>
				<?php 
        foreach ( $family as $f ) {
            echo  '<option value="' . $f->ID . '" ' ;
            if ( $f->ID == $spouse ) {
                echo  "selected=\"selected\"" ;
            }
            echo  '>' . $f->ID . ' - ' . $f->post_title . '</option>' ;
        }
        ?>
			</select>
			</td>

		</tr>

		<?php 
        ?>

		<?php 
        ?>
			<?php 
        
        if ( tre_fs()->is_not_paying() ) {
            echo  '<table><tr><td style="line-height: 30px;">' . __( 'Want more fields for facts on this family member? ', 'treepress' ) ;
            echo  '<a href="' . tre_fs()->get_upgrade_url() . '">' . __( 'Upgrade to the Premium version here', 'treepress' ) . '</a></td>' ;
            echo  '</tr></table>' ;
        }
        
        ?>
		<?php 
    }
    
    function treepress_gallery_save_custom_meta( $post_id )
    {
        
        if ( class_exists( 'TreepressGallery' ) ) {
            global  $custom_meta_fields ;
            // // Verify nonce
            // if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__)))
            //         return $post_id;
            // // Check autosave
            // if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            //         return $post_id;
            // // Check permissions
            // if ('page' == $_POST['post_type']) {
            //         if (!current_user_can('edit_page', $post_id))
            //                 return $post_id;
            // } elseif (!current_user_can('edit_post', $post_id)) {
            //         return $post_id;
            // }
            // Loop through meta fields
            foreach ( $custom_meta_fields as $field ) {
                $new_meta_value = esc_attr( $_POST['treepress_gallery_gallery'] );
                //image caption value
                $new_meta_caption_value = esc_attr( $_POST['treepress_gallery_gallery_title'] );
                print_r( $new_meta_caption_value );
                $meta_key = "treepress_gallery_gallery";
                $meta_caption_key = "treepress_gallery_gallery_title";
                $meta_value = get_post_meta( $post_id, $meta_key, true );
                $meta_value_caption = get_post_meta( $post_id, $meta_caption_key, true );
                // If theres a new meta value and the existing meta value is empty
                
                if ( $new_meta_value && $meta_value == null ) {
                    add_post_meta(
                        $post_id,
                        $meta_key,
                        $new_meta_value,
                        true
                    );
                    // If theres a new meta value and the existing meta value is different
                } elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
                    update_post_meta( $post_id, $meta_key, $new_meta_value );
                } elseif ( $new_meta_value == null && $meta_value ) {
                    delete_post_meta( $post_id, $meta_key, $meta_value );
                }
                
                //image caption vlaue saved here
                
                if ( $new_meta_caption_value && $meta_value_caption == null ) {
                    add_post_meta(
                        $post_id,
                        $meta_caption_key,
                        $new_meta_caption_value,
                        true
                    );
                    // If theres a new meta value and the existing meta value is different
                } elseif ( $new_meta_caption_value && $new_meta_caption_value != $meta_value_caption ) {
                    update_post_meta( $post_id, $meta_caption_key, $new_meta_caption_value );
                } elseif ( $new_meta_caption_value == null && $meta_value_caption ) {
                    delete_post_meta( $post_id, $meta_caption_key, $meta_value_caption );
                }
            
            }
        }
    
    }
    
    /** 
     * When the post is saved, saves our custom data 
     * 
     * @since Hide Featured Images 1.0
     */
    public function featured_image_save_postdata( $post_id )
    {
        $tp_post_types = array( 'member' );
        // $arg=array('post_type'=>'member');
        // $tp_post_types = get_post_types( '', 'names');
        // echo $tp_post_types;
        // unset( $tp_post_types['attachment'], $tp_post_types['revision'], $tp_post_types['nav_menu_item'] );
        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        // if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times
        if ( !wp_verify_nonce( @$_POST[$_POST['post_type'] . '_noncename'], plugin_basename( __FILE__ ) ) ) {
            return;
        }
        // OK,nonce has been verified and now we can save the data according the the capabilities of the user
        if ( in_array( $_POST['post_type'], $tp_post_types ) ) {
            
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return;
            } else {
                $hide_featured = ( isset( $_POST['_hide_featured'] ) && $_POST['_hide_featured'] == 1 ? '1' : $_POST['_hide_featured'] );
                update_post_meta( $post_id, '_hide_featured', $hide_featured );
            }
        
        }
    }
    
    /**
     *  To hide featured image from single post page
     * 
     * @since Hide Featured Image 1.0
     */
    public function treepress_featured_image()
    {
        
        if ( is_single() || is_page() ) {
            $hide = false;
            $nohide = false;
            // $sh_hide_all = get_option('sh_hide_all_image');/* Hide all post or image */
            // echo $sh_hide_all;
            $hide_image = get_post_meta( get_the_ID(), '_hide_featured', true );
            /* Hide single post */
            // $hide = ( is_page() && isset( $sh_hide_all['page_image'] ) && $sh_hide_all['page_image'] && $hide_image != 2 ) ? true : $hide ;
            // $hide = ( is_singular( 'post' ) && isset( $sh_hide_all['post_image'] ) && $sh_hide_all['post_image'] && $hide_image != 2 ) ? true : $hide ;
            $hide = ( isset( $hide_image ) && $hide_image && $hide_image != 2 ? true : $hide );
            /* Hide single post */
            $nohide = ( isset( $hide_image ) && $hide_image && $hide_image == 2 ? true : $nohide );
            /* no single post */
            if ( $hide ) {
                ?>
		          <style>
		          .featured-media img {
		    	  display: none !important ;
		      }           

		          </style><?php 
            }
        } elseif ( $nohide ) {
            ?>
		    	<style>
		    		.featured-media img
		    		{
		    			max-width: 50% !important;
		    		}

		    	</style>

		  <?php 
        }
    
    }
    
    public function save_post_member( $id )
    {
        
        if ( isset( $_POST['animal-name'] ) ) {
            $animal_species = stripcslashes( strip_tags( $_POST['animal-name'] ) );
            add_option( 'animalname', $animal_species );
        }
        
        
        if ( isset( $_POST['species'] ) ) {
            $species_name = stripcslashes( strip_tags( $_POST['species'] ) );
            add_option( 'speciesname', $species_name );
        }
        
        if ( isset( $_POST['born'] ) ) {
            $born = stripslashes( strip_tags( $_POST['born'] ) );
        }
        if ( isset( $_POST['died'] ) ) {
            $died = stripslashes( strip_tags( $_POST['died'] ) );
        }
        if ( isset( $_POST['mother'] ) ) {
            $mother = stripslashes( strip_tags( $_POST['mother'] ) );
        }
        if ( isset( $_POST['father'] ) ) {
            $father = stripslashes( strip_tags( $_POST['father'] ) );
        }
        if ( isset( $_POST['spouse'] ) ) {
            $spouse = stripslashes( strip_tags( $_POST['spouse'] ) );
        }
        if ( isset( $_POST['gender'] ) ) {
            $gender = stripslashes( strip_tags( $_POST['gender'] ) );
        }
        if ( isset( $_POST['cslink'] ) ) {
            $cslink = stripslashes( strip_tags( $_POST['cslink'] ) );
        }
        if ( isset( $_POST['address'] ) ) {
            $address = stripslashes( strip_tags( $_POST['address'] ) );
        }
        if ( isset( $_POST['adoption'] ) ) {
            $adoption = stripslashes( strip_tags( $_POST['adoption'] ) );
        }
        if ( isset( $_POST['baptism'] ) ) {
            $baptism = stripslashes( strip_tags( $_POST['baptism'] ) );
        }
        if ( isset( $_POST['bar_mitzvah'] ) ) {
            $bar_mitzvah = stripslashes( strip_tags( $_POST['bar_mitzvah'] ) );
        }
        if ( isset( $_POST['bat_mitzvah'] ) ) {
            $bat_mitzvah = stripslashes( strip_tags( $_POST['bat_mitzvah'] ) );
        }
        if ( isset( $_POST['burial'] ) ) {
            $burial = stripslashes( strip_tags( $_POST['burial'] ) );
        }
        if ( isset( $_POST['circumcision'] ) ) {
            $circumcision = stripslashes( strip_tags( $_POST['circumcision'] ) );
        }
        if ( isset( $_POST['confirmation'] ) ) {
            $confirmation = stripslashes( strip_tags( $_POST['confirmation'] ) );
        }
        if ( isset( $_POST['confirmation_lds'] ) ) {
            $confirmation_lds = stripslashes( strip_tags( $_POST['confirmation_lds'] ) );
        }
        if ( isset( $_POST['cremation'] ) ) {
            $cremation = stripslashes( strip_tags( $_POST['cremation'] ) );
        }
        if ( isset( $_POST['divorced'] ) ) {
            $divorced = stripslashes( strip_tags( $_POST['divorced'] ) );
        }
        if ( isset( $_POST['emigration'] ) ) {
            $emigration = stripslashes( strip_tags( $_POST['emigration'] ) );
        }
        if ( isset( $_POST['employment'] ) ) {
            $employment = stripslashes( strip_tags( $_POST['employment'] ) );
        }
        if ( isset( $_POST['endowment_lds'] ) ) {
            $endowment_lds = stripslashes( strip_tags( $_POST['endowment_lds'] ) );
        }
        if ( isset( $_POST['funeral'] ) ) {
            $funeral = stripslashes( strip_tags( $_POST['funeral'] ) );
        }
        if ( isset( $_POST['immigration'] ) ) {
            $immigration = stripslashes( strip_tags( $_POST['immigration'] ) );
        }
        if ( isset( $_POST['known_as'] ) ) {
            $known_as = stripslashes( strip_tags( $_POST['known_as'] ) );
        }
        if ( isset( $_POST['medical_condition'] ) ) {
            $medical_condition = stripslashes( strip_tags( $_POST['medical_condition'] ) );
        }
        if ( isset( $_POST['military_serial_number'] ) ) {
            $military_serial_number = stripslashes( strip_tags( $_POST['military_serial_number'] ) );
        }
        if ( isset( $_POST['military_service'] ) ) {
            $military_service = stripslashes( strip_tags( $_POST['military_service'] ) );
        }
        if ( isset( $_POST['mission_lds'] ) ) {
            $mission_lds = stripslashes( strip_tags( $_POST['mission_lds'] ) );
        }
        if ( isset( $_POST['nationality'] ) ) {
            $nationality = stripslashes( strip_tags( $_POST['nationality'] ) );
        }
        if ( isset( $_POST['occupation'] ) ) {
            $occupation = stripslashes( strip_tags( $_POST['occupation'] ) );
        }
        if ( isset( $_POST['religion'] ) ) {
            $religion = stripslashes( strip_tags( $_POST['religion'] ) );
        }
        if ( isset( $_POST['sealed_to_parents_lds'] ) ) {
            $sealed_to_parents_lds = stripslashes( strip_tags( $_POST['sealed_to_parents_lds'] ) );
        }
        if ( isset( $_POST['social_security_number'] ) ) {
            $social_security_number = stripslashes( strip_tags( $_POST['social_security_number'] ) );
        }
        if ( isset( $_POST['title'] ) ) {
            $title = stripslashes( strip_tags( $_POST['title'] ) );
        }
        if ( isset( $_POST['banns'] ) ) {
            $banns = stripslashes( strip_tags( $_POST['banns'] ) );
        }
        if ( isset( $_POST['divorce'] ) ) {
            $divorce = stripslashes( strip_tags( $_POST['divorce'] ) );
        }
        if ( isset( $_POST['sealed_to_spouse_lds'] ) ) {
            $sealed_to_spouse_lds = stripslashes( strip_tags( $_POST['sealed_to_spouse_lds'] ) );
        }
        if ( isset( $_POST['pv_notes'] ) ) {
            $pv_notes = stripslashes( strip_tags( $_POST['pv_notes'] ) );
        }
        
        if ( !empty($animal_species) ) {
            update_post_meta( $id, 'animalname', $animal_species );
            //update_option()
        }
        
        
        if ( !empty($species_name) ) {
            update_post_meta( $id, 'speciesname', $species_name );
            //update_option()
        }
        
        if ( !empty($born) ) {
            update_post_meta( $id, 'born', $born );
        }
        if ( !empty($died) ) {
            update_post_meta( $id, 'died', $died );
        }
        if ( !empty($mother) ) {
            update_post_meta( $id, 'mother', $mother );
        }
        if ( !empty($father) ) {
            update_post_meta( $id, 'father', $father );
        }
        if ( !empty($spouse) ) {
            update_post_meta( $id, 'spouse', $spouse );
        }
        if ( !empty($gender) ) {
            update_post_meta( $id, 'gender', $gender );
        }
        if ( !empty($cslink) ) {
            update_post_meta( $id, 'cslink', $cslink );
        }
        if ( !empty($address) ) {
            update_post_meta( $id, 'address', $address );
        }
        if ( !empty($adoption) ) {
            update_post_meta( $id, 'adoption', $adoption );
        }
        if ( !empty($baptism) ) {
            update_post_meta( $id, 'baptism', $baptism );
        }
        if ( !empty($bar_mitzvah) ) {
            update_post_meta( $id, 'bar_mitzvah', $bar_mitzvah );
        }
        if ( !empty($bat_mitzvah) ) {
            update_post_meta( $id, 'bat_mitzvah', $bat_mitzvah );
        }
        if ( !empty($burial) ) {
            update_post_meta( $id, 'burial', $burial );
        }
        if ( !empty($circumcision) ) {
            update_post_meta( $id, 'circumcision', $circumcision );
        }
        if ( !empty($confirmation) ) {
            update_post_meta( $id, 'confirmation', $confirmation );
        }
        if ( !empty($confirmation_lds) ) {
            update_post_meta( $id, 'confirmation_lds', $confirmation_lds );
        }
        if ( !empty($cremation) ) {
            update_post_meta( $id, 'cremation', $cremation );
        }
        if ( !empty($divorced) ) {
            update_post_meta( $id, 'divorced', $divorced );
        }
        if ( !empty($emigration) ) {
            update_post_meta( $id, 'emigration', $emigration );
        }
        if ( !empty($employment) ) {
            update_post_meta( $id, 'employment', $employment );
        }
        if ( !empty($endowment_lds) ) {
            update_post_meta( $id, 'endowment_lds', $endowment_lds );
        }
        if ( !empty($funeral) ) {
            update_post_meta( $id, 'funeral', $funeral );
        }
        if ( !empty($immigration) ) {
            update_post_meta( $id, 'immigration', $immigration );
        }
        if ( !empty($known_as) ) {
            update_post_meta( $id, 'known_as', $known_as );
        }
        if ( !empty($medical_condition) ) {
            update_post_meta( $id, 'medical_condition', $medical_condition );
        }
        if ( !empty($military_serial_number) ) {
            update_post_meta( $id, 'military_serial_number', $military_serial_number );
        }
        if ( !empty($military_service) ) {
            update_post_meta( $id, 'military_service', $military_service );
        }
        if ( !empty($mission_lds) ) {
            update_post_meta( $id, 'mission_lds', $mission_lds );
        }
        if ( !empty($nationality) ) {
            update_post_meta( $id, 'nationality', $nationality );
        }
        if ( !empty($occupation) ) {
            update_post_meta( $id, 'occupation', $occupation );
        }
        if ( !empty($religion) ) {
            update_post_meta( $id, 'religion', $religion );
        }
        if ( !empty($sealed_to_parents_lds) ) {
            update_post_meta( $id, 'sealed_to_parents_lds', $sealed_to_parents_lds );
        }
        if ( !empty($social_security_number) ) {
            update_post_meta( $id, 'social_security_number', $social_security_number );
        }
        if ( !empty($title) ) {
            update_post_meta( $id, 'title', $title );
        }
        if ( !empty($banns) ) {
            update_post_meta( $id, 'banns', $banns );
        }
        if ( !empty($divorce) ) {
            update_post_meta( $id, 'divorce', $divorce );
        }
        if ( !empty($pv_notes) ) {
            update_post_meta( $id, 'pv_notes', $pv_notes );
        }
        if ( !empty($sealed_to_spouse_lds) ) {
            update_post_meta( $id, 'sealed_to_spouse_lds', $sealed_to_spouse_lds );
        }
        
        if ( isset( $_POST['more_fact_label'] ) ) {
            $more_fact_label = ( isset( $_POST['more_fact_label'] ) ? (array) $_POST['more_fact_label'] : array() );
            $more_fact_label = array_map( 'esc_attr', $more_fact_label );
            update_post_meta( $id, 'more_fact_label', $more_fact_label );
        }
        
        
        if ( isset( $_POST['more_fact_value'] ) ) {
            $more_fact_value = ( isset( $_POST['more_fact_value'] ) ? (array) $_POST['more_fact_value'] : array() );
            $more_fact_value = array_map( 'esc_attr', $more_fact_value );
            update_post_meta( $id, 'more_fact_value', $_POST['more_fact_value'] );
        }
    
    }
    
    public function member_change_title_text( $title )
    {
        $screen = get_current_screen();
        if ( 'member' == $screen->post_type ) {
            $title = __( 'Enter name here', 'treepress' );
        }
        return $title;
    }
    
    public function member_columns( $columns )
    {
        $columns['ID'] = __( 'ID', 'treepress' );
        $columns['born'] = __( 'Born', 'treepress' );
        $columns['title'] = __( 'Name', 'treepress' );
        $columns['father'] = __( 'Father', 'treepress' );
        $columns['mother'] = __( 'Mother', 'treepress' );
        return $columns;
    }
    
    public function member_sortable_columns( $columns )
    {
        $columns['born'] = 'born';
        $columns['taxonomy-family'] = 'family';
        return $columns;
    }
    
    public function member_posts_born_column( $column, $post_id )
    {
        switch ( $column ) {
            case 'father':
                $fid = get_post_meta( $post_id, 'father', true );
                echo  '<a href="' . get_edit_post_link( $fid ) . '">' . get_the_title( $fid ) . '</a>' ;
                break;
            case 'mother':
                $mid = get_post_meta( $post_id, 'mother', true );
                echo  '<a href="' . get_edit_post_link( $fid ) . '">' . get_the_title( $mid ) . '</a>' ;
                break;
            case 'born':
                echo  get_post_meta( $post_id, 'born', true ) ;
                break;
            case 'ID':
                echo  $post_id ;
                break;
        }
    }
    
    public function member_born_orderby( $query )
    {
        $orderby = $query->get( 'orderby' );
        
        if ( 'born' == $orderby ) {
            $query->set( 'meta_key', 'born' );
            $query->set( 'orderby', 'meta_value' );
        }
        
        
        if ( 'family' == $orderby ) {
            $query->set( 'tax_query', array(
                'taxonomy' => 'family',
            ) );
            $query->set( 'orderby', 'meta_value' );
        }
    
    }
    
    public function treepress_paypal_link( $links )
    {
        $url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=RSXSRDQ7HANFQ&source=in-plugin-donate-link';
        $_link = '<a href="' . $url . '" target="_blank">' . __( 'Donate', 'treepress' ) . '</a>';
        $links[] = $_link;
        return $links;
    }
    
    public function add_family_column_content( $content, $column_name, $term_id )
    {
        $term = get_term( $term_id, 'family' );
        switch ( $column_name ) {
            case 'ID':
                $content = '<code>' . $term_id . '</code>';
                break;
        }
        return $content;
    }
    
    public function add_family_columns( $columns )
    {
        $columns['ID'] = 'ID';
        return $columns;
    }
    
    //The Following registers an api route with multiple parameters.
    public function treepress_connect()
    {
        register_rest_route( 'treepress/v1', '/connect', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'treepress_connect_callback' ),
        ) );
    }
    
    public function treepress_connect_callback( $data )
    {
        $arg = $_POST;
        if ( $arg['key'] != get_option( 'tree_site_key' ) ) {
            return false;
        }
        $terms = get_terms( 'family', array(
            'hide_empty' => false,
        ) );
        return $terms;
    }
    
    public function treepress_export()
    {
        register_rest_route( 'treepress/v1', '/export', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'treepress_export_callback' ),
        ) );
    }
    
    public function treepress_export_callback( $data )
    {
        $arg = $_POST;
        if ( $arg['key'] != get_option( 'tree_site_key' ) ) {
            return false;
        }
        $family = $arg['family'];
        return $this->get_family_by_family( $family );
    }
    
    public function get_family_by_family( $family )
    {
        $query = new WP_Query( array(
            'post_type'      => 'member',
            'posts_per_page' => -1,
            'tax_query'      => array( array(
            'taxonomy' => 'family',
            'field'    => 'term_id',
            'terms'    => $family,
        ) ),
        ) );
        $posts = $query->posts;
        
        if ( $posts ) {
            foreach ( $posts as $key => $post ) {
                $post->meta = get_post_meta( $post->ID );
                $post->father = get_post_meta( $post->ID, 'father', true );
                $post->mother = get_post_meta( $post->ID, 'mother', true );
                $post->spouse = get_post_meta( $post->ID, 'spouse', true );
                $post->gender = get_post_meta( $post->ID, 'gender', true );
                $post->born = get_post_meta( $post->ID, 'born', true );
                $post->died = get_post_meta( $post->ID, 'died', true );
            }
            return (object) array(
                'family'  => get_term( $family ),
                'members' => $posts,
            );
        } else {
            die;
        }
    
    }
    
    public function treepress_import()
    {
        register_rest_route( 'treepress/v1', '/import', array(
            'methods'  => 'POST',
            'callback' => array( $this, 'treepress_import_callback' ),
        ) );
    }
    
    public function treepress_import_callback( $data )
    {
        $arg = $_POST;
        if ( $arg['key'] != get_option( 'tree_site_key' ) ) {
            return false;
        }
        foreach ( $_POST['data'] as $key => $member ) {
            foreach ( $member as $keyx => $value ) {
                $wp_member_id = $member['wp_member_id'];
                if ( !is_array( $value ) && !is_object( $value ) ) {
                    if ( $keyx != 'mother' && $keyx != 'father' && $keyx != 'id' && $keyx != 'wp_member_id' && $keyx != 'user_id' && $keyx != 'owner_id' && $keyx != 'family_group_id' && $keyx != 'created_at' && $keyx != 'updated_at' ) {
                        update_post_meta( $wp_member_id, $keyx, $value );
                    }
                }
                if ( $keyx == 'mother' ) {
                    foreach ( $_POST['data'] as $key2 => $valuex ) {
                        
                        if ( $valuex['id'] == $member['mother'] ) {
                            $mother = $member['wp_member_id'];
                            //update_post_meta($wp_member_id, 'mother', $mother);
                        }
                    
                    }
                }
                if ( $keyx == 'father' ) {
                    foreach ( $_POST['data'] as $key3 => $valuey ) {
                        
                        if ( $valuey['id'] == $member['father'] ) {
                            $father = $member['wp_member_id'];
                            //update_post_meta($wp_member_id, 'father', $father);
                        }
                    
                    }
                }
                if ( $keyx == 'spouses' ) {
                    if ( $member['spouses'] ) {
                        foreach ( $member['spouses'] as $key3 => $spou ) {
                            //update_post_meta($wp_member_id, 'spouse', $spou);
                        }
                    }
                }
                
                if ( $keyx == 'more_facts' ) {
                    $more_fact_label = array();
                    $more_fact_value = array();
                    foreach ( $value as $keyxz => $valuer ) {
                        array_push( $more_fact_label, $valuer['name'] );
                        array_push( $more_fact_value, $valuer['value'] );
                    }
                    update_post_meta( $wp_member_id, 'more_fact_label', $more_fact_label );
                    update_post_meta( $wp_member_id, 'more_fact_value', $more_fact_value );
                }
                
                
                if ( $keyx == 'user' ) {
                    update_post_meta( $wp_member_id, 'born', $value['date_of_birth'] );
                    update_post_meta( $wp_member_id, 'died', $value['date_of_death'] );
                    $my_post = array(
                        'ID'         => $wp_member_id,
                        'post_title' => $value['name'],
                    );
                    wp_update_post( $my_post );
                }
            
            }
        }
        return $_POST['data'];
    }

}