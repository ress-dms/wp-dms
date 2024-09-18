<?php
function ct_settings_page($post_type) {
    // Get all possible supports
    $supports = array(
        'title' => true,
        'editor' => true,
        'author' => true,
        'thumbnail' => true,
        'excerpt' => true,
        'trackbacks' => true,
        'custom-fields' => true,
        'comments' => true,
        'revisions' => true,
        'page-attributes' => true,
        'post-formats' => true,
    );

    // Ensure $current_supports is an array
    $current_supports = (array) get_option("ct_supports_{$post_type}", array());

    // Shortcode settings
    $shortcode_settings = get_option("ct_shortcode_settings_{$post_type}", array(
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ));

    // Teaser settings
    $teaser_settings = get_option("ct_teaser_settings_{$post_type}", array(
        'show_title' => true,
        'show_content' => true,
        'show_featured_image' => true,
        'show_date' => true,
        'show_taxonomy' => true,
    ));
?>
    <div class="wrap">
        <h1><?php echo ucfirst($post_type); ?> Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields("ct_settings_group_{$post_type}"); ?>
            <?php do_settings_sections("ct_settings_group_{$post_type}"); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo ucfirst($post_type); ?> Supports</th>
                    <td>
                        <fieldset>
                            <?php
                            // List all possible supports
                            foreach ($supports as $support => $enabled) {
                            ?>
                                <label>
                                    <input type="checkbox" name="ct_supports_<?php echo $post_type; ?>[]" value="<?php echo esc_attr($support); ?>" <?php checked(in_array($support, $current_supports)); ?>>
                                    <?php echo ucfirst($support); ?>
                                </label><br>
                            <?php
                            }
                            ?>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php echo ucfirst($post_type); ?> Shortcode Settings</th>
                    <td>
                        <fieldset>
                            <label for="posts_per_page"><?php _e('Posts per Page'); ?></label>
                            <input type="number" name="ct_shortcode_settings_<?php echo $post_type; ?>[posts_per_page]" value="<?php echo esc_attr($shortcode_settings['posts_per_page']); ?>" /><br>

                            <label for="orderby"><?php _e('Order By'); ?></label>
                            <select name="ct_shortcode_settings_<?php echo $post_type; ?>[orderby]">
                                <option value="date" <?php selected($shortcode_settings['orderby'], 'date'); ?>><?php _e('Date'); ?></option>
                                <option value="title" <?php selected($shortcode_settings['orderby'], 'title'); ?>><?php _e('Title'); ?></option>
                                <option value="rand" <?php selected($shortcode_settings['orderby'], 'rand'); ?>><?php _e('Random'); ?></option>
                            </select><br>

                            <label for="order"><?php _e('Order'); ?></label>
                            <select name="ct_shortcode_settings_<?php echo $post_type; ?>[order]">
                                <option value="ASC" <?php selected($shortcode_settings['order'], 'ASC'); ?>><?php _e('Ascending'); ?></option>
                                <option value="DESC" <?php selected($shortcode_settings['order'], 'DESC'); ?>><?php _e('Descending'); ?></option>
                            </select>
                        </fieldset>
                    </td>
                </tr>
                <!-- Teaser settings section -->
                <tr valign="top">
                    <th scope="row"><?php echo ucfirst($post_type); ?> Teaser Settings</th>
                    <td>
                        <fieldset>
                            <label>
                                <input type="checkbox" name="ct_teaser_settings_<?php echo $post_type; ?>[show_title]" value="1" <?php checked($teaser_settings['show_title'], true); ?>>
                                <?php _e('Show Title'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="ct_teaser_settings_<?php echo $post_type; ?>[show_content]" value="1" <?php checked($teaser_settings['show_content'], true); ?>>
                                <?php _e('Show Content'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="ct_teaser_settings_<?php echo $post_type; ?>[show_featured_image]" value="1" <?php checked($teaser_settings['show_featured_image'], true); ?>>
                                <?php _e('Show Featured Image'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="ct_teaser_settings_<?php echo $post_type; ?>[show_date]" value="1" <?php checked($teaser_settings['show_date'], true); ?>>
                                <?php _e('Show Date'); ?>
                            </label><br>
                            <label>
                                <input type="checkbox" name="ct_teaser_settings_<?php echo $post_type; ?>[show_taxonomy]" value="1" <?php checked($teaser_settings['show_taxonomy'], true); ?>>
                                <?php _e('Show Taxonomy'); ?>
                        </fieldset>
                    </td>
                </tr>
            </table>
            <?php
            //submit and redirect back to list of content types
            submit_button();
            ?>
            <a href="<?php echo admin_url('admin.php?page=content-types'); ?>" class="button button-secondary">
                <?php _e('Back to Content Types'); ?>
            </a>


        </form>
    </div>
<?php
}


function ct_settings_init() {
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'names');
    foreach ($post_types as $post_type) {
        register_setting("ct_settings_group_{$post_type}", "ct_supports_{$post_type}");
        register_setting("ct_settings_group_{$post_type}", "ct_shortcode_settings_{$post_type}");
        register_setting("ct_settings_group_{$post_type}", "ct_teaser_settings_{$post_type}");
    }
}
add_action('admin_init', 'ct_settings_init');

// disable Posts
function disable_posts() {
    global $wp_post_types;
    $wp_post_types['post']->show_in_menu = false;
}
add_action('init', 'disable_posts');
