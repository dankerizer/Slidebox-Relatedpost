<?php
/* 
Plugin Name: Slidebox Related Post 
Plugin URI: http://www.comic2.com/ 
Description: This plugin will automaticly add related post with slidebox at single post.
Author: Hadie Danker 
Version: 0.1.1 
Author URI: http://www.comic2.com/ 
Change Log : CSS for close button update
*/  


define('RELATEDSLIDEBOXURL', WP_PLUGIN_URL."/".dirname( plugin_basename( __FILE__ ) ) );
define('RELATEDSLIDEBOXPATH', WP_PLUGIN_DIR."/".dirname( plugin_basename( __FILE__ ) ) );
function danker_tambah_code() {
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
	wp_enqueue_script( 'jquery' );
    wp_enqueue_script('relatedslidebox', RELATEDSLIDEBOXURL.'/slidebox.js', array('jquery'));
	
	wp_enqueue_script( 'relatedslidebox' );
	//$stylecss	= 
	wp_register_style( 'relatedslideboxstyle',RELATEDSLIDEBOXURL. '/css/relatedslidebox.css');
	 wp_enqueue_style( 'relatedslideboxstyle' );
}
add_action('wp_enqueue_scripts', danker_tambah_code);
function danker_prependekcontent($text, $excerpt_length) {
	if ( !empty( $text ) ) {
		$text = strip_shortcodes( $text );
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
		if ( count($words) > $excerpt_length ) {
			array_pop($words);
			$text = implode(' ', $words);
			$text = $text . ' ';
		} else {
			$text = implode(' ', $words);
		}
	}
	return $text;
}

function danker_related_images($postid=0, $size='thumbnail', $attributes='') {
if ($postid<1) $postid = get_the_ID();
if ($images = get_children(array(
'post_parent' => $postid,
'post_type' => 'attachment',
'numberposts' => 1,
'post_mime_type' => 'image',)))
foreach($images as $image) {
$attachment=wp_get_attachment_image_src($image->ID, 'thumbnail');
return $attachment[0];
}
}


function danker_related_slidebox($jenis,$judul){
global $post;
$postid	= $post->ID;
	if ($jenis == 'category'){
		$jeniscarelated = get_the_category($postid);
		$catid	= $jeniscarelated[0]->term_id;
		
	}else{
		$jeniscarelated	 = get_the_tags($postid);
		$catid	= $jeniscarelated[0]->term_id;
	}
	$relatedarray = array( 'numberposts' => 1, 'orderby' => 'rand','category' => $catid,'post_status' => 'publish' );
	$relatednya = get_posts( $relatedarray );
	$tampilanrelated	= '';
		foreach( $relatednya as $poste ) {
		$shorandlink	= get_permalink($poste);
		$shorandtitle	= get_the_title($poste);
		$image			= danker_related_images($poste->ID);
		$category		= get_the_category(' ');
		$cats			= $category[0]->cat_name;
		$tgl			= get_the_date('F j, Y');
		$tgl			= str_replace(array('<p>','</p>'),'',$tgl);
		$relco			= $poste->post_content;
		$singkatan		= danker_prependekcontent($relco,20);

		apply_filters('get_the_date', tgl, 'F j, Y');
		$isirelatednya	= ' <p id="last"></p><div id="slidebox">
            <a class="close" href="#" title="close"></a>
            <p>'.$judul.'</p>
            <h3><a href="'.$shorandlink.'" rel="nofollow">'.$shorandtitle.'</a></h3>
			<span><img src="'.$image.'" width="70" height="70" alt="'.$shorandtitle.'"/>'.$singkatan.'</span>
            <a class="more" href="'.$shorandlink.'">Read More &raquo;</a>
        </div>';
		$tampilanrelated	.= $isirelatednya;
		}
		$isirelatednya	.='';
if (is_single()){
	return	$isirelatednya;
	}
}



?>
