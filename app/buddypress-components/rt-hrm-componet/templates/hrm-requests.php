<?php get_header() ?>
	<div id="content">
		<div class="padder">

			<div id="item-header">
				<?php locate_template( array( 'members/single/member-header.php' ), true ) ?>
			</div>

			<div id="item-nav">
				<div class="item-list-tabs no-ajax" id="object-nav">
					<ul>
						<?php bp_get_displayed_user_nav() ?>
					</ul>
				</div>
			</div>

			<div id="item-body">

				<div class="item-list-tabs no-ajax" id="subnav">
					<ul>
						<?php bp_get_options_nav() ?>
					</ul>
				</div>
				<!-- code for requests -->
				<?php 
				global $rt_hrm_module, $rt_hrm_attributes, $bp, $wpdb;
				
				$post_meta = $wpdb->get_row( "SELECT * from {$wpdb->postmeta} WHERE meta_key = 'rt_biz_contact_user_id' and meta_value = {$bp->displayed_user->id} ");
				$args = array(
					'meta_query' => array(
						array(
							'key' => 'leave-user-id',
							'value' => $post_meta->post_id
						)
					),
					'post_type' => $rt_hrm_module->post_type,
					'post_status' => 'any',
					'nopaging' => true
				);
				$leave_posts = get_posts($args);
				?>
				<h2><?php esc_html_e('requests', 'rt_hrm');?></h2>
				<table cellspacing="0" class="requests-lists">
					<tbody>
						<tr>
							<th align="center" scope="row"></th>
							<th align="center" scope="row"><?php esc_html_e('Name', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Leave Type', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Start Date', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('End Date', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Status', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Approver', 'rt_hrm');?></th>
						</tr>
						<?php
							if ( count($leave_posts) > 0 ) {
								foreach ( $leave_posts as $post ) : setup_postdata( $post ); ?>
									<?php $get_the_id =  get_the_ID();
									$get_user_meta = get_post_meta($get_the_id);
									$leave_user_value = get_post_meta( $get_the_id, 'leave-user', true );
									$leave_duration_value = get_post_meta( $get_the_id, 'leave-duration', true );
									$leave_duration_type = get_term_by('slug', $leave_duration_value, 'rt-leave-type');
									
									$leave_start_date_value = get_post_meta( $get_the_id, 'leave-start-date', true );
									$leave_user_value = get_post_meta( $get_the_id, 'leave-user', true );
									
									//Returns Array of Term Names for "rt-leave-type"
									$rt_leave_type_list = wp_get_post_terms($post->ID, 'rt-leave-type', array("fields" => "names")); // tod0:need to call in correct way
									
								?>
								<tr>
									<th align="center" scope="row">//image//</th>
									<th align="center" scope="row"><?php echo $leave_user_value;?></th>
									<th align="center" scope="row"><?php echo $rt_leave_type_list[0];?></th>
									<th align="center" scope="row"><?php echo $leave_start_date_value;?></th>
									<th align="center" scope="row"><?php esc_html_e('End Date', 'rt_hrm');?></th>
									<th align="center" scope="row"><?php echo $post->post_status;?></th>
									<th align="center" scope="row"><?php esc_html_e('Approver', 'rt_hrm');?></th>
								</tr>
								<?php endforeach; 
								wp_reset_postdata();
							}
						?>
					</tbody>
				</table>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>