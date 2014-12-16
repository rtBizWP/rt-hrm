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
			add_action('plugins_loaded', array( $this, 'hooks' ), 45 );
            add_action( 'wp_ajax_render_leave_slide_panel', array( $this, 'render_leave_slide_panel' ), 10 );
        }
        
        function includes() {
            
             global $rt_hrm_buddypress_hrm;
             
             $rt_hrm_buddypress_hrm = new RT_WP_Autoload( RT_HRM_BP_HRM_PATH . 'bp-hrm/' );
        }
        
        function define_constants() {
        	$rt_hrm_options = maybe_unserialize( get_option( RT_HRM_TEXT_DOMAIN . '_options' ) );
            $menu_label = $rt_hrm_options[ 'menu_label' ];
            
                if ( ! defined( 'RT_HRM_BP_HRM_PATH' ) ) {
                        define( 'RT_HRM_BP_HRM_PATH', plugin_dir_path( __FILE__ ) );
                }
				if ( ! defined( 'RT_HRM_BP_HRM_SLUG' ) ){
			        define( 'RT_HRM_BP_HRM_SLUG', sanitize_title( $menu_label ) );
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
			//if ( current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
				add_action(  'pending_to_approved',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'rejected_to_approved', array( $this, 'on_change_pending_post_status' ), 10, 1 );
				
				add_action(  'pending_to_rejected',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'approved_to_rejected', array( $this, 'on_change_pending_post_status' ), 10, 1 );
				
				add_action(  'approved_to_pending',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
				add_action(  'rejected_to_pending',  array( $this, 'on_change_pending_post_status' ), 10, 1 );
			//}
		}
		
		function on_change_pending_post_status( $post ) {
			
			$post_id = $_GET['rt_leave_id'];
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

        function render_leave_slide_panel(){

            $data = array();

            if ( isset( $_GET['template'] ) ) {

                ob_start();
                $template = $_GET['template'];

                switch( $template ){
                    case 'approve_leave':
                    case 'denny_leave':

                        require( RT_HRM_BP_HRM_PATH.'templates/wall-leave-status.php' );
                        break;
                    case 'calender':

                        require( RT_HRM_BP_HRM_PATH.'/templates/hrm-calender.php' );
                        break;

                }

                $output = ob_get_contents();
                ob_end_clean();
                $data['html'] = $output;
            }


            restore_current_blog();
            wp_send_json( $data );

        }



    }
    
}