<?php
/*
Plugin Name:  Nikhil
Plugin URI:   https://nikhilkumar.ga
Description:  WordPress Plugin by Nikhil for TBS Planet
Version:      1.0
Author:       Nikhil Kumar
Author URI:   https://nikhilkumar.ga
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  nikhil
*/

//get category
$nikhilcats = get_categories();

//add URL hook
add_action('parse_request', 'nikhilurlhandler');
function nikhilurlhandler() {
	global $nikhilcats;
	foreach($nikhilcats as $cat){
		//for Newspoint
   if($_SERVER["REQUEST_URI"] == '/feed/newspoint/'.$cat->slug) {
	  $posts=get_posts(array('numberposts'=>-1,'category'=>$cat->term_id,'orderby' => 'date','order' => 'DESC'));
	  //page details
	  echo '"tr": "'.sizeof($posts).'",'.PHP_EOL;
	  echo '"pp": "'.sizeof($posts).'",'.PHP_EOL;
	  echo '"tp": "1'.'",'.PHP_EOL;
	  echo '"cp": "1"'.PHP_EOL;
	  echo PHP_EOL;
	  //gallery details
	   echo '"upd": "'.$posts[0]->post_date.'",'.PHP_EOL;
	   echo '"id": "'.$cat->term_id.'",'.PHP_EOL;
	   echo '"h1": "'.$cat->name.'",'.PHP_EOL;
	   echo '"dm": "tbsplanet.com",'.PHP_EOL;
	   echo '"tn": "photo",'.PHP_EOL;
	   echo '"m": "https://www.tbsplanet.com/blog/"'.PHP_EOL;
	   echo PHP_EOL;
	  //post details
	  foreach($posts as $post){
		  $image=get_children(array('post_parent' => $post->ID,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => 'ASC',
                        'orderby' => 'menu_order ID'));
			$attachment_id='';
			foreach ($image as $imageid=>$imageobject){$attachment_id=$imageobject->ID;}
		  echo '"id": "'.$post->ID.'",'.PHP_EOL;
		  echo '"h1": "'.$post->post_title.'",'.PHP_EOL;
		  echo '"dm": "tbsplanet.com",'.PHP_EOL;
		  echo '"cap": "';
		  echo wp_get_attachment_caption($attachment_id).'",'.PHP_EOL;
		  echo '"wu": "'.wp_get_attachment_url($attachment_id).'",'.PHP_EOL;
		  echo '"m": "'.wp_get_attachment_url($attachment_id).'",'.PHP_EOL;
		  echo '"ag": "TBS Planet Comics",'.PHP_EOL;
		  echo '"d1": "'.$post->post_date.'"'.PHP_EOL;
		  echo PHP_EOL;
	  }
      exit();
	}
	}
}