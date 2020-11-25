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
class Treepress_Tree {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public function get_id_by_name($name, $tree) {
		if (is_array($tree)) {
			foreach ($tree as $node) {
				if ($node->name == $name) {
					return $node->post_id;
				}
			}
		}
		return -1;
	}

	public function get_node_by_id($id, $tree) {
		if (is_array($tree)) {
			foreach ($tree as $node) {
				if ($node->post_id == $id) {
					return $node;
				}
			}
		}
		return false;
	}

	public function get_tree($family='') {
		$treepress = new Treepress;

		global $wpdb;

		$the_family = array();

		if($family) {
			$q = new WP_Query(
				array(
					'post_type' => 'member',
					'posts_per_page' => -1,
					'tax_query' => array(
						array(
							'taxonomy' => 'family',
							'field'    => 'term_id',
							'terms'    => $family,
						)
					)
				)
			);
			

			foreach ($q->posts as $post) {
				$the_family[$post->ID] = $treepress->node->get_node($post);
			}
			
		}

		wp_reset_postdata();

		foreach ($the_family as $fm) {
			if (isset($fm->father) && !empty($fm->father) && is_numeric($fm->father) && isset($the_family[$fm->father])) {
				$the_family[$fm->post_id]->name_father 	= $the_family[$fm->father]->name;
				$the_family[$fm->post_id]->url_father 		= $the_family[$fm->father]->url;
				$father = $the_family[$fm->father];
				$father->children[] = $fm->post_id;
			}
			if (isset($fm->mother) && !empty($fm->mother) && is_numeric($fm->mother) && isset($the_family[$fm->mother])) {
				$the_family[$fm->post_id]->name_mother 	= $the_family[$fm->mother]->name;
				$the_family[$fm->post_id]->url_mother 		= $the_family[$fm->mother]->url;
				$mother = $the_family[$fm->mother];
				$mother->children[] = $fm->post_id;
			}
		}
		foreach ($the_family as $fm) {
			$siblings = array();
			$siblings_f = array();
			$siblings_m = array();

			if (isset($fm->father) && !empty($fm->father) && is_numeric($fm->father) && isset($the_family[$fm->father])) {
				$father = $the_family[$fm->father];
				if (is_array($father->children)) {
					$siblings_f = $father->children;
				}
			}
			if (isset($fm->mother) && !empty($fm->mother) && is_numeric($fm->mother) && isset($the_family[$fm->mother])) {
				$mother = $the_family[$fm->mother];
				if (is_array($mother->children)) {
					$siblings_m = $mother->children;
				}
			}
			$siblings = array_merge( $siblings_f, array_diff($siblings_m, $siblings_f));
			$temp = array();
			$temp[] = $fm->post_id;
			$fm->siblings = array_diff($siblings, $temp);
		}
		foreach ($the_family as $fm) {
			$fm->partners = array();
			if (!empty($fm->spouse)) {
				$fm->partners[] = $fm->spouse;
			}
			if (is_array($fm->children)) {
				foreach ($fm->children as $childid) {
					$prospective_partner = "";
					$child = $the_family[$childid];
					if ($fm->gender == 'm') {
						if (!empty($child->mother)) {
							$prospective_partner = $child->mother;
						}
					} else if ($fm->gender == 'f') {
						if (!empty($child->father)) {
							$prospective_partner = $child->father;
						}
					}
					if (!empty($prospective_partner) && is_numeric($prospective_partner)) {
						$found = false;
						foreach ($fm->partners as $p) {
							if ($p == $prospective_partner) {
								$found = true;
								break;
							}
						}
						if (!$found) {
							$fm->partners[] = $prospective_partner;
						}
					}
				}
			}
		}
		uasort($the_family, array($this, "cmp_birthdates"));

		return $the_family;
	}

	public function cmp_birthdates($a, $b) {
		$a = explode("-", $a->born);
		$b = explode("-", $b->born);

		if(isset($a[0])) {
			if(!is_numeric($a[0])){
				$a[0] = intval($a[0]);
			}
		} else {
				$a[0] = 0;
		}

		if(isset($a[1])) {
			if(!is_numeric($a[1])){
				$a[1] = intval($a[1]);
			}
		} else {
				$a[1] = 0;
		}


		if(isset($a[2])) {
			if(!is_numeric($a[2])){
				$a[2] = intval($a[2]);
			}
		} else {
				$a[2] = 0;
		}


		if(isset($b[0])) {
			if(!is_numeric($b[0])){
				$b[0] = intval($b[0]);
			}
		} else {
				$b[0] = 0;
		}


		if(isset($b[1])) {
			if(!is_numeric($b[1])){
				$b[1] = intval($b[1]);
			}
		} else {
				$b[1] = 0;
		}


		if(isset($b[2])) {
			if(!is_numeric($b[2])){
				$b[2] = intval($b[2]);
			}
		} else {
				$b[2] = 0;
		}



		$yd = $a[0] - $b[0];
		if ($yd != 0) {
			return $yd;
		} else {
			$md = $a[1] - $b[1];
			if ($md != 0) {
				return $md;
			} else {
				$dd = $a[2] - $b[2];
				return $dd;
			}
		}
	}
}
