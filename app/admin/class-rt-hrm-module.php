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
         * Object initialization
         */
        public function __construct() {
			$this->get_custom_labels();
			$this->get_custom_statuses();
			add_action( 'init', array( $this, 'init_hrm' ) );
			$this->hooks();
		}

        /**
         * call for register leave CPT & its status
         */
        function init_hrm() {
			$menu_position = 30;
			$this->register_custom_post( $menu_position );
			$this->register_custom_statuses();

			$settings = get_site_option( 'rt_wp_hrm_settings', false );
			if ( isset( $settings['attach_contacts'] ) && $settings['attach_contacts'] == 'yes' && function_exists( 'rt_contacts_register_person_connection' ) ) {
				rt_contacts_register_person_connection( $this->post_type, $this->labels['name'] );
			}
			if ( isset( $settings['attach_accounts'] ) && $settings['attach_accounts'] == 'yes' && function_exists( 'rt_contacts_register_organization_connection' ) ) {
				rt_contacts_register_organization_connection( $this->post_type, $this->labels['name'] );
			}

		}

        /**
         * Apply hook for leave CPT
         */
        function hooks() {
			add_action( 'admin_menu', array( $this, 'register_custom_pages' ), 1 );
			add_action( 'add_meta_boxes', array( $this, 'add_custom_metabox' ) );
			add_action('save_post', array( $this, 'save_leave_meta' ), 1, 2);
			add_action('wp_before_admin_bar_render', array( $this, 'add_leave_custom_status' ), 11);

            add_action( 'wp_ajax_seach_employees_name', array( $this, 'employees_autocomplete_ajax' ) );
		}

        /**
         * Register custom pages for HRM module [ Dashboard | Calendar ]
         */
        function register_custom_pages() {
			global $rt_hrm_dashboard, $rt_hrm_calendar;

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Dashboard' ), __( 'Dashboard' ), 'read_'.$this->post_type, 'rthrm-'.$this->post_type.'-dashboard', array( $this, 'dashboard' ) );
			$rt_hrm_dashboard->add_screen_id( $screen_id );
			$rt_hrm_dashboard->setup_dashboard();

			/* Metaboxes for dashboard widgets */
			add_action( 'add_meta_boxes', array( $this, 'add_dashboard_widgets' ) );

			$screen_id = add_submenu_page( 'edit.php?post_type='.$this->post_type, __( 'Calendar' ), __( 'Calendar' ), 'read_'.$this->post_type, 'rthrm-'.$this->post_type.'-calendar', array( $this, 'calendar_view' ) );
			$rt_hrm_calendar->add_screen_id( $screen_id );
			$rt_hrm_calendar->setup_calendar();
		}

        /**
         * Register leave cpt for HRM module
         * @param $menu_position
         */
        function register_custom_post( $menu_position ) {
			$hrm_logo_url = get_site_option( 'rthrm_logo_url' );

			if ( empty( $hrm_logo_url ) ) {
				$hrm_logo_url = RT_HRM_URL.'app/assets/img/hrm-16X16.png';
			}

			$args = array(
				'labels' => $this->labels,
				'public' => false,
				'publicly_queryable' => false,
				'show_ui' => true, // Show the UI in admin panel
				'menu_icon' => $hrm_logo_url,
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
				array(
					'slug' => 'pending',
					'name' => 'Pending Review',
					'description' => 'Leave application is pending',
				),
				array(
					'slug' => 'approved',
					'name' => 'Approved',
					'description' => 'Leave application is approved',
				),
				array(
					'slug' => 'rejected',
					'name' => 'Rejected',
					'description' => 'Leave application is rejected',
				),
			);
			return $this->statuses;
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
            global $current_user;
			wp_nonce_field( 'rthrm_leave_additional_details_meta', 'rthrm_leave_additional_details_meta_nonce' );

            $leave_user = get_post_meta( $post->ID, 'leave-user', false);
            $leave_user_id = get_post_meta( $post->ID, 'leave-user-id', false);
            $leave_duration = get_post_meta( $post->ID, 'leave-duration', false);
			$leave_start_date = get_post_meta( $post->ID, 'leave-start-date', false);
			$leave_end_date = get_post_meta( $post->ID, 'leave-end-date', false);
			?>
			<table class="form-table rthrm-container">
				<tbody>
                <?php if ( in_array( 'rt_wp_hrm_manager', $current_user->roles ) ) { ?>
                    <tr>
                        <td class="tblkey">
                            <label class="label">Employee Name</label>
                        </td>
                        <td class="tblval">
                            <input type="text" id="leave-user" size="30" name="post[leave-user]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" autocomplete="off" class="rt-form-text user-autocomplete" value="<?php if ( isset( $leave_user ) && !empty( $leave_user ) ) { echo $leave_user[0]; }  ?>">
                            <input type="hidden" id="leave-user-id" name="post[leave-user-id]" placeholder="<?php echo esc_attr( _x( 'Employee Name', 'User Name') ); ?>" class="rt-form-text" value="<?php if ( isset( $leave_user_id ) && !empty( $leave_user_id ) ) { echo $leave_user_id[0]; }  ?>">
                        </td>
                    </tr>
                <?php } ?>
				<?php
					global $rt_hrm_module,$rt_hrm_attributes;
					$attributes = rthrm_get_attributes( $rt_hrm_module->post_type );
					foreach ( $attributes as $attr ){
						?>
						<tr>
							<td>
								<label for="<?php echo $attr->attribute_name ?>">
									<?php echo $attr->attribute_label; ?>
								</label>
							</td>
							<td>
								<?php $rt_hrm_attributes->render_attribute( $attr, isset($post->ID) ? $post->ID : '', true ); ?>
							</td>
						</tr>
						<?php
					}
				?>
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
			if ( !wp_verify_nonce( $_POST['rthrm_leave_additional_details_meta_nonce'], 'rthrm_leave_additional_details_meta' ) ) {
				return $post_id;
			}
			if ( !current_user_can( 'edit_' . $rt_hrm_module->post_type, $post_id ))
				return $post_id;
			if( $post->post_type == 'revision' ) return;

			$leave_meta = $_POST['post'];

			$attributes = rthrm_get_attributes( $rt_hrm_module->post_type );
			foreach ( $attributes as $attr ){
				$rt_hrm_attributes->save_attributes( $attr, isset($post_id) ? $post_id : '', $leave_meta );
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
			global $post,$rt_hrm_module;
			$complete = '';
			if( isset( $post) && !empty( $post ) && $post->post_type == $rt_hrm_module->post_type){
				$option='';
				foreach ( $rt_hrm_module->get_custom_statuses() as $status ){
					if($post->post_status == $status['slug']){
						$complete = " selected='selected'";
					}else{
						$complete = '';
					}
					$option .= "<option value='" . $status['slug'] . "' " . $complete . ">"  .  $status['name'] .  "</option>";
				}
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
					$("#title").attr("readonly","readonly");
					$("#leave-user").blur(function(){
					    if($("#leave-user").val().length > 0){
					        $("#title-prompt-text").addClass("screen-reader-text");
                            $("#title").val( $("#leave-user").val() + " Leave");
                        }else{
                            $("#title-prompt-text").removeClass("screen-reader-text");
                            $("#title").val("");
                        }
					});
		      });

		      </script>';
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
