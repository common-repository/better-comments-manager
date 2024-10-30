<?php
/*
Plugin Name: Better Comments Manager
Plugin URI: http://techie-buzz.com/wordpress-plugins/better-comments-manager-wordpress-plugin-release.html
Description: Better Comments manager allows you to view your comments post wise, it also allows you to reply to your comments from within admin panel without you having to visit the site to respond to comments
Version: 1.5.1
Author: Keith Dsouza
Author URI: http://techie-buzz.com/

V1.2 Change LOG
Added support to reply to comments from within the page
The comments are now saved using AJAX, so users will not have to leave the page while replying
Removed unwanted files from the release
Note: Mass Edit mode still uses old comment reply method
Fixed regular reply to comments which were throwing erros
Shows the post title now in place of View link, suggestion i picked up from Lorelle's site
Fixed bug in navigating comments using next page link with view all by posts link

v1.3 Change Log
	* Removed scrolling effect on adding a new comment
	* Made plugin compatible to store threaded replies
	* Added functionality to edit comments from within the same page
	* You can either add a threaded reply or create a new reply altogether, useful when you want to reply to more than one comments at a single time.
	* Made better comments manager accesible from the main menu
	* 
	
v1.4 Change Log
	* Fix for IE 7 where comments were not being added
	* WordPress 2.3 compatibility fix
*/

@define('BCM_FILEPATH', '/wp-content/plugins/better-comments-manager');

$bcmDirPath = get_bloginfo('wpurl') . BCM_FILEPATH;
$bcmDirJSPath = get_bloginfo('wpurl') . "/wp-includes/js";
$bcmDirAdPath = get_bloginfo('wpurl') . "/wp-admin";

/*
* the variable defines whether to show the title link in the comment or not
*
* if you want to disable this feature just change the value to 0 
* $showLinkTitle = 0;
* 
* if you want to enable this feature just change the value to 1 
* $showLinkTitle = 1;
*/
$showLinkTitle = 1;

if( isset($_REQUEST['bcmode']) ) {
	$bcmode = $_REQUEST['bcmode'];
}
if( isset($_REQUEST['c']) ) {
	$commentid = $_REQUEST['c'];
}
function bcm_manage_page() {
	global $wpdb, $submenu;
	if ( isset( $submenu['edit-comments.php'] ) )
		add_submenu_page('edit-comments.php', 'Better Comments Manager', 'Better Comments Manager', 'moderate_comments', 'bcm-edit-comments', 'better_comment_manager' );
		add_menu_page('Better Comments Manager', 'Better Comments Manager', 'moderate_comments', 'better-comments-manager/bcm-edit-comments.php' , 'better_comment_manager');
}

function better_comment_manager() {
	global $bcmode;
	switch($bcmode) {
		case 'reply':
			show_reply_page();
			break;
		case 'savebulkreply':
			save_comment_reply();
			break;
		case 'viewall':
			show_all_comments_by_post();
			break;
		default:
			show_comments_page();
			break;
	}
}

function show_comments_page() {
	global $bcmDirPath, $bcmDirJSPath, $bcmDirAdPath, $showLinkTitle;
	include 'bcm-edit-comments.php';
}

function show_reply_page() {
	include "bcm-comment.php";
}

function save_comment_reply() {
	include "bcm-bulk-save-comment.php";
}

function show_all_comments_by_post() {
	global $bcmDirPath, $bcmDirJSPath, $bcmDirAdPath, $showLinkTitle;
	include 'bcm-edit-comments.php';
}



/**
	* Adds in the necessary JavaScript files for the automated version
	**/
	function bcm_add_scripts() {	
		global $bcmDirPath;
		if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) {
			wp_enqueue_script('bcm_edit_script', $bcmDirPath.'/editcomments.js.php');
			wp_enqueue_script('prototype');
			wp_enqueue_script('scriptaculous-effects');
			wp_enqueue_script( 'admin-comments' );
		} else {
			wpau_add_scripts_legacy();
		}
	}
	function bcm_add_scripts_legacy() {
		if (function_exists('wp_enqueue_script') && function_exists('wp_register_script')) { bcm_add_scripts(); return; }
		print('<script type="text/javascript" src="'.$bcmDirPath.'/editcomments.js.php"></script>'."\n");
		print('<script type="text/javascript" src="'.$bcmDirPath.'/prototype.js"></script>'."\n");
		print('<script type="text/javascript" src="'.$bcmDirPath.'/effects.js"></script>'."\n");
	}
	add_action('admin_menu', 'bcm_manage_page');
	add_action('admin_print_scripts', 'bcm_add_scripts');
	add_action('admin_head', 'bcm_add_scripts_legacy');
?>