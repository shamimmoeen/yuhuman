<?php
/**
 * Yuhuman Theme functions and definitions.
 *
 * @link    https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package yuhuman
 */

add_action( 'wp_enqueue_scripts', 'yuhuman_enqueue_styles' );

/**
 * Enqueue scripts and styles.
 */
function yuhuman_enqueue_styles() {
	wp_enqueue_style( 'twentytwentyone-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'yuhuman-style',
		get_stylesheet_directory_uri() . '/style.css',
		[ 'twentytwentyone-style' ]
	);

	wp_dequeue_style( 'wpum-frontend' );

	wp_enqueue_style( 'dashicons' );

	wp_enqueue_style(
		'yuhuman-secondary-style',
		get_stylesheet_directory_uri() . '/assets/css/yuhuman-secondary-style.css',
		[ 'twentytwentyone-style' ],
		filemtime( get_stylesheet_directory() . '/assets/css/yuhuman-secondary-style.css' )
	);

	wp_enqueue_style(
		'yuhuman-wpum-frontend',
		get_stylesheet_directory_uri() . '/assets/css/wpum-frontend.css',
		[ 'twentytwentyone-style' ],
		filemtime( get_stylesheet_directory() . '/assets/css/wpum-frontend.css' )
	);

	wp_enqueue_style(
		'yuhuman-remodal',
		get_stylesheet_directory_uri() . '/assets/lib/remodal/remodal.css',
		[ 'twentytwentyone-style' ],
		filemtime( get_stylesheet_directory() . '/assets/lib/remodal/remodal.css' )
	);

	wp_enqueue_style(
		'yuhuman-remodal-theme',
		get_stylesheet_directory_uri() . '/assets/lib/remodal/remodal-default-theme.css',
		[ 'twentytwentyone-style' ],
		filemtime( get_stylesheet_directory() . '/assets/lib/remodal/remodal-default-theme.css' )
	);

	wp_enqueue_script(
		'yuhuman-script',
		get_stylesheet_directory_uri() . '/assets/js/yuhuman-script.js',
		[ 'jquery' ],
		filemtime( get_stylesheet_directory() . '/assets/js/yuhuman-script.js' ),
		true
	);

	wp_enqueue_script(
		'yuhuman-remodal-script',
		get_stylesheet_directory_uri() . '/assets/lib/remodal/remodal.min.js',
		[ 'jquery' ],
		filemtime( get_stylesheet_directory() . '/assets/lib/remodal/remodal.min.js' ),
		true
	);

	wp_localize_script(
		'yuhuman-script',
		'yuhuman_params',
		array(
			'upazillas' => yuhuman_available_upazillas(),
		)
	);
}

add_action( 'admin_enqueue_scripts', 'yuhuman_admin_scripts' );

function yuhuman_admin_scripts() {
	wp_enqueue_script(
		'yuhuman-script',
		get_stylesheet_directory_uri() . '/assets/js/yuhuman-script.js',
		[ 'jquery' ],
		filemtime( get_stylesheet_directory() . '/assets/js/yuhuman-script.js' ),
		true
	);

	wp_localize_script(
		'yuhuman-script',
		'yuhuman_params',
		array(
			'upazillas' => yuhuman_available_upazillas(),
		)
	);
}

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/core-user-profile-meta.php';
require_once __DIR__ . '/includes/wp-user-manager-functions.php';
require_once __DIR__ . '/includes/request-form-blood-form.php';
require_once __DIR__ . '/includes/blood-request-post-custom-fields.php';
require_once __DIR__ . '/includes/blood-request-post-status.php';
require_once __DIR__ . '/includes/fake-user-data.php';
require_once __DIR__ . '/includes/fake-blood-requests.php';

/**
 * Enqueue the shortcode scripts.
 */
function yuhuman_shortcode_scripts() {
	global $post;

	if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'yuhuman_request_for_blood_form' ) ) {
		wpum_enqueue_scripts();
	}
}

add_action( 'wp_enqueue_scripts', 'yuhuman_shortcode_scripts' );

function yuhuman_load_wpum_scripts_in_admin( $hook ) {
	$valid_hooks = array( 'post.php', 'profile.php', 'user-new.php' );

	if ( ! in_array( $hook, $valid_hooks ) ) {
		return;
	}

	if ( 'post.php' === $hook && 'blood-request' !== get_post_type() ) {
		return;
	}

	wpum_enqueue_scripts();
}

add_action( 'admin_enqueue_scripts', 'yuhuman_load_wpum_scripts_in_admin' );

/**
 * Rename the 'subscriber' role label.
 *
 * @return void
 */
function yuhuman_change_role_name() {
	global $wp_roles;

	if ( ! isset( $wp_roles ) ) {
		$wp_roles = new WP_Roles();
	}

	$wp_roles->roles['subscriber']['name'] = 'Donor';
	$wp_roles->role_names['subscriber']    = 'Donor';
}

add_action( 'init', 'yuhuman_change_role_name' );

/**
 * Disable wp-admin for 'subscriber' user role.
 *
 * @see https://wordpress.stackexchange.com/a/23008/88654
 *
 * @return void
 */
function yuhuman_disable_admin_access_for_subscriber() {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) && current_user_can( 'subscriber' ) ) {
		wp_redirect( home_url() );
		exit;
	}
}

add_action( 'init', 'yuhuman_disable_admin_access_for_subscriber' );

/**
 * Hide admin bar for 'subscriber' user role.
 *
 * @source https://css-tricks.com/snippets/wordpress/remove-admin-bar-for-subscribers/
 *
 * @return void
 */
function yuhuman_hide_admin_bar() {
	if ( ! current_user_can( 'edit_posts' ) ) {
		show_admin_bar( false );
	}
}

add_action( 'set_current_user', 'yuhuman_hide_admin_bar' );
