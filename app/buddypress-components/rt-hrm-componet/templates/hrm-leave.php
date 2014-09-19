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
				<!-- code for leaves -->
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
						'meta_query' => array(
							array(
								'key' => 'leave-user-id',
								'value' => $post_meta->post_id
							)
						),
						'post_type' => $rt_hrm_module->post_type,
						'post_status' => array( 'approved', 'rejected' ),
						'posts_per_page' => $posts_per_page,
						'offset' => $offset
					);
					// The Query
					$the_query = new WP_Query( $args );
					
					$max_num_pages =  $the_query->max_num_pages;
					$leave_posts = get_posts($args);
					?>
					<table cellspacing="0" class="lists">
						<tbody>
							<tr class="lists-header">
								<th align="center" scope="row">
									<?php esc_html_e('Leave Type', 'rt_hrm');?>
									<!--<select name="leavetype" class="order leavetype">
									  <option value="ASC">ASC</option>
									  <option value="DESC">DESC</option>
									</select>-->
								</th>
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
								<th align="center" scope="row">
									<?php esc_html_e('Status', 'rt_hrm');?>
									<!--<select name="status" class="order status">
									  <option value="ASC">ASC</option>
									  <option value="DESC">DESC</option>
									</select>-->
								</th>
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
									$leave_end_date_value = get_post_meta( $get_the_id, 'leave-end-date', true );
									$leave_user_id = get_post_meta( $get_the_id, 'leave-user-id', true );
									$rt_biz_contact_user_id = get_post_meta( $leave_user_id, 'rt_biz_contact_user_id', true );
									
									//Returns Array of Term Names for "rt-leave-type"
									$rt_leave_type_list = wp_get_post_terms($post->ID, 'rt-leave-type', array("fields" => "names")); // tod0:need to call in correct way
								if ( bp_loggedin_user_id() == bp_displayed_user_id() ) {
								?>
								<tr class="lists-data">
									<td align="center" scope="row">
										<?php if ( ! empty( $rt_leave_type_list ) ) echo $rt_leave_type_list[0];
										edit_post_link('Edit', '<br /><span>', '</span>&nbsp;&#124;');?>
										<a href="<?php the_permalink();?>">View</a>
									</td>
									<td align="center" scope="row"><?php echo $leave_start_date_value;?></td>
									<td align="center" scope="row"><?php echo $leave_end_date_value;?></td>
									<td align="center" scope="row" class="<?php echo strtolower ( get_post_status() ); ?>"><?php echo get_post_status(); ?></td>
								</tr>
								<?php
								} endforeach; 
								wp_reset_postdata();
							} else {
								?>
								<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>
								<?php
							}
							?>
						</tbody>
					</table>
					<?php if ( $max_num_pages > 1 ) { ?>
					<ul id="requests-pagination"><li id="prev"><a class="page-link">Previous</a></li><li id="next"><a class="page-link next">Next</a></li></ul>
					<?php } ?>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>