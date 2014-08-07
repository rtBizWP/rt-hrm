<?php

/**
 * rtHRM Studio Functions
 *
 * Helper functions for rtHRM Studio
 *
 * @author Dipesh
 */

/**
 * Call for template
 * @param $template_name
 * @param $args
 * @param string $template_path
 * @param string $default_path
 * @return mixed
 */
function rthrm_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array($args) )
		extract( $args );

	$located = rthrm_locate_template( $template_name, $template_path, $default_path );

	do_action( 'rthrm_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'rthrm_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Call for local template
 * @param $template_name
 * @param string $template_path
 * @param string $default_path
 * @return mixed
 */
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

/**
 * Call for sanitize taxonomy name
 * @param $taxonomy
 * @return mixed|string
 */
function rthrm_sanitize_taxonomy_name( $taxonomy ) {
	$taxonomy = strtolower( stripslashes( strip_tags( $taxonomy ) ) );
	$taxonomy = preg_replace( '/&.+?;/', '', $taxonomy ); // Kill entities
	$taxonomy = str_replace( array( '.', '\'', '"' ), '', $taxonomy ); // Kill quotes and full stops.
	$taxonomy = str_replace( array( ' ', '_' ), '-', $taxonomy ); // Replace spaces and underscores.

	return $taxonomy;
}

/**
 * Call for hrm taxonomy name
 * @param $name
 * @return string
 */
function rthrm_attribute_taxonomy_name( $name ) {
	return 'rt_' . rthrm_sanitize_taxonomy_name( $name );
}

/**
 * Call for hrm post type  name
 * @param $name
 * @return string
 */
function rthrm_post_type_name( $name ) {
	return 'rt_' . rthrm_sanitize_taxonomy_name( $name );
}


/*     * ********* Post Term To String **** */
/**
 * Call to convert post_terms to string
 * @param $postid
 * @param $taxonomy
 * @param string $termsep
 * @return string
 */
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

/**
 * Call for extract key for attributes
 * @param $attr
 * @return mixed
 */
function rthrm_extract_key_from_attributes( $attr ) {
	return $attr->attribute_name;
}

/**
 * call for get table name
 * @return string
 */
function rthrm_get_lead_table_name() {

	global $wpdb, $blog_id;
	return $wpdb->prefix . ( ( is_multisite() ) ? $blog_id.'_' : '' ) . 'rt_wp_hrm_hrm_index';
}

/**
 * Call for get user id
 * @param $user
 * @return mixed
 */
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

function rthrm_leave_label( ) {
    global $rt_hrm_module;
    return $rt_hrm_module->labels['name'];
}

function rthrm_update_post_term_count( $terms, $taxonomy ) {
	global $wpdb;

	$object_types = (array) $taxonomy->object_type;

	foreach ( $object_types as &$object_type )
		list( $object_type ) = explode( ':', $object_type );

	$object_types = array_unique( $object_types );

	if ( false !== ( $check_attachments = array_search( 'attachment', $object_types ) ) ) {
		unset( $object_types[ $check_attachments ] );
		$check_attachments = true;
	}

	if ( $object_types )
		$object_types = esc_sql( array_filter( $object_types, 'post_type_exists' ) );

	foreach ( (array) $terms as $term ) {
		$count = 0;

		// Attachments can be 'inherit' status, we need to base count off the parent's status if so
		if ( $check_attachments )
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts p1 WHERE p1.ID = $wpdb->term_relationships.object_id  AND post_type = 'attachment' AND term_taxonomy_id = %d", $term ) );

		if ( $object_types )
			$count += (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM $wpdb->term_relationships, $wpdb->posts WHERE $wpdb->posts.ID = $wpdb->term_relationships.object_id  AND post_type IN ('" . implode("', '", $object_types ) . "') AND term_taxonomy_id = %d", $term ) );

		do_action( 'edit_term_taxonomy', $term, $taxonomy );
		$wpdb->update( $wpdb->term_taxonomy, compact( 'count' ), array( 'term_taxonomy_id' => $term ) );
		do_action( 'edited_term_taxonomy', $term, $taxonomy );
	}
}