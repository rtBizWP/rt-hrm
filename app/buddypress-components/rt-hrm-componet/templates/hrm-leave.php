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
				<!-- code for leave -->
				<?php 
					echo '<div class=""><p>'.__( 'code for leave.' ).'</p></div>';
					global $rt_hrm_module, $rt_hrm_attributes, $bp;
					
					
					$args = array(
					'meta_query' => array(
						array(
							'key' => 'leave-user-id',
							'value' => $bp->displayed_user->id
						)
					),
					'post_type' => $rt_hrm_module->post_type,
					'post_status' => 'any',
					'nopaging' => true
				);

				$posts = get_posts($args);
				// echo "<pre>";
				// print_r($posts);
				// echo $bp->displayed_user->id;
				// echo "</pre>";
				?>
			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>