<?php 
global $rt_hrm_module, $rt_hrm_attributes, $bp, $wpdb,  $wp_query;
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$paged = $page = max( 1, get_query_var('paged') );

$posts_per_page = get_option( 'posts_per_page' );
	
$order = 'DESC';
$attr = 'startdate';
$orderby = 'meta_value_num';

$meta_key = 'leave-start-date';
if ( $attr == "startdate" ){
	$meta_key = 'leave-start-date';
} else if( $attr == "enddate" ) {
	$meta_key = 'leave-end-date';
} else if( $attr == "leavetype" ) {
	$meta_key = 'leave-start-date';
	$orderby = 'rt-leave-type';
}

$offset = ( $paged - 1 ) * $posts_per_page;
if ($offset <=0) {
	$offset = 0;
}

if( isset( $_GET['orderby'] ) ) {
        $meta_key = $args['orderby'] = $_GET['orderby'];
        $order = $args['order'] =  $_GET['order'];
}
if( $meta_key == "rt-leave-type" ) {
	$meta_key = 'leave-start-date';
	$orderby = 'rt-leave-type';
}

$post_meta = $wpdb->get_row( "SELECT * from {$wpdb->postmeta} WHERE meta_key = 'rt_biz_contact_user_id' and meta_value = {$bp->displayed_user->id} ");
$args = array(
	'post_type' => $rt_hrm_module->post_type,
	'meta_key'   => $meta_key,
	'orderby' => $orderby,
	'order'      => $order,
	'post_status' => 'any',
	'posts_per_page' => $posts_per_page,
	'offset' => $offset
);

/*echo "<pre>";
print_r($args);
echo "</pre>";*/


$columns = array(
    array(
            'column_label' => __( '', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => false
    ),
    array(
            'column_label' => __( 'Name', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => false
    ),
    array(
            'column_label' => __( 'Leave Type', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => true,
            'orderby' => 'rt-leave-type',
            'order' => 'asc'
    ),
    array(
            'column_label' => __( 'Start Date', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => true,
            'orderby' => 'leave-start-date',
            'order' => 'asc'
    ),
    array(
            'column_label' => __( 'End Date', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => true,
            'orderby' => 'leave-end-date',
            'order' => 'asc'
    ),
    array(
            'column_label' => __( 'Status', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => false,
          
    ),
    array(
            'column_label' => __( 'Approver', RT_HRM_TEXT_DOMAIN ) ,
            'sortable' => false,
    ),

);
// The Query
$the_query = new WP_Query( $args );

$totalPage = $max_num_pages =  $the_query->max_num_pages;

?>
<div class="row list-heading">
    <div class="large-10 columns list-title">
        <h4><?php _e( 'Leave Approval', RT_HRM_TEXT_DOMAIN ) ?></h4>
    </div>
    <div class="large-2 columns">
       
    </div>
</div>
<table cellspacing="0" class="requests-lists">
	<tbody>
		<tr class="lists-header">
          <?php foreach ( $columns as $column ) {
          ?>
                <th>
                    <?php
                    if(  $column['sortable']  ) {

                            if ( isset( $_GET['orderby'] ) && $column['orderby']  == $_GET['orderby'] ) {
                               
                                $current_order = $_GET['order'];
                               
                                $order = 'asc' == $current_order ? 'desc' : 'asc';
                                
                                printf( __('<a href="%s">%s <i class="fa fa-sort-%s"></i> </a>'), esc_url( add_query_arg( array( 'orderby' => $column['orderby'] ,'order' => $order ) ) ), $column['column_label'], $order );
                                
                            }else{
                                  printf( __('<a href="%s">%s <i class="fa fa-sort"></i> </a>'), esc_url( add_query_arg( array( 'orderby' => $column['orderby'] ,'order' => 'desc' ) ) ), $column['column_label'] );
                            }
                          
                    }else{
                            echo $column['column_label'];
                    }

                    ?>
                </th>
        <?php  } ?>
        </tr>
		<?php
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) { ?>
					<?php 
					$the_query->the_post();
					$get_the_id =  get_the_ID();
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
					if ( ! empty( $leave_start_date_value ) ){
						$leave_start_date_value_array = explode("/", $leave_start_date_value);
						$day = $leave_start_date_value_array[0];
						$month = $leave_start_date_value_array[1];
						$year = $leave_start_date_value_array[2];
						$leave_start_date_value = $month .'/'. $day .'/'. $year;
						$leave_start_date_value = strtotime($leave_start_date_value);
						$leave_start_date_value = date("d-M-Y",  ((int) $leave_start_date_value));
					}
					$leave_end_date_value = get_post_meta( $get_the_id, 'leave-end-date', true );
					if ( ! empty( $leave_end_date_value ) ){
						$leave_end_date_value_array = explode("/", $leave_end_date_value);
						$day = $leave_end_date_value_array[0];
						$month = $leave_end_date_value_array[1];
						$year = $leave_end_date_value_array[2];
						$leave_end_date_value = $month .'/'. $day .'/'. $year;
						$leave_end_date_value = strtotime($leave_end_date_value);
						$leave_end_date_value = date("d-M-Y",  ((int) $leave_end_date_value));
					}
					//Returns Array of Term Names for "rt-leave-type"
					$rt_leave_type_list = wp_get_post_terms( $get_the_id, 'rt-leave-type', array("fields" => "names")); // todo:need to call in correct way
				?>
				<tr class="lists-data">
					<td class="person-avatar"><?php echo get_avatar( $rt_biz_contact_user_id, 32 ); ?> </td>
					<td>
						<?php echo $leave_user_value;
						if ( current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
							printf( __('<br /><span><a href="%s">Edit</a></span>&nbsp;&#124;'), esc_url( add_query_arg( array( 'rt_leave_id'=> $get_the_id, 'action'=>'edit' ) ) ) );
						}
						printf( __('<span><a href="%s">View</a></span>'), esc_url( add_query_arg( array( 'rt_leave_id'=> $get_the_id, 'action'=>'view' ) ) ) );
						?>
						<?php
						if ( current_user_can( rt_biz_get_access_role_cap( RT_HRM_TEXT_DOMAIN, 'editor' ) ) ) {
						?>
						&#124;&nbsp;<?php printf( __('<span><a class="deletepostlink" href="%s">Delete</a></span>'), esc_url( add_query_arg( array( 'rt_leave_id'=> $get_the_id, 'action'=>'deletepost' ) ) ) );?>
						<?php
						}
						?>
						
					</td>
					<td><?php if ( ! empty( $rt_leave_type_list ) ) echo $rt_leave_type_list[0]; ?></td>
					<td><?php echo $leave_start_date_value;?></td>
					<td><?php echo $leave_end_date_value;?></td>
					<td class="<?php echo strtolower ( get_post_status() ); ?>"><?php echo get_post_status(); ?></td>
					<th>
						<?php
							if ( ! empty( $user_nicename ) && get_post_status() != 'pending' ){
								echo $user_nicename;
							} else {
								esc_html_e('Awaiting', 'rt_hrm');
							}
						?>
					</th>
				</tr>
				<?php
				}
			} else {
				?>
				<tr class="lists-data"><td colspan="7" align="center" scope="row">No Leave Listing</td></tr>
				<?php
			}
			wp_reset_postdata();
		?>
	</tbody>
</table>
<?php /*if ( $max_num_pages > 1 ) { ?>
	<ul id="requests-pagination"><li id="prev"><a class="page-link"> &laquo; Previous</a></li><li id="next"><a class="page-link next">Next &raquo;</a></li></ul>
<?php } */?>
<?php       
	hrm_pagination( $totalPage, $page );
?>