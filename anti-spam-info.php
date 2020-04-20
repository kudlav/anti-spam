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
				$blocked_total = esc_html($antispam_stats['blocked_total']);
			}
			?>
			<div class="notice notice-info">
				<p>
					<?php echo $blocked_total; ?> spam comments were blocked by <a href="http://wordpress.org/plugins/anti-spam-reloaded/">Anti-spam Reloaded</a> plugin so far.
				</p>
			</div>
			<?php
		}
	}
}
add_action('admin_notices', 'antispamrel_admin_notice');


function antispamrel_display_screen_option() {
	global $pagenow;
	if ($pagenow == 'edit-comments.php') {
		$user_id = get_current_user_id();
		$antispam_info_visibility = get_user_meta($user_id, 'antispamrel_info_visibility', true);

		if ($antispam_info_visibility == 1 OR $antispam_info_visibility == '') {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		?>
		<script>
			jQuery(function($){
				$('.antispamrel_screen_options_group').insertAfter('#screen-options-wrap #adv-settings');
			});
		</script>
		<form method="post" class="antispamrel_screen_options_group" style="padding-top:20px;">
			<input type="hidden" name="antispamrel_option_submit" value="1" />
			<label>
				<input name="antispamrel_info_visibility" type="checkbox" value="1" <?php echo $checked; ?> />
				Anti-spam Reloaded info
			</label>
			<input type="submit" class="button" value="<?php _e('Apply'); ?>" />
		</form>
		<?php
	}
}


function antispamrel_register_screen_option() {
	add_filter('screen_layout_columns', 'antispamrel_display_screen_option');
}
add_action('admin_head', 'antispamrel_register_screen_option');


function antispamrel_update_screen_option() {
	if (isset($_POST['antispamrel_option_submit']) AND $_POST['antispamrel_option_submit'] == 1) {
		$user_id = get_current_user_id();
		if (isset($_POST['antispamrel_info_visibility']) AND $_POST['antispamrel_info_visibility'] == 1) {
			update_user_meta($user_id, 'antispamrel_info_visibility', 1);
		} else {
			update_user_meta($user_id, 'antispamrel_info_visibility', 0);
		}
	}
}
add_action('admin_init', 'antispamrel_update_screen_option');
