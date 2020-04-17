<?php
/*
Anti-spam Reloaded settings code
used WordPress Settings API - http://codex.wordpress.org/Settings_API
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}


function antispam_menu() { // add menu item
	add_options_page('Anti-spam Reloaded', 'Anti-spam Reloaded', 'manage_options', 'anti-spam', 'antispam_settings');
}
add_action('admin_menu', 'antispam_menu');


function antispam_admin_init() {
	register_setting('antispam_settings_group', 'antispam_settings', 'antispam_settings_validate');

	add_settings_section('antispam_settings_automatic_section', '', 'antispam_section_callback', 'antispam_automatic_page');

	add_settings_field('save_spam_comments', 'Save spam comments', 'antispam_field_save_spam_comments_callback', 'antispam_automatic_page', 'antispam_settings_automatic_section');

}
add_action('admin_init', 'antispam_admin_init');


function antispam_settings_init() { // set default settings
	global $antispam_settings;
	$antispam_settings = antispam_get_settings();
	update_option('antispam_settings', $antispam_settings);
}
add_action('admin_init', 'antispam_settings_init');


function antispam_settings_validate($input) {
	$default_settings = antispam_get_settings();

	// checkbox
	$output['save_spam_comments'] = $input['save_spam_comments'];

	return $output;
}


function antispam_section_callback() { // Anti-spam settings description
	echo '';
}


function antispam_field_save_spam_comments_callback() {
	$settings = antispam_get_settings();
	echo '<label><input type="checkbox" name="antispam_settings[save_spam_comments]" '.checked(1, $settings['save_spam_comments'], false).' value="1" />';
	echo ' Save spam comments into spam section</label>';
	echo '<p class="description">Useful for testing how the plugin works. <a href="'. admin_url( 'edit-comments.php?comment_status=spam' ) . '">View spam section</a>.</p>';
}


function antispam_settings() {
	$antispam_stats = get_option('antispam_stats', array());
	$blocked_total = $antispam_stats['blocked_total'];
	if (empty($blocked_total)) {
		$blocked_total = 0;
	}
	?>
	<div class="wrap">

		<h2><span class="dashicons dashicons-admin-generic"></span> Anti-spam Reloaded</h2>

		<p>
			<span class="dashicons dashicons-chart-bar"></span>
			<strong><?php echo $blocked_total; ?></strong> spam comments were blocked by <a href="https://wordpress.org/plugins/anti-spam-reloaded/" target="_blank">Anti-spam Reloaded</a>
		</p>

		<form method="post" action="options.php">
			<?php settings_fields('antispam_settings_group'); ?>
			<div class="antispam-group-automatic">
				<?php do_settings_sections('antispam_automatic_page'); ?>
			</div>
			<?php submit_button(); ?>
		</form>

	</div>
	<?php
}
