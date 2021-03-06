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
	if(preg_match("/newspoint/",$_SERVER["REQUEST_URI"])&&preg_match("/cats/",$_SERVER["REQUEST_URI"])&&preg_match("/json/",$_SERVER["REQUEST_URI"])) {
	  header("Content-type: application/json; charset=utf-8");
	  $posts=get_posts(array('numberposts'=>-1,'category'=>$cat->term_id,'orderby' => 'date','order' => 'DESC'));
	  
	  $childcats=get_categories(array(
'type'                     => 'post',
'child_of'                 => get_category_by_slug( 'times' )->term_id,
'orderby'                  => 'name',
'order'                    => 'ASC',
'taxonomy'                 => 'category'
)); 
	  //page details
	  $pg=array(
	  "tr" => sizeof($childcats),
	  "pp" => sizeof($childcats),
	  "tp" => 1,
	  "cp"=>1
	  );
	  $items=array();
	  //post details
	  $catcount=0;
	  $items=array();
	  foreach($childcats as $cats){
                   $posts=get_posts(array('numberposts'=>-1,'category'=>$cats->term_id,'orderby' => 'date','order' => 'ASC'));
                   $image=get_children(array('post_parent' => $posts[0]->ID,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => 'ASC',
                        'orderby' => 'menu_order ID'));
                    $attachment_id='';
		    foreach ($image as $imageid=>$imageobject){$attachment_id=$imageobject->ID;}
			$items[$catcount]=array(
			"id" => $cats->slug,
			"h1" => $cats->name,
			"dm" => "tbsplanet.com",
			"cap"=>$cats->description,
			"ag"=>"TBS Planet Comics",
                        "imageid"=>wp_get_attachment_url($attachment_id),
                        "wu"=>get_category_link($cats)
			);
			$catcount++;
	  }
	  $jsonarray=array('pg'=>$pg,'items'=>$items);
	  echo json_encode($jsonarray);
      exit();
	}
	
	foreach($nikhilcats as $cat){
		//for Newspoint
   if(preg_match("/newspoint/",$_SERVER["REQUEST_URI"])&&preg_match("/$cat->slug/",$_SERVER["REQUEST_URI"])&&preg_match("/json/",$_SERVER["REQUEST_URI"])) {
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
			"id" => wp_get_attachment_url($attachment_id),
			"h1" => $post->post_title,
			"dm" => "tbsplanet.com",
			"cap"=>wp_get_attachment_caption($attachment_id),
			"wu"=>get_permalink($post),
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
xmlwriter_start_element($xw, 'items');

foreach($posts as $post){
	
	$posttstamp=strtotime(date('Y-m-d H:i:s').'-1 hour');
	if ($posttstamp<strtotime($post->post_date)){
	
$image=get_children(array('post_parent' => $post->ID,
                        'post_status' => 'inherit',
                        'post_type' => 'attachment',
                        'post_mime_type' => 'image',
                        'order' => 'ASC',
                        'orderby' => 'menu_order ID'));
$attachment_id='';
foreach ($image as $imageid=>$imageobject){$attachment_id=$imageobject->ID;}

xmlwriter_start_element($xw, 'item');

xmlwriter_start_Attribute($xw, 'title');
xmlwriter_text($xw, $post->post_title);
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'caption');
xmlwriter_text($xw,wp_get_attachment_caption($attachment_id));
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'imageID');
xmlwriter_text($xw,$post->ID);
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'contentType');
xmlwriter_text($xw,'Image');
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'agency');
xmlwriter_text($xw,'TBS Planet Comics');
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'imageURL');
xmlwriter_text($xw,wp_get_attachment_url($attachment_id));
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'wu');
xmlwriter_text($xw,get_permalink($post));
xmlwriter_end_Attribute($xw);

xmlwriter_start_Attribute($xw,'timestamp');
xmlwriter_text($xw,$post->post_date);
xmlwriter_end_Attribute($xw);

xmlwriter_end_element($xw);
	}
	  }
	  
	    xmlwriter_end_element($xw);
		xmlwriter_end_document($xw);
		echo xmlwriter_output_memory($xw);
      exit();
	}
	if($_SERVER["REQUEST_URI"] == '/feeds/xml/times/log/'.$cat->slug) {
		header("Content-type: application/json; charset=utf-8");
	  $posts=get_posts(array('numberposts'=>-1,'category'=>$cat->term_id,'orderby' => 'date','order' => 'DESC'));
$ids;
$pos=0;
foreach($posts as $post){
$ids[$pos]=$post->ID;
$pos++;
	  }
	  echo json_encode($ids);
      exit();
	}
	}
}
