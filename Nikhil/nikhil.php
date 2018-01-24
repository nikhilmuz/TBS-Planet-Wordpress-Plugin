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
   if($_SERVER["REQUEST_URI"] == '/feeds/json/newspoint/'.$cat->slug) {
	  header("Content-type: application/json; charset=utf-8");
	  $posts=get_posts(array('numberposts'=>-1,'category'=>$cat->term_id,'orderby' => 'date','order' => 'DESC'));
	  //page details
	  $pg=array(
	  "tr" => sizeof($posts),
	  "pp" => sizeof($posts),
	  "tp" => 1,
	  "cp"=>1
	  );
	  $items=array();
	  //gallery details
	  $galdetails=array(
	  "upd" => $posts[0]->post_date,
	  "id" => $cat->term_id,
	  "h1" => $cat->name,
	  "dm"=>"tbsplanet.com",
	  "tn"=>"photo",
	  "m"=>"https://www.tbsplanet.com/blog/"
	  );
	  //post details
	  $postcount=0;
	  $items=array();
	  foreach($posts as $post){
		  $image=get_children(array('post_parent' => $post->ID,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => 'ASC',
                        'orderby' => 'menu_order ID'));
			$attachment_id='';
			foreach ($image as $imageid=>$imageobject){$attachment_id=$imageobject->ID;}
			$items[$postcount]=array(
			"id" => $post->ID,
			"h1" => $post->post_title,
			"dm" => "tbsplanet.com",
			"cap"=>wp_get_attachment_caption($attachment_id),
			"wu"=>wp_get_attachment_url($attachment_id),
			"m"=>wp_get_attachment_url($attachment_id),
			"ag"=>"TBS Planet Comics",
			"dl"=>$post->post_date
			);
			$postcount++;
	  }
	  $jsonarray=array('pg'=>$pg,'items'=>$items);
	  echo json_encode($jsonarray);
      exit();
	}
	
	//for TOI
	if($_SERVER["REQUEST_URI"] == '/feeds/xml/times/'.$cat->slug) {
	  $posts=get_posts(array('numberposts'=>-1,'category'=>$cat->term_id,'orderby' => 'date','order' => 'DESC'));
	  //post details
header('Content-Type: text/xml');
	  
$xw = xmlwriter_open_memory();
xmlwriter_set_indent($xw, 1);
$res = xmlwriter_set_indent_string($xw, ' ');

xmlwriter_start_document($xw, '1.0', 'UTF-8');
xmlwriter_start_element($xw, 'posts');

foreach($posts as $post){
	
$image=get_children(array('post_parent' => $post->ID,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => 'ASC',
                        'orderby' => 'menu_order ID'));
$attachment_id='';
foreach ($image as $imageid=>$imageobject){$attachment_id=$imageobject->ID;}

xmlwriter_start_element($xw, 'post');
xmlwriter_start_Attribute($xw,'id');
xmlwriter_text($xw,$post->ID);
xmlwriter_end_Attribute($xw);


xmlwriter_start_element($xw, 'h1');
xmlwriter_text($xw, $post->post_title);
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'dm');
xmlwriter_text($xw, 'tbsplanet.com');
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'cap');
xmlwriter_text($xw, wp_get_attachment_caption($attachment_id));
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'wu');
xmlwriter_text($xw, wp_get_attachment_url($attachment_id));
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'm');
xmlwriter_text($xw, wp_get_attachment_url($attachment_id));
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'ag');
xmlwriter_text($xw, 'TBS Planet Comics');
xmlwriter_end_element($xw);

xmlwriter_start_element($xw, 'd1');
xmlwriter_text($xw, $post->post_date);
xmlwriter_end_element($xw);


xmlwriter_end_element($xw);
	  }
	  
	    xmlwriter_end_element($xw);
		xmlwriter_end_document($xw);
		echo xmlwriter_output_memory($xw);
      exit();
	}
	}
}