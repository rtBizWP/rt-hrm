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
 * Description of Rt_HRM_Roles
 *
 * @author dipesh
 */
if( !class_exists( 'Rt_HRM_Roles' ) ) {
    /**
     * Class Rt_HRM_Roles
     */
    class Rt_HRM_Roles {

        /**
         * Global cap for HRM module
         * @var array
         */
        public $global_caps = array(
			'manage_wp_hrm' => 'manage_wp_hrm',
			'manage_attributes' => 'manage_attributes',

			'manage_rthrm_terms' => 'manage_rthrm_terms',
			'edit_rthrm_terms' => 'edit_rthrm_terms',
			'delete_rthrm_terms' => 'delete_rthrm_terms',
			'assign_rthrm_terms' => 'assign_rthrm_terms',

			'manage_wp_hrm_settings' => 'manage_wp_hrm_settings',
		);

        /**
         * Register role & add hook for display HRM role on User profile
         */
        public function __construct() {
            $this->remove_hrm_roles();

			/*$this->register_roles();

			add_action( 'edit_user_profile', array( $this, 'add_access_profile_fields' ), 1 );
			add_action( 'show_user_profile', array( $this, 'add_access_profile_fields' ), 1 );
			add_action( 'profile_update', array( $this, 'update_access_profile_fields' ), 10, 2 );
			add_filter( 'editable_roles', array( $this, 'remove_wp_hrm_roles' ) );*/
		}

        /**
         * Call for Remove HRM roles
         * @param $roles
         * @return mixed
         */
        function remove_wp_hrm_roles( $roles ) {
			unset( $roles['rt_wp_hrm_manager'] );
			// Add admin & user roles
			return $roles;
		}

        /**
         * Call for Remove HRM roles
         */
        function remove_hrm_roles( ) {
            $users = get_users( array( 'role' => 'rt_wp_hrm_manager' ) );
            foreach ( $users as $user ) {
                $u_obj = new WP_User( $user );
                $u_obj->remove_role( 'rt_wp_hrm_manager' );
            }
            remove_role( 'rt_wp_hrm_manager' );
        }

        /**
         * Register role for HRM module & handle reset role request
         */
        function register_roles() {

			if ( isset( $_REQUEST['rt_wp_hrm_reset_roles'] ) && ! empty( $_REQUEST['rt_wp_hrm_reset_roles'] ) ) {
				remove_role( 'rt_wp_hrm_manager' );
			}

			global $rt_hrm_module;
			$role = get_role( 'rt_wp_hrm_manager' );
			$post_type = $rt_hrm_module->post_type;
			if( empty( $role ) ) {

				$caps = array(
					$this->global_caps['manage_wp_hrm'] => true,
					$this->global_caps['manage_attributes'] => true,
					$this->global_caps['manage_wp_hrm_settings'] => true,

					"edit_{$post_type}" => true,
					"read_{$post_type}" => true,
					"delete_{$post_type}" => true,
					"edit_{$post_type}s" => true,
					"edit_others_{$post_type}s" => true,
					"publish_{$post_type}s" => true,
					"read_private_{$post_type}s" => true,
					"delete_{$post_type}s" => true,
					"delete_private_{$post_type}s" => true,
					"delete_published_{$post_type}s" => true,
					"delete_others_{$post_type}s" => true,
					"edit_private_{$post_type}s" => true,
					"edit_published_{$post_type}s" => true,

					$this->global_caps['manage_rthrm_terms'] => true,
					$this->global_caps['edit_rthrm_terms'] => true,
					$this->global_caps['delete_rthrm_terms'] => true,
					$this->global_caps['assign_rthrm_terms'] => true,
				);

				if ( function_exists( 'rt_contacts_get_organization_capabilities' ) ) {
					$caps = array_merge( $caps, rt_contacts_get_organization_capabilities() );
				}

				if ( function_exists( 'rt_contacts_get_person_capabilities' ) ) {
					$caps = array_merge( $caps, rt_contacts_get_person_capabilities() );
				}

				if ( function_exists( 'rt_contacts_get_dependent_capabilities' ) ) {
					$caps = array_merge( $caps, rt_contacts_get_dependent_capabilities() );
				}

				add_role( 'rt_wp_hrm_manager', __( 'WordPress HRM Manager' ), $caps );
			}

			if ( isset( $_REQUEST['rt_wp_hrm_reset_roles'] ) && ! empty( $_REQUEST['rt_wp_hrm_reset_roles'] ) ) {
				$users = get_users( array( 'role' => 'rt_wp_hrm_manager' ) );
				foreach ( $users as $user ) {
					$u_obj = new WP_User( $user );
					$u_obj->remove_role( 'rt_wp_hrm_manager' );
					$u_obj->add_role( 'rt_wp_hrm_manager' );
				}
			}

			// Add caps for admin & user too
		}

        /**
         * Render HRM Role select box on user profile page
         * @param $user
         */
        function add_access_profile_fields( $user ) {
			$current_user = new WP_User( get_current_user_id() );
			if ( $current_user->has_cap( 'create_users' ) ) {
				if ( in_array( 'rt_wp_hrm_manager', $user->roles ) ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				} ?>
				<h3><?php _e( 'WordPress HRM' ); ?></h3>
				<a href="?rt_wp_hrm_reset_roles=true"><?php _e('Reset Roles'); ?></a>
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="rt_wp_hrm_role"><?php _e('WordPress HRM Role'); ?></label></th>
							<td>
								<select id="rt_wp_hrm_role" name="rt_wp_hrm_role">
									<option value="no_role"><?php _e( 'No Role' ); ?></option>
									<option value="wp_hrm_manager" <?php echo $selected; ?>><?php _e( 'WordPress HRM Manager' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<?php
			}
		}

        /**
         * Hendle user HRM module role update request
         * @param $user_id
         * @param $old_data
         */
        function update_access_profile_fields( $user_id, $old_data ) {
			if ( current_user_can( 'create_users' ) ) {
				$user = new WP_User( $user_id );
				if ( isset( $_REQUEST['rt_wp_hrm_role'] ) ) {
					switch( $_REQUEST['rt_wp_hrm_role'] ) {
						case 'wp_hrm_manager':
							if ( ! in_array( 'rt_wp_hrm_manager', $user->roles ) ) {
								$user->add_role( 'rt_wp_hrm_manager' );
							}
						break;
						default:
							if ( in_array( 'rt_wp_hrm_manager', $user->roles ) ) {
								$user->remove_role( 'rt_wp_hrm_manager' );
							}
						break;
					}
				}
			}
		}
	}
}
