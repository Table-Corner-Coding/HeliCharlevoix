<?php
include_once('inc/simple_html_dom.php');

add_filter('tg_register_item_skin', function($skins) {
	
	// just push your skin slugs (file name) inside the registered skin array
	array_push($skins, 'excursions', 'forfaits', 'youtube-videos');
	return $skins;
	
});

add_filter('body_class', 'append_language_class');
function append_language_class($classes){
  $classes[] = ICL_LANGUAGE_CODE;  //or however you want to name your class based on the language code
  return $classes;
}



function wpb_adding_scripts() {

if(is_admin())
{
	wp_register_script( 'tccAdminScripts', get_stylesheet_directory_uri().'/js/tcc-admin.js', array('jquery'), false, false );
	wp_enqueue_script( 'tccAdminScripts' );
}

}
//add_action( 'wp_enqueue_scripts', 'wpb_adding_scripts' );
add_action( 'admin_enqueue_scripts', 'wpb_adding_scripts' );


function tripAdvisorReview()
{
//$url = "https://www.tripadvisor.fr/ShowUserReviews-g316031-d2018813-r375649462-Heli_Charlevoix_Helicoptere_touristique-Baie_St_Paul_Quebec.html#REVIEWS";
	
	$url = "https://www.tripadvisor.fr/Attraction_Review-g316031-d2018813-Reviews-Heli_Charlevoix_Helicoptere_touristique-Baie_St_Paul_Quebec.html#REVIEWS";

$pageContent = file_get_html($url);

	
}


// retrieves the attachment ID from the file URL
function get_image_id_by_url($image_url) {
	global $wpdb;
	$attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $image_url )); 
        return $attachment[0]; 
}

// Add Shortcode
function tripAdvisor_shortcode( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'src' => '',
			'width' => '',
			'height' => '',
		),
		$atts,
		'video_embed'
	);


//$url = "https://www.tripadvisor.fr/ShowUserReviews-g316031-d2018813-r375649462-Heli_Charlevoix_Helicoptere_touristique-Baie_St_Paul_Quebec.html#REVIEWS";

	$url = "https://www.tripadvisor.fr/Attraction_Review-g316031-d2018813-Reviews-Heli_Charlevoix_Helicoptere_touristique-Baie_St_Paul_Quebec.html#REVIEWS";
	
$pageContent = file_get_html($url);

$articles = array();

foreach($pageContent->find('div.reviewSelector') as $article) {
    $item['quote']     = $article->find('div.quote', 0)->plaintext;
    $item['entry']    = $article->find('div.entry', 0)->plaintext;
    $articles[] = $item;
}

	// Return custom embed code
	return '<div id="TA_excellent806" class="TA_excellent"><div id="CDSWIDEXC" class="widEXC"> <div class="bravoBox"> <div class="bravoWrapper"> <div class="bravoText"> Bravo ! </div> </div> <img src="https://www.tripadvisor.fr/img/cdsi/partner/transparent_pixel-11863-2.gif" height="1" width="1" style="display: none !important;"> </div> <br> <div id="CDSWIDEXCLINK" class="widEXCLINK"> <a target="_blank" href="https://www.tripadvisor.fr/Attraction_Review-g316031-d2018813-Reviews-Heli_Charlevoix_Helicoptere_touristique-Baie_St_Paul_Quebec.html" onclick="ta.cds.handleTALink(11863,this);return true;" rel="nofollow">'.$articles[0]['entry'].'</a><br> </div> <div class="widEXCTALOGO"> <a target="_blank" href="https://www.tripadvisor.fr/"><img src="https://static.tacdn.com/img2/widget/tripadvisor_logo_115x18.gif" alt="TripAdvisor" class="widEXCIMG" id="CDSWIDEXCLOGO"></a> </div> </div><!--/ cdsEXCBadge--> </div>';

}
add_shortcode( 'tripAdvisor', 'tripAdvisor_shortcode' );


