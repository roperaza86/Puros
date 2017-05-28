<?php
/*
Plugin Name: Title Stats All in One SEO Pack Addon
Plugin URI: http://www.netattingo.com/
Description: With the help of this add-on we can see the stats of Titles for All in One SEO Pack
Author: NetAttingo Technologies
Version: 1.0.0
Author URI: http://www.netattingo.com/
*/

define('WP_DEBUG',true);
//initilize constant
define('TSAIOSPA_DIR', plugin_dir_path(__FILE__));
define('TSAIOSPA_URL', plugin_dir_url(__FILE__));
define('TSAIOSPA_PAGE_DIR', plugin_dir_path(__FILE__).'pages/');
define('TSAIOSPA_INCLUDE_URL', plugin_dir_url(__FILE__).'includes/');

//Include menu and assign page
function tsaiospa_plugin_menu() {
    $icon = TSAIOSPA_URL. 'includes/icon.png';
	add_menu_page("All in One SEO Titles", "All in One SEO Titles", "administrator", "tsaiospa-title-stats", "tsaiospa_plugin_pages", $icon ,30);
	add_submenu_page("tsaiospa-title-stats", "About Us", "About Us", "administrator", "about-us", "tsaiospa_plugin_pages");
}
add_action("admin_menu", "tsaiospa_plugin_menu");

function tsaiospa_plugin_pages() {

   $itm = TSAIOSPA_PAGE_DIR.$_GET["page"].'.php';
   include($itm);
}

//add admin css
function tsaiospa_admin_css() {
  wp_register_style('tsaiospa_admin_css', plugins_url('includes/admin-style.css',__FILE__ ));
  wp_enqueue_style('tsaiospa_admin_css');
}
add_action( 'admin_init','tsaiospa_admin_css');

//function for pagination
 function tsaiospa_pagination($pages = '', $range = 4)
{ 
     $showitems = ($range * 2)+1; 
     //global $paged;
	 $paged = (sanitize_text_field( $_GET['paged'] )) ? sanitize_text_field( $_GET['paged'] ) : 1;
     if(empty($paged)) $paged = 1;
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }  
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;&lsaquo;</a>"; //previous
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">&rsaquo;&rsaquo;</a>"; //next
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}


//function for post cout by meta key
function tsaiospa_get_post_count( $kword){
  
    $post_nct='';
	global $wpdb;
	ob_start();
	//all post types array
	$post_type_to_exclude=array('attachment','revision' , 'nav_menu_item');
	$post_type_to_include=array();
	$all_post_types = get_post_types( '', 'names' ); 
	foreach ( $all_post_types as $post_type ) {
		 if ( !in_array($post_type, $post_type_to_exclude)) {
			$post_type_to_include[]= $post_type;
		 }
	}
	
	$args = array(
		   'post_type' => $post_type_to_include,
		   'meta_key' => '_aioseop_title',
		   'post_status'       => 'publish',
		   'posts_per_page' => -1,
		   'meta_query' => array(
			   array(
				   'key' => '_aioseop_title',
				   'value' =>  $kword,
				   'compare' => '=',
			   )
		   )
		 );
	$the_query = new WP_Query( $args );	 
	$post_nct= $the_query->found_posts;
	return $post_nct; 
}

?>