<?php

/**
 * DMS functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package DMS
 */

/**
 * Register block styles.
 */

if (! function_exists('dms_block_styles')) :
	/**
	 * Register custom block styles
	 *
	 * @return void
	 */
	function dms_block_styles() {

		register_block_style(
			'core/details',
			array(
				'name'         => 'arrow-icon-details',
				'label'        => __('Arrow icon', 'dms'),
				/*
				 * Styles for the custom Arrow icon style of the Details block
				 */
				'inline_style' => '
				.is-style-arrow-icon-details {
					padding-top: var(--wp--preset--spacing--10);
					padding-bottom: var(--wp--preset--spacing--10);
				}

				.is-style-arrow-icon-details summary {
					list-style-type: "\2193\00a0\00a0\00a0";
				}

				.is-style-arrow-icon-details[open]>summary {
					list-style-type: "\2192\00a0\00a0\00a0";
				}',
			)
		);
		register_block_style(
			'core/post-terms',
			array(
				'name'         => 'pill',
				'label'        => __('Pill', 'dms'),
				/*
				 * Styles variation for post terms
				 * https://github.com/WordPress/gutenberg/issues/24956
				 */
				'inline_style' => '
				.is-style-pill a,
				.is-style-pill span:not([class], [data-rich-text-placeholder]) {
					display: inline-block;
					background-color: var(--wp--preset--color--base-2);
					padding: 0.375rem 0.875rem;
					border-radius: var(--wp--preset--spacing--20);
				}

				.is-style-pill a:hover {
					background-color: var(--wp--preset--color--contrast-3);
				}',
			)
		);
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __('Checkmark', 'dms'),
				/*
				 * Styles for the custom checkmark list block style
				 * https://github.com/WordPress/gutenberg/issues/51480
				 */
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
		register_block_style(
			'core/navigation-link',
			array(
				'name'         => 'arrow-link',
				'label'        => __('With arrow', 'dms'),
				/*
				 * Styles for the custom arrow nav link block style
				 */
				'inline_style' => '
				.is-style-arrow-link .wp-block-navigation-item__label:after {
					content: "\2197";
					padding-inline-start: 0.25rem;
					vertical-align: middle;
					text-decoration: none;
					display: inline-block;
				}',
			)
		);
	}
endif;

add_action('init', 'dms_block_styles');

/**
 * Enqueue block stylesheets.
 */

if (! function_exists('dms_block_stylesheets')) :
	/**
	 * Enqueue custom block stylesheets
	 *
	 * @return void
	 */
	function dms_block_stylesheets() {
		/**
		 * The wp_enqueue_block_style() function allows us to enqueue a stylesheet
		 * for a specific block. These will only get loaded when the block is rendered
		 * (both in the editor and on the front end), improving performance
		 * and reducing the amount of data requested by visitors.
		 *
		 * See https://make.wordpress.org/core/2021/12/15/using-multiple-stylesheets-per-block/ for more info.
		 */
		wp_enqueue_block_style(
			'core/button',
			array(
				'handle' => 'dms-button-style-outline',
				'src'    => get_parent_theme_file_uri('assets/css/button-outline.css'),
				'ver'    => wp_get_theme(get_template())->get('Version'),
				'path'   => get_parent_theme_file_path('assets/css/button-outline.css'),
			)
		);
	}
endif;

add_action('init', 'dms_block_stylesheets');

/**
 * Register pattern categories.
 */

if (! function_exists('dms_pattern_categories')) :
	/**
	 * Register pattern categories
	 *
	 * @return void
	 */
	function dms_pattern_categories() {

		register_block_pattern_category(
			'dms_page',
			array(
				'label'       => _x('Pages', 'Block pattern category', 'dms'),
				'description' => __('A collection of full page layouts.', 'dms'),
			)
		);
	}
endif;

add_action('init', 'dms_pattern_categories');

function register_custom_menu() {
	register_nav_menus([
		'primary_menu' => 'Primary Menu',
		'secondary_menu' => 'Secondary Menu',
		'social_menu' => 'Social Media Menu',
	]);
}

add_action('init', 'register_custom_menu');

function add_li_class($classes, $item, $args) {
	if (isset($args->li_class)) {
		$classes[] = $args->li_class;
	}

	return $classes;
}

function add_link_class($classes, $item, $args) {
	if (isset($args->link_class)) {
		$classes['class'] = $args->link_class;
	}

	return $classes;
}

add_filter('nav_menu_css_class', 'add_li_class', 1, 3);
add_filter('nav_menu_link_attributes', 'add_link_class', 1, 3);

add_theme_support('menus');

function change_post_menu_label() {
	global $menu;
	global $submenu;

	$menu[20][0] = 'Views';
	$submenu['edit.php'][5][0] = 'All Views';
	$submenu['edit.php'][10][0] = 'Add a View';
	$submenu['edit.php'][16][0] = 'View Tags';
}
add_action('admin_menu', 'change_post_menu_label');

require_once get_template_directory() . '/dms-functions/general_settings.php';
require_once get_template_directory() . '/dms-functions/ct.php';
require_once get_template_directory() . '/dms-functions/ct_settings.php';
require_once get_template_directory() . '/dms-functions/shortcode.php';
require_once get_template_directory() . '/dms-functions/guttenberg.php';
require_once get_template_directory() . '/dms-functions/image_styles.php';
require_once get_template_directory() . '/dms-functions/taxonomies.php';