// Add Shortcode
function taxonomie_archive_shortcode( $atts , $content = null ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'name' => 'category',
			'display' => 'post'
		),
		$atts,
		'recent-posts'
	);

	// Query

	
	$terms = get_terms( array( 
    'taxonomy' => $atts['name'],
		'parent'   => 0,
		'hide_empty' => false
	) );
	
	// Posts
	$output = '';
	
	if($atts['display'] == 'post')
	{
		
	foreach( $terms as $term ) {
		$term_meta = get_term_meta($term->term_id);
		
		$contenu = $term_meta['wpcf-contenu-attrait'][0];
		$link = $term_meta['wpcf-lien-externe'];
		$photo = $term_meta['wpcf-photo-attrait'][0];
		
			if(!empty($photo))
			{
				
			$posts_in_term = get_posts(array(
			  'post_type' => array('excursion', 'forfait'),
			  'numberposts' => -1,
			  'tax_query' => array(
				array(
				  'taxonomy' => $atts['name'],
				  'field' => 'id',
				  'terms' => $term->term_id, // Where term_id of Term 1 is "1".
				  'include_children' => false
				)
			  )
			));
			
			$post_list = '<ul class="posts_in_'.$atts['name'].'">';
			$forfaits = '';
			foreach($posts_in_term as $current_post)
			{
				if($current_post->post_type == 'excursion')
				{
				$dist = get_post_meta($current_post->ID,'wpcf-distance', true);
				$post_list .= '<li><a href="'.get_permalink($current_post->ID).'">'.$dist.' - '.$current_post->post_title.'</a></li>';
				}else{
					$forfaits .= '<li><a href="'.get_permalink($current_post->ID).'">Forfait - '.$current_post->post_title.'</a></li>';
				}
			}
			
			$post_list .= $forfaits;
			
			
			$post_list .= '</ul>';
			
			$output .= '<div class="tax_term">';
			$output .= '<a id="anchor_'.$term->slug.'" name="anchor_'.$term->slug.'"></a>';
			$tempoutput = '<div>[one_half]<h2>'.$term->name.'</h2><div>' . $contenu . '</div><p><h3>'.__('En plus des vols sur-mesure, retrouvez cet attrait lors des vols suivants :','heli').'</h3></p>'.$post_list.'[/one_half][one_half_last]<img class="tax_term_photo" src="'.$photo.'" />';
			
				
			if(!empty($link)){	
			$tempoutput .= '<div style="border:2px solid #e6e7e8;padding:5px;color:#000000;margin-top:5px;display:block;margin-bottom: 10px;"><strong>'.__('POUR EN SAVOIR PLUS...','heli').' </strong>';
			
			if(is_array($link))
			{
				foreach($link as $current_link)
				{
					$parts = explode('|',$current_link);
					$tempoutput .= '<a class="attrait_link" target="_blank" href="https://'.str_replace('https://','',strtolower($parts[1])).'">'.$parts[0].'</a>';
				}	
			}
			}
			 
			$tempoutput .= '</div>[/one_half_last]</div>';
			
			$output .= do_shortcode($tempoutput);
			
			$output .= '</div>';
			
		}
	}
	
	}
	elseif($atts['display'] == 'anchor_list')
	{
		$output .= '<ul id="menu-menu-principal" class="menu">';
		foreach( $terms as $term ) {
		
		$photo = '';
		$term_meta = get_term_meta($term->term_id);	
		$photo = $term_meta['wpcf-photo-attrait'][0];
			if(!empty($photo))
			{
				$output .= '<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-'.$term->term_id.'"><a href="#anchor_'.$term->slug.'">'.$term->name.'</a></li>';
			}
		}
		$output .= '</ul>';	
	}
	
	
	// Return code
	return $output;

}
add_shortcode( 'taxonomie_archive', 'taxonomie_archive_shortcode' );


function my_et_builder_post_types( $post_types ) {
    $post_types[] = 'excursion';
     $post_types[] = 'forfait';
    return $post_types;
}
add_filter( 'et_builder_post_types', 'my_et_builder_post_types' );


