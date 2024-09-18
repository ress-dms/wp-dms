<?php
// Restrict block types
function allowed_block_types($allowed_block_types, $block_editor_context) {
    // Allowed core blocks
    $allowed_block_types = array(
        'core/media-text',
        'core/gallery',
        'core/list',
        'core/image',
        'core/file',
        'core/video',
        'core/text',
        'core/columns',
        'core/paragraph',
        'core/heading',
        'core/button',
        'wpforms/form-selector',
        'core/shortcode',
    );

    return $allowed_block_types;
}

add_filter('allowed_block_types_all', 'allowed_block_types', 10, 2);