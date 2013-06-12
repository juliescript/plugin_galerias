<?php

	/*
		Plugin Name: Galerías Personalizadas
		Plugin URI: http://hacemoscodigo.com
		Description: Plugin que permite crear sliders usando la galería integrada en Wordpress
		Author: Jullieta Campos (Julie) para Hacemos Código
		Author URI: http://julietacampos.com

	*/



// Para las galerías

add_image_size('galeria', 600, 400, true);
add_image_size('carrusel', 200, 200, true);

function my_scripts_method() {
	wp_enqueue_script(
		'custom-script',
		plugins_url('/js/jquery.cycle2.min.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script(
		'custom-script-2',
		plugins_url('/js/jquery.cycle2.carousel.js', __FILE__),
		array('jquery')
	);
	wp_enqueue_script(
		'custom-script-3',
		plugins_url('/js/functions.js', __FILE__),
		array('jquery')
	);
}
add_action('wp_enqueue_scripts', 'my_scripts_method');

add_action('wp_enqueue_scripts', 'prefix_add_my_stylesheet');
function prefix_add_my_stylesheet(){
	wp_register_style('prefix-style', plugins_url('/css/style.css', __FILE__));
	wp_enqueue_style('prefix-style');
}

// Script para agregar custom fields a media
// /**
//  * Add Photographer Name and URL fields to media uploader
//  *
//  * @param $form_fields array, fields to include in attachment form
//  * @param $post object, attachment record in database
//  * @return $form_fields, modified form fields
//  */
 
// function be_attachment_field_credit( $form_fields, $post ) {
// 	$form_fields['be-photographer-name'] = array(
// 		'label' => 'Photographer Name',
// 		'input' => 'text',
// 		'value' => get_post_meta( $post->ID, 'be_photographer_name', true ),
// 		'helps' => 'If provided, photo credit will be displayed',
// 	);

// 	$form_fields['be-photographer-url'] = array(
// 		'label' => 'Photographer URL',
// 		'input' => 'text',
// 		'value' => get_post_meta( $post->ID, 'be_photographer_url', true ),
// 		'helps' => 'Add Photographer URL',
// 	);

// 	return $form_fields;
// }

// add_filter( 'attachment_fields_to_edit', 'be_attachment_field_credit', 10, 2 );

// /**
//  * Save values of Photographer Name and URL in media uploader
//  *
//  * @param $post array, the post data for database
//  * @param $attachment array, attachment fields from $_POST form
//  * @return $post array, modified post data
//  */

// function be_attachment_field_credit_save( $post, $attachment ) {
// 	if( isset( $attachment['be-photographer-name'] ) )
// 		update_post_meta( $post['ID'], 'be_photographer_name', $attachment['be-photographer-name'] );

// 	if( isset( $attachment['be-photographer-url'] ) )
// update_post_meta( $post['ID'], 'be_photographer_url', esc_url( $attachment['be-photographer-url'] ) );

// 	return $post;
// }

// add_filter( 'attachment_fields_to_save', 'be_attachment_field_credit_save', 10, 2 );




function fix_my_gallery_wpse43558($output, $attr) {
	    global $post;

	    static $instance = 0;
	    $instance++;

	    

	    /**
	     *  will remove this since we don't want an endless loop going on here
	     */
	    // Allow plugins/themes to override the default gallery template.
	    //$output = apply_filters('post_gallery', '', $attr);

	    // We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	    if ( isset( $attr['orderby'] ) ) {
	        $attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
	        if ( !$attr['orderby'] )
	            unset( $attr['orderby'] );
	    }

	    extract(shortcode_atts(array(
	        'order'      => 'ASC',
	        'orderby'    => 'menu_order ID',
	        'id'         => $post->ID,
	        'itemtag'    => '',
	        'icontag'    => 'li',
	        'captiontag' => '',
	        'columns'    => 5,
	        'size'       => 'carrusel',
	        'include'    => '',
	        'exclude'    => ''
	    ), $attr));

	    $id = intval($id);
	    if ( 'RAND' == $order )
	        $orderby = 'none';

	    if ( !empty($include) ) {
	        $include = preg_replace( '/[^0-9,]+/', '', $include );
	        $_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

	        $attachments = array();
	        foreach ( $_attachments as $key => $val ) {
	            $attachments[$val->ID] = $_attachments[$key];
	        }
	    } elseif ( !empty($exclude) ) {
	        $exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
	        $attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	    } else {
	        $attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	    }

	    if ( empty($attachments) )
	        return '';

	    if ( is_feed() ) {
	        $output = "\n";
	        foreach ( $attachments as $att_id => $attachment )
	            $output .= wp_get_attachment_link($att_id, $size, true) . "\n";
	        return $output;
	    }

	    $itemtag = tag_escape($itemtag);
	    $captiontag = tag_escape($captiontag);
	    $columns = intval($columns);
	    $itemwidth = $columns > 0 ? floor(100/$columns) : 100;
	    $float = is_rtl() ? 'right' : 'left';

	    $selector = "gallery-{$instance}";

	    $next_img = plugins_url('/images/next_galeria.png', __FILE__);
	    $prev_img = plugins_url('/images/prev_galeria.png', __FILE__);
	    $bg = plugins_url('/images/bg.png', __FILE__);

	    $gallery_style = $gallery_div = '';
	    if ( apply_filters( 'use_default_gallery_style', true ) )
	        /**
	         * this is the css you want to remove
	         *  #1 in question
	         */
	        
	    $size_class = sanitize_html_class( $size );
	    $gallery_div = "
	                    <div id='$selector' class=' gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class} cycle-slideshow' 
	                        data-cycle-timeout=5000
	                        data-cycle-slides='li'
	                        data-cycle-fx='carousel'
	                        data-cycle-carousel-visible='3'
	                        data-cycle-carousel-fluid=false
	                        data-allow-wrap=false
	                        >
	                        
	                        <div class='cycle-prev'></div>
	                        <div class='cycle-next'></div>
	                    ";
	    $output = apply_filters( 'gallery_style', $gallery_style . "\n\t\t" . $gallery_div );

	    $i = 0;
	    foreach ( $attachments as $id => $attachment ) {
	        $link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

	        //$output .= "<{$itemtag} class='gallery-item'>";
	        $output .= "
	            <li class='gallery-item'"; 

	        if ( trim($attachment->post_excerpt) ) {
	            $output .= " data-cycle-desc = '" . wptexturize($attachment->post_excerpt) . "'";
	        }


	        $output .= ">
	                $link
	            </li>";
	        /*
	         * This is the caption part so i'll comment that out
	         * #2 in question
	         */
	        
	        // if ( $captiontag && trim($attachment->post_excerpt) ) {
	        //     $output .= "
	        //         <{$captiontag} class='wp-caption-text gallery-caption'>
	        //         " . wptexturize($attachment->post_excerpt) . "
	        //         </{$captiontag}>";
	        // }
	        //$output .= "</{$itemtag}>";
	        // if ( $columns > 0 && ++$i % $columns == 0 )
	        //     $output .= '<br style="clear: both" />';
	    }

	    /**
	     * this is the extra br you want to remove so we change it to jus closing div tag
	     * #3 in question
	     */
	    /*$output .= "
	            <br style='clear: both;' />
	        </div>\n";
	     */

	    $output .= "</div>\n";
	    return $output;
	}
	add_filter("post_gallery", "fix_my_gallery_wpse43558",10,2);