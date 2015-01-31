<?php
/**
 * Don't load this file directly!
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Implementation of Rt_Hrm_Bp_Hrm_Loader
 *
 * @package BuddyPress_HRM_Component
 * @author kishore
 */
if ( !class_exists( 'Rt_Hrm_Bp_Hrm_Loader' ) ) {
	class Rt_Hrm_Bp_Hrm_Loader extends BP_Component {
            
        /**
         * Start the messages component creation process
         *
         * @since BuddyPress (1.5)
         */

        private $menu_order = 93;

        public function __construct() {
                	
				global $rt_biz_options;
                $rt_hrm_options = maybe_unserialize( get_option( RT_HRM_TEXT_DOMAIN . '_options' ) );
                $menu_label = $rt_hrm_options[ 'menu_label' ];
                $this->hrm_label = $menu_label;
                $this->hrm_component['slug'] =  sanitize_title( $menu_label );

                parent::start(
                        $this->hrm_component['slug'],
                        __( $this->hrm_label, 'buddypress' ),
                       RT_HRM_BP_HRM_PATH,
                        array(
                                'adminbar_myaccount_order' => $this->menu_order
                        )
                );
                $this->includes();
        }
        
	
        /**
         * Include files
         *
         * @global BuddyPress $bp The one true BuddyPress instance
         */
        public function includes( $includes = array() ) {
            
               $includes = array(
					'screens',
					'functions',
                    'actions',
				);

               
                foreach( $includes as $include ) {

                    include( RT_HRM_BP_HRM_PATH."bp-hrm/bp-hrm-{$include}.php" );
                }
        }
        
        /**
         * Setup globals
         *
         * The BP_MESSAGES_SLUG constant is deprecated, and only used here for
         * backwards compatibility.
         *
         * @since BuddyPress (1.5)
         */
        public function setup_globals( $args = array() ) {
                $bp = buddypress();

                // Define a slug, if necessary
                if ( !defined( 'BP_HRM_SLUG' ) )
                        define( 'BP_HRM_SLUG', $this->id );

               

                // All globals for messaging component.
                // Note that global_tables is included in this array.
                $globals = array(
                        'slug'                  => BP_HRM_SLUG,
                        'has_directory'         => false,
                        'notification_callback' => 'messages_format_notifications',
                        'search_string'         => __( 'Search Messages...', 'buddypress' ),
                        
                );

                $this->autocomplete_all = defined( 'BP_MESSAGES_AUTOCOMPLETE_ALL' );

                parent::setup_globals( $globals );
        }
        
		/**
		 * Set up your component's navigation.
		 *
		 * The navigation elements created here are responsible for the main site navigation (eg
		 * Profile > Activity > Mentions), as well as the navigation in the BuddyBar. WP Admin Bar
		 * navigation is broken out into a separate method; see
		 * BP_Example_Component::setup_admin_bar().
		 *
		 * @global obj $bp
		 */
		function setup_nav( $nav = array(), $sub_nav = array() ) {
            	
			// Determine user to use -- only
			if ( bp_loggedin_user_id() !== bp_displayed_user_id() ) {
				return;
			}
			
            $nav_name = __( $this->hrm_label, 'buddypress' );

			// Add 'hrm' to the main navigation
			$main_nav = array(
				'name' 		      => __( $this->hrm_label ),
				'slug' 		      => $this->id,
				'position' 	      =>  $this->menu_order,
				'screen_function'     => 'bp_hrm_screen',
				'default_subnav_slug' => 'calender',
			);

            // Determine user to use
            if ( bp_displayed_user_domain() ) {
                    $user_domain = bp_displayed_user_domain();
            } elseif ( bp_loggedin_user_domain() ) {
                    $user_domain = bp_loggedin_user_domain();
            } else {
                    return;
            }

            // Link to user HRM
            $hrm_link = trailingslashit( $user_domain . $this->slug );
			$author_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'author' );
			$editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
		    if ( current_user_can( $author_cap ) || current_user_can( $editor_cap ) ) {
				// Add the subnav items
				$sub_nav[] = array(
					'name'            =>  __( 'Calender' ),
					'slug'            => 'calender',
					'parent_url'      => $hrm_link,
					'parent_slug'     =>  $this->id,
					'screen_function' => 'bp_hrm_calender',
					'position'        => 10,
				);
                $sub_nav[] = array(
                    'name'            =>  __( 'Leave' ),
                    'slug'            => 'leave',
                    'parent_url'      => $hrm_link,
                    'parent_slug'     =>  $this->id,
                    'screen_function' => 'bp_hrm_leave',
                    'position'        => 20,
                );
			}
                        
			// Add a few subnav items
			if ( current_user_can( $editor_cap ) ) {

				
				$sub_nav[] = array(
					'name'            =>  __( 'Requests' ),
					'slug'            => 'requests',
					'parent_url'      => $hrm_link,
					'parent_slug'     =>  $this->id,
					'screen_function' => 'bp_hrm_requests',
					'position'        => 30,
				);
			}

			parent::setup_nav( $main_nav, $sub_nav );

		}

		public function setup_admin_bar( $wp_admin_nav = array() ) {
                   
				// The instance
				$bp = buddypress();
		
				// Menus for logged in user
				if ( is_user_logged_in() ) {
		
					// Setup the logged in user variables
					$user_domain   = bp_loggedin_user_domain();
					$hrm_link = trailingslashit( $user_domain . $this->slug );
					
					$editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
					$author_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'author' );
					
					if ( current_user_can( $editor_cap ) ) {
						$this->sub_nav_items = array(
			                array(
			                    'name' => __( 'Calender' ),
			                    'slug'  => 'calender',
			                    'screen_function' => 'bp_hrm_calender',
			                ),
			                array(
			                    'name' =>  __( 'Leave' ),
			                    'slug'  => 'leave',
			                    'screen_function' => 'bp_hrm_leave',
			                ),
			                array(
			                    'name' =>  __( 'Requests' ),
			                    'slug'  => 'requests',
			                    'screen_function' => 'bp_hrm_requests',
			                )                
			            );
					} else if ( current_user_can( $author_cap ) ) {
						$this->sub_nav_items = array(
			                array(
			                    'name' => __( 'Calender' ),
			                    'slug'  => 'calender',
			                    'screen_function' => 'bp_hrm_calender',
			                )  ,
                            array(
                                'name' =>  __( 'Leave' ),
                                'slug'  => 'leave',
                                'screen_function' => 'bp_hrm_leave',
                            ),
			            );	
					} else {
						$this->sub_nav_items = array();
					}
		
					// Add main Settings menu
					$wp_admin_nav[] = array(
						'parent' => $bp->my_account_menu_id,
						'id'     => 'my-account-' . $this->id,
						'title'  => __( $this->hrm_label, 'buddypress' ),
						'href'   => trailingslashit( $hrm_link )
					);
		
					
					foreach ($this->sub_nav_items as $item) {
						$author_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'author' );
						$editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
					    if ( ! current_user_can( $author_cap ) && ! current_user_can( $editor_cap ) )
							continue;
						// Add a few subnav items
						$wp_admin_nav[] = array(
							'parent' => 'my-account-' . $this->id,
							'id'     => 'my-account-' . $this->id . '-'.$item['slug'],
							'title'  => __( $item['name'], 'buddypress' ),
							'href'   => trailingslashit( $hrm_link . $item['slug'] )
						);
					}
		
					
				}
		
				parent::setup_admin_bar( $wp_admin_nav );
			
		}

	
	}
}