// Add Shortcode
function info_box_shortcode( $atts ) {

global $post;

	// Attributes
	$atts = shortcode_atts(
		array(
			'src' => '',
			'width' => '',
			'height' => '',
		),
		$atts,
		'video_embed'
	);

	$distance = get_post_meta($post->ID,'wpcf-distance', true);
	$duree = get_post_meta($post->ID,'wpcf-duree', true);
	$dureeAct = get_post_meta($post->ID, 'wpcf-duree-de-l-activite',true);
	
	if($post->post_type == 'forfait' ){
	$duree = $dureeAct;	
	}
	
	$prix = get_post_meta($post->ID,'wpcf-prix');
	$saisons = get_post_meta($post->ID,'wpcf-saisons',true);
	$allmeta = get_post_meta($post->ID);

	$retVal = '<div class="info_box"><table><tbody>';

	$retVal .= '<tr><th>'.__('PRIX:','heli').' </th><td>';
		foreach($prix as $current_prix)
		{
			$parts = explode('$',$current_prix);
			//$retVal .= '<div>'.$parts[0].'$<small class="sub">'.$parts[1].'</small></div>';
			$retVal .= '<div>'.$current_prix.'</div>';
		}
	$retVal .= '</td></tr>';
	$retVal .= '<tr><th>'.__('DURÉE:','heli').' </th><td>'.$duree.'</td></tr>';
	$retVal .= '<tr><th>'.__('SAISON:','heli').' </th><td>';
	
	$retVal .= ' <!-- '.print_r($allmeta, true).' --> ';
	
	foreach($saisons as $saison)
	{
		if(!empty($saison[0])){
			$retVal .= '<img src="'.get_stylesheet_directory_uri().'/images/'.$saison[0].'.png" /> ';
		}
			
	}
	
	$retVal .= '</td></tr></tbody></table>';
	
	// Return custom embed code
	return $retVal;

}
add_shortcode( 'info_box', 'info_box_shortcode' );


function custom_field_shortcode( $atts ) {

global $post;

	// Attributes
	$atts = shortcode_atts(
		array(
			'field_slug' => '',
			'box_title' => '',
			'in_a_box' => 1
		),
		$atts,
		'video_embed'
	);

	if($atts['field_slug'] == 'titre')
	{
		$field_value = $post->post_title;
	}
	elseif($atts['field_slug'] == 'distance' && $post->post_type == 'forfait')
	{
		$field_value = get_post_meta($post->ID,'wpcf-duree');
	}
	else
	{
		$field_value = get_post_meta($post->ID,'wpcf-'.$atts['field_slug']);
	}

	if($atts['in_a_box'] == 1)
	{
	$retVal = '<div class="info_box_light">';

	$retVal .= '<div class="box_title">'.$atts['box_title'].'</div>';
	
	if(is_array($field_value))
	{
		foreach($field_value as $current_value)
		{
			$retVal .= '<p>'.$current_value.'</p>';
		}
	}
	
	$retVal .= '</div>';
	}
	else
	{
		if(is_array($field_value))
		{
			$retVal = $field_value[0];
		}
		else
		{
			$retVal = $field_value;
		}
	}
	// Return custom embed code
	return $retVal;

}
add_shortcode( 'custom_field', 'custom_field_shortcode' );


