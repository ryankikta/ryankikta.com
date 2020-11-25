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
class Treepress_Options {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */


	public function get_option($option) {
		$value = get_option($option);

		if ($value !== false) {
			return $value;
		}
		// Option did not exist in database so return default values...
		switch ($option) {
		case "canvasbgcol":
			return '#f1eef1';		// Background colour for tree canvas
		case "nodeoutlinecol":
			return '#adbbbd';		// Outline colour for nodes
		case "nodefillcol":
			return '#ced6d7';		// Fill colour for nodes
		case "nodefillopacity":
			return '1';		// Node opacity (0 to 1)
		case "nodetextcolour":
			return '#555555';		// Node text colour
		case "treepress_toolbar_blogpage":
			return 'true';			// Toolbar button for navigating to the node's blog page
		case "treepress_toolbar_treenav":
			return 'true';			// Toolbar button for navigating to the node's tree

		case "bOneNamePerLine":		// Wrap names
			return 'true';
		case "bOnlyFirstName":
			return 'true';
		case "bBirthAndDeathDates":
			return 'true';
		case "bBirthAndDeathDatesOnlyYear":
			return 'true';
		case "bBirthDatePrefix":
			return 'b';
		case "bDeathDatePrefix":
			return 'd';
		case "bConcealLivingDates":
			return 'true';
		case "bShowSpouse":
			return 'true';
		case "bShowOneSpouse":
			return 'false';
		case "bVerticalSpouses":
			return 'false';
		case "bMaidenName":
			return 'true';
		case "bShowGender":
			return 'true';
		case "bDiagonalConnections":
			return 'false';
		case "bRefocusOnClick":
			return 'false';
		case "bShowToolbar":
			return 'true';
		case "nodecornerradius":
			return '5';
		case "nodeminwidth":
			return '0';
		case "showcreditlink":
			return 'true';
		case "generationheight":
			return '100';
		}
		return '';
	}

	public function check_options() {
		$value = get_option('treepress_link');
		if ($value === false) {
			echo '<script language="javascript">alert("'.__('You need to configure the TreePress plugin and set the \"family tree link\" parameter in the administrator panel.\n\nThis parameter will tell the family tree plugin which page is used to display the main family tree. I.e, the page where you have put the [family-tree] shortcode.\n\n");', 'treepress').'</script>';
		}
	}
}
