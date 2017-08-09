<?php
function kapost_byline_settings_url()
{
	return admin_url('options-general.php?page=kapost_settings');
}

function kapost_byline_settings()
{
	$defaults = array('attr_create_user' => 'on', 'attr_update_user_bio' => 'on', 'analytics' => 'on');
	return wp_parse_args((array) get_option(KAPOST_BYLINE_DEFAULT_SETTINGS_KEY), $defaults);
}

function kapost_byline_settings_update($settings)
{
	if(!is_array($settings)) $settings = array();
	update_option(KAPOST_BYLINE_DEFAULT_SETTINGS_KEY,$settings);	
}

function kapost_byline_settings_menu() 
{
	if(function_exists("add_submenu_page"))
	    add_submenu_page('options-general.php','Kapost Settings', 'Kapost Settings', 'manage_options', 'kapost_settings', 'kapost_byline_settings_options');
}

function kapost_byline_page_settings_link($links, $file) 
{
	if($file == KAPOST_BYLINE_BASENAME) 
	{
		$link = '<a href="'.kapost_byline_settings_url().'">Settings</a>';
		array_unshift($links, $link); 
	}

	return $links;
}

function kapost_byline_settings_checkbox($instance, $name, $label)
{
	$state = ($instance[$name] == 'on') ?' checked="checked"' : '';
	return '<blockquote><input type="checkbox" name="' . KAPOST_BYLINE_DEFAULT_SETTINGS_KEY . '[' . $name  . ']" ' . $state . '/> ' . $label . '</blockquote>';
}

function kapost_byline_settings_form($instance)
{
	$attr_options = '<h3>Attribution Options</h3>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_create_user', 'Create a new WordPress user for each promoted user unless their account (based on email) already exists.');
	$attr_options .= '<blockquote>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_user_bio', 'Update new Wordpress user\'s bio based on promoted user.');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_user_meta', 'Update new Wordpress user\'s metadata based on promoted user.');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_user_photo', 'Update new Wordpress user\'s <b>*</b>profile photo (avatar) based on promoted user.');
	$attr_options .= '</blockquote>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_existing_user_bio', 'Update existing Wordpress user\'s bio based on promoted user.');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_existing_user_meta', 'Update existing Wordpress user\'s metadata based on promoted user.');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'attr_update_existing_user_photo', 'Update existing Wordpress user\'s <b>*</b>profile photo (avatar) based on promoted user.');
	$attr_options .= '<blockquote><b>* this feature requires the <a href="http://wordpress.org/plugins/user-photo/">user-photo</a> plugin</b></blockquote>';
	$attr_options .= '<h3>Custom Field Options</h3>';
	$attr_options .= '<blockquote>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'image_custom_fields', 'Image Custom fields');
	$attr_options .= '</blockquote>';
	$attr_options .= '<h3>Preview Options</h3>';
	$attr_options .= '<blockquote>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'preview', 'Enable Preview');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'preview_byline', 'Enable Attribution Preview');
	$attr_options .= '</blockquote>';
	$attr_options .= '<h3>Expert Options</h3>';
	$attr_options .= '<blockquote>';
	$attr_options .= kapost_byline_settings_checkbox($instance, 'analytics', 'Insert Analytics Tracking Code');
	$attr_options .= kapost_byline_settings_checkbox($instance, 'sql_server_compat', 'SQL Server Compatibility');
	$attr_options .= '</blockquote>';

	echo '
		<form action="" method="post" autocomplete="off" id="options_form">
		'.$attr_options.'
		<blockquote>
			<p class="submit">
				<input type="submit" value="Update Settings" id="submit" class="button-primary" name="submit"/>
			</p>
		</form>
	</div>';
}

function kapost_byline_message($msg, $style="updated")
{
	echo "<div class=\"${style} fade\" id=\"message\"><p><strong>{$msg}</strong></p></div>";
}

function kapost_byline_settings_form_update($new_instance, $old_instance)
{
	if(!is_array($new_instance)) $new_instance = array();

	$instance = array(
		'attr_create_user' => '',
		'attr_update_user_meta' => '',
		'attr_update_user_photo' => '',
		'attr_update_user_bio' => '',
		'attr_update_existing_user_meta' => '',
		'attr_update_existing_user_photo' => '',
		'attr_update_existing_user_bio' => '',
		'image_custom_fields' => '',
		'preview' => '',
		'preview_byline' => '',
		'sql_server_compat' => '',
		'analytics' => ''
	);

	foreach($instance as $k => $v)
		if($new_instance[$k] == 'on')
			$instance[$k] = 'on';

	kapost_byline_settings_update($instance);
	kapost_byline_message("Settings successfully updated.");
	return $instance;
}

function kapost_byline_settings_options() 
{
    if(!current_user_can('manage_options'))  
        wp_die('You do not have sufficient permissions to access this page.');

	$old_instance = kapost_byline_settings();

	echo '<div class="wrap"><h2>Kapost Settings</h2>';
	
	if(isset($_POST['submit']))
		$old_instance = kapost_byline_settings_form_update($_POST[KAPOST_BYLINE_DEFAULT_SETTINGS_KEY], $old_instance);

	echo '<div>';

	kapost_byline_settings_form($old_instance);

	echo '</div>';
}

add_action('admin_menu', 'kapost_byline_settings_menu');
add_filter('plugin_action_links', 'kapost_byline_page_settings_link', 10, 2);
?>
