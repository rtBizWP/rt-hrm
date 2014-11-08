<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Description of Rt_Hrm_Bp_Hrm
 *
 * @author kishore
 */
if ( ! class_exists( 'Rt_Hrm_Bp_Hrm' ) ) {
    
    class Rt_Hrm_Bp_Hrm {
        
        function __construct() {
           
            // Define constants
            $this->define_constants();

            // Include required files
            $this->includes();

            // Init API
            $this->api = new Rt_Hrm_Bp_Hrm_Loader();
			
			// Init Hooks
		//	$this->hooks();
        }
        
        function includes() {
            
             global $rt_hrm_buddypress_hrm;
             
             $rt_hrm_buddypress_hrm = new RT_WP_Autoload( RT_HRM_BP_HRM_PATH . 'bp-hrm/' );
        }
        
        function define_constants() {
            
                if ( ! defined( 'RT_HRM_BP_HRM_PATH' ) ) {
                        define( 'RT_HRM_BP_HRM_PATH', plugin_dir_path( __FILE__ ) );
                }
				if ( ! defined( 'RT_HRM_BP_HRM_SLUG' ) ){
			        define( 'RT_HRM_BP_HRM_SLUG', 'hrm' );
			}
        }
		
		function get_component_root_url(){
			global $bp;
			foreach ( $bp->bp_nav as $nav ) {
			  if ( $nav['slug'] == RT_HRM_BP_HRM_SLUG ){
				$link = $nav['link'];
			  }
			}
			return $link;
		}
		
		/**
		 * hooks function.
		 * Call all hooks :)
		 *
		 * @access public
		 * @return void
		 */
		public function hooks() {
			if ( current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
				add_action(  'pending_to_approved',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'rejected_to_approved', array( $this, 'on_change_pending_post_status' ), 10, 1 );
				
				add_action(  'pending_to_rejected',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'approved_to_rejected', array( $this, 'on_change_pending_post_status' ), 10, 1 );
				
				add_action(  'approved_to_pending',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'rejected_to_pending',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
			}
		}
		
		function on_change_pending_post_status( $post ) {
			
			$post_id = $_POST['ID'] ;
			$post_type = $_POST['post_type'];
			
			// If this is just a revision, don't.
			if ( wp_is_post_revision( $post_id ) || empty( $post_type ) ){
				return;
			}
			  
			// If this isn't a 'rt_leave' post, don't update it.
			if ( 'rt_leave' != $post_type ){
				return;
			}
			
			$current_user = wp_get_current_user();
			$current_user_id = $current_user->ID;
			update_post_meta( $post_id, 'leave-user-approver', $current_user_id );
		}
		
    
    }
    
}