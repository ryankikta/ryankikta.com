<?php

/**
 *
 * @link       https://wordpress.org/plugins/treepress
 * @since      1.0.0
 *
 * @package    Treepress
 * @subpackage Treepress/includes
 */

/**
 *
 *
 * @since      1.0.0
 * @package    Treepress
 * @subpackage Treepress/includes
 * @author     Md Kabir Uddin <bd.kabiruddin@gmail.com>
 */
class Treepress_Node {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	var $post_id;
	var $gender;
	var $spouse;
	var $partners;
	var $father;
	var $mother;
	var $born;
	var $died;
	var $thumbsrc;
	var $thumbhtml;

	var $children;
	var $siblings;

	var $name;
	var $name_father;
	var $name_mother;
	var $url;
	var $url_father;
	var $url_mother;

	function __construct() {
		$children = array();

	}

	
	static function get_node($post) {
		$node = new Treepress_Node();
		$node->post_id 	= $post->ID;
		$node->name 		= $post->post_title;
		$node->url		= get_permalink($post->ID);
		$node->gender	= get_post_meta($post->ID, 'gender', true);
		$node->father	= get_post_meta($post->ID, 'father', true);
		$node->mother	= get_post_meta($post->ID, 'mother', true);
		$node->spouse	= get_post_meta($post->ID, 'spouse', true);

		$node->born	= get_post_meta($post->ID, 'born', true);
		$node->died	= get_post_meta($post->ID, 'died', true);

		if (function_exists('get_post_thumbnail_id')) {
			$thumbid = get_post_thumbnail_id($post->ID);
			$thumbsrc = wp_get_attachment_image_src($thumbid, 'thumbnail');
			$node->thumbsrc = $thumbsrc[0];
			$node->thumbhtml = get_the_post_thumbnail($post->ID, 'thumbnail');
		}
		return $node;
	}
	//$creat_type= get_posts('post_type=member&numberposts=-1');
	//echo $creat_type->post_title;
	//if Animlatreepress plugin activate then update the label




	public function more_fact_check($post_id){
		if(!get_post_meta($this->post_id, 'more_fact_label', true)) {
			return false;
		}

		if(!get_post_meta($this->post_id, 'more_fact_label', true)[0]) {
			return false;
		}
		return true;
	}


