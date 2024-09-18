<?php

// Function to register custom post types
function register_ct() {
    $post_types = array(
        'article' => array(
            'name' => __('Articles', 'Article'),
        ),
        'event' => array(
            'name' => __('Events', 'Event'),
        ),
        'location' => array(
            'name' => __('Locations', 'Location'),
        ),
    );

    update_option('custom_post_types', $post_types);
    flush_rewrite_rules();
}

// Register custom post types
add_action('init', 'register_ct');

// Function to register saved custom post types on every load
function register_custom_post_types() {
    $post_types = get_option('custom_post_types', array());

    if (is_array($post_types)) {
        foreach ($post_types as $post_type => $details) {
            if (is_array($details)) {
                register_post_type($post_type, array(
                    'labels' => array(
                        'name' => $details['name'],
                        'singular_name' => ucfirst($post_type),
                        'add_new' => __('Add New', ucfirst($post_type)),
                        'add_new_item' => __("Add New " . ucfirst($post_type), ucfirst($post_type)),
                        'edit_item' => __("Edit " . ucfirst($post_type), ucfirst($post_type)),
                        'new_item' => __("New " . ucfirst($post_type), ucfirst($post_type)),
                        'view_item' => __("View " . ucfirst($post_type), ucfirst($post_type)),
                        'search_items' => __("Search " . ucfirst($post_type), ucfirst($post_type)),
                        'not_found' => __("No " . $post_type . " found", ucfirst($post_type)),
                        'not_found_in_trash' => __("No " . $post_type . " found in Trash", ucfirst($post_type)),
                        'menu_name' => $details['name'],
                    ),
                    'public' => true,
                    'has_archive' => true,
                    'rewrite' => array('slug' => $post_type . 's'),
                    'show_in_rest' => true,
                    'show_in_menu' => false,
                    'supports' => get_option("ct_supports_{$post_type}", array('title', 'editor', 'thumbnail')),
                ));
            } else {
                error_log("Error: Details for post type $post_type are not an array.");
            }
        }
    } else {
        error_log("Error: post_types is not an array.");
    }
}

add_action('init', 'register_custom_post_types');

// Display the custom page for "Content Types"
function content_types_page() {
    $post_types = get_option('custom_post_types', array());

    if (isset($_POST['new_content_type'])) {
        $new_post_type = sanitize_text_field($_POST['new_content_type']);
        $new_post_type_singular = ucfirst($new_post_type);

        if (!is_array($post_types)) {
            $post_types = array();
        }

        $post_types[$new_post_type] = array(
            'name' => $new_post_type_singular . 's',
            'singular_name' => $new_post_type_singular
        );

        update_option('custom_post_types', $post_types);

        flush_rewrite_rules();
    }

    if (isset($_POST['remove_content_type'])) {
        $remove_post_type = sanitize_text_field($_POST['remove_content_type']);

        if (is_array($post_types) && isset($post_types[$remove_post_type])) {
            unset($post_types[$remove_post_type]);

            update_option('custom_post_types', $post_types);

            flush_rewrite_rules();
        }
    }
?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Content Types</h1>

        <h2>Add New Content Type</h2>
        <form method="post" action="">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">
                        <label for="new_content_type">Content Type Name</label>
                    </th>
                    <td>
                        <input type="text" id="new_content_type" name="new_content_type" placeholder="Content Type Name" required />
                    </td>
                </tr>
            </table>
            <input type="submit" value="Add Content Type" class="button button-primary" />
        </form>

        <h2>Existing Content Types</h2>
        <?php if ($post_types) : ?>
            <table class="form-table striped">
                <thead>
                    <tr>
                        <th><?php _e('Content Type'); ?></th>
                        <th><?php _e('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($post_types as $post_type => $details) : ?>
                        <tr>
                            <td>
                                <strong><?php echo ucfirst($post_type); ?></strong>
                            </td>
                            <td>
                                <a href="<?php echo admin_url("admin.php?page=settings-{$post_type}"); ?>" class="button button-primary">
                                    <?php _e('Edit'); ?>
                                </a>
                                <form method="post" action="" style="display:inline;">
                                    <input type="hidden" name="remove_content_type" value="<?php echo esc_attr($post_type); ?>" />
                                    <input type="submit" value="<?php _e('Remove'); ?>" class="button button-secondary" onclick="return confirm('Are you sure you want to remove this content type?');" />
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><?php _e('No custom content types found.'); ?></p>
        <?php endif; ?>
    </div>
<?php
}

// Register admin menu and submenus for content types
function register_custom_admin_menu() {
    add_menu_page(
        'Content Types',
        'Content Types',
        'manage_options',
        'content-types',
        'content_types_page',
        'dashicons-admin-generic',
        3
    );

    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
    foreach ($post_types as $post_type) {
        add_submenu_page(
            null,
            'Add ' . ucfirst($post_type),
            'Add ' . ucfirst($post_type),
            'manage_options',
            "post-new.php?post_type={$post_type}"
        );

        add_submenu_page(
            null,
            ucfirst($post_type) . ' Settings',
            ucfirst($post_type),
            'manage_options',
            "settings-{$post_type}",
            function () use ($post_type) {
                ct_settings_page($post_type);
            },
        );
        add_submenu_page(
            'content-types',
            ucfirst($post_type) . 's',
            ucfirst($post_type) . 's',
            'manage_options',
            "edit.php?post_type={$post_type}"
        );
    }
}

add_action('admin_menu', 'register_custom_admin_menu');
