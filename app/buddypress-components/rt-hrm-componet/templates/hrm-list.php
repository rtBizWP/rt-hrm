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
                            
				
				<?php 
                                $title;
                                global $bp;
                                if ( $bp->current_action == 'employee' ) {
                                    
                                   $title = 'Employee';
                                    $meta = array( 
                                                array(
                                                    'key'     => Rt_Person::$meta_key_prefix . Rt_Person::$our_team_mate_key,
                                                    'value'   => '1',
                                                ) 
                                            );
                                }elseif ( $bp->current_action == 'clients' ) {
                                    
                                    $title = 'Clients';
                                    $meta = array( 
                                                array(
                                                    'key'     => Rt_Person::$meta_key_prefix . Rt_Person::$our_team_mate_key,
                                                    'compare' => 'NOT EXISTS', // works!
                                                    'value' => '' // This is ignored, but is necessary...
                                                ) 
                                            );
                                }
                                
                                $args = array(
                                    'post_type' => 'rt_contact',
                                    'meta_query' =>  $meta,
                                       
                                );
                                
                                $peoples = get_posts( $args );
                              
                                if ( !empty($peoples)  ) : ?>
                                        <input type="submit" value="Add New"/>
					<h4><?php _e( $title, RT_BIZ_TEXT_DOMAIN ) ?></h4>

					<table id="high-fives">
                                            <tr>
                                            <th><?php _e( 'Name', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Author', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Comments', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Date', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Phone', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Email', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Oragnization', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            <th><?php _e( 'Lead', RT_BIZ_TEXT_DOMAIN ) ?></th>
                                            </tr>
						<?php foreach ( $peoples as $people ) : ?>
						<tr>
                                                    <td><?php echo $people->post_title ?></td>
                                                    
                                                    <td><?php
                                                     $user_info = get_userdata($people->post_author);
                                                    echo  $user_info->user_login;
                                                    ?></td>
                                                    
                                                    <td><?php
                                                     $comments_count = wp_count_comments($people->ID);
                                                    echo  $comments_count->total_comments;
                                                    ?></td>
                                                    
                                                    <td><?php echo $people->post_date ?></td>
                                                    
                                                    <td><?php 
                                                    $val = Rt_Person::get_meta( $people->ID, 'contact_phone' );
                                                    if ( ! empty( $val ) ) {
                                                            echo implode( ' , ', $val );
                                                    } ?>
                                                    </td>
                                                    
                                                    <td><?php 
                                                    $val = Rt_Person::get_meta( $people->ID, 'contact_email' );
                                                    if ( ! empty( $val ) ) {
                                                            $emails = array();
                                                            foreach ( $val as $e ) {
                                                                    $emails[] = '<a href="mailto:' . $e . '">' . $e . '</a>';
                                                            }
                                                            echo implode( ' , ', $emails );
                                                    } ?>
                                                    </td>
                                                    
                                                    <td><?php
                                                       $val = rt_biz_get_organization_to_person_connection( $people->ID );
                                                        if ( ! empty( $val ) ) {
                                                                $organizations = array();
                                                                foreach ( $val as $o ) {
                                                                        $organizations[] = '<a href="' . get_edit_post_link( $o->ID ) . '">' . $o->post_title . '</a>';
                                                                }
                                                                echo implode( ', ', $organizations );
                                                        } 
                                                    ?></td>
                                                    
                                                    <td><?php
                                                    global $rt_crm_module;
                                                       $post_details = get_post( $people->ID );
                                                       $pages = rt_biz_get_post_for_person_connection( $people->ID, $rt_crm_module->post_type );
                                                       echo '<a href = edit.php?' . $post_details->post_type . '=' . $post_details->ID . '&post_type='.$rt_crm_module->post_type.'>' . count( $pages ) . '</a>';

                                                    ?></td>
			 			</tr>
						<?php endforeach; ?>
					</table>
				<?php endif; ?>

			</div><!-- #item-body -->

		</div><!-- .padder -->
	</div><!-- #content -->

	<?php locate_template( array( 'sidebar.php' ), true ) ?>

<?php get_footer() ?>