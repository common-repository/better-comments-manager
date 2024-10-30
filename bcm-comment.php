<?php
require_once('../wp-admin/admin.php');

$parent_file = 'bcm-edit-comment.php';
$submenu_file = 'bcm-edit-comment.php';

wp_reset_vars(array('action'));

global $bcmode;

switch($bcmode) {
case 'reply':
	$title = __('Edit Comment');

	require_once ('admin-header.php');

	$comment = (int) $_GET['c'];

	if ( ! $comment = get_comment($comment) )
		wp_die(__('Oops, no comment with this ID.').sprintf(' <a href="%s">'.__('Go back').'</a>!', 'javascript:history.go(-1)'));

	if ( !current_user_can('edit_post', $comment->comment_post_ID) )
		wp_die( __('You are not allowed to edit comments on this post.') );

	$comment = get_comment_to_edit($comment);

	include('bcm-reply-comment-form.php');

	break;

default:
	echo "<p>Illegal Action cannot access file directly</p>";
	break;
} // end switch

//include('admin-footer.php');

?>
