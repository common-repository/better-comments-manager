<?php
//require_once('../../../wp-admin/admin.php');
require_once(dirname(__FILE__)."/../../../wp-config.php");

$parent_file = 'bcm-edit-comment.php';
$submenu_file = 'bcm-edit-comment.php';

if (! empty($_GET['p'])) $postid = attribute_escape($_GET['p']);

$bcmode = $_REQUEST['bcmode'];

switch($bcmode) {
case 'savereply':
	global $user_ID, $tablecomments;
	$title = __('Edit Comment');
	$comment_content      = trim($_POST['comment_content']);
	$comment_post_ID      = trim($_POST['comment_post_ID']);
	$comment_parent      = trim($_POST['comment_parent']);
	$isReplyOrEdit = trim($_POST['isReplyOrEdit']);
	
	$comment_author = '';
	$comment_author_email = '';
	$comment_author_url = '';

		
	if($isReplyOrEdit == "false") { //edit a comment
		$comment_author = trim($_POST['comment_author']);
		$comment_author_email = trim($_POST['comment_author_email']);
		$comment_author_url = trim($_POST['comment_author_url']);
		wp_update_comment( $_POST );
	}
	else { //add new comment
		$user = get_userdata( $user_ID );
		if ( !empty($user->display_name) )
			$comment_author = $user->display_name;
		else 
			$comment_author = $user->user_nicename;
			
		$comment_author_email = $user->user_email;
		$comment_author_url = $user->user_url;
		$comment_author_IP = $_SERVER['REMOTE_ADDR'];

		$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_parent', 'comment_type', 'user_ID');
		$comment_id = wp_new_comment( $commentdata );
		$addParent = $wpdb->query("UPDATE $tablecomments SET comment_parent='$comment_parent' WHERE comment_ID='$comment_id'");
		if(trim($comment_parent) != '') {
			$addParent = $wpdb->query("UPDATE $tablecomments SET comment_parent='$comment_parent' WHERE comment_ID='$comment_id'");
		}
		$comment = $wpdb->get_row("SELECT * FROM $wpdb->comments WHERE comment_ID = $comment_id");
		show_replied_comment($comment);
	}
	break;
default:
	echo "<p>Illegal Action cannot access file directly</p>";
	break;
} // end switch

function show_replied_comment($comment) {
	global $user_ID;
			echo "<li id='comment-$comment->comment_ID'>";
?>
<p><strong><span id="comment_author_<?php echo $comment->comment_ID;?>"><?php comment_author() ?></span></strong> <?php if ($comment->comment_author_email) { ?>| <span id="comment_author_email_<?php echo $comment->comment_ID;?>"><?php comment_author_email_link() ?></span> <?php }  ?> <span id="comment_author_url_<?php echo $comment->comment_ID;?>"><?php if ($comment->comment_author_url && 'http://' != $comment->comment_author_url) { ?> | <?php comment_author_url_link() ?> <?php } ?></span> | <?php _e('IP:') ?> <a href="http://ws.arin.net/cgi-bin/whois.pl?queryinput=<?php comment_author_IP() ?>"><?php comment_author_IP() ?></a></p>

<span id="comment_content_<?php echo $comment->comment_ID?>">
<?php comment_text() ?>
</span>

<p><?php comment_date('M j, g:i A');  ?> &#8212; [
<?php
if ( current_user_can('edit_post', $comment->comment_post_ID) ) {
		echo " <a href=\"javascript:buildReplyForm('".$comment->comment_ID."', '".$comment->comment_post_ID."', '".$user_ID."', false, 'Edit', 'edit-comment-', false, '".$comment->comment_author."', '".$comment->comment_author_email."', '".$comment->comment_author_url."', '".addslashes($comment->comment_content)."')\">Edit</a>";
	echo ' | <a href="' . wp_nonce_url('comment.php?action=deletecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . '" onclick="return deleteSomething( \'comment\', ' . $comment->comment_ID . ', \'' . js_escape(sprintf(__("You are about to delete this comment by '%s'.\n'Cancel' to stop, 'OK' to delete."), $comment->comment_author)) . "', theCommentList );\">" . __('Delete') . '</a> ';
	if ( ('none' != $comment_status) && ( current_user_can('moderate_comments') ) ) {
		echo '<span class="unapprove"> | <a href="' . wp_nonce_url('comment.php?action=unapprovecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'unapprove-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Unapprove') . '</a> </span>';
		echo '<span class="approve"> | <a href="' . wp_nonce_url('comment.php?action=approvecomment&amp;p=' . $comment->comment_post_ID . '&amp;c=' . $comment->comment_ID, 'approve-comment_' . $comment->comment_ID) . '" onclick="return dimSomething( \'comment\', ' . $comment->comment_ID . ', \'unapproved\', theCommentList );">' . __('Approve') . '</a> </span>';
	}
	echo " | <a href=\"" . wp_nonce_url("comment.php?action=deletecomment&amp;dt=spam&amp;p=" . $comment->comment_post_ID . "&amp;c=" . $comment->comment_ID, 'delete-comment_' . $comment->comment_ID) . "\" onclick=\"return deleteSomething( 'comment-as-spam', $comment->comment_ID, '" . js_escape(sprintf(__("You are about to mark as spam this comment by '%s'.\n'Cancel' to stop, 'OK' to mark as spam."), $comment->comment_author))  . "', theCommentList );\">" . __('Spam') . "</a> ";
}
$post = get_post($comment->comment_post_ID);
$post_title = wp_specialchars( $post->post_title, 'double' );
$post_title = ('' == $post_title) ? "# $comment->comment_post_ID" : $post_title;

echo " | <a href=\"javascript:buildReplyForm('".$comment->comment_ID."', '".$comment->comment_post_ID."', '".$user_ID."', true, 'Reply', 'reply-comment-', false)\">Threaded Reply</a>";
echo " | <a href=\"javascript:buildReplyForm('".$comment->comment_ID."', '".$comment->comment_post_ID."', '".$user_ID."', true, 'Reply', 'reply-comment-', true)\">New Reply</a>";
echo " | <a href='?page=better-comments-manager/bcm-edit-comments.php&amp;bcmode=viewall&amp;p=".$comment->comment_post_ID."'>View All</a>";
?>
 | <a href="<?php echo get_permalink($comment->comment_post_ID); ?>" title="<?php echo $post_title; ?>"><?php echo $post_title; ?></a> ]</p>
 <div id="reply-comment-<?php echo $comment->comment_ID; ?>"></div>
  <div id="reply-comment-<?php echo $comment->comment_ID; ?>-response"></div>
 <div id="edit-comment-<?php echo $comment->comment_ID; ?>"></div>
  <div id="edit-comment-<?php echo $comment->comment_ID; ?>-response"></div>	
		</li>

<?php
}
?>
