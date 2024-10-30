<?php
require_once('../wp-admin/admin.php');
$parent_file = 'bcm-edit-comment.php';
$submenu_file = 'bcm-edit-comment.php';

if (! empty($_GET['p'])) $postid = attribute_escape($_GET['p']);
wp_reset_vars(array('action'));

global $bcmode;

switch($bcmode) {
case 'savebulkreply':
	global $user_ID;
	$title = __('Edit Comment');
	$comment_content      = trim($_POST['content']);
	$comment_post_ID      = trim($_POST['comment_post_ID']);
	$user = get_userdata( $user_ID );
	if ( !empty($user->display_name) )
		$comment_author = $user->display_name;
	else 
		$comment_author = $user->user_nicename;
	$comment_author_email = $user->user_email;
	$comment_author_url = $user->user_url;
	$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');
	$comment_id = wp_new_comment( $commentdata );
	$location = "?page=bcm-edit-comments&mode=edit&message=Reply Saved&p=".$postid;
	wp_redirect($location);
	break;
default:
	echo "<p>Illegal Action cannot access file directly</p>";
	break;
} // end switch

//include('admin-footer.php');

?>