function attrait_grid_shortcode( $atts ) {

global $post;

	
	$attraits = get_terms( array(
		'taxonomy' => 'attrait',
		'hide_empty' => false,
		'suppress_filters' => false
	) );

	$allAttraits = array();	

	$itt = 0;
	$line_nb = 1;
	$col_nb = 1;
	
	$retVal = '<div class="attrait_grid">';
	foreach($attraits as $attrait)
	{
		$the_attrait = array();
		
		$nom = $attrait->name;
		$image = get_term_meta( $attrait->term_id, $key = 'wpcf-photo-attrait', true);
		$the_image_id = get_image_id_by_url($image);
	if(!empty($image))
	{
		if($line_nb == 1 || $line_nb == 3 || $line_nb == 4)
		{
			if($col_nb == 1)
			{
				$retVal .= '<div style="padding:0;width:100%;margin-bottom: 12px;" class=" et_pb_row et_pb_row_'.$line_nb.'">';
			}
			
			$image_thumb = wp_get_attachment_image_src($the_image_id, 'attraits-grid-3cols');
			
			$retVal .= '<div class="et_pb_column et_pb_column_1_3 et_pb_column_'.$col_nb.' attrait-'.$attrait->term_id.'">';
			
				$retVal .= '<a href="'.get_bloginfo('url').'/attraits-de-charlevoix/#anchor_'.$attrait->slug.'">';
				$retVal .= '<img src="'.$image_thumb[0].'" />';
				$retVal .= '</a>';

			
			$count_string = $attrait->count.' expérience';
				if($attrait->count > 1)
				{
					$count_string .= 's';	
				}
				
				$retVal .= '<h3>'.$nom.'<small>'.$count_string .'</small></h3>';
			$retVal .= '</div>';
			
			if($col_nb == 3)
			{
				$retVal .= '</div> <!-- End of line '.$line_nb.' --> ';
				$col_nb = 0;
				
				if($line_nb == 4)
				{
					$line_nb = 0;
				}
				$line_nb++;	
			}
			
			
			
		}elseif($line_nb == 2){
			if($col_nb == 1)
			{
				$retVal .= '<div style="padding:0;width:100%;margin-bottom: 12px;" class="et_pb_row et_pb_row_'.$line_nb.' et_pb_row_4col">';
			}
			
			
				$image_thumb = wp_get_attachment_image_src($the_image_id, 'attraits-grid-4cols');	
						
				$retVal .= '<div class="et_pb_column et_pb_column_1_4 middle et_pb_column_'.$col_nb.' attrait-'.$attrait->term_id.'">';
				
				$retVal .= '<a href="'.get_bloginfo('url').'/attraits-de-charlevoix/#anchor_'.$attrait->slug.'">';
				$retVal .= '<img src="'.$image_thumb[0].'" />';
				$retVal .= '</a>';
				
				$count_string = $attrait->count.' expérience';
				if($attrait->count > 1)
				{
					$count_string .= 's';	
				}
				
				$retVal .= '<h3>'.$nom.'<small>'.$count_string .'</small></h3>';
				$retVal .= '</div>';
			
			if($col_nb == 4)
			{
				$retVal .= '</div> <!-- End of line '.$line_nb.' --> ';
				$col_nb = 0;
				$line_nb++;	
			}
			
		}
		
		$col_nb++;
		//$line_nb++;
		
		}
		
	}
	
	if(($line_nb == 2) && $col_nb <= 4)
	{
		while($col_nb <= 4)
		{
			$retVal .= '<div class="et_pb_column et_pb_column_1_4 middle et_pb_column_'.$col_nb.' et_pb_column_empty">';
			$retVal .= '</div>';
				
				if($col_nb == 4)
				{
					$retVal .= '</div> <!-- End of line '.$line_nb.' --> ';
					//$col_nb == 0;
					
					if($line_nb == 4)
					{
						$line_nb = 0;
					}
					$line_nb++;	
				}
			$col_nb++;	
		}
	}elseif(($line_nb == 1 || $line_nb == 3 || $line_nb == 4) && $col_nb <= 3){
		$retVal .= ' <!-- EMpty columns --> ';
		
		while($col_nb <= 3)
		{
			$retVal .= '<div class="et_pb_column et_pb_column_1_3 et_pb_column_'.$col_nb.' et_pb_column_empty">';
			$retVal .= '</div>';
				
				if($col_nb == 3)
				{
					$retVal .= '</div> <!-- End of line '.$line_nb.' --> ';
					//$col_nb == 0;
					
					if($line_nb == 3)
					{
						$line_nb = 0;
					}
					$line_nb++;	
				}
			$col_nb++;	
		}
	}
	
	$retVal .= '</div>';
	
	
	// Return custom embed code
	return $retVal;

}
add_shortcode( 'attrait_grid', 'attrait_grid_shortcode' );


function tccMap_shortcode( $atts ) {

global $post;

	$adresses = array();
	$attraits = wp_get_post_terms( $post->ID, 'attrait');


	foreach($attraits as $attrait)
	{
		//$the_term = get_term_by( 'id', $attrait->term_id, $taxonomy, $output, $filter )
		$adresse = array();
	
		$adresse['infos'] = '';
		$adresse['Title'] = get_term_meta( $attrait->term_id, $key = 'wpcf-titre-de-la-pin-google-map', true);
		$adresse['adresse'] = get_term_meta( $attrait->term_id, $key = 'wpcf-adresse-de-la-pin-google-map', true);
		
		$adresses[] = $adresse;
	}
	
	
	// Return custom embed code
	return outputMap($adresses);

}
add_shortcode( 'tccMap', 'tccMap_shortcode' );



/* Map functions */

