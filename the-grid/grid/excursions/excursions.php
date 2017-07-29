<?php
$tg_el = The_Grid_Elements();
// Available options to retrieve and customize markup
$options = array(
	'poster' => true,  // Media poster for audio/video (if false no play buttons will be created)
	'icons' => array(  // set all icons
		'link'       => '<i class="tg-icon-link"></i>', // Button link icon
		'comment'    => '',                             // Button link icon
		'image'      => '<i class="tg-icon-add"></i>',  // Ligthbox icon
		'audio'      => '<i class="tg-icon-play"></i>', // Audio icon
		'video'      => '<i class="tg-icon-play"></i>', // HTML Video icon
		'vimeo'      => '<i class="tg-icon-play"></i>', // Vimeo icon
		'youtube'    => '<i class="tg-icon-play"></i>', // Youtube icon
		'soundcloud' => '<i class="tg-icon-play"></i>', // SoundCloud icon
	),
	'excerpt_length'  => 0,       // Excerpt character length
	'excerpt_tag'     => '',      // Excerpt more tag
	'read_more'       => '',      // Read more text
	'date_format'     => '' ,     // Date format
	'get_terms'       => true,    // Get all post terms (if false $content['terms'] will be empty)
	'term_color'      => 'color', // Get terms color (option: 'color', 'background', 'none'); default 'none'
	'term_link'       => true,    // Add link to term
	'term_separator'  => ', ',    // terms separator
	'author_prefix'   => '',      // Author prefix like 'By',...
	'avatar'          => false    // Add author avatar
);

// If function do not exists, then return immediately
if (!function_exists('The_Grid_Item_Content')) {
	return;
}

global $post;

// Main function (it's tiny wrapper function class) to retrieve all necessary item content/markup
$content = The_Grid_Item_Content($options);
$gridItem = $tg_el->grid_item;
$prix = get_post_meta($gridItem['ID'],'wpcf-prix', true);
$distance = get_post_meta($gridItem['ID'],'wpcf-distance', true);
$allmeta = get_post_meta($gridItem['ID']);
$excerpt = get_the_excerpt($gridItem['ID']);		
$html  = null;
$html .= $content['media_wrapper_start']; // open the media markup
$html .= '<a class="tg-item-img-link" href="'.get_permalink($gridItem['ID']).'"></a>';
	$html .= $content['media_markup']; // we output the media, it can be an image, gallery, video or audio depending of the post format

	$html .= $content['overlay']; // we add the overlay over the media
	//$html .= '<pre>Meta: '.print_r($allmeta, true).'</pre>';
	$html .= '<div class="prix ajenson">'.$prix.'</div>';
	$html .= '<div class="distance ajenson">'.$distance.'</div>';
	$html .= '<div class="tg-item-content">'; // we place the content holder over the media and overlay
		$html .= '<a class="tg-item-link" href="'.get_the_permalink().'">';
		//$html .= $content['terms']; // we retrieve the terms for the current post
		$html .= $content['title']; // we retrieve the title for the current post
		$html .= '<div class="tg-item-footer">'; // a custom markup to make a footer
			$html .= $excerpt; // we retrieve the post excerpt
			//$html .= $content['media_button']; // and we add the play button. We only appear if the format is a video or an audio
		$html .= '</div>';
	$html .= '</div>';
$html .= $content['media_wrapper_end']; // close the media wrapper


$html .= '<script>jQuery("document").ready(function(){
	jQuery("#choice_1_21_0").click();
	});</script>';


// always return, not echo because we output thanks to a shortcode	
return $html;
?>