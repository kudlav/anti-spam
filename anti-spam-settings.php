<?php
/*
Anti-spam Reloaded settings code
used WordPress Settings API - http://codex.wordpress.org/Settings_API
*/

if ( ! defined( 'ABSPATH' ) ) { // Avoid direct calls to this file and prevent full path disclosure
	exit;
}


function antispamrel_menu() { // add menu item
	add_options_page('Anti-spam Reloaded', 'Anti-spam Reloaded', 'manage_options', 'anti-spam-reloaded', 'antispamrel_settings');
}
add_action('admin_menu', 'antispamrel_menu');


function antispamrel_admin_init() {
	register_setting('antispamrel_settings_group', 'antispamrel_settings');

	add_settings_section('antispamrel_settings_automatic_section', '', 'antispamrel_section_callback', 'antispamrel_automatic_page');

	add_settings_field('save_spam_comments', 'Save spam comments', 'antispamrel_field_save_spam_comments_callback', 'antispamrel_automatic_page', 'antispamrel_settings_automatic_section');

}
add_action('admin_init', 'antispamrel_admin_init');


function antispamrel_settings_init() { // set default settings
	update_option('antispamrel_settings', antispamrel_get_settings());
}
add_action('admin_init', 'antispamrel_settings_init');


function antispamrel_section_callback() { // Anti-spam settings description
	echo '';
}


function antispamrel_field_save_spam_comments_callback() {
	$settings = antispamrel_get_settings();
	echo '<label><input type="checkbox" name="antispamrel_settings[save_spam_comments]" ', checked(1, $settings['save_spam_comments'], false), ' value="1" />',
		' Save spam comments into spam section</label>',
		'<p class="description">Useful for testing how the plugin works. <a href="', admin_url( 'edit-comments.php?comment_status=spam' ), '">View spam section</a>.</p>';
}


function antispamrel_settings() {
	$blocked_total = 0; // show 0 by default
	$antispam_stats = get_option('antispamrel_stats', array());
	if (isset($antispam_stats['blocked_total'])) {
		$blocked_total = esc_html($antispam_stats['blocked_total']);
	}
	?>
	<div class="wrap">
		<h2><span class="dashicons dashicons-admin-generic"></span> Anti-spam Reloaded</h2>
		<p>
			<span class="dashicons dashicons-chart-bar"></span>
			<strong><?php echo $blocked_total; ?></strong> spam comments were blocked by <a href="https://wordpress.org/plugins/anti-spam-reloaded/" target="_blank" rel="noreferrer">Anti-spam Reloaded</a>
		</p>
		<form method="post" action="options.php">
		<?php
			settings_fields('antispamrel_settings_group');
			do_settings_sections('antispamrel_automatic_page');
			submit_button();
		?>
		</form>
	</div>
	<?php
}
