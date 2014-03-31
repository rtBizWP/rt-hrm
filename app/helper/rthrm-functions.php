<?php

/**
 * rtHRM Studio Functions
 *
 * Helper functions for rtHRM Studio
 *
 * @author Dipesh
 */

function rthrm_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array($args) )
		extract( $args );

	$located = rthrm_locate_template( $template_name, $template_path, $default_path );

	do_action( 'rthrm_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'rthrm_after_template_part', $template_name, $template_path, $located, $args );
}

function rthrm_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	global $rt_wp_hrm;
	if ( ! $template_path ) { $template_path = $rt_wp_hrm->templateURL; }
	if ( ! $default_path ) { $default_path = RT_HRM_PATH_TEMPLATES; }

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name
		)
	);

	// Get default template
	if ( ! $template )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters('rthrm_locate_template', $template, $template_name, $template_path);
}

function rthrm_sanitize_taxonomy_name( $taxonomy ) {
	$taxonomy = strtolower( stripslashes( strip_tags( $taxonomy ) ) );
	$taxonomy = preg_replace( '/&.+?;/', '', $taxonomy ); // Kill entities
	$taxonomy = str_replace( array( '.', '\'', '"' ), '', $taxonomy ); // Kill quotes and full stops.
	$taxonomy = str_replace( array( ' ', '_' ), '-', $taxonomy ); // Replace spaces and underscores.

	return $taxonomy;
}

function rthrm_attribute_taxonomy_name( $name ) {
	return 'rt_' . rthrm_sanitize_taxonomy_name( $name );
}

function rthrm_post_type_name( $name ) {
	return 'rt_' . rthrm_sanitize_taxonomy_name( $name );
}

function rthrm_get_all_attributes( $attribute_store_as = '' ) {
	global $rt_attributes_model;
	$attrs = $rt_attributes_model->get_all_attributes();

	if( empty( $attribute_store_as ) ) {
		return $attrs;
	}

	$newAttr = array();
	foreach ($attrs as $attr) {
		if( $attr->attribute_store_as == $attribute_store_as )
			$newAttr[] = $attr;
	}

	return $newAttr;
}

function rthrm_get_attributes( $post_type, $attribute_store_as = '' ) {
	global $rt_attributes_relationship_model, $rt_attributes_model;
	$relations = $rt_attributes_relationship_model->get_relations_by_post_type( $post_type );
	$attrs = array();

	foreach ($relations as $relation) {
		$attrs[] = $rt_attributes_model->get_attribute( $relation->attr_id );
	}

	if( empty( $attribute_store_as ) ) {
		return $attrs;
	}

	$newAttr = array();
	foreach ($attrs as $attr) {
		if ( $attr->attribute_store_as == $attribute_store_as )
			$newAttr[] = $attr;
	}
	return $newAttr;
}


/*     * ********* Post Term To String **** */
function rthrm_post_term_to_string( $postid, $taxonomy, $termsep = ',' ) {
	$termsArr = get_the_terms( $postid, $taxonomy );
	$tmpStr = '';
	if ( $termsArr ) {
		$sep = '';
		foreach ( $termsArr as $tObj ) {
			$tmpStr .= $sep . $tObj->name;
			$sep = $termsep;
		}
	}
	return $tmpStr;
}
/*     * ********* Post Term To String **** */

function rthrm_extract_key_from_attributes( $attr ) {
	return $attr->attribute_name;
}

function rthrm_get_lead_table_name() {

	global $wpdb, $blog_id;
	return $wpdb->prefix . ( ( is_multisite() ) ? $blog_id.'_' : '' ) . 'rt_wp_hrm_hrm_index';
}

function rthrm_get_user_ids( $user ) {
	return $user->ID;
}

/**
 * Function to encrypt or decrypt the given value
 * @param string
 * @return string
 */
function rthrm_encrypt_decrypt( $string ) {

	$string_length = strlen( $string );
	$encrypted_string = "";

	/**
	 * For each character of the given string generate the code
	 */
	for ( $position = 0; $position < $string_length; $position++ ) {
		$key = ( ( $string_length + $position ) + 1 );
		$key = ( 255 + $key ) % 255;
		$get_char_to_be_encrypted = substr( $string, $position, 1 );
		$ascii_char = ord( $get_char_to_be_encrypted );
		$xored_char = $ascii_char ^ $key;  //xor operation
		$encrypted_char = chr( $xored_char );
		$encrypted_string .= $encrypted_char;
	}

	/**
	 * Return the encrypted/decrypted string
	 */
	return $encrypted_string;
}