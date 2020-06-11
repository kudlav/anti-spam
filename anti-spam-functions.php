<?php
/*
Anti-spam Reloaded plugin
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}


function antispamrel_get_settings() {
	$default_settings = array(
		'save_spam_comments' => 0
	);
	$antispam_settings = (array) get_option('antispamrel_settings');

	return array_merge($default_settings, $antispam_settings); // set empty options with default values;
}


function antispamrel_counter_stats() {
	$antispam_stats = get_option('antispamrel_stats', array());
	if ( isset($antispam_stats['blocked_total']) ) {
		$antispam_stats['blocked_total']++;
	} else {
		$antispam_stats['blocked_total'] = 1;
	}
	update_option('antispamrel_stats', $antispam_stats);
}


function antispamrel_check_for_spam() {
	$spam_flag = false;

	if ( !isset($_POST['antspmrl-q']) || trim($_POST['antspmrl-q']) != date('Y') ) { // year-answer is wrong -> spam
		if ( !isset($_POST['antspmrl-d']) || trim($_POST['antspmrl-d']) != date('Y') ) { // extra js-only check: no js added input -> spam
			$spam_flag = true;
		}
	}

	if ( !isset($_POST['antspmrl-e-email-url-website']) || trim($_POST['antspmrl-e-email-url-website']) != '' ) {
		$spam_flag = true; // trap field is not empty - it is spam
	}

	return $spam_flag;
}


function antispamrel_store_comment($commentdata) {
	global $wpdb;

	if ( isset( $commentdata['user_ID'] ) ) {
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	}

	$prefiltered_user_id = ( isset( $commentdata['user_id'] ) ) ? (int) $commentdata['user_id'] : 0;

	$commentdata['comment_post_ID'] = (int) $commentdata['comment_post_ID'];
	if ( isset( $commentdata['user_ID'] ) && $prefiltered_user_id !== (int) $commentdata['user_ID'] ) {
		$commentdata['user_id'] = $commentdata['user_ID'] = (int) $commentdata['user_ID'];
	} elseif ( isset( $commentdata['user_id'] ) ) {
		$commentdata['user_id'] = (int) $commentdata['user_id'];
	}

	$commentdata['comment_parent'] = isset($commentdata['comment_parent']) ? absint($commentdata['comment_parent']) : 0;
	$parent_status = ( 0 < $commentdata['comment_parent'] ) ? wp_get_comment_status($commentdata['comment_parent']) : '';
	$commentdata['comment_parent'] = ( 'approved' == $parent_status || 'unapproved' == $parent_status ) ? $commentdata['comment_parent'] : 0;

	if ( ! isset( $commentdata['comment_author_IP'] ) ) {
		$commentdata['comment_author_IP'] = $_SERVER['REMOTE_ADDR'];
	}
	$commentdata['comment_author_IP'] = preg_replace( '/[^0-9a-fA-F:., ]/', '', $commentdata['comment_author_IP'] );

	if ( ! isset( $commentdata['comment_agent'] ) ) {
		$commentdata['comment_agent'] = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT']: '';
	}
	$commentdata['comment_agent'] = substr( $commentdata['comment_agent'], 0, 254 );

	if ( empty( $commentdata['comment_date'] ) ) {
		$commentdata['comment_date'] = current_time('mysql');
	}

	if ( empty( $commentdata['comment_date_gmt'] ) ) {
		$commentdata['comment_date_gmt'] = current_time( 'mysql', 1 );
	}

	$commentdata = wp_filter_comment($commentdata);

	$commentdata['comment_approved'] = wp_allow_comment( $commentdata, true );
	if ( is_wp_error( $commentdata['comment_approved'] ) ) {
		return false;
	}

	$comment_ID = wp_insert_comment($commentdata);
	if ( ! $comment_ID ) {
		$fields = array( 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content' );

		foreach ( $fields as $field ) {
			if ( isset( $commentdata[ $field ] ) ) {
				$commentdata[ $field ] = $wpdb->strip_invalid_text_for_column( $wpdb->comments, $field, $commentdata[ $field ] );
			}
		}

		$commentdata = wp_filter_comment( $commentdata );

		$commentdata['comment_approved'] = wp_allow_comment( $commentdata, true );
		if ( is_wp_error( $commentdata['comment_approved'] ) ) {
			return false;
		}

		$comment_ID = wp_insert_comment( $commentdata );
		if ( ! $comment_ID ) {
			return false;
		}
	}

	wp_set_comment_status( $comment_ID, 'spam' );

	return true;
}
