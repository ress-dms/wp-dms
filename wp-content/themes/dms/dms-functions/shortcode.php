<?php
function ct_shortcode($atts) {
    // Shortcode attributes
    $atts = shortcode_atts(array(
        'type' => 'article',
    ), $atts);

    // Retrieve settings from the options
    $type = $atts['type'];
    $shortcode_settings = get_option("ct_shortcode_settings_{$type}", array(
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    $teaser_settings = get_option("ct_teaser_settings_{$type}", array(
        'show_title' => true,
        'show_content' => true,
        'show_featured_image' => true,
        'show_author' => true,
        'show_date' => true,
        'show_taxonomy' => true,
    ));

    ob_start();

    $args = array(
        'post_type' => $type,
        'posts_per_page' => $shortcode_settings['posts_per_page'],
        'orderby' => $shortcode_settings['orderby'],
        'order' => $shortcode_settings['order'],
    );
    $query = new WP_Query($args);

    if ($query->have_posts()) :
        while ($query->have_posts()) : $query->the_post();
?>
            <div class="ct-teaser">
                <?php if ($teaser_settings['show_featured_image'] && has_post_thumbnail()) : ?>
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('medium'); ?></a>
                <?php endif; ?>

                <?php if ($teaser_settings['show_title']) : ?>
                    <a href="<?php the_permalink(); ?>">
                        <h2><?php the_title(); ?></h2>
                    </a>
                <?php endif; ?>

                <?php if ($teaser_settings['show_date']) : ?>
                    <p class="date"><?php echo get_the_date(); ?></p>
                <?php endif; ?>

                <?php if ($teaser_settings['show_content']) : ?>
                    <div class="content"><?php the_excerpt(); ?></div>
                <?php endif; ?>

                <?php if ($teaser_settings['show_taxonomy']) : ?>
                    <?php
                    // Display taxonomies linked to the post
                    $taxonomies = get_object_taxonomies($type, 'objects');
                    foreach ($taxonomies as $taxonomy) {
                        $terms = get_the_terms(get_the_ID(), $taxonomy->name);
                        if ($terms && !is_wp_error($terms)) {
                            $terms_list = array();
                            foreach ($terms as $term) {
                                $terms_list[] = '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                            }
                            echo '<p class="taxonomy">' . sprintf(__('%s: %s'), esc_html($taxonomy->label), implode(', ', $terms_list)) . '</p>';
                        }
                    }
                    ?>
                <?php endif; ?>

            </div>
<?php
        endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No content found</p>';
    endif;

    return ob_get_clean();
}
add_shortcode('ct', 'ct_shortcode');
