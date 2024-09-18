<?php

function register_custom_taxonomies() {
    // Check for taxonomies in the options
    $custom_taxonomies = get_option('custom_taxonomies', array());

    foreach ($custom_taxonomies as $taxonomy) {
        register_taxonomy($taxonomy['name'], $taxonomy['post_types'], array(
            'labels' => array(
                'name' => __($taxonomy['label']),
                'singular_name' => __($taxonomy['label']),
            ),
            'hierarchical' => true,
            'public' => true,
            'show_in_rest' => true,
        ));
    }
}
add_action('init', 'register_custom_taxonomies');

function register_global_taxonomies_menu() {
    add_menu_page(
        __('Global Taxonomies'),
        __('Taxonomies'),
        'manage_options',
        'global-taxonomies',
        'global_taxonomies_page_callback',
        'dashicons-category',
        3
    );
}
add_action('admin_menu', 'register_global_taxonomies_menu');

function global_taxonomies_page_callback() {
    $taxonomies = get_taxonomies(array('public' => true), 'objects');

    // Remove default taxonomies
    unset($taxonomies['post_format']);
    unset($taxonomies['post_tag']);
    unset($taxonomies['category']);

    echo '<div class="wrap">';
    echo '<h1>' . __('Global Taxonomies Overview') . '</h1>';

    // Add new taxonomy form
    echo '<h2>' . __('Add New Taxonomy') . '</h2>';
    echo '<form method="post" action="' . admin_url('admin-post.php') . '">';
    echo '<input type="hidden" name="action" value="add_taxonomy">';
    echo '<table class="form-table">';
    echo '<tr valign="top">';
    echo '<th scope="row"><label for="taxonomy_name">' . __('Taxonomy Name') . '</label></th>';
    echo '<td><input type="text" id="taxonomy_name" name="taxonomy_name" placeholder="' . __('Taxonomy Name') . '" required /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row"><label for="taxonomy_label">' . __('Taxonomy Label') . '</label></th>';
    echo '<td><input type="text" id="taxonomy_label" name="taxonomy_label" placeholder="' . __('Taxonomy Label') . '" required /></td>';
    echo '</tr>';
    echo '<tr valign="top">';
    echo '<th scope="row"><label for="taxonomy_post_types">' . __('Associated Post Types') . '</label></th>';
    echo '<td><select id="taxonomy_post_types" name="taxonomy_post_types[]" multiple required>';
    foreach (get_post_types(array('public' => true), 'objects') as $post_type) {
        if ($post_type->name === 'post' || $post_type->name === 'page' || $post_type->name === 'attachment') {
            continue;
        }
        echo '<option value="' . $post_type->name . '">' . $post_type->label . '</option>';
    }
    echo '</select></td>';
    echo '</tr>';
    echo '</table>';
    echo '<input type="submit" value="' . __('Add Taxonomy') . '" class="button button-primary" />';
    echo '</form>';
    echo '<p>&nbsp;</p>';

    if ($taxonomies) {
        echo '<table class="widefat fixed striped">';
        echo '<thead><tr>';
        echo '<th>' . __('Taxonomy Name') . '</th>';
        echo '<th>' . __('Associated Post Types') . '</th>';
        echo '<th>' . __('Actions') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($taxonomies as $taxonomy) {
            echo '<tr>';
            echo '<td><strong>' . esc_html($taxonomy->label) . '</strong></td>';
            echo '<td>' . implode(', ', $taxonomy->object_type) . '</td>';
            echo '<td><a href="' . admin_url('edit-tags.php?taxonomy=' . $taxonomy->name) . '" class="button button-primary">' . __('Manage') . '</a>';
            echo '&nbsp;';
            echo '<a href="' . admin_url('admin-post.php?action=delete_taxonomy&taxonomy=' . $taxonomy->name) . '" class="button button-danger">' . __('Delete') . '</a></td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    } else {
        echo '<p>' . __('No public taxonomies found.') . '</p>';
    }

    echo '</div>';
}

function add_taxonomy_action() {
    if (!current_user_can('manage_options') || !isset($_POST['action']) || $_POST['action'] !== 'add_taxonomy') {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if (isset($_POST['taxonomy_name'], $_POST['taxonomy_label'], $_POST['taxonomy_post_types'])) {
        $taxonomy_name = sanitize_key($_POST['taxonomy_name']);
        $taxonomy_label = sanitize_text_field($_POST['taxonomy_label']);
        $post_types = array_map('sanitize_text_field', $_POST['taxonomy_post_types']);

        // Ensure the taxonomy name is valid
        if (empty($taxonomy_name) || empty($taxonomy_label) || empty($post_types)) {
            wp_die(__('Invalid data.'));
        }

        // Save the new taxonomy to options
        $custom_taxonomies = get_option('custom_taxonomies', array());
        $custom_taxonomies[] = array(
            'name' => $taxonomy_name,
            'label' => $taxonomy_label,
            'post_types' => $post_types,
        );
        update_option('custom_taxonomies', $custom_taxonomies);

        wp_redirect(admin_url('admin.php?page=global-taxonomies'));
        exit;
    }

    wp_die(__('Invalid data.'));
}
add_action('admin_post_add_taxonomy', 'add_taxonomy_action');

function delete_taxonomy_action() {
    if (!current_user_can('manage_options') || !isset($_GET['action']) || $_GET['action'] !== 'delete_taxonomy') {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    if (isset($_GET['taxonomy'])) {
        $taxonomy = sanitize_text_field($_GET['taxonomy']);

        // Check if the taxonomy exists before attempting to delete it
        if (taxonomy_exists($taxonomy)) {
            // Get the existing custom taxonomies
            $custom_taxonomies = get_option('custom_taxonomies', array());

            // Remove the taxonomy from the list
            $custom_taxonomies = array_filter($custom_taxonomies, function ($tax) use ($taxonomy) {
                return $tax['name'] !== $taxonomy;
            });
            update_option('custom_taxonomies', $custom_taxonomies);

            wp_redirect(admin_url('admin.php?page=global-taxonomies'));
            exit;
        } else {
            wp_die(__('Invalid taxonomy.'));
        }
    } else {
        wp_die(__('No taxonomy specified.'));
    }
}
add_action('admin_post_delete_taxonomy', 'delete_taxonomy_action');
