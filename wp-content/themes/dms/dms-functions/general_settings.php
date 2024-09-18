<?php

function dms_add_admin_menu() {
    add_menu_page(
        'DM&S General Settings', // Page title
        'DM&S', // Menu title
        'manage_options', // Capability
        'dms_general_settings', // Menu slug
        'dms_general_settings_page', // Callback function
        get_template_directory_uri() . '/assets/images/favicon-16x16.png', // Custom image URL
        2 // Position
    );

    add_submenu_page(
        'dms_general_settings', // Parent slug
        'General Settings', // Page title
        'General', // Menu title
        'manage_options', // Capability
        'dms_general_settings', // Menu slug
        'dms_general_settings_page' // Callback function
    );

    add_submenu_page(
        'dms_general_settings', // Parent slug
        'Social Settings', // Page title
        'Social', // Menu title
        'manage_options', // Capability
        'dms_social_settings', // Menu slug
        'dms_social_settings_page' // Callback function
    );

    add_submenu_page(
        'dms_general_settings', // Parent slug
        'Address Settings', // Page title
        'Address', // Menu title
        'manage_options', // Capability
        'dms_address_settings', // Menu slug
        'dms_address_settings_page' // Callback function
    );
}
add_action('admin_menu', 'dms_add_admin_menu');

function dms_general_settings_page() {
?>
    <div class="wrap">
        <h1>DM&S General Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dms_general_settings_group');
            do_settings_sections('dms_general_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Language</th>
                    <td>
                        <select name="dms_language">
                            <option value="en_GB" lang="en_GB" <?php selected(get_option('dms_language'), 'en_GB'); ?>>English</option>
                            <option value="nl_NL" lang="nl_NL" <?php selected(get_option('dms_language'), 'nl_NL'); ?>>Dutch</option>
                            <option value="fr_FR" lang="fr_FR" <?php selected(get_option('dms_language'), 'fr_FR'); ?>>French</option>
                        </select>
                </tr>
                <tr valign="top">
                    <th scope="row">Site Name</th>
                    <td><input type="text" name="dms_site_name" value="<?php echo esc_attr(get_option('dms_site_name')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">VAT Number</th>
                    <td><input type="text" name="dms_vat_number" value="<?php echo esc_attr(get_option('dms_vat_number')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Phone</th>
                    <td><input type="text" name="dms_phone" value="<?php echo esc_attr(get_option('dms_phone')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Email</th>
                    <td><input type="email" name="dms_email" value="<?php echo esc_attr(get_option('dms_email')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function dms_social_settings_page() {
?>
    <div class="wrap">
        <h1>Social Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dms_social_settings_group');
            do_settings_sections('dms_social_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Facebook URL</th>
                    <td><input type="text" name="dms_social_facebook" value="<?php echo esc_attr(get_option('dms_social_facebook')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Instagram URL</th>
                    <td><input type="text" name="dms_social_instagram" value="<?php echo esc_attr(get_option('dms_social_instagram')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">LinkedIn URL</th>
                    <td><input type="text" name="dms_social_linkedin" value="<?php echo esc_attr(get_option('dms_social_linkedin')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Twitter URL</th>
                    <td><input type="text" name="dms_social_twitter" value="<?php echo esc_attr(get_option('dms_social_twitter')); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row">YouTube URL</th>
                    <td><input type="text" name="dms_social_youtube" value="<?php echo esc_attr(get_option('dms_social_youtube')); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function dms_address_settings_page() {
?>
    <div class="wrap">
        <h1>Address Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('dms_address_settings_group');
            do_settings_sections('dms_address_settings');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Physical Address</th>
                    <td><textarea name="dms_address"><?php echo esc_textarea(get_option('dms_address')); ?></textarea></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
<?php
}

function dms_settings_init() {
    // General Settings
    register_setting('dms_general_settings_group', 'dms_language', 'dms_update_language');
    register_setting('dms_general_settings_group', 'dms_site_name', 'dms_update_site_title');
    register_setting('dms_general_settings_group', 'dms_vat_number');
    register_setting('dms_general_settings_group', 'dms_phone');
    register_setting('dms_general_settings_group', 'dms_email', 'dms_update_admin_email');

    // Social Settings
    register_setting('dms_social_settings_group', 'dms_social_facebook');
    register_setting('dms_social_settings_group', 'dms_social_instagram');
    register_setting('dms_social_settings_group', 'dms_social_linkedin');
    register_setting('dms_social_settings_group', 'dms_social_twitter');
    register_setting('dms_social_settings_group', 'dms_social_youtube');

    // Address Settings
    register_setting('dms_address_settings_group', 'dms_address');

    add_settings_field('dms_address', 'Physical Address', 'dms_address_field', 'dms_address_settings', 'dms_address_settings_section');
}
add_action('admin_init', 'dms_settings_init');

function dms_site_name_field() {
?>
    <input type="text" name="dms_site_name" value="<?php echo esc_attr(get_option('dms_site_name')); ?>" />
<?php
}

function dms_vat_number_field() {
?>
    <input type="text" name="dms_vat_number" value="<?php echo esc_attr(get_option('dms_vat_number')); ?>" />
<?php
}

function dms_phone_field() {
?>
    <input type="text" name="dms_phone" value="<?php echo esc_attr(get_option('dms_phone')); ?>" />
<?php
}

function dms_email_field() {
?>
    <input type="email" name="dms_email" value="<?php echo esc_attr(get_option('dms_email')); ?>" />
<?php
}

function dms_social_facebook_field() {
?>
    <input type="text" name="dms_social_facebook" value="<?php echo esc_attr(get_option('dms_social_facebook')); ?>" />
<?php
}

function dms_social_instagram_field() {
?>
    <input type="text" name="dms_social_instagram" value="<?php echo esc_attr(get_option('dms_social_instagram')); ?>" />
<?php
}

function dms_social_linkedin_field() {
?>
    <input type="text" name="dms_social_linkedin" value="<?php echo esc_attr(get_option('dms_social_linkedin')); ?>" />
<?php
}

function dms_social_twitter_field() {
?>
    <input type="text" name="dms_social_twitter" value="<?php echo esc_attr(get_option('dms_social_twitter')); ?>" />
<?php
}

function dms_social_youtube_field() {
?>
    <input type="text" name="dms_social_youtube" value="<?php echo esc_attr(get_option('dms_social_youtube')); ?>" />
<?php
}

function dms_address_field() {
?>
    <textarea name="dms_address"><?php echo esc_textarea(get_option('dms_address')); ?></textarea>
<?php
}

function dms_update_site_title($value) {
    // Update the WordPress Site Title (blogname) with the new 'Site Name' value
    update_option('blogname', sanitize_text_field($value));

    return $value; // Return the value so it is saved as 'dms_site_name'
}
// update Administration Email Address with the new 'Email' value
function dms_update_admin_email($value) {
    update_option('admin_email', sanitize_email($value));

    return $value;
}

// update language
function dms_update_language($value) {
    update_option('WPLANG', sanitize_text_field($value));
    return $value;
}
