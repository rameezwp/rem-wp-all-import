<?php
/*
 * Plugin Name: Real Estate Manager Importer for WP All Import
 * Description: Import existing property listings into Real Estate Manager using WP All Import.
 * Plugin URI: https://webcodingplace.com/real-estate-manager-wordpress-plugin/
 * Version: 1.5
 * Author: WebCodingPlace
 * Author URI: https://webcodingplace.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: rem-importer-wp-all-import
 * Domain Path: /languages
*/


include "rapid-addon.php";

$rem_addon = new RapidAddon('Real Estate Manager Settings', 'rem_importer');

$all_fields = rem_importer_single_property_fields();

foreach ($all_fields as $field) {
	if ($field['key'] != '' && $field['key'] != 'file_attachments') {
		if ($field['key'] == 'property_price') {
			$rem_addon->add_field('rem_'.$field['key'], $field['title'], 'text', null, 'Only digits, example: 435000');
		} else {
			$rem_addon->add_field('rem_'.$field['key'], $field['title'], 'text', null, $field['help']);
		}
	}
	if ($field['key'] == 'file_attachments') {
		$rem_addon->import_images( 'rem_set_property_attachments', 'Property Attachments', 'files' );
	}
}

$rem_addon->add_field('rem_property_features_cbs', 'Property Features', 'textarea', null, 'Each on a line');

$rem_addon->import_images( 'rem_set_property_images', 'Property Gallery Images' );


$rem_addon->set_import_function('rem_addon_import_properties');

if (1) {
	$rem_addon->run();
} else {
	$rem_addon->admin_notice(
		'The Real Estate Manager Importer Add-On requires WP All Import <a href="http://wordpress.org/plugins/wp-all-import" target="_blank">Free</a> and the <a href="https://wordpress.org/plugins/real-estate-manager/">Real Estate Manager</a> plugin.'
	);
}

function rem_addon_import_properties($post_id, $data, $import_options) {

	global $rem_addon;
	$all_fields = rem_importer_single_property_fields();
	foreach ($all_fields as $field) {
		if ($rem_addon->can_update_meta('rem_'.$field['key'], $import_options)) {
			update_post_meta($post_id, 'rem_'.$field['key'], $data['rem_'.$field['key']]);
		}
	}

	if ($rem_addon->can_update_meta('rem_property_features_cbs', $import_options)) {
		if ($data['rem_property_features_cbs'] != '') {
			$data_to_save = array();
			$features_arr = explode("\n", $data['rem_property_features_cbs']);
			foreach ($features_arr as $feature) {
				$data_to_save[trim($feature)] = 'on';
			}
			update_post_meta($post_id, 'rem_property_detail_cbs', $data_to_save);
		}
	}

}

function rem_set_property_images( $post_id, $att_id, $filepath, $is_keep_existing_images ) {
     $key = 'rem_property_images';
     $gallery = get_post_meta($post_id, $key, TRUE);
     if (empty($gallery)) {
        $gallery = array();
     }
     if (!in_array($att_id, $gallery)) {
         $gallery[] = $att_id;
         update_post_meta($post_id, $key, $gallery);
     }
}

function rem_set_property_attachments( $post_id, $att_id, $filepath, $is_keep_existing_images ) {
     $key = 'rem_file_attachments';
     $attachments = get_post_meta($post_id, $key, TRUE);
     if (strpos($attachments, $att_id) == false) {
         $attachments .= $att_id;
         $attachments .= "\n";
         update_post_meta($post_id, $key, $attachments);
     }
}

function rem_importer_single_property_fields(){
    $saved_fields = get_option( 'rem_property_fields' );
    $inputFields  = array();
    if ($saved_fields != '' && is_array($saved_fields)) {
    	$inputFields  = $saved_fields;
    } else {
		$inputFields = array(
		    array(
		        'key' => 'property_price',
		        'title' => __( 'Price', 'real-estate-manager' ),
		        'help' => __( 'Regular Price of Property', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'before_price_text',
		        'title' => __( 'Before Price', 'real-estate-manager' ),
		        'help' => __( 'Text to display before price, Eg: Starting From', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'after_price_text',
		        'title' => __( 'After Price', 'real-estate-manager' ),
		        'help' => __( 'Text to display after price, Eg: / Month', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_sale_price',
		        'title' => __( 'Sale Price', 'real-estate-manager' ),
		        'help' => __( 'Sale Price of Property', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_latitude',
		        'title' => __( 'Latitude', 'real-estate-manager' ),
		        'help' => __( 'Latitude of property, will use for map', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_longitude',
		        'title' => __( 'Longitude', 'real-estate-manager' ),
		        'help' => __( 'Longitude of property, will use for map', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_video',
		        'title' => __( 'Video URL', 'real-estate-manager' ),
		        'help' => __( 'Provide video URL', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_type',
		        'title' => __( 'Property Type', 'real-estate-manager' ),
		        'help' => __( 'Choose type of property', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_purpose',
		        'title' => __( 'Purpose', 'real-estate-manager' ),
		        'help' => __( 'Choose purpose of property', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_status',
		        'title' => __( 'Status', 'real-estate-manager' ),
		        'help' => __( 'Choose status of property', 'real-estate-manager' ),
		    ),
		    array(
		        'key' => 'property_bedrooms',
		        'title' => __( 'Bedrooms', 'real-estate-manager' ),
		        'help' => __( 'Number of bedrooms', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_bathrooms',
		        'title' => __( 'Bathrooms', 'real-estate-manager' ),
		        'help' => __( 'Number of bathrooms', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_area',
		        'title' => __( 'Area', 'real-estate-manager' ),
		        'help' => __( 'Property total area size', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_address',
		        'title' => __( 'Address', 'real-estate-manager' ),
		        'help' => __( 'If latitude and longitude fields are blank, this address will be used for rendering map', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_state',
		        'title' => __( 'State', 'real-estate-manager' ),
		        'help' => __( 'State', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_zipcode',
		        'title' => __( 'Zip Code', 'real-estate-manager' ),
		        'help' => __( 'Zipcode', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_city',
		        'title' => __( 'City', 'real-estate-manager' ),
		        'help' => __( 'City', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_country',
		        'title' => __( 'Country', 'real-estate-manager' ),
		        'help' => __( 'Country', 'real-estate-manager' ),
		    ),

		    array(
		        'key' => 'property_rooms',
		        'title' => __( 'Rooms', 'real-estate-manager' ),
		        'help' => __( 'Number of rooms', 'real-estate-manager' ),
		    ),      
		);        
    }

    return $inputFields;
}