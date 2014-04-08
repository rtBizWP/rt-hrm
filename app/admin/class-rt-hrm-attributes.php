<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Rt_HRM_Attributes
 *
 * @author udit
 */
if( !class_exists( 'Rt_HRM_Attributes' ) ) {
	class Rt_HRM_Attributes {

		var $attributes_page_slug = 'rthrm-attributes';

		public function __construct() {
			add_action( 'init', array( $this, 'init_attributes' ) );
		}

		function init_attributes() {
			global $rt_attributes, $rt_hrm_plugin_info, $rt_hrm_roles, $rt_hrm_module, $rt_attributes_model, $rt_attributes_relationship_model;
			$rt_attributes = new RT_Attributes( $rt_hrm_plugin_info->name );
			$terms_caps = array(
				'manage_terms' => $rt_hrm_roles->global_caps['manage_rthrm_terms'],
				'edit_terms' => $rt_hrm_roles->global_caps['edit_rthrm_terms'],
				'delete_terms' => $rt_hrm_roles->global_caps['delete_rthrm_terms'],
				'assign_terms' => $rt_hrm_roles->global_caps['assign_rthrm_terms'],
			);
			$rt_attributes->add_attributes_page( $this->attributes_page_slug, 'edit.php?post_type='.$rt_hrm_module->post_type, $rt_hrm_module->post_type, $rt_hrm_roles->global_caps['manage_attributes'], $terms_caps, $render_type = true, $storage_type = false, $orderby = true );
			$rt_attributes_model = new RT_Attributes_Model();
			$rt_attributes_relationship_model = new RT_Attributes_Relationship_Model();
		}
	}
}
