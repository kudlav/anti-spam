<?php
/*
Plugin Name: Anti-spam Reloaded
Plugin URI: http://wordpress.org/plugins/anti-spam-reloaded/
Description: No spam in comments. No captcha.
Version: 6.1
Author: kudlav, webvitaly
Text Domain: anti-spam-reloaded
Author URI: https://kudlav.github.io/
License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}

define('ANTISPAMREL_PLUGIN_VERSION', '6.1');

include('anti-spam-functions.php');
include('anti-spam-settings.php');
include('anti-spam-info.php');


function antispamrel_enqueue_script() {
	global $withcomments; // WP flag to show comments on all pages
	if ((is_singular() || $withcomments) && comments_open()) { // load script only for pages with comments form
		wp_enqueue_script(
			'anti-spam-reloaded-script',
			plugins_url('/js/anti-spam.min.js', __FILE__),
			null,
			ANTISPAMREL_PLUGIN_VERSION,
			true
		);
	}
}
add_action('wp_enqueue_scripts', 'antispamrel_enqueue_script');


function antispamrel_form_part() {
	if ( ! is_user_logged_in()) { // add anti-spam fields only for not logged in users
		echo '
			<!-- Anti-spam Reloaded plugin wordpress.org/plugins/anti-spam-reloaded/ -->
			<p class="antispamrel-group" style="clear: both;">
				<label>Current ye@r <span class="required">*</span></label>
				<input type="hidden" name="antspmrl-a" class="antispamrel-control-a" value="', date('Y'), '" />
				<input type="text" name="antspmrl-q" class="antispamrel-control-q" value="', rand(0, 99), '" autocomplete="off" />
			</p>
			<p class="antispamrel-group" style="display: none;">
				<label>Leave this field empty</label>
				<input type="text" name="antspmrl-e-email-url-website" class="antispamrel-control-e" value="" autocomplete="off" />
			</p>
		'; // empty field (hidden with css); trap for spammers because many bots will try to put email or url here
	}
}
add_action('comment_form', 'antispamrel_form_part'); // add anti-spam inputs to the comment form


function antispamrel_check_comment($commentdata) {
	$antispam_settings = antispamrel_get_settings();

	extract($commentdata);

	if ( ! is_user_logged_in() && $comment_type != 'pingback' && $comment_type != 'trackback') { // logged in user is not a spammer
		if( antispamrel_check_for_spam() ) {
			if( $antispam_settings['save_spam_comments'] ) {
				antispamrel_store_comment($commentdata);
			}
			antispamrel_counter_stats();
			wp_die('Comment is a spam.'); // die - do not send comment and show error message
		}
	}

	if ($comment_type == 'trackback') {
		if( $antispam_settings['save_spam_comments'] ) {
			antispamrel_store_comment($commentdata);
		}
		antispamrel_counter_stats();
		wp_die('Trackbacks are disabled.'); // die - do not send trackback and show error message
	}

	return $commentdata; // if comment does not looks like spam
}
if ( ! is_admin()) { // without this check it is not possible to add comment in admin section
	add_filter('preprocess_comment', 'antispamrel_check_comment', 1);
}


function antispamrel_plugin_meta($links, $file) { // add some links to plugin meta row
	if ( $file == plugin_basename( __FILE__ ) ) {
		$row_meta = array(
			'github' => '<a href="https://github.com/kudlav/anti-spam/" target="_blank" rel="noreferrer">' . __( 'Github', 'github' ) . '</a>'
		);
		$links = array_merge( $links, $row_meta );
	}
	return (array) $links;
}
add_filter('plugin_row_meta', 'antispamrel_plugin_meta', 10, 2);
