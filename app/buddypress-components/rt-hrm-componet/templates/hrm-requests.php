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
				global $rt_hrm_module, $rt_hrm_attributes, $bp, $wpdb,  $wp_query;
				$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
				
				$posts_per_page = 1;
		
				$offset = ( $paged - 1 ) * $posts_per_page;
				if ($offset <=0) {
					$offset = 0;
				}
				
				$post_meta = $wpdb->get_row( "SELECT * from {$wpdb->postmeta} WHERE meta_key = 'rt_biz_contact_user_id' and meta_value = {$bp->displayed_user->id} ");
				$args = array(
					'post_type' => $rt_hrm_module->post_type,
					'post_status' => 'any',
					'posts_per_page' => $posts_per_page,
					'offset' => $offset
				);
				$leave_posts = get_posts($args);
				?>
				<h2><?php esc_html_e('requests', 'rt_hrm');?></h2>
				<table cellspacing="0" class="requests-lists">
					<tbody>
						<tr class="lists-header">
							<th align="center" scope="row"></th>
							<th align="center" scope="row"><?php esc_html_e('Name', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Leave Type', 'rt_hrm');?></th>
							<th align="center" scope="row">
								<?php esc_html_e('Start Date', 'rt_hrm');?>
								<select name="startdate" class="order startdate">
								  <option value="ASC">ASC</option>
								  <option value="DESC">DESC</option>
								</select>
							</th>
							<th align="center" scope="row">
								<?php esc_html_e('End Date', 'rt_hrm');?>
								<select name="enddate" class="order enddate">
								  <option value="ASC">ASC</option>
								  <option value="DESC">DESC</option>
								</select>
							</th>
							<th align="center" scope="row"><?php esc_html_e('Status', 'rt_hrm');?></th>
							<th align="center" scope="row"><?php esc_html_e('Approver', 'rt_hrm');?></th>
						</tr>
						<?php
							if ( count($leave_posts) > 0 ) {
								foreach ( $leave_posts as $post ) : setup_postdata( $post ); ?>
									<?php $get_the_id =  get_the_ID();
									$get_user_meta = get_post_meta( $get_the_id );
									$leave_user_value = get_post_meta( $get_the_id, 'leave-user', true );
									$leave_duration_value = get_post_meta( $get_the_id, 'leave-duration', true );
									$leave_duration_type = get_term_by('slug', $leave_duration_value, 'rt-leave-type');
									
									
									$leave_user_id = get_post_meta( $get_the_id, 'leave-user-id', true );
									$rt_biz_contact_user_id = get_post_meta( $leave_user_id, 'rt_biz_contact_user_id', true );
									$leave_user_approver = get_post_meta( $get_the_id, 'leave-user-approver', true );
									$approver_info = get_user_by( 'id', $leave_user_approver );
									if ( ! empty( $approver_info->user_nicename ) ){							
										$user_nicename = $approver_info->user_nicename;
									}
									
									$leave_start_date_value = get_post_meta( $get_the_id, 'leave-start-date', true );
									$leave_end_date_value = get_post_meta( $get_the_id, 'leave-end-date', true );
									
									//Returns Array of Term Names for "rt-leave-type"
									$rt_leave_type_list = wp_get_post_terms( $get_the_id, 'rt-leave-type', array("fields" => "names")); // todo:need to call in correct way
								?>
								<tr class="lists-data">
									<td align="center" scope="row"><?php echo get_avatar( $rt_biz_contact_user_id, 24 ); ?> </td>
									<td align="center" scope="row">
										<?php echo $leave_user_value;
										if ( current_user_can('edit_posts') ) {
											edit_post_link('Edit', '<br /><span>', '</span>&nbsp;&#124;');
										}
										?>
										<a href="<?php the_permalink();?>"><?php esc_html_e('View', 'rt_hrm');?></a>
										<?php
										if ( current_user_can('delete_posts') ) {
										?>
										&#124;&nbsp;<a href="<?php echo get_delete_post_link( $get_the_id ); ?>"><?php esc_html_e('Delete', 'rt_hrm');?></a>
										<?php
										}
										?>
										
									</td>
									<td align="center" scope="row"><?php if ( ! empty( $rt_leave_type_list ) ) echo $rt_leave_type_list[0]; ?></td>
									<th align="center" scope="row"><?php echo $leave_start_date_value;?></th>
									<td align="center" scope="row"><?php echo $leave_end_date_value;?></td>
									<td align="center" scope="row" class="<?php echo strtolower ( get_post_status() ); ?>"><?php echo get_post_status(); ?></td>
									<th align="center" scope="row">
										<?php
											if ( ! empty( $user_nicename ) && get_post_status() != 'pending' ){
												echo $user_nicename;
											} else {
												esc_html_e('Awaiting', 'rt_hrm');
											}
										?>
									</th>
								</tr>
								<?php endforeach; 
								wp_reset_postdata();
							}
						?>
					</tbody>
				</table>
				<?php if ( count($leave_posts) > 0 ) { ?>
					<ul id="pagination"><li id="prev"><a class="page-link">Previous</a></li><li id="next"><a class="page-link next">Next</a></li></ul>
				<?php } ?>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>