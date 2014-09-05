<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Description of class-rt-biz-settings
 *
 * @author paresh
 */
if ( ! class_exists( 'Rt_Hrm_Bp_Hrm' ) ) {
    
    class Rt_Hrm_Bp_Hrm {
        
        function __construct() {
           
            // Define constants
            $this->define_constants();
			// print_r(RT_HRM_BP_HRM_PATH);

            // Include required files
            $this->includes();

            // Init API
            $this->api = new Rt_Hrm_Bp_Hrm_Loader();
            
        }
        
        function includes(){
            
             global $rt_hrm_buddypress_hrm;
             
             $rt_hrm_buddypress_hrm = new RT_WP_Autoload( RT_BP_PEOPLE_PATH . '/bp-hrm/' );
			 
			 // print_r($rt_hrm_buddypress_hrm);
            
        }
        
        function define_constants(){
            
                if ( ! defined( 'RT_BP_PEOPLE_PATH' ) ) {
                        define( 'RT_BP_PEOPLE_PATH', plugin_dir_path( __FILE__ ) );
                }
                
               
        }
    
    }
    
}