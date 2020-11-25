<?php
// only show page if logged in
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    //wp_redirect("/login");
    //exit();
}

get_header();
//include('sidebar.php');
//echo get_template_directory_uri();
echo $current_user->display_name;
atlas();
?>
<a href = "http://www.oswegocountynewsnow.com/news/new-partnership-advances-virtual-reality-research-at-suny-oswego/article_9d6be22c-88d1-11e6-a71b-dbf37061a2a9.html">LINK</a>
<a href = "https://www.oswego.edu/news/story/new-partnership-advances-virtual-reality-research-suny-oswego">LINK</a>
<a href = "https://www.researchgate.net/profile/Ryan_Kikta">LINK</a>
<a href = "https://meritpages.com/ryankikta">LINK</a>
<a href = "https://www.ancestry.com/search/categories/34/?name=_kikta">LINK</a>
<a href = "https://www.nny360.com/2-faculty-at-suny-oswego-honored-for-scholarly-and-creative-activity/article_04d7a753-f730-5d9c-a954-57c14d0794de.html">LINK</a>
<a href = "https://oswegocountytoday.com/new-partnership-advances-virtual-reality-research-at-suny-oswego/community/">LINK</a>
<a href = "https://romesentinel.com/stories/suny-polytechnic-institute-announces-deans-list,52290">LINK</a>
<a href = "http://oswego.sobeklibrary.com/SUOS000022/00001">LINK</a>
<?php
get_footer();
?>
