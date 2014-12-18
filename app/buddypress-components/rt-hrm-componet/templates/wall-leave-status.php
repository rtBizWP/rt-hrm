<?php
/**
 * Created by PhpStorm.
 * User: paresh
 * Date: 15/12/14
 * Time: 6:16 PM
 */
global $rt_hrm_module;

$post_id = $_GET['id'];
?>

<form action="" method="post">

    <input type="hidden" name="post[template]" value="<?php echo $_GET['template'] ?>" />
    <input type="hidden" name="post[post_id]" value="<?php echo $post_id ?>" />
    <?php wp_nonce_field('rt_hrm_wall_leave_status_update','rt_hrm_wall_leave_status_update') ?>

    <?php if( isset( $_GET['rt_voxxi_blog_id'] ) ) { ?>
        <input type="hidden" name="post[rt_voxxi_blog_id]" value="<?php echo $_GET['rt_voxxi_blog_id'] ?>" />
    <?php } ?>

    <div class="row">
        <div class="small-10 columns">
            <h2>Leave</h2>
        </div>
        <div class="small-2 columns">
            <a title="Close" class="right close-sidepanel"><i class="fa fa-caret-square-o-right fa-2x"></i></a>
        </div>
    </div>

    <div class="row column-title">
        <div class="small-12 columns">
            <label>Note
                <textarea name="post[leave_comment_content]" rows="5" placeholder="Comment"></textarea>
            </label>

        </div>

    </div>

    <div class="row ">
        <div class="small-12 columns">
           <input class="right" type="submit" value="Save" />
        </div>

    </div>

</form>