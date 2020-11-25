<?php

//[purple_llama] summon a purple llama from the interwebs//
add_shortcode('purple_llama', 'purple_llama_function');
function purple_llama_function() {
     return '<img src="https://images-na.ssl-images-amazon.com/images/I/61Y5IF9HBnL._AC_SX569_.jpg" 
    alt="doti-avatar" width="1440px" height="896" class="left-align" />';
}

add_shortcode('zammad_form', 'zammad_form_function');
function zammad_form_function() {

return '<script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
<script id="zammad_form_script" src="https://support.ryankikta.com/assets/form/form.js#asyncload">
</script>
<script>
jQuery(function() {
    jQuery("[id="feedback-form"]").ZammadForm({
        messageTitle: "<?php //$current_user = wp_get_current_user(); echo " ID: "; echo $current_user->ID; echo " Email: "; echo $current_user->user_email; ?>",
        messageSubmit: "Submit",
        messageThankYou: "Thank you for your inquiry (#%s)! We\'ll contact you as soon as possible.",
        debug: true,    
        modal: true,
        noCSS: true,
        attachmentSupport: true
    });
});
</script>
<a href="" id="feedback-form" class="btn-primary">Contact Us</a>';

}

//[get_style_type product_style_type_id=3,4] use this to call product styles://
//1 mens, 2 ladies, 3 unisex, 4 infant, 5 toddler, 6 youth, 7 accessory, 8 rigid, 9 flexable, 10 household, 11 travel, 12 sticker, 13 small, 14 medium, 15 large, 16 pet//
add_shortcode('get_style_type', 'get_style_type_function');
function get_style_type_function($atts = array()) {
  global $product_style_type_id;
  extract(shortcode_atts(array(
    'product_style_type_id' => '100'
  ), $atts));
  return $product_style_type_id;
}

//[get_category_id category_id=3,4] use this to call product categories://
//1 T-shirts, 2 Sweatshirts, 3 Tank Top, 4 Polos, 5 Dresses, 6 Onesies, 7 Pocket T-shirts, 8 Rompers, 9 Pants, 10 Hats, 11 Bags, 12 Phone Cases, 13 Drinkware, 14 Wall Art, 15 Accessories, 16 Outerwear, 17 Flat Stock//
add_shortcode('get_category_id', 'get_category_function');
function get_category_function($atts = array()) {
  global $category_id;
  extract(shortcode_atts(array(
    'category_id' => '100'
  ), $atts));
  return $category_id;
}

//[get_subcategory product_subcategory_id=1,5] use this to call product subcategories://
//1 shortsleeve, 2 longsleeve, 3 tanktop, 4 hoodie, 5 zipup, 6 crewneck, 7 dress, 8 bib, 9 tote, 10 mug, 11 bottle, 12 athletic, 13 varsity, 14 flexi, 15 folio, 16 poster, 17 coozie, 18 snapcase, 19 toughcase, 20 bakpak1, 21 adhesive, 22 clearcase, 23 trucker, 24 beenie, 25 cap, 26 leggings, 27 apron, 28 frame//
add_shortcode('get_subcategory', 'get_subcategory_function');
function get_subcategory_function($atts = array()) {
  global $product_subcategory_id;
  extract(shortcode_atts(array(
    'product_subcategory_id' => '100'
  ), $atts));
  return $product_subcategory_id;
}

//[get_brand_id brand_id=3,4] use this to call product brand://
//  1 Gildan, 2 American Apparel, 3 Rabbit Skins, 4 Anvil, 5 Bella Canvas, 6 Alternative, 8 Hanes, 9 ALO, 10 Augusta, 11 Precious Cargo, 12 Other, 13 Jerzees, 15 Liberty Bags, 16 Port Authority, 17 Next Level, 18 BAGedge, 19 Ryan Kikta Drinkware, 20 Big Accessories, 21 Yupoong, 22 Bayside, 23 Adams, 24 Independent Trading, 25 Epson Paper, 26 Brand Synchronize, 27 Ryan Kikta Cases, 28 Canvas Eyewear, 29 Ryan Kikta Home, 30 J America, 31 Fruit of the Loom, 32 Econscious, 33 Royal Apparel, 34 Customer Specified, 35 Champion, 36 Ultra Club, 37 Independent Trading EMB, 38 Code V, 39 LAT, 40 Gildan ScreenPrint, 41 Next Level Screenprint, 42 Bella Canvas Screenprint, 43 Independent Screenprint, 44 American Apparel Screenprint, 45 Customer Supplied, 46 Unassigned, 47 Doggie Skins//
add_shortcode('get_brand_id', 'get_brand_id_function');
function get_brand_id_function($atts = array()) {
  global $brand_id;
  extract(shortcode_atts(array(
    'brand_id' => '100'
  ), $atts));
  return $brand_id;
}

//[get_country_id country_id=1] use this to call product country://
// 1 USA
add_shortcode('get_country_id', 'get_country_id_function');
function get_country_id_function($atts = array()) {
  global $country_id;
  extract(shortcode_atts(array(
    'country_id' => '100'
  ), $atts));
  return $country_id;
}
/*
add_shortcode('design_tool', 'design_tool_function');
function design_tool_function() {
        //echo("<script>console.log('this works: " . $data . "');</script>");
	return plugin_dir_path . 'design-tool/index.php';
	//include plugin_dir_path( __FILE__ ) . 'design-tool/index.php';
}*/
/*
add_shortcode('wp-authorize-stripe', 'wp_stripe_shortcode_authorize');
function wp_stripe_shortcode_authorize()
{
    return wp_stripe_form_authorize();
}
*/
?>
