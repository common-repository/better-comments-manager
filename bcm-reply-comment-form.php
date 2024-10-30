<?php
$submitbutton_text = __('Reply to Comment &raquo;');
$toprow_title = sprintf(__('Replying to Comment # %s'), $comment->comment_ID);
$form_action = 'repliedcomment';
$form_extra = "' />\n<input type='hidden' name='comment_post_ID' value='".$comment->comment_post_ID;
global $user_ID;
if (! empty($_GET['p'])) $postid = attribute_escape($_GET['p']);
?>
<form name="post" action="edit-comments.php?page=bcm-edit-comments&bcmode=savebulkreply&p=<?php echo $postid;?>" method="post" id="post">
<?php wp_nonce_field('update-comment_' . $comment->comment_ID) ?>
<div class="wrap">
<input type="hidden" name="user_ID" value="<?php echo $user_ID ?>" />
<input type="hidden" name="action" value='<?php echo $form_action . $form_extra ?>' />

<script type="text/javascript">
function focusit() { // focus on first input field
	document.post.name.focus();
}
addLoadEvent(focusit);
</script>
<fieldset id="commenter">
<legend>
<p>
	<strong>Comment Author</strong>:  <?php echo $comment->comment_author ?><br />
	<strong>Author Email</strong>:  <?php echo $comment->comment_author_email ?><br />
	<strong>Comment URL</strong>:  <?php echo $comment->comment_author_url ?><br />
		<strong>Comment Content</strong>:  <?php echo $comment->comment_content ?><br />
</p>
</legend>
</fieldset>

<fieldset style="clear: both;">
        <legend>Your Reply</legend>
	<?php the_editor('', 'content', 'newcomment_author_url'); ?>
</fieldset>

<p class="submit"><input type="submit" name="editcomment" id="editcomment" value="<?php echo $submitbutton_text ?>" style="font-weight: bold;" tabindex="6" />
  <input name="referredby" type="hidden" id="referredby" value="<?php echo wp_get_referer(); ?>" />
</p>

</div>

</form>