$script_string = '//maps.googleapis.com/maps/api/js?key=AIzaSyAu-28yLjhhXUcs2WIIuOOdLO8b5Gw5DrM&amp;v=3.exp';

function get_long_lat($address)
{
	$parts = explode(',',$address);
	/*
	$prepAddr = str_replace(' ','+',$address);
	 
	
	$geocode=file_get_contents('http://maps.google.com/maps/api/geocode/json?key=AIzaSyAu-28yLjhhXUcs2WIIuOOdLO8b5Gw5DrM&amp;address='.$prepAddr);
	 
	$output= json_decode($geocode);
	 
	$lat = $output->results[0]->geometry->location->lat;
	$long = $output->results[0]->geometry->location->lng;
	*/
	
	
	$retVal = array('lat'=>$parts[1], 'long'=>$parts[0]);
	 
	return $retVal;	
}

function DMStoDEC($deg,$min,$sec)
{

// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    return $deg+((($min*60)+($sec))/3600);
}    

function DECtoDMS($dec)
{

// Converts decimal longitude / latitude to DMS
// ( Degrees / minutes / seconds ) 

// This is the piece of code which may appear to 
// be inefficient, but to avoid issues with floating
// point math we extract the integer part and the float
// part by using a string function.

    $vars = explode(".",$dec);
    $deg = $vars[0];
    $tempma = "0.".$vars[1];

    $tempma = $tempma * 3600;
    $min = floor($tempma / 60);
    $sec = $tempma - ($min*60);

    return array("deg"=>$deg,"min"=>$min,"sec"=>$sec);
}   


function cleanGPS2($input)
{
	$latdeg = $input['latdeg'];
	$latmin = $input['latmin'];
	$latsec = $input['latsec'];
	
	$lngdeg = $input['lngdeg'];
	$lngmin = $input['lngmin'];
	$lngsec = $input['lngsec'];
	
	$lat = DMStoDEC($latdeg,$latmin,$latsec);
	$lng = DMStoDEC($lngdeg,$lngmin,$lngsec);
	
	//$lat = str_replace('*','.',str_replace(array(' ','.',"'","‘",'N','E'),'',str_replace(array('S','W','s','w'),'-',$parts[0])));
	//$lng = str_replace('*','.',str_replace(array(' ','.',"'","‘",'N','E'),'',str_replace(array('S','W','s','w'),'-',$parts[1])));
	
	//$retVal = '{lat: '.$lat.', lng: '.$lng.'}';
	$retVal = array('lat' => $lat, "lng" => '-'.$lng);	
	
	return $retVal;
}


