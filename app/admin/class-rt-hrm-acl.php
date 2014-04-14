<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class-rt-hrm-acl
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_HRM_ACL' ) ) {
	class Rt_HRM_ACL {
		public function __construct() {
			add_filter( 'rt_biz_modules', array( $this, 'register_rt_hrm_module' ) );
		}

		function register_rt_hrm_module( $modules ) {
			global $rt_hrm_module;
			$module_key = ( function_exists( 'rt_biz_sanitize_module_key' ) ) ? rt_biz_sanitize_module_key( RT_HRM_TEXT_DOMAIN ) : '';
            $modules[ $module_key ] = array(
				'label' => __( 'rtHRM' ),
				'post_types' => array( $rt_hrm_module->post_type ),
			);
			return $modules;
		}
	}
}