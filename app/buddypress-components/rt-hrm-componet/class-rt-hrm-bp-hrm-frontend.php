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
 * Description of Rt_Hrm_Bp_Hrm_Frontend
 *
 * @author kishore
 */
if( !class_exists( 'Rt_Hrm_Bp_Hrm_Frontend' ) ) {
    /**
     * Class Rt_HRM_Admin
     */
    class Rt_Hrm_Bp_Hrm_Frontend extends Rt_HRM_Admin {

        /**
         * Object initialization
         */
        public function __construct() {
			if ( ! is_admin() ) {
				$this->hooks();
			}
		}

        /**
         * Load script & style fot rt-hrm plugin
         */
        function load_styles_scripts() {
			global $post, $rt_hrm_module, $rt_hrm_bp_hrm;
			$rt_hrm_options = maybe_unserialize( get_option( RT_HRM_TEXT_DOMAIN . '_options' ) );
            $menu_label = $rt_hrm_options[ 'menu_label' ];
			
			if ( bp_is_current_component( sanitize_title( $menu_label ) ) ){
				
				wp_enqueue_script('jquery-ui-core', true );

				if ( ! wp_style_is( 'rt-jquery-ui-css' ) ) {
	                wp_enqueue_style('rt-jquery-ui-css', RT_HRM_URL . 'app/assets/css/jquery-ui-1.9.2.custom.css', false, RT_HRM_VERSION, 'all');
				}
				//wp_enqueue_style( 'foundation-css', RT_HRM_URL . 'app/assets/css/foundation.min.css' , array('buddyboss-wp-frontend') );
				//wp_enqueue_style('rthrm-frontend-css', RT_HRM_URL . 'app/assets/css/hrm-frontend.css', false, RT_HRM_VERSION, 'all');


                if( bp_is_current_action('calender') ){
                    wp_enqueue_style( 'rt-bp-person-css', RT_HRM_BP_HRM_URL . 'assets/css/calender.css', false );
                }else{
                    wp_enqueue_style( 'rt-bp-person-css', RT_HRM_BP_HRM_URL . 'assets/css/style.css', false );
                }

                if( ! wp_script_is('jquery-ui-datepicker') ) {
                    wp_enqueue_script( 'jquery-ui-datepicker' );
                }

                if( ! wp_script_is('jquery-ui-autocomplete') ) {
                    wp_enqueue_script('jquery-ui-autocomplete', '', array('jquery-ui-widget', 'jquery-ui-position'), '1.9.2',true);
                }


				wp_enqueue_script('rthrm-frontend-js', RT_HRM_BP_HRM_URL . 'assets/js/rt-bp-hrm.min.js','jquery', RT_HRM_VERSION, true);
				wp_enqueue_script('rt-hrm-moment-js', RT_HRM_URL . 'app/assets/javascripts/moment.min.js','jquery', "", true);

				wp_localize_script( 'rthrm-admin-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
				wp_localize_script( 'rthrm-frontend-js', 'ajaxurl', admin_url( 'admin-ajax.php' ) );
				wp_localize_script( 'rthrm-frontend-js', 'rthrmurl', RT_HRM_URL );
                wp_localize_script( 'rthrm-admin-js', 'frontendhrmurl', $rt_hrm_bp_hrm->get_component_root_url() );

              //  wp_enqueue_style('rt-hrm-calendar-css', RT_HRM_BP_HRM_URL . 'assets/css/bp-fullcalender.css', true, RT_HRM_VERSION, 'all');
                wp_enqueue_script('rt-hrm-calendar-js', RT_HRM_URL . 'app/lib/rt-calendar/calendar/fullcalendar.js','jquery', "", true);

			}
		}

        /**
         * Apply hooks
         */
        function hooks() {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_styles_scripts' ) );
		}
	}
}