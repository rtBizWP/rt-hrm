<?php
/**
 * Created by PhpStorm.
 * User: paresh
 * Date: 18/12/14
 * Time: 2:46 PM
 */
global $rt_hrm_module;;

$post_id = $_GET['id'];

$arg = array(
    'post_type' => $rt_hrm_module->post_type,
    'p' => $post_id,
    'no_found_rows' => true,
);

$query = new WP_Query( $arg );

$leave = $query->posts[0];


$leave_user_id = get_post_meta( $post_id, 'leave-user-id', true );


//HR department email id

$hr_department = get_term_by( 'slug', KWS_User_Groups::$hr_department['slug'], 'user-group' );


    $to_id = KWS_User_Groups::get_meta( 'email_address', $hr_department->term_id );

    if( empty( $to_id ) ){


        $user_data = get_userdata( '1' ) ;
        $user_data =  $user_data->data;

        $to_id =  $user_data->user_email;

    }
?>


<form action="" method="post" name="post[leave-ask-for-more]">

    <input type="hidden" name="post[template]" value="<?php echo $_GET['template'] ?>" />
    <input type="hidden" name="post[post_type]" value="<?php echo $rt_hrm_module->post_type ?>" />
    <input type="hidden" name="post[post_id]" value="<?php echo $post_id ?>" />
    <input type="hidden" name="post[leave_user_id]" value="<?php echo $leave_user_id ?>" />

    <?php if( isset( $_GET['rt_voxxi_blog_id'] ) ) { ?>
        <input type="hidden" name="post[rt_voxxi_blog_id]" value="<?php echo $_GET['rt_voxxi_blog_id'] ?>" />
    <?php } ?>

    <?php wp_nonce_field('rt_hrm_wall_leave_ask_more','rt_hrm_wall_leave_ask_more') ?>

    <div class="row">
        <div class="small-10 columns">
            <h2> <?php _e('Ask for  more info', RT_HRM_TEXT_DOMAIN ) ?></h2>
        </div>
        <div class="small-2 columns">
            <a title="Close" class="right close-sidepanel"><i class="fa fa-caret-square-o-right fa-2x"></i></a>
        </div>
    </div>

    <div class="row column-title">
        <div class="small-12 columns">
            <label>
                <?php _e('Subject', RT_HRM_TEXT_DOMAIN ) ?>
                <input type="text" name="post[subject]" value="<?php echo $leave->post_title ?>" placeholder="Subject" />
            </label>

        </div>
    </div>

    <div class="row">
        <div class="small-12 columns">
            <label>
                <?php _e('To', RT_HRM_TEXT_DOMAIN ) ?>
                <input type="text" readonly name="post[to]" placeholder="To" value="<?php echo $to_id ?>" />
            </label>

        </div>
    </div>

    <div class="row column-title">
        <div class="small-12 columns">
                <textarea name="post[mail_content]" rows="5" placeholder="Content"></textarea>
        </div>
    </div>

    <div class="row ">
        <div class="small-12 columns">
            <input class="right" type="submit" value="Send" />
        </div>
    </div>

</form>