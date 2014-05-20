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
 * Description of Rt_HRM_Module
 *
 * @author Dipesh
 */
if( !class_exists( 'Rt_HRM_Module' ) ) {
	/**
	 * Class Rt_HRM_Module
	 */
	class Rt_HRM_Module {

        /**
         * slug for leave CPT
         * @var string
         */
        var $post_type = 'rt_leave';

        /**
         * menu position for HRM
         * @var string
         */
        var $menu_position = 32;

        /**
         * Module Name
         * @var string
         */
        var $name = 'HRM';

        /**
         * Array of labels for leave CPT
         * @var array
         */
        var $labels = array();

        /**
         * Array of statuses for leave CPT
         * @var array
         */
        var $statuses = array();

        /**
         * Array of statuses for leave CPT
         * @var array
         */
        var $custom_menu_order = array();

		static $user_leave_quota_key = 'rt_hrm_leaves_quota';

        /**
         * Object initialization
         */
        public function __construct() {
			$this->get_custom_labels();
			$this->get_custom_statuses();
            $this->get_custom_menu_order();
			add_action( 'init', array( $this, 'init_hrm' ) );
			$this->hooks();
		}

        /**
         * call for register leave CPT & its status
         */
        function init_hrm() {

			$this->register_custom_post( $this->menu_position );
			$this->register_custom_statuses();

			$settings = get_site_option( 'rt_hrm_settings', false );
			if ( isset( $settings['attach_contacts'] ) && $settings['attach_contacts'] == 'yes' ) {
				rt_contacts_register_person_connection( $this->post_type, $this->labels['name'] );
			}
			if ( isset( $settings['attach_accounts'] ) && $settings['attach_accounts'] == 'yes' ) {
				rt_contacts_register_organization_connection( $this->post_type, $this->labels['name'] );
			}

		}

        /**
         * Apply hook for leave CPT
         */
        function hooks() {
			add_action( 'admin_menu', array( $this, 'register_custom_pages' ), 1 );
            add_filter( 'custom_menu_order', array($this, 'custom_pages_order') );
			add_action( 'add_meta_boxes', array( $this, 'add_custom_metabox' ) );
			add_action( 'save_post', array( $this, 'save_leave_meta' ), 1, 2);
			add_action( 'wp_before_admin_bar_render', array( $this, 'add_leave_custom_status' ), 11);

            add_action( 'wp_ajax_seach_employees_name', array( $this, 'employees_autocomplete_ajax' ) );

			add_action( 'rt_biz_entity_meta_boxes', array( $this, 'contact_documents_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_contact_documents_meta_box' ) );

			add_action( 'edit_user_profile', array( $this, 'render_contact_documents_profile' ), 1 );
			add_action( 'show_user_profile', array( $this, 'render_contact_documents_profile' ), 1 );
			add_action( 'profile_update', array( $this, 'save_contact_documents_profile' ), 10, 2 );

			add_action( 'edit_user_profile', array( $this, 'render_paid_leave_quota' ), 1 );
			add_action( 'show_user_profile', array( $this, 'render_paid_leave_quota' ), 1 );
			add_action( 'profile_update', array( $this, 'save_paid_leave_quota' ), 10, 2 );

			add_action( 'wp_ajax_rt_hrm_get_attachment_size', array( $this, 'get_attachment_size' ) );
			add_action( 'wp_ajax_rt_hrm_check_user_leave_quota', array( $this, 'check_employee_leave_quota' ) );
		}

		function get_attachment_size() {
			if ( ! isset( $_POST['attachment_id'] ) ) {
				echo json_encode( array( 'status' => 'error', 'message' => __( 'Attachemnt ID not given. Please try again.' ) ) );
				die();
			}

			$attachment_id = intval( $_POST['attachment_id'] );
			$attachment_file = get_attached_file( $attachment_id );
			if ( empty( $attachment_file ) ) {
				echo json_encode( array( 'status' => 'error', 'message' => __( 'Attachment file not found to know the file size. Please try again.' ) ) );
				die();
			}

			$file_size = filesize( $attachment_file ) / 1024 / 1024;
			echo json_encode( array( 'status' => 'success', 'file_size' => $file_size ) );
			die();
		}

		function check_employee_leave_quota() {
			if ( ! isset( $_POST['employee_id'] ) ) {
				echo json_encode( array( 'status' => 'error', 'message' => __( 'Employee ID not given. Please try again.' ) ) );
				die();
			}

			$employee_id = intval( $_POST['employee_id'] );
			$user_id = rt_biz_get_wp_user_for_person( $employee_id );
			$leave_quota = $this->get_user_remaining_leaves( $user_id );
			if ( $leave_quota > 0 ) {
				echo json_encode( array( 'status' => 'success', 'leave_quota' => $leave_quota ) );
			} else {
				echo json_encode( array( 'status' => 'error', 'message' => __( 'No leaves remained.' ) ) );
			}
			die();
		}

		function contact_documents_meta_box( $post_type ) {
			global $rt_person;
			if ( $post_type != $rt_person->post_type ) {
				return;
			}

			global $post;
			if ( ! isset( $post ) ) {
				return;
			}

			$is_our_team_mate = get_post_meta( $post->ID, Rt_Person::$meta_key_prefix.Rt_Person::$our_team_mate_key, true );
			if ( ! $is_our_team_mate ) {
				return;
			}

			add_meta_box( 'rt-hrm-contact-documents', __( 'Documents' ), array( $this, 'render_documents_meta_box' ), $rt_person->post_type );
		}

		function get_user_leave_quota( $user_id ) {
			$leave_quota = get_user_meta( $user_id, self::$user_leave_quota_key, true );
			if ( $leave_quota === '' ) {
				$leave_quota = Rt_HRM_Settings::$settings['leaves_quota_per_user'];
			}
			return intval( $leave_quota );
		}

		function get_user_remaining_leaves( $user_id ) {
			$leaves = get_posts( array(
				'posts_per_page' => -1,
				'post_type' => $this->post_type,
				'post_status' => 'approved',
				'post_author' => $user_id,
				'meta_query' => array(
					array(
						'key' => '_rt_hrm_leave_quota_use',
						'value' => '1',
					),
				),
			) );
			return $this->get_user_leave_quota( $user_id ) - count( $leaves );
		}

		function save_user_leave_quota( $user_id, $leave_quota ) {
			update_user_meta( $user_id, self::$user_leave_quota_key, $leave_quota );
		}

		function render_paid_leave_quota( $user ) {
			$editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
			$leave_quota = $this->get_user_leave_quota( $user->ID );
			?>
			<h3><?php _e( 'Leaves Section' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<th><?php _e( 'Paid Leaves Quota : ' ); ?></th>
						<td>
							<?php if ( current_user_can( $editor_cap ) ) { ?>
							<input name="rt_hrm_leave_quota" type="number" step="1" min="0" value="<?php echo $leave_quota; ?>" />
							<?php } else { ?>
							<?php echo $leave_quota; ?>
							<?php } ?>
						</td>
					</tr>
					<tr>
						<th><?php _e( 'Remaining Leaves : ' ); ?></th>
						<td><?php echo $this->get_user_remaining_leaves( $user->ID ); ?></td>
					</tr>
				</tbody>
			</table>
			<?php
		}

		function save_paid_leave_quota( $user_id, $old_data ) {
			if ( ! isset( $_POST['rt_hrm_leave_quota'] ) ) {
				return;
			}

			$leave_quota = $_POST['rt_hrm_leave_quota'];
			$this->save_user_leave_quota( $user_id, $leave_quota );
		}

		function render_contact_documents_profile( $user ) {
			$contact = rt_biz_get_person_for_wp_user( $user->ID );
			if ( empty( $contact ) ) {
				return;
			}
			$contact = $contact[0];
			$is_user_change_allowed = Rt_HRM_Settings::$settings['is_user_allowed_to_upload_edit_docs'];
			$editor_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' );
			if ( current_user_can( $editor_cap ) ) {
				$is_user_change_allowed = true;
			}
			?>
			<h3><?php _e( 'Documents' ); ?></h3>
			<table class="form-table">
				<tbody>
					<tr>
						<td>
						<?php $this->render_documents_view( $contact, $is_user_change_allowed ); ?>
						</td>
					</tr>
				</tbody>
			</table>
			<?php
		}

		function render_documents_meta_box( $post ) {
			$this->render_documents_view( $post, $is_user_change_allowed = 1 );
		}

		function render_documents_view( $post, $is_user_change_allowed ) {
			if ( $is_user_change_allowed ) {
			?>
			<a href="#" id="rt_hrm_add_doc_btn" class="button"><?php _e( 'Add Document' ); ?></a>
			<?php
			}
			$docs = get_posts( array(
				'posts_per_page' => -1,
				'post_parent' => $post->ID,
				'post_type' => 'attachment',
			));
			$existing_data_size = 0;
			$upload_limit = Rt_HRM_Settings::$settings['storage_quota_per_user'];
			foreach ( $docs as $doc ) {
				$existing_data_size += ( filesize( get_attached_file( $doc->ID ) ) / 1024 / 1024 );
			}
			if ( $existing_data_size > $upload_limit ) { ?>
			<div class="rthrm_upload_limit_exceeds"><?php _e( sprintf( 'Upload Limit for this user exceeds. Documents of size %s are already uploaded.', '<strong>'.$existing_data_size.' MB</strong>' ) ); ?></div>
			<?php }
			?>
			<div id="rt_hrm_doc_container">
			<?php foreach ( $docs as $doc ) { ?>
				<?php $extn_array = explode('.', $doc->guid); $extn = $extn_array[count($extn_array) - 1]; ?>
				<div class="doc-item" data-doc-id="<?php echo $doc->ID; ?>">
					<img class="rthrm_doc_img" height="20px" width="20px" src="<?php echo RT_HRM_URL . "app/assets/file-type/" . $extn . ".png"; ?>" />
					<a target="_blank" href="<?php echo get_edit_post_link( $doc->ID ); ?>" title="<?php echo $doc->post_content; ?>" class="rt_hrm_doc_title"><?php echo $doc->post_title; ?></a>
					<a target="_blank" href="<?php echo wp_get_attachment_url($doc->ID); ?>" class="rthrm_download_doc"><?php _e( 'Download' );?></a>
					<?php if ( $is_user_change_allowed ) { ?>
					<a href="#" class="rthrm_delete_doc">x</a>
					<input type="hidden" name="rt_hrm_doc[]" value="<?php echo $doc->ID; ?>" />
					<?php } ?>
				</div>
			<?php } ?>
			</div>
			<?php
			do_action( 'rt_hrm_render_documents_view', $post, $this );
			wp_nonce_field( 'rt_hrm_documents_metabox', 'rt_hrm_documents_metabox_nonce' );
			$this->print_documents_view_js( $existing_data_size );
			do_action( 'rt_hrm_print_documents_view_js', $post, $this );
		}

		function print_documents_view_js( $existing_data_size ) { ?>
			<style>
				.rthrm_delete_doc {
					margin-left: 10px;
					color: red;
				}
				.rthrm_doc_img {
					/*margin-top: -5px;*/
				}
				.rt_hrm_doc_title, .rthrm_download_doc {
					margin-left: 10px;
				}
				.doc-item {
					border: 1px solid grey;
					margin: 2px 2px;
					display: inline-block;
					padding: 2px 5px;
				}
				.rthrm_upload_limit_exceeds {
					background-color: #FF9999;
					border: 1px solid red;
					padding: 5px;
					margin: 5px 2px;
				}
				#rt_hrm_doc_container {
					margin: 5px 2px;
				}
			</style>
			<script>
				var hrm_doc_upload_frame;
				var hrm_upload_limit = <?php echo Rt_HRM_Settings::$settings['storage_quota_per_user']; ?>;
				var hrm_existing_data_size = <?php echo $existing_data_size; ?>;
				jQuery(document).ready(function($){
					$(document).on('click', '#rt_hrm_add_doc_btn', function(e) {
						e.preventDefault();
						if (hrm_doc_upload_frame) {
							hrm_doc_upload_frame.open();
							return;
						}
						hrm_doc_upload_frame = wp.media.frames.file_frame = wp.media({
							title: jQuery(this).data('uploader_title'),
							searchable: true,
							button: {
								text: 'Attach Selected Files',
							},
							multiple: true // Set to true to allow multiple files to be selected
						});
						hrm_doc_upload_frame.on('select', function() {
							var selection = hrm_doc_upload_frame.state().get('selection');
							var strAttachment = '';
							selection.map(function(attachment) {
								attachment = attachment.toJSON();
								console.log(attachment);
								strAttachment = '<div class="doc-item" data-doc-id="'+attachment.id+'">';
								strAttachment += '<img class="rthrm_doc_img" height="20px" width="20px" src="' +attachment.icon + '" >';
								strAttachment += '<a target="_blank" href="'+attachment.editLink+'" title="'+attachment.description+'" class="rt_hrm_doc_title">'+attachment.title+'</a>';
								strAttachment += '<a target="_blank" href="'+attachment.url+'" class="rthrm_download_doc"><?php _e( 'Download' );?></a>';
								strAttachment += '<a href="#" class="rthrm_delete_doc">x</a>';
								strAttachment += '<input type="hidden" name="rt_hrm_doc[]" value="' + attachment.id +'" /></div>';

								jQuery("#rt_hrm_doc_container").append(strAttachment);

								jQuery.post('<?php echo admin_url('admin-ajax.php'); ?>', {
									action: 'rt_hrm_get_attachment_size',
									attachment_id: attachment.id
								}, function(data) {
									data = jQuery.parseJSON(data);
									jQuery('.rthrm_upload_limit_exceeds').remove();
									if ( data.status != 'success' ) {
										jQuery("#rt_hrm_doc_container div.doc-item[data-doc-id="+attachment.id+"]").remove();
										jQuery('<div class="rthrm_upload_limit_exceeds">'+data.message+'</div>').insertBefore('#rt_hrm_doc_container');
									} else if ( ( hrm_existing_data_size + data.file_size ) > hrm_upload_limit ) {
										jQuery("#rt_hrm_doc_container div.doc-item[data-doc-id="+attachment.id+"]").remove();
										jQuery('<div class="rthrm_upload_limit_exceeds"><?php _e( sprintf( 'Upload Limit for this user exceeds. Documents of size %s are already uploaded. Size of uploaded file is ', '<strong>'.$existing_data_size.' MB</strong>' ) ); ?><strong>'+data.file_size+' MB</strong>.</div>').insertBefore('#rt_hrm_doc_container');
									}
								});

								// Do something with attachment.id and/or attachment.url here
							});
							// Do something with attachment.id and/or attachment.url here
						});
						hrm_doc_upload_frame.open();
					});

					$(document).on('click', '.rthrm_delete_doc', function(e) {
						e.preventDefault();
						jQuery(this).parent().remove();
					});
				});
			</script>
		<?php }

		function save_contact_documents_profile( $user_id, $old_data ) {
			$contact = rt_biz_get_person_for_wp_user( $user_id );
			if ( empty( $contact ) ) {
				return;
			}
			$contact = $contact[0];
			$this->save_contact_documents( $contact->ID );
		}

		function save_contact_documents_meta_box( $post_id ) {
			/*
			 * We need to verify this came from the our screen and with proper authorization,
			 * because save_post can be triggered at other times.
			 */

			// Check if our nonce is set.
			if ( ! isset( $_POST['rt_hrm_documents_metabox_nonce'] ) ) {
				return;
			}

			$nonce = $_POST['rt_hrm_documents_metabox_nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'rt_hrm_documents_metabox' ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			$this->save_contact_documents( $post_id );
		}

		function save_contact_documents( $post_id ) {
			$old_docs = get_posts( array(
				'post_parent' => $post_id,
				'post_type' => 'attachment',
				'fields' => 'ids',
				'posts_per_page' => -1,
			));
			$new_docs = array();
			if ( isset( $_POST['rt_hrm_doc'] ) ) {
				$new_docs = $_POST['rt_hrm_doc'];
				$existing_data_size = 0;
				$upload_limit = Rt_HRM_Settings::$settings['storage_quota_per_user'];
				foreach ( $new_docs as $doc ) {
					$existing_data_size += ( filesize( get_attached_file( $doc ) ) / 1024 / 1024 );
				}
				if ( $existing_data_size > $upload_limit ) {
					return;
				}

				foreach ( $new_docs as $doc ) {
					if ( ! in_array( $doc, $old_docs ) ) {
						$file = get_post( $doc );
						$filepath = get_attached_file( $doc );

						$post_doc_hashes = get_post_meta( $post_id, '_rt_wp_hrm_doc_hash' );
						if ( ! empty( $post_doc_hashes ) && in_array( md5_file( $filepath ), $post_doc_hashes ) ) {
							continue;
						}

						if ( ! empty( $file->post_parent ) ) {
							$args = array(
								'post_mime_type' => $file->post_mime_type,
								'guid' => $file->guid,
								'post_title' => $file->post_title,
								'post_content' => $file->post_content,
								'post_parent' => $post_id,
								'post_author' => get_current_user_id(),
							);
							wp_insert_attachment( $args, $file->guid, $post_id );

							add_post_meta( $post_id, '_rt_wp_hrm_doc_hash', md5_file( $filepath ) );

						} else {
							wp_update_post( array( 'ID' => $doc, 'post_parent' => $post_id ) );
							$file = get_attached_file( $doc );
							add_post_meta( $post_id, '_rt_wp_hrm_doc_hash', md5_file( $filepath ) );
						}
					}
				}

				foreach ( $old_docs as $doc ) {
					if ( ! in_array( $doc, $new_docs ) ) {
						wp_update_post( array( 'ID' => $doc, 'post_parent' => '0' ) );
						$filepath = get_attached_file( $doc );
						delete_post_meta( $post_id, '_rt_wp_hrm_doc_hash', md5_file( $filepath ) );
					}
				}
			} else {
				foreach ( $old_docs as $doc ) {
					wp_update_post( array( 'ID' => $doc, 'post_parent' => '0' ) );
					$filepath = get_attached_file( $doc );
					delete_post_meta( $post_id, '_rt_wp_hrm_doc_hash', md5_file( $filepath ) );
				}
			}
		}

        /**
         * Register custom pages for HRM module [ Dashboard | Calendar ]
         */
        function register_custom_pages() {
			global $rt_hrm_dashboard, $rt_hrm_calendar;

            $author_cap = rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'author' );

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Dashboard' ), __( 'Dashboard' ), $author_cap, 'rthrm-'.$this->post_type.'-dashboard', array( $this, 'dashboard' ) );
			$rt_hrm_dashboard->add_screen_id( $screen_id );
			$rt_hrm_dashboard->setup_dashboard();

			/* Metaboxes for dashboard widgets */
			add_action( 'add_meta_boxes', array( $this, 'add_dashboard_widgets' ) );

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Calendar' ), __( 'Calendar' ), $author_cap, 'rthrm-'.$this->post_type.'-calendar', array( $this, 'calendar_view' ) );
			$rt_hrm_calendar->add_screen_id( $screen_id );
			$rt_hrm_calendar->setup_calendar();
		}

        /**
         * Register leave cpt for HRM module
         * @param $menu_position
         */
        function register_custom_post( $menu_position ) {

			$logo_url = Rt_HRM_Settings::$settings['logo_url'];

			$args = array(
				'labels' => $this->labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true, // Show the UI in admin panel
				'menu_icon' => $logo_url,
				'menu_position' => $menu_position,
				'supports' => array('title',),
				'capability_type' => $this->post_type,
			);
			register_post_type( $this->post_type, $args );
		}

        /**
         * Register Status for leave
         */
        function register_custom_statuses() {
			foreach ($this->statuses as $status) {

				register_post_status($status['slug'], array(
					'label' => $status['slug']
					, 'protected' => true
					, '_builtin' => false
					, 'label_count' => _n_noop("{$status['name']} <span class='count'>(%s)</span>", "{$status['name']} <span class='count'>(%s)</span>"),
				));
			}
		}

        /**
         * Getter method for leave CPT labels
         * @return array
         */
        function get_custom_labels() {
			$this->labels = array(
				'name' => __( 'Leave' ),
				'singular_name' => __( 'Leave' ),
				'menu_name' => __( 'HRM' ),
				'all_items' => __( 'Leaves' ),
				'add_new' => __( 'Add Leave' ),
				'add_new_item' => __( 'Add Leave' ),
				'new_item' => __( 'Add Leave' ),
				'edit_item' => __( 'Edit Leave' ),
				'view_item' => __( 'View Leave' ),
				'search_items' => __( 'Search Leave' ),
			);
			return $this->labels;
		}

        /**
         * Getter method for leave CPT status
         * @return array
         */
        function get_custom_statuses() {
			$this->statuses = array(
				'pending' => array(
					'slug' => 'pending',
					'name' => 'Pending Review',
					'description' => 'Leave application is pending',
					'color' => '#404040',
				),
                'approved' =>array(
					'slug' => 'approved',
					'name' => 'Approved',
					'description' => 'Leave application is approved',
					'color' => '#006600',
				),
                'rejected' =>array(
					'slug' => 'rejected',
					'name' => 'Rejected',
					'description' => 'Leave application is rejected',
					'color' => '#D00000',
				),
			);
			return $this->statuses;
		}

        /**
         * Getter method for HRM Custom menu order
         * @return array
         */
        function get_custom_menu_order(){
            global $rt_hrm_attributes;
            $this->custom_menu_order = array(
                'rthrm-'.$this->post_type.'-dashboard',
                'rthrm-'.$this->post_type.'-calendar',
                'edit.php?post_type='.$this->post_type,
                'post-new.php?post_type='.$this->post_type,
				'edit-tags.php?taxonomy='.Rt_HRM_Attributes::$leave_type_tax.'&amp;post_type='.$this->post_type,
				RT_WP_HRM::$settings_page_slug,
                $rt_hrm_attributes->attributes_page_slug,
            );
        }

        /**
         * Call for calendar view
         */
        function calendar_view(){
			global $rt_hrm_calendar;
			$rt_hrm_calendar->ui( $this->post_type );
		}

        /**
         * Call for HRM dashboard
         */
        function dashboard() {
			global $rt_hrm_dashboard;
			$rt_hrm_dashboard->ui( $this->post_type );
		}

        /**
         *
         */
        function add_dashboard_widgets() {
			global $rt_hrm_dashboard;


		}

        function custom_pages_order( $menu_order ) {
            global $submenu;
            global $menu;
            if ( isset( $submenu['edit.php?post_type='.$this->post_type] ) && !empty( $submenu['edit.php?post_type='.$this->post_type] ) ) {
                $module_menu = $submenu['edit.php?post_type='.$this->post_type];
                $is_employee = false;
                $current_employee = rt_biz_get_person_for_wp_user( get_current_user_id( ) );
                if ( isset( $current_employee ) && !empty( $current_employee ) ){
                    $is_employee = true;
                }
                unset($submenu['edit.php?post_type='.$this->post_type]);
                if ( ! $is_employee && ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ){
                    unset($menu[$this->menu_position]);
                }elseif ( $is_employee || current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ){
                    $new_index=5;
                    foreach( $this->custom_menu_order as $item ){
                        foreach ( $module_menu as $p_key => $menu_item ){
                            if ( in_array( $item, $menu_item ) ) {
								if ( $item == 'edit-tags.php?taxonomy='.Rt_HRM_Attributes::$leave_type_tax.'&amp;post_type='.$this->post_type ) {
									$menu_item[0]= '--- '.$menu_item[0];
								}
                                $submenu['edit.php?post_type='.$this->post_type][$new_index] = $menu_item;
                                unset ( $module_menu[$p_key] );
                                $new_index += 5;
								break;
                            }
                        }
                    }
//                    foreach( $module_menu as $p_key => $menu_item ){
//                        $menu_item[0]= '--- '.$menu_item[0];
//                        $submenu['edit.php?post_type='.$this->post_type][$new_index] = $menu_item;
//                        unset ( $module_menu[$p_key] );
//                        $new_index += 5;
//                    }
                }
            }
            return $menu_order;
        }


        /**
         * Add meta-box for leave CPT
         */
        function add_custom_metabox(){

			add_meta_box(
				'leave_meta_box',
				__( 'Additional Information' ),
				array( $this, 'ui_metabox' ),
				$this->post_type,
				'advanced'
				,'high'
			);
		}

        /**
         * Additional leave details metabox UI
         * @param $post
         */
        function ui_metabox( $post ){
            global $current_user, $rt_hrm_attributes;
			wp_nonce_field( 'rthrm_leave_additional_details_meta', 'rthrm_leave_additional_details_meta_nonce' );

            $leave_user = get_post_meta( $post->ID, 'leave-user', false);
            $leave_user_id = get_post_meta( $post->ID, 'leave-user-id', false);
            $leave_duration = get_post_meta( $post->ID, 'leave-duration', false);
			$leave_start_date = get_post_meta( $post->ID, 'leave-start-date', false);
			$leave_end_date = get_post_meta( $post->ID, 'leave-end-date', false);
			$leave_quota_use = get_post_meta( $post->ID, '_rt_hrm_leave_quota_use', true );

            $current_employee = rt_biz_get_person_for_wp_user( get_current_user_id() );
            if ( isset( $current_employee ) && !empty( $current_employee ) ){
                $current_employee=$current_employee[0];
            }
			?>
			<table class="form-table rthrm-container">
				<tbody>

                    <tr  <?php if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) { ?>  class="hide" <?php } ?>>
                        <td class="tblkey">
                            <label class="label">Employee Name</label>
                        </td>
                        <td class="tblval">
                            <input type="text" id="leave-user" size="30" name="post[leave-user]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" autocomplete="off" class="rt-form-text user-autocomplete" value="<?php if ( isset( $leave_user ) && !empty( $leave_user ) ) { echo $leave_user[0]; } elseif ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) { echo $current_employee->post_title; }  ?>">
                            <input type="hidden" id="leave-user-id" name="post[leave-user-id]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" class="rt-form-text" value="<?php if ( isset( $leave_user_id ) && !empty( $leave_user_id ) ) { echo $leave_user_id[0]; } elseif ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) { echo $current_employee->ID; }  ?>">
                        </td>
                    </tr>
					<tr>
						<td>
							<label for="<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>">
								<?php echo $rt_hrm_attributes->leave_type_tax_label; ?>
							</label>
						</td>
						<td>
							<?php
								$options = array();
								$terms = get_terms( Rt_HRM_Attributes::$leave_type_tax, array( 'hide_empty' => false, 'order' => 'asc' ) );
								$post_term = wp_get_post_terms( ( isset( $post->ID ) ) ? $post->ID : '', Rt_HRM_Attributes::$leave_type_tax, array( 'fields' => 'ids' ) );
								// Default Selected Term for the attribute. can beset via settings -- later on
								$selected_term = '-11111';
								if( !empty( $post_term ) ) {
									$selected_term = $post_term[0];
								}
								foreach ($terms as $term) {
									$options[] = array(
										$term->name => $term->term_id,
										'title' => $term->name,
										'checked' => ($term->term_id == $selected_term) ? true : false,
									);
								}
								global $rt_form;
								$args = array(
									'id' => Rt_HRM_Attributes::$leave_type_tax,
									'name' => 'post['.Rt_HRM_Attributes::$leave_type_tax.'][]',
									'rtForm_options' => $options,
								);
								echo $rt_form->get_radio( $args );
							?>
						</td>
					</tr>
					<tr>
						<td>
							<label for="leave-duration">Duration</label>
						</td>
						<td>
							<select id="leave-duration" name="post[leave-duration]" class="rt-form-select">
								<option value="full-day" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'full-day' ) { echo 'selected'; } ?> >Full Day</option>
								<option value="half-day" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'half-day' ) { echo 'selected'; } ?>>Half Day</option>
								<option value="other" <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] == 'other' ) { echo 'selected'; } ?>>Other</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label for="leave-start-date">Start Date</label>
						</td>
						<td>

							<div >
								<input id="leave-start-date" name="post[leave-start-date]"  class="rt-form-text datepicker" placeholder="Select Start Date" readonly="readonly" value="<?php if ( isset( $leave_start_date ) && !empty( $leave_start_date ) ) { echo $leave_start_date[0]; }  ?>" type="text">
							</div>
						</td>
					</tr>

					<tr <?php if ( isset( $leave_duration ) && !empty( $leave_duration ) &&  $leave_duration[0] != 'other' ) { echo "style='display:none'"; } ?> >
						<td>
							<label for="leave-end-date">End Date</label>
						</td>
						<td>

							<div>
								<input id="leave-end-date" name="post[leave-end-date]" class="rt-form-text datepicker" placeholder="Select End Date" readonly="readonly" value="<?php if ( isset( $leave_end_date ) && !empty( $leave_end_date ) ) { echo $leave_end_date[0]; }  ?>" type="text">
							</div>
						</td>
					</tr>
					<tr>
						<td>
							<label class="label">Description </label>
						</td>
						<td>
							<textarea id="content" class="rt-form-text" name="content" style="resize: none;width: 100%; height: 75px;" aria-hidden="true"><?php echo $post->post_content ?></textarea>
						</td>
					</tr>
				<?php
				$display_checkbox = false;
				if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
					$leave_quota = $this->get_user_remaining_leaves( get_current_user_id() );
					if ( intval( $leave_quota ) > 0 ) {
						$display_checkbox = true;
					}
				} else if ( isset( $current_employee->ID ) && isset( $current_employee->post_title ) ) {
					$display_checkbox = true;
				} ?>
				<tr <?php echo ( ! $display_checkbox ) ? 'class="hide"' : ''; ?>>
					<td></td>
					<td>
						<label><input type="checkbox" id="leave_quota_use" name="leave_quota_use" value="1" <?php checked( '1', $leave_quota_use ); ?> /> <?php _e( 'Use Paid Leaves that are left ?' ); ?></label>
					</td>
				</tr>
				</tbody>
			</table>

		<?php
		}

        /**
         * Save meta-box values for leave CPT
         * @param $post_id
         * @param $post
         * @return mixed
         */
        function save_leave_meta($post_id, $post){
			global $rt_hrm_module,$rt_hrm_attributes;
            if ( !isset( $_POST['rthrm_leave_additional_details_meta_nonce'] ) || !wp_verify_nonce( $_POST['rthrm_leave_additional_details_meta_nonce'], 'rthrm_leave_additional_details_meta' ) ) {
				return $post_id;
			}
			if ( !current_user_can( 'edit_' . $rt_hrm_module->post_type, $post_id ) )
				return $post_id;
			if( $post->post_type == 'revision' ) return;

			$leave_meta = $_POST['post'];

			if ( isset( $leave_meta[  Rt_HRM_Attributes::$leave_type_tax] ) ) {
				wp_set_post_terms( $post_id, implode( ',', array_map( 'intval', $leave_meta[Rt_HRM_Attributes::$leave_type_tax] ) ), Rt_HRM_Attributes::$leave_type_tax );
			}

			if ( isset( $_POST['leave_quota_use'] ) ) {
				update_post_meta( $newLeaveID, '_rt_hrm_leave_quota_use', $_POST['leave_quota_use'] );
			}

			update_post_meta( $post_id, 'leave-user', $leave_meta['leave-user'] );
            update_post_meta( $post_id, 'leave-user-id', $leave_meta['leave-user-id'] );
            update_post_meta( $post_id, 'leave-duration', $leave_meta['leave-duration'] );
			update_post_meta( $post_id, 'leave-start-date', $leave_meta['leave-start-date'] );

			if ( $leave_meta['leave-duration'] == 'other' ){
				update_post_meta( $post_id, 'leave-end-date', $leave_meta['leave-end-date'] );
			}else {
				delete_post_meta( $post_id, 'leave-end-date' );
			}

		}

        /**
         * Manage Custom statuses for leave CPT
         */
        function add_leave_custom_status(){
			global $post, $pagenow;
			$complete = '';
			if( isset( $post) && !empty( $post ) && $post->post_type == $this->post_type){
				$option='';
                $custom_statuses = $this->get_custom_statuses();
                if ( ! current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
                    unset($custom_statuses['approved']);
                    unset($custom_statuses['rejected']);
                }
                foreach ( $custom_statuses as $status ){
					if($post->post_status == $status['slug']){
						$complete = " selected='selected'";
					}else{
						$complete = '';
					}
					$option .= "<option value='" . $status['slug'] . "' " . $complete . ">"  .  $status['name'] .  "</option>";
				}
                if ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ){
                    echo '<script>
                        jQuery(document).ready(function($) {
                            $("select#post_status").html("'. $option .'");
                            $(".inline-edit-status select").html("'. $option .'");
                            $("#post-status-display").html("'. $post->post_status .'");
                            $("#publish").hide();
                            $("#publishing-action").html("<span class=\"spinner\"><\/span><input name=\"original_publish\" type=\"hidden\" id=\"original_publish\" value=\"Update\"><input type=\"submit\" id=\"save-publish\" class=\"button button-primary button-large\" value=\"Update\" ><\/input>");
                            $("#save-publish").click(function(){
                                $("#publish").click();
                            });
                            if($("#leave-user").val().length > 0){
                                $("#title-prompt-text").addClass("screen-reader-text");
                                $("#title").val("Leave: " + $("#leave-user").val());
                            }
                            $("#title").attr("readonly","readonly");
                            $("#leave-user").blur(function(){
                                if($("#leave-user").val().length > 0){
                                    $("#title-prompt-text").addClass("screen-reader-text");
                                    $("#title").val("Leave: " + $("#leave-user").val());
                                }else{
                                    $("#title-prompt-text").removeClass("screen-reader-text");
                                    $("#title").val("");
                                }
                            });
                        });
                        </script>';
                }elseif (  $pagenow == 'edit.php' ){
                    echo '<script>
                        jQuery(document).ready(function($) {
                            $("select[name=_status]").html("'. $option .'");
                        });
                         </script>';
                }
			}
		}

        function employees_autocomplete_ajax(){
            if ( ! isset($_POST['query'] ) ) {
                wp_die( 'Invalid request Data' );
            }
            $query = $_POST['query'];

            $results = rt_biz_search_employees($query);
            $arrReturn = array();
            foreach ($results as $author) {
                $arrReturn[] = array("id" => $author->ID, "label" => $author->post_title);
            }
            header('Content-Type: application/json');
            echo json_encode($arrReturn);
            die(0);
        }

	}
}
