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
 * Description of class-rt-biz-help
 *
 * @author udit
 */
if ( ! class_exists( 'Rt_Hrm_Help' ) ) {

	class Rt_Hrm_Help {

		var $tabs = array();
		var $help_sidebar_content;

		public function __construct() {

			add_action( 'init', array( $this, 'init_help' ) );
		}

		function init_help() {
			global $rt_hrm_module;
			$this->tabs = apply_filters( 'rt_hrm_help_tabs', array(
				'post-new.php' => array(
					array(
						'id' => 'add_leave_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
					),
				),
				'post.php' => array(
					array(
						'id' => 'edit_leave_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
					),
				),
				'edit.php' => array(
					array(
						'id' => 'leave_list_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
					),
					array(
						'id' => 'leave_list_screen_content',
						'title' => __( 'Screen Content' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
					),
					array(
						'id' => 'dashboard_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
						'page' => 'rthrm-' . $rt_hrm_module->post_type . '-dashboard',
					),
					array(
						'id' => 'calander_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
						'page' => 'rthrm-' . $rt_hrm_module->post_type . '-calendar',
					),
					array(
						'id' => 'settings_general',
						'title' => __( 'General' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
						'page' => RT_WP_HRM::$settings_page_slug,
					),
					array(
						'id' => 'settings_leaves',
						'title' => __( 'Leaves' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
						'page' => RT_WP_HRM::$settings_page_slug,
					),
					array(
						'id' => 'settings_documents',
						'title' => __( 'Documents' ),
						'content' => '',
						'post_type' => $rt_hrm_module->post_type,
						'page' => RT_WP_HRM::$settings_page_slug,
					),
				),
				'admin.php' => array(
					array(
						'id' => 'dashboard_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'page' => 'rthrm-' . $rt_hrm_module->post_type . '-dashboard',
					),
					array(
						'id' => 'calander_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'page' => 'rthrm-' . $rt_hrm_module->post_type . '-calendar',
					),
				),
				'edit-tags.php' => array(
					array(
						'id' => 'leave_type_overview',
						'title' => __( 'Overview' ),
						'content' => '',
						'taxonomy' => 'rt-leave-type',
					),
				),
					) );

			$documentation_link = apply_filters( 'rt_hrm_help_documentation_link', '#' );
			$support_forum_link = apply_filters( 'rt_hrm_help_support_forum_link', '#' );
			$this->help_sidebar_content = apply_filters( 'rt_hrm_help_sidebar_content', '<p><strong>' . __( 'For More Information : ' ) . '</strong></p><p><a href="' . $documentation_link . '">' . __( 'Documentation' ) . '</a></p><p><a href="' . $support_forum_link . '">' . __( 'Support Forum' ) . '</a></p>' );

			add_action( 'current_screen', array( $this, 'check_tabs' ) );
		}

		function check_tabs() {


			if ( isset( $this->tabs[ $GLOBALS[ 'pagenow' ] ] ) ) {
				switch ( $GLOBALS[ 'pagenow' ] ) {
					case 'post-new.php':
						if ( isset( $_GET[ 'post_type' ] ) ) {
							foreach ( $this->tabs[ $GLOBALS[ 'pagenow' ] ] as $args ) {
								if ( $args[ 'post_type' ] == $_GET[ 'post_type' ] ) {
									$this->add_tab( $args );
								}
							}
						}
						break;
					case 'edit.php':
						if ( isset( $_GET[ 'post_type' ] ) ) {
							foreach ( $this->tabs[ $GLOBALS[ 'pagenow' ] ] as $args ) {
								if ( isset( $_GET[ 'page' ] ) && isset( $args[ 'page' ] ) && $args[ 'page' ] == $_GET[ 'page' ] ) {
									$this->add_tab( $args );
								} else if ( empty( $args[ 'page' ] ) && empty( $_GET[ 'page' ] ) && $args[ 'post_type' ] == $_GET[ 'post_type' ] ) {
									$this->add_tab( $args );
								}
							}
						}
						break;
					case 'post.php':
						if ( isset( $_GET[ 'post' ] ) ) {
							$post_type = get_post_type( $_GET[ 'post' ] );
							foreach ( $this->tabs[ $GLOBALS[ 'pagenow' ] ] as $args ) {
								if ( $args[ 'post_type' ] == $post_type ) {
									$this->add_tab( $args );
								}
							}
						}
						break;
					case 'admin.php':
						if ( isset( $_GET[ 'page' ] ) ) {
							foreach ( $this->tabs[ $GLOBALS[ 'pagenow' ] ] as $args ) {
								if ( $args[ 'page' ] == $_GET[ 'page' ] ) {
									$this->add_tab( $args );
								}
							}
						}
						break;
					case 'edit-tags.php':
						if ( isset( $_GET[ 'taxonomy' ] ) ) {
							foreach ( $this->tabs[ $GLOBALS[ 'pagenow' ] ] as $args ) {
								if ( $args[ 'taxonomy' ] == $_GET[ 'taxonomy' ] ) {
									$this->add_tab( $args );
								}
							}
						}
						break;
				}
			}
		}

		function add_tab( $args ) {

			get_current_screen()->add_help_tab( array(
				'id' => $args[ 'id' ],
				'title' => $args[ 'title' ],
				'callback' => array( $this, 'tab_content' ),
			) );
			get_current_screen()->set_help_sidebar( $this->help_sidebar_content );
		}

		function tab_content( $screen, $tab ) {

			// Some Extra content with logic
			switch ( $tab[ 'id' ] ) {
				case 'dashboard_overview':
					echo 'HRM Dashboard';
					break;
				case 'calander_overview':
					?>
					<ul>
						<li><?php _e( 'Welcome to the HRM calendar. This screen can be used to mark leaves at any particular date in the future or today. You have 3 calendar views namely Month, Week, and Day views. Basically it gives an overview of all the leaves that have been applied for and the status of those leaves based on the leave label color.' ); ?></li>
						<li><?php _e( 'For applying for a leave you just need to click on the particular date and you will be greeted with a popup where you can fill the necessary leave details such as Employee Name, Leave Type, Duration and Description.' ); ?></li>
						<li><?php _e( 'An admin will be sent a notification for a leave applied for and then they can deal with the applied leave by click on it. Upon clicking the respected leave, you are redirected to the single leaves page.' ); ?></li>
					</ul>
					<?php
					break;
				case 'leave_list_overview':
					?>
					<p><?php _e( 'This screen provides access to all Leave detail. You can customize the display of this screen to suit your workflow.' ); ?></p>
					<?php
					break;
				case 'leave_list_screen_content':
					?>
					<p><?php _e( 'You can customize the display of this screen’s contents in a number of ways :' ); ?></p>
					<ul>
						<li><?php _e( 'You can hide/display columns based on your needs and decide how many leaves to list per screen using the Screen Options tab.' ); ?></li>
						<li>
							<?php _e( 'You can filter the list of leave by status using the text links in the upper left to show All, Approved, Rejected, or Pending Review leave.' ); ?>
							<?php _e( 'The default view is to show all leave.' ); ?>
						</li>
						<li>
							<?php _e( 'You can view leave in a simple title list or with an excerpt.' ); ?>
							<?php _e( 'Choose the view you prefer by clicking on the icons at the top of the list on the right.' ); ?>
						</li>
						<li>
							<?php _e( 'You can refine the list to show only leave in a specific category or from a specific month by using the dropdown menus above the leave list.' ); ?>
							<?php _e( 'Click the Filter button after making your selection.' ); ?>
							<?php _e( 'You also can refine the list by clicking on the author in the leave list.' ); ?>
						</li>
					</ul>
					<?php
					break;
				case 'add_leave_overview':
				case 'edit_leave_overview':
					?>
					<ul>
						<li><?php _e( "This is the single leaves screen where the leaves can be added/edited and be approved or rejected along with a note for rejection or approval. This screen shows all the details such as  Employee Name, Leave Type, Duration and Description. Apart from that a leave's status can be left as it is or be approved or rejected through the Publish section." ); ?></li>
						<li><?php _e( 'Also a checkbox to deduct the leaves from the available paid leaves can be checked incase the user has any paid leaves left.' ); ?></li>
					</ul>
					<?php
					break;
				case 'leave_type_overview':
					?>
					<p><?php _e( 'This screen allows you to define the leave types available to a user applying for a leave.' ); ?></p>
					<?php
					break;
				case 'settings_general':
					?>
					<p><?php _e( 'Here you can set the label for this module and the menu icon ( preferably 16x16 ) that is supposed to be shown' ); ?></p>
					<?php
					break;
				case 'settings_leaves':
					?>
					<p><?php _e( 'Here you can set the color of the leaves status that you would see on the calendar view. You can set a color for Pending Review, Approved Leaves and Rejected Leaves. Apart from that you can also set the color for the Leaves Text and set a general Paid leaves quota per user here.' ) ?></p>
					<?php
					break;
				case 'settings_documents':
					?>
					<p><?php _e( 'You can customize the display of this screen’s contents in a number of ways :' ); ?></p>
					<?php
					break;
				default:
					do_action( 'rt_hrm_help_tab_content', $screen, $tab );
					break;
			}
		}

	}

}
