<?php
/*
Anti-spam Reloaded plugin
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}

function antispamrel_admin_notice() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php') {
		$user_id = get_current_user_id();
		$antispam_info_visibility = get_user_meta($user_id, 'antispamrel_info_visibility', true);
		if ($antispam_info_visibility == 1 OR $antispam_info_visibility == '') {
			$blocked_total = 0; // show 0 by default
			$antispam_stats = get_option('antispamrel_stats', array());
			if (isset($antispam_stats['blocked_total'])) {
				$blocked_total = $antispam_stats['blocked_total'];
			}

			esc_html(printf(
				'<div class="notice notice-info"><p>' .
				/* translators: 1: number of blocked comments, 2: plugin name. */
				_n(
					'%1$s spam comment was blocked by %2$s plugin so far',
					'%1$s spam comments were blocked by %2$s plugin so far',
					$blocked_total,
					'anti-spam-reloaded'
				) .
				'</p></div>',
				number_format_i18n($blocked_total),
				'<a href="https://wordpress.org/plugins/anti-spam-reloaded" target="_blank" rel="noreferrer">Anti-spam Reloaded</a>'
			));
		}
	}
}
add_action('admin_notices', 'antispamrel_admin_notice');


function antispamrel_display_screen_option() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php') {
		$user_id = get_current_user_id();
		$antispam_info_visibility = get_user_meta($user_id, 'antispamrel_info_visibility', true);

		$checked = checked(($antispam_info_visibility == 1 || $antispam_info_visibility == ''), true, false);
		$nonce = esc_textarea(wp_create_nonce('antispamrel_info_nonce'));

		echo '
			<form method="post" id="antispamrel_screen_options_group">
				<fieldset>
					<legend>Anti-spam Reloaded</legend>
					<input type="hidden" name="antispamrel_info_nonce" value="', $nonce, '" />
					<label>
						<input name="antispamrel_info_visibility" type="checkbox" value="1" ', $checked, ' />
						', esc_html__('Show number of blocked comments', 'anti-spam-reloaded'), '
					</label>
					<input type="submit" class="button" value="', esc_attr__('Apply', 'anti-spam-reloaded'), '" />
				</fieldset>
			</form>
			<script>
				document.onreadystatechange = function () {
					if (document.readyState === "complete") {
						const antspmrl_advsett = document.getElementById(\'screen-options-wrap\');
						const antspmrl_advopts = document.getElementById(\'antispamrel_screen_options_group\');
						antspmrl_advsett.appendChild(antspmrl_advopts);
					}
				}
			</script>
		';
	}
}


function antispamrel_register_screen_option() {
	add_filter('screen_layout_columns', 'antispamrel_display_screen_option');
}
add_action('admin_head', 'antispamrel_register_screen_option');


function antispamrel_update_screen_option() {
	if (isset($_POST['antispamrel_info_nonce']) &&
		wp_verify_nonce(sanitize_text_field($_POST['antispamrel_info_nonce']), 'antispamrel_info_nonce')
	) {
		$user_id = get_current_user_id();
		if ( isset($_POST['antispamrel_info_visibility']) && $_POST['antispamrel_info_visibility'] == 1 ) {
			update_user_meta($user_id, 'antispamrel_info_visibility', 1);
		} else {
			update_user_meta($user_id, 'antispamrel_info_visibility', 0);
		}
	}
}
add_action('admin_init', 'antispamrel_update_screen_option');