function outputMap($adresses)
{
	global $post;

	ob_start();
	if($post->post_type != 'forfait')
	{
	
		$lats = get_post_meta($post->ID, 'wpcf-latitude');
		
		
		$latitudes = array();
		foreach($lats as $curentLine)
		{
			$parts = explode('°',$curentLine);
			$deg = $parts[0];
			
			$parts = explode("'",$parts[1]);
			$min = $parts[0];
			
			$sec = $parts[1];
			
			$latitudes[] = array(
			'deg' => $deg,
			'min' => $min,
			'sec' => $sec
			);
		}
		
		
		// Longitudes
		
		$lngs = get_post_meta($post->ID, 'wpcf-longitude');
		
		$longitudes = array();
		
		foreach($lngs as $curentLine)
		{
			$parts = explode('°',$curentLine);
			$deg = $parts[0];
			
			$parts = explode("'",$parts[1]);
			$min = $parts[0];
			
			$sec = $parts[1];
			
			$longitudes[] = array(
			'deg' => $deg,
			'min' => $min,
			'sec' => $sec
			);
		}
				
	?>
		<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAu-28yLjhhXUcs2WIIuOOdLO8b5Gw5DrM&amp;v=3.exp" type="text/javascript"></script>
		<script type="text/javascript">
		
		the_map = '';
		positions = [];
		infos = [];
		titles = [];
		adresses = [];
		
		
		geocoder = new google.maps.Geocoder();
		
		var myLatLng = {lat: 47.523634, lng: -70.515092};
		positions.push(myLatLng);
		infos.push("<h6>Héli-Charlevoix</h6>");
		titles.push("<h6>Héli-Charlevoix</h6>");
		adresses.push("1608, Mgr-de-Laval (Route 138), Baie-Saint-Paul, QC G3Z 2X7"); 
							
		var infowindow;
		function placeMarkers()
		{
			//alert(positions.length);	
			for (var i = 0; i < positions.length; i++) 
			{
				// init markers
				var marker = new google.maps.Marker({
					position: positions[i],
					//map: map,
					map: the_map,
					title: titles[i]
				});
	
				
				(function(marker, i) {
					// add click event
					google.maps.event.addListener(marker, 'click', function() {
						
						//infowindow.close();
						if(infowindow)infowindow.close();
						infowindow = new google.maps.InfoWindow({
							content: infos[i]
						});
						//infowindow.open(map, marker);
						
						infowindow.open(the_map, marker);
						//windows = infowindow;
					});
				})(marker, i);
				}
				
				//google.maps.event.trigger(the_map,'resize');
				//alert('PlaceMarkers() has been triggered.');
		}
		
			function draw_the_county()
			{
				
				<?php
				$valstfrancois = '';
				
				$valstfrancois = rtrim($valstfrancois,']');
				
				$coordArray = explode('],',$valstfrancois);
				$myStack = array();
				
				$coordString = '';
				
				for($itt=0; $itt < count($latitudes); $itt++)
				{
					//$theValue = ltrim($coordinate,'[');
					//$theValue = rtrim($coordinate,']');
					//$myCoord = explode(',',$theValue);
					
					$longitude = DMStoDEC($longitudes[$itt]['deg'],$longitudes[$itt]['min'],$longitudes[$itt]['sec']);
					$latitude = DMStoDEC($latitudes[$itt]['deg'],$latitudes[$itt]['min'],$latitudes[$itt]['sec']);
									
					$coordString .= 'new google.maps.LatLng('.$latitude.', -'.$longitude.'),';
				}
				
				$coordString = rtrim($coordString,',');
				?>
				
			  
			  var polygonCoords = [<?php echo $coordString; ?>];
			  
			  // Construct the polygon.
			  
			 
			  valstfrancois = new google.maps.Polygon({
				paths: polygonCoords,
				strokeColor: '#ed6624',
				strokeOpacity: 0.25,
				strokeWeight: 1,
				fillColor: '#ed6624',
				fillOpacity: 0.32
			  });
			  
			
			  if(valstfrancois.getPath())
			  {
			  
			  var bounds = new google.maps.LatLngBounds();
	
				for (var i=0; i<valstfrancois.getPath().length; i++) {
				
					var point = new google.maps.LatLng(polygonCoords[i].lat(), polygonCoords[i].lng());
					bounds.extend(point);
				}
				
				the_map.fitBounds(bounds);
			  }
			  
			  valstfrancois.setMap(the_map);
			  //placeMarkers();
		
		}
	
		
		function load_query_and_center_map() {
			
			var options = {
				zoom: 8,
				center: new google.maps.LatLng(47.523651,-70.515145), // centré sur Héli-Charlevoix
				mapTypeId: google.maps.MapTypeId.ROADMAP,
				mapTypeControl: true,
			};
		
			// init map
			the_map = new google.maps.Map(document.getElementById('map_canvas'), options);
			
			
			placeMarkers();
			draw_the_county();
		}
		
		// check DOM Ready
	
		jQuery(document).ready(function() {
			
			load_query_and_center_map();
			//placeMarkers();
			// set multiple marker
			
			//draw_the_county();
			//setInterval( placeMarkers(), 500 );
			
		});
		
		jQuery(window).load(function(){
			placeMarkers();	
		});
		
		jQuery( document ).ajaxComplete(function() {
			//alert('AJAX complete!');
		  placeMarkers();
		});
		
		
		</script>
		<div class="carte_des_attraits" style="float:right;width:60%;">
			<div id="map_canvas" style="width: 100%; height:300px;"></div>	
			<input style="display:none;" type="button" value="Voir les emplacements" onClick="placeMarkers();" />
		</div>
		
	<?php
	}
	?>
    <div class="liste_des_attraits" style="width:40%; float:left;">
    	<h3 style="font-family:'Avenir Next LT Pro Demi';"><?php _e('LES ATTRAITS DE CE VOL: ', 'heli'); ?></h3>
    	<ul class="liste-attraits">
    	<?php
		
		
		
		global $post;
		
		$attraits = wp_get_post_terms( $post->ID, 'attrait' );
		
		$ghosts = '<ul class="liste-attraits ghosts">';
		
		foreach($attraits as $attrait)
		{
			$term_meta = get_term_meta($attrait->term_id);
		
			$contenu = $term_meta['wpcf-contenu-attrait'][0];
			$link = $term_meta['wpcf-lien-externe'];
			$photo = $term_meta['wpcf-photo-attrait'][0];
		
			if(!empty($photo))
			{
				echo '<li><a href="'.get_bloginfo('url').'/'.__('attraits-de-charlevoix','heli').'/#anchor_'.$attrait->slug.'">'.$attrait->name.'</a></li>';
			}else{
				$ghosts .= '<li>'.$attrait->name.'</li>';
			}
		}
		
		$ghosts .= '';
		
		?>
        </ul>
        
        <?php 
			echo $ghosts;
		?>
        
    </div>
    <?php
	if(isset($_REQUEST['ppp']) && $post->post_type != 'forfait')
	{
	?>
    <script type="text/javascript">
	placeMarkers();
    </script>
    <?php
	}
	
	$retVal = ob_get_clean();
	
	return $retVal;
}



