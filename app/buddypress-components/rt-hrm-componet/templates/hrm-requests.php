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
				<?php echo '<div class=""><p>'.__( 'code for leave.' ).'</p></div>'; ?>
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
						<tr>
							<td align="center" scope="row">//</td>
							<td align="center" scope="row"><?php esc_html_e('Name', 'rt_hrm');?></td>
							<td align="center" scope="row"><?php esc_html_e('Leave Type', 'rt_hrm');?></td>
							<td align="center" scope="row"><?php esc_html_e('Start Date', 'rt_hrm');?></td>
							<td align="center" scope="row"><?php esc_html_e('End Date', 'rt_hrm');?></td>
							<td align="center" scope="row"><?php esc_html_e('Status', 'rt_hrm');?></td>
							<td align="center" scope="row"><?php esc_html_e('Approver', 'rt_hrm');?></td>
						</tr>
					</tbody>
				</table>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>