	public function get_html($the_family) {
		$treepress = new Treepress;
		$html = '<table border="0" width="100%">';
		$html .= '<tr><td width="33.333333%" style="vertical-align:bottom"><b><a href="'.$this->url.'">';
		if (!empty($this->thumbhtml)) {
			$html .= $this->thumbhtml;
		}
		$html .= "<br>".$this->name.'</a> </b>';
		$plugloc = treepress_plugin_dir_url;

		if ($this->gender == 'm') {
			$html .= '<img alt="'.__('Male', 'treepress').'" title="'.__('Male', 'treepress').'" src="'.$plugloc.'public/imgs/icon-male-small.gif"/>';
		} else if ($this->gender == 'f') {
			$html .= '<img alt="'.__('Female', 'treepress').'" title="'.__('Female', 'treepress').'" src="'.$plugloc.'public/imgs/icon-female-small.gif"/>';
		} else {
			$html .= '<img alt="'.__('Gender not specified', 'treepress').'" title="'.__('Gender not specified', 'treepress').'" src="'.$plugloc.'public/imgs/icon-qm-small.gif"/>';
		}



		$terms = wp_get_post_terms($this->post_id, 'family');
		if($terms){
			$term_id = $terms[0]->term_id;
			$ftlink = get_term_meta( $term_id, 'family_tree_link', true);
		} else {
			$ftlink = '';
		}

		if (strpos($ftlink, '?') === false) {
			$html .=' <a href="'.$ftlink.'?ancestor='.$this->post_id.'"><img border="0" alt="'.__('View tree', 'treepress').'" title="'.__('View tree', 'treepress').'" src="'.$plugloc.'public/imgs/icon-tree-small.gif"/></a>';
		} else {
			$html .=' <a href="'.$ftlink.'&ancestor='.$this->post_id.'"><img border="0" alt="'.__('View tree', 'treepress').'" title="'.__('View tree', 'treepress').'" src="'.$plugloc.'public/imgs/icon-tree-small.gif"/></a>';
		}






		$html .= '</td>';

		$hideall = get_option('bConcealLivingDates');

		if($this->died) {
			$hideall = 'false';
		}

		if ($hideall == 'false') {

			if (!empty($this->born) && strlen($this->born) > 1 && get_option('bBirthAndDeathDates')=='true') {

				if(get_option('bBirthAndDeathDatesOnlyYear')=='true'){
					$html .= '
					<td style="vertical-align:bottom" width="33.333333%">'.__('Born:', 'treepress').' '.explode('-', $this->born)[0].'</td>';
				} else {
					$html .= '
					<td style="vertical-align:bottom" width="33.333333%">'.__('Born:', 'treepress').' '.$this->born.'</td>';
				}
			} else {
				$html .= '
				<td></td>';
			}
			if (!empty($this->died) && strlen($this->died) > 1 && get_option('bBirthAndDeathDates')=='true') {
				if(get_option('bBirthAndDeathDatesOnlyYear')=='true'){
					$html .= '
					<td style="vertical-align:bottom" width="33.333333%">'.__('Died:', 'treepress').' '.	explode('-', $this->died)[0].'</td>';
				} else {
					$html .= '
					<td style="vertical-align:bottom" width="33.333333%">'.__('Died:', 'treepress').' '.	$this->died.'</td>';
				}
			} else {
				$html .= '
				<td></td>';
			}
		} else {
				$html .= '
				<td></td>';
		}


	$html .= '</tr>';
	
		/**Tree's Member Label dynamically upgration.
		* When Animal is selected for the Tree Family then the 
		* Termonology used to describe them also change

		*/
		global $post;
		//$mothers=$post->ID;
		
		$animalnames=get_post_meta($this->post_id,'animalname',true);
		$terms1 = get_the_terms($this->post_id, 'family' );
			 $term_id1 = $terms1[0]->name;
		if(is_plugin_active( 'TreePressAnimals-premium/treePress-animals.php'))
	{
					// Plugin is active
		global $wpdb;
		$animalname=$term_id1;

		 $res = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wphr_animal_file WHERE animal_name='$animalnames'");
		//print_r($res);
				  if(!empty($res))
			{
				  	 foreach($res as $row)
				  	 {

				$father_termonology=$row->father_name;
				$mother_termonology=$row->mother_name;
				$son_termonology=$row->child_name;
				$plural_son_termonology=$row->children_name;
				$mate_termonology="Mate";
					} 
			}
			
			else
			{
				$mother_termonology="Mother";
				$father_termonology="Father";
				$son_termonology="Children";
				$mate_termonology="Spouse";

			}
	
	}
	else
	{
		$mother_termonology="Mother";
		$father_termonology="Father";
		$son_termonology="Children";
		$mate_termonology="Spouse";

	}


		
		$html .= '<tr>
		<td>'.__($father_termonology.': ', 'treepress');
		if (isset($this->name_father)) {
			$html .= '<a href="'.$this->url_father.'">'.$this->name_father.'</a>';
		} else {
			$html .= __('Unspecified', 'treepress');
		}
		
		$html .= '</td>';
		$html .= '<td>'.__($mother_termonology.': ', 'treepress');
		if (isset($this->name_mother)) {
			$html .= '<a href="'.$this->url_mother.'">'.$this->name_mother.'</a>';
		} else {
			$html .= __('Unspecified', 'treepress');
		}
		$html .= '</td>';

		$html .= '<td>';

		if (isset($this->spouse) && get_post($this->spouse)) {
			if($this->spouse){
				$html .= __($mate_termonology.': ', 'treepress');
				$html .= '<a href="'.get_the_permalink($this->spouse).'">'.get_post($this->spouse)->post_title.'</a>';
			}  
		} 

		$html .= '</td>
		</tr>';
		if(isset($this->children) && count($this->children)>1 && !empty($plural_son_termonology))

		{
			$html .= '<tr><td colspan="3">'.__($plural_son_termonology.': ', 'treepress');
		}
			else
		{
		$html .= '<tr><td colspan="3">'.__($son_termonology.': ', 'treepress');
		}
		if (isset($this->children) && count($this->children) > 0) {
			$first = true;
			foreach ($this->children as $child) {
				if (!$first) {
					$html .= ', ';
				} else {
					$first = false;
				}
				$html .= '<a href="'.$the_family[$child]->url.'">'.$the_family[$child]->name.'</a>';
			}
		} else {
			$html .= 'none';
		}
		$html .= '</td></tr>';
		$html .= '<tr><td colspan="3">'.__('Siblings: ', 'treepress');
		if (count($this->siblings) > 0) {
			$first = true;
			foreach ($this->siblings as $sibling) {
				if (!$first) {
					$html .= ', ';
				} else {
					$first = false;
				}
				$html .= '<a href="'.$the_family[$sibling]->url.'">'.$the_family[$sibling]->name.'</a>';
			}
		} else {
			$html .= 'none';
		}
		$html .= '</td></tr>';


			$ifarray = array(
				array(
					'name' => 'Address',
					'display' => __('Address', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Adoption',
					'display' => __('Adoption', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Baptism',
					'display' => __('Baptism', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Bar Mitzvah',
					'display' => __('Bar Mitzvah', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Bat Mitzvah',
					'display' => __('Bat Mitzvah', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Burial',
					'display' => __('Burial', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Circumcision',
					'display' => __('Circumcision', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Confirmation',
					'display' => __('Confirmation', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Confirmation (LDS)',
					'display' => __('Confirmation (LDS)', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Cremation',
					'display' => __('Cremation', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Divorced',
					'display' => __('Divorced', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => __('Emigration', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Employment',
					'display' => __('Employment', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Endowment (LDS)',
					'display' => __('Endowment (LDS)', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Funeral',
					'display' => __('Funeral', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Immigration',
					'display' => __('Immigration', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Known as',
					'display' => __('Known as', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Medical Condition',
					'display' => __('Medical Condition', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Military Serial Number',
					'display' => __('Military Serial Number', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Military Service',
					'display' => __('Military Service', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Mission (LDS)',
					'display' => __('Mission (LDS)', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Nationality',
					'display' => __('Nationality', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Occupation',
					'display' => __('Occupation', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Religion',
					'display' => __('Religion', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Sealed to Parents (LDS)',
					'display' => __('Sealed to Parents (LDS)', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Social Security Number',
					'display' => __('Social Security Number', 'treepress'),
					'type' => 'text',
				),
				array(
					'name' => 'Title',
					'display' => __('Title', 'treepress'),
					'type' => 'text',
				),
			);

			$sfarray = array(
				array(
					'name' => 'Banns',
					'display' => __('Banns', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Divorce',
					'display' => __('Divorce', 'treepress'),
					'type' => 'date',
				),
				array(
					'name' => 'Sealed to Spouse (LDS)',
					'display' => __('Sealed to Spouse (LDS)', 'treepress'),
					'type' => 'date',
				),
			);


		$ifarray_check = array();
		foreach ($ifarray as $key => $ifarray_item) {
			if(get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $ifarray_item['name'])), true)) {
				array_push($ifarray_check, '1');
			}
		}

		$sfarray_check = array();
		foreach ($sfarray as $key => $sfarray_item) {
			if(get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $sfarray_item['name'])), true)) {
				array_push($sfarray_check, '1');
			}
		}

		$more_fact_check = $this->more_fact_check($this->post_id);



		if($ifarray_check || $sfarray_check || $more_fact_check) {
			$html .= '<tr><td width="33.3333333%" valign="top">';
			if($ifarray_check){
				$html .= '<strong>'.__('Individual Facts', 'treepress').'</strong><br>';
				foreach ($ifarray as $key => $ifarray_item) {
					if(get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $ifarray_item['name'])), true)) {
						$html .= $ifarray_item['display'].': '.get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $ifarray_item['name'])), true).'<br>';
					}
				}
			}
			$html .= '</td><td width="33.3333333%" valign="top">';

			if($sfarray_check){
				$html .= '<strong>'.__('Shared Facts', 'treepress').'</strong><br>';
				foreach ($sfarray as $key => $sfarray_item) {
					if(get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $sfarray_item['name'])), true)) {
						$html .= $sfarray_item['display'].': '.get_post_meta($this->post_id, strtolower(str_replace(' ', '_', $sfarray_item['name'])), true).'<br>';
					}
				}
			}
			$html .= '</td><td width="33.3333333%" valign="top">';
			if($more_fact_check){
				$html .= '<strong>'.__('More Facts', 'treepress').'</strong><br>';
				foreach (get_post_meta($this->post_id, 'more_fact_label', true) as $key_m_f => $more_fact) {
					if($more_fact){
						$html .=  get_post_meta($this->post_id, 'more_fact_label', true)[$key_m_f].': '.get_post_meta($this->post_id, 'more_fact_value', true)[$key_m_f].'<br>';
					}
				}
			}
			$html .= '</td></tr>';
		}
		$html .= '</table>';

		$facts = array (
			'adop' => 'ADOPTION',
			'birt' => 'BIRTH',
			'bapm' => 'BAPTISM',
			'barm' => 'BAR_MITZVAH',
			'bles' => 'BLESSING',
			'buri' => 'BURIAL',
			'cens' => 'CENSUS',
			'chr' => 'CHRISTENING',
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
		$events = get_post_meta($this->post_id, 'event', true);

		if($events && current($events)) {
			$html .= '<h4>Member Facts</h4>';
			foreach ($events as $key_event => $event) {
				if($event) {
					$fac_name = isset($facts[$key_event]) ? $facts[$key_event] : $key_event;
					$html .= '<h5> '.strtoupper(str_replace('_', ' ', $fac_name)) .'</h5>';
					$key_x = 0;
					foreach ($event as $key_e => $e) {
						$key_x++;
						# code...
						$html .= '<strong> '.  $key_x .'. </strong>';
						$html .= '<table>';
							$html .= '<tr><td width="160">Date</td><td>'.$e['date'].'</td></tr>';
							$html .= '<tr><td>Place</td><td>'.$e['place'].'</td></tr>';
						$html .= '</table>';
					}
				}
			}

		}
	
	//Treepress Member Gallery section 
			// if(is_plugin_active('Treepress-Gallery/Treepress-Gallery.php'))
			// {
			if(class_exists('TreepressGallery'))
			{
				$html .= '<table border="0" width="100%">';
			$html.='<tr><td>';
			//$gallery_display = null;
			// $gallery_array = explode(',', get_post_meta($this->post_id,'treepress-gallery-gallery',true));
			// print_r($gallery_array);
			$gallery_array = explode(',', get_post_meta($this->post_id,'treepress_gallery_gallery',true));
			$gallery_title = explode(',', get_post_meta($this->post_id, 'treepress_gallery_gallery_title', true));

			if (is_array($gallery_array)) {
				$imagetitle=0;
				$html .= '<ul class="treepress-gallery-gallery">';
				$html .= '<sections data-featherlight-gallery data-featherlight-filter="a">';
				foreach ($gallery_array as $gallery_item) {
					$html .= '<li>
					
					<a href="' . wp_get_attachment_url($gallery_item) . '">
					<img id="treepress-gallery-item-' . $gallery_item . '" src="' . wp_get_attachment_thumb_url($gallery_item) . '">
					</a><p class="treepress_gallery_gallery_caption">'.$gallery_title[$imagetitle].'</p></li>';
					$imagetitle++;
				}
				$html .= '</section>
				</ul>';
			}
			$html.='</tr></td>';
			$html .= '</table>';
			}
			else
			{

			}
			return $html;
		}

	function get_toolbar_div() {
		$treepress = new Treepress;

		$plugloc = treepress_plugin_dir_url;

		$terms = wp_get_post_terms($this->post_id, 'family');
		if($terms){
			$term_id = $terms[0]->term_id;
			$ftlink = get_term_meta( $term_id, 'family_tree_link', true);
		} else {
			$ftlink = '';
		}

		if (strpos($ftlink, '?') === false) {
			$ftlink = $ftlink.'?ancestor='.$this->post_id;
		} else {
			$ftlink = $ftlink.'&ancestor='.$this->post_id;
		}

		$cslink = get_post_meta( $this->post_id, 'cslink', true);


		$permalink = get_permalink($this->post_id);
		$html = '';

		if ($treepress->options->get_option('bShowToolbar') == 'true') {
			$html .= '<div class="toolbar" id="toolbar'.$this->post_id.'">';
			if ($treepress->options->get_option('treepress_toolbar_blogpage') == 'true') {
				$html .= '<a class="toolbar-blogpage" href="'.$permalink.'" title="'.__('View information about', 'treepress').' '.htmlspecialchars($this->name).'">
				<img border="0" class="toolbar-blogpage" src="'.$plugloc.'public/imgs/open-book.png">
				</a>';
			}
			if ($treepress->options->get_option('treepress_toolbar_treenav') == 'true') {
				$html .= '<a class="toolbar-treenav" href="'.$ftlink.'" title="'.__('View the family of', 'treepress').' '.htmlspecialchars($this->name).'">
				<img width="13" border="0" class="toolbar-treenav" src="'.$plugloc.'public/imgs/tree.gif">
				</a>';
			}

			if ($cslink) {
				$html .= '<a class="toolbar-treenav" href="'.$cslink.'" title="'.__('View the family of', 'treepress').' '.htmlspecialchars($this->name).'">
				<img width="13" border="0" class="toolbar-treenav" src="'.$plugloc.'public/imgs/cslink.jpg">
				</a>';
			}

			$html .= '</div>';
		}
		return $html;
	}

	function get_thumbnail_div() {
		$plugloc = treepress_plugin_dir_url;

		$html = '';
		$html .= '<div class="treepress_thumbnail" id="thumbnail'.$this->post_id.'">';
		if (!empty($this->thumbsrc)) {
			$html .= '<img src="'.$this->thumbsrc.'">';
		} else {
			$html .= '<img style="width:50px;" src="'.$plugloc.'public/imgs/no-avatar.png">';
		}
		$html .= '</div>';

		return $html;
	}


	function get_box_html($the_family) {
		$html = '';
		$html .= '<a href="'.$this->url.'">'.$this->name.'</a>';
		$html .= '<br>'.__('Born:', 'treepress').' '.$this->born;
		if (!empty($this->died) && strlen($this->died) > 1) {
			$html .= '<br>'.__('Died:', 'treepress').' '.	$this->died;
		}
		return $html;
	}
}