/* Hooks Gravity forms */

add_filter( 'gform_pre_render', 'populate_posts' );
add_filter( 'gform_pre_validation', 'populate_posts' );
add_filter( 'gform_pre_submission_filter', 'populate_posts' );
add_filter( 'gform_admin_pre_render', 'populate_posts' );
function populate_posts( $form ) {

    foreach ( $form['fields'] as &$field ) {

        if ( $field->type != 'select' || (!strpos( $field->cssClass, 'populate-excursion' ) && !strpos( $field->cssClass, 'populate-forfait' )) ) {
            continue;
        }

        // you can add additional parameters here to alter the posts that are retrieved
        // more info: [http://codex.wordpress.org/Template_Tags/get_posts](http://codex.wordpress.org/Template_Tags/get_posts)
        
		$distanceKey = '';
		
		if(strpos( $field->cssClass, 'populate-excursion' ))
		{
			//$posts = get_posts( 'post_type=excursion&numberposts=-1&post_status=publish&suppress_filters=false' );
			
			$args = array(	'post_type' => 'excursion',
						  	'posts_per_page' => 999,
						  	'post_status' => 'publish',
						  	'suppress_filters' => false);
						  

			$posts = get_posts($args);
			//$distance = get_post_meta($gridItem['ID'],'wpcf-distance', true);
			$distanceKey = 'wpcf-distance';
		}elseif(strpos( $field->cssClass, 'populate-forfait' ))
		{
			//$posts = get_posts( 'post_type=forfait&numberposts=-1&post_status=publish&suppress_filters=false' );
			$args = array(	'post_type' => 'forfait',
						  	'posts_per_page' => 999,
						  	'post_status' => 'publish',
						  	'suppress_filters' => false);
						  

			$posts = get_posts($args);
			//$distance = get_post_meta($gridItem['ID'],'wpcf-distance', true);
		}

        $choices = array();

        foreach ( $posts as $post ) {
			
			$distance = get_post_meta($post->ID,'wpcf-distance', true);
			if(strpos( $field->cssClass, 'populate-excursion' )){
            $choices[] = array( 'text' => $distance.' : '.$post->post_title, 'value' => $post->ID );
			}
			else{
				$choices[] = array( 'text' => $post->post_title, 'value' => $post->ID );
				
			}
        }

        // update 'Select a Post' to whatever you'd like the instructive option to be
        
		 
		if(strpos( $field->cssClass, 'populate-excursion' ))
		{
			$field->placeholder = __('Choisissez une excursion','heli');
		}
		elseif(strpos( $field->cssClass, 'populate-forfait' ))
		{
			$field->placeholder = __('Choisissez un forfait','heli');
		}
        
		
		$field->choices = $choices;

    }

    return $form;
}


add_action('admin_head', 'my_custom_fonts');

function my_custom_fonts() {
  echo '<style>
    div.notice.is-dismissible.custom-sidebars-wp-checkup,
	.widgets-php .sidebars-column-1 .inner .custom-sidebars-upfront .devman{
      display:none;
    } 
  </style>';
}

?>