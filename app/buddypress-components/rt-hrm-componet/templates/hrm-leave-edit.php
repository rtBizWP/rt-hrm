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
				<!-- code for calender -->
				<?php 
				global $rt_calendar, $rt_hrm_module, $rt_hrm_bp_hrm_calendar, $rt_hrm_bp_hrm_module, $post;
				$rt_leave_id = $_GET['rt_leave_id'];
				$is_user_change_allowed = 1;
				
				/* render data into calendar */
				
				$rt_hrm_bp_hrm_module->save_leave_meta( $rt_leave_id, $post );
				$rt_hrm_bp_hrm_module->save_leave( $rt_leave_id, $post );
				$rt_hrm_bp_hrm_module->ui_metabox( $rt_leave_id );
				if ( isset($_POST['update']) ) {
					//echo '<script> window.location=""; </script> ';
					//die();
				}
				
				?>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>