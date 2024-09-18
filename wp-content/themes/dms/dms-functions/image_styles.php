<?php

// Add admin submenu for managing image styles under Media
function image_styles_menu() {
    add_submenu_page(
        'upload.php', // Parent slug for Media menu
        __('Image Styles'),
        __('Image Styles'),
        'manage_options',
        'image-styles',
        'image_styles_page_html'
    );
}
add_action('admin_menu', 'image_styles_menu');

function image_styles_page_html() {
    global $_wp_additional_image_sizes;

    // Default sizes
    $default_sizes = array(
        'thumbnail' => array(
            'width' => get_option('thumbnail_size_w'),
            'height' => get_option('thumbnail_size_h'),
            'crop' => get_option('thumbnail_crop')
        ),
        'medium' => array(
            'width' => get_option('medium_size_w'),
            'height' => get_option('medium_size_h'),
            'crop' => get_option('medium_crop')
        ),
        'medium_large' => array(
            'width' => get_option('medium_large_size_w'),
            'height' => get_option('medium_large_size_h'),
            'crop' => get_option('medium_large_crop')
        ),
        'large' => array(
            'width' => get_option('large_size_w'),
            'height' => get_option('large_size_h'),
            'crop' => get_option('large_crop'),
        )
    );

    // Custom sizes
    $custom_sizes = $_wp_additional_image_sizes;
    var_dump(get_intermediate_image_sizes());

    // Merge arrays
    $sizes = array_merge($default_sizes, $custom_sizes);

?>
    <div class="wrap">
        <h1><?php _e('Manage Image Styles'); ?></h1>

        <form method="post" action="">
            <input type="hidden" name="action" value="update_sizes">
            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php _e('Name'); ?></th>
                        <th><?php _e('Width'); ?></th>
                        <th><?php _e('Height'); ?></th>
                        <th><?php _e('Crop'); ?></th>
                        <th><?php _e('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($sizes as $name => $size_data) {
                    ?>
                        <tr>
                            <td>
                                <?php if (!array_key_exists($name, $default_sizes)) : ?>
                                    <input type="text" name="size[<?php echo esc_attr($name); ?>][name]" value="<?php echo esc_attr($name); ?>" />
                                <?php else : ?>
                                    <?php echo esc_html($name); ?>
                                <?php endif; ?>
                            </td>
                            <td><input type="number" name="size[<?php echo esc_attr($name); ?>][width]" value="<?php echo esc_attr($size_data['width']); ?>" /></td>
                            <td><input type="number" name="size[<?php echo esc_attr($name); ?>][height]" value="<?php echo esc_attr($size_data['height']); ?>" /></td>
                            <td><input type="checkbox" name="size[<?php echo esc_attr($name); ?>][crop]" value="1" <?php checked($size_data['crop']); ?> /></td>
                            <td>
                                <?php if (!array_key_exists($name, $default_sizes)) : ?>
                                    <a href="?page=image-styles&action=remove_size&size=<?php echo esc_attr($name); ?>" class="button button-danger"><?php _e('Remove'); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php submit_button(__('Save Changes')); ?>
        </form>

        <h2><?php _e('Add New Image Size'); ?></h2>
        <form method="post" action="">
            <input type="hidden" name="action" value="add_size">
            <table class="form-table">
                <tr>
                    <th scope="row"><?php _e('Image Size Name'); ?></th>
                    <td><input type="text" id="new_size_name" name="new_size_name" required></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Width'); ?></th>
                    <td><input type="number" id="new_size_width" name="new_size_width" required></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Height'); ?></th>
                    <td><input type="number" id="new_size_height" name="new_size_height" required></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('Crop'); ?></th>
                    <td>
                        <input type="checkbox" id="new_size_crop" name="new_size_crop" value="1">
                        <label for="new_size_crop"><?php _e('Crop Image'); ?></label>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Add Size')); ?>
        </form>
    </div>
<?php
}

// Handle adding, removing, and updating image sizes
function image_styles_handle_actions() {
    if (isset($_GET['action']) && $_GET['action'] === 'remove_size' && isset($_GET['size'])) {
        $size_name = sanitize_text_field($_GET['size']);
        remove_image_size($size_name);
        delete_option("image_size_{$size_name}");
        wp_redirect(admin_url('admin.php?page=image-styles'));
        exit;
    }

    if (
        isset($_POST['action']) && $_POST['action'] === 'add_size' &&
        isset($_POST['new_size_name']) &&
        isset($_POST['new_size_width']) &&
        isset($_POST['new_size_height'])
    ) {
        $name = sanitize_text_field($_POST['new_size_name']);
        $width = intval($_POST['new_size_width']);
        $height = intval($_POST['new_size_height']);
        $crop = isset($_POST['new_size_crop']) && $_POST['new_size_crop'] === '1';
        add_image_size($name);
        // Store image size metadata
        update_option("image_size_{$name}", array(
            'width' => $width,
            'height' => $height,
            'crop' => $crop
        ));

        wp_redirect(admin_url('admin.php?page=image-styles'));
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_sizes' && isset($_POST['size'])) {
        $sizes = $_POST['size'];
        foreach ($sizes as $old_name => $size_data) {
            $name = sanitize_text_field($size_data['name']);
            $width = intval($size_data['width']);
            $height = intval($size_data['height']);
            $crop = isset($size_data['crop']) && $size_data['crop'] === '1';

            // Remove old size if name changed
            if ($name !== $old_name) {
                remove_image_size($old_name);
                add_image_size($name, $width, $height, $crop);
                delete_option("image_size_{$old_name}");
            } else {
                // Update existing size
                remove_image_size($name);
                add_image_size($name, $width, $height, $crop);
            }

            // Store image size metadata
            update_option("image_size_{$name}", array(
                'width' => $width,
                'height' => $height,
                'crop' => $crop
            ));
        }
        wp_redirect(admin_url('admin.php?page=image-styles'));
        exit;
    }
}
add_action('admin_init', 'image_styles_handle_actions');
