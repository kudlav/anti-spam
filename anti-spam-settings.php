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

	add_settings_field('save_spam_comments', __('Save spam comments', 'anti-spam-reloaded'), 'antispamrel_field_save_spam_comments_callback', 'antispamrel_automatic_page', 'antispamrel_settings_automatic_section');

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
	$checked = checked(1, $settings['save_spam_comments'], false);
	echo '
		<label>
			<input type="checkbox" name="antispamrel_settings[save_spam_comments]" ', $checked, ' value="1" /> ',
			esc_html__('Save spam comments into spam section', 'anti-spam-reloaded'),
		'</label>',
		'<p class="description">', esc_html__('Useful for testing how the plugin works', 'anti-spam-reloaded'), '. ',
			'<a href="', admin_url( 'edit-comments.php?comment_status=spam' ), '">',
				esc_html__('View spam section', 'anti-spam-reloaded'),
			'</a>.
		</p>';
}


function antispamrel_settings() {
	$blocked_total = 0; // show 0 by default
	$antispam_stats = get_option('antispamrel_stats', array());
	if (isset($antispam_stats['blocked_total'])) {
		$blocked_total = $antispam_stats['blocked_total'];
	}

	?>
		<div class="wrap">
			<h2><span class="dashicons dashicons-admin-generic"></span> Anti-spam Reloaded</h2>
			<p>
				<span class="dashicons dashicons-chart-bar"></span>
				<?php
					esc_html(printf(
						/* translators: 1: number of blocked comments, 2: plugin name. */
						_n(
							'%1$s spam comment was blocked by %2$s plugin so far',
							'%1$s spam comments were blocked by %2$s plugin so far',
							$blocked_total,
							'anti-spam-reloaded'
						),
						number_format_i18n($blocked_total),
						'<a href="https://wordpress.org/plugins/anti-spam-reloaded" target="_blank" rel="noreferrer">Anti-spam Reloaded</a>'
					));
				?>
			</p>
			<form method="post" action="options.php">
				<?php
					settings_fields('antispamrel_settings_group');
					do_settings_sections('antispamrel_automatic_page');
					submit_button()
				?>
			</form>
		</div>
	<?php
}
