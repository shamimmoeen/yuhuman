<?php

function yuhuman_get_request_for_blood_page_id(): int {
	$page = get_page_by_path( 'request-for-blood' );

	if ( ! $page ) {
		return 0;
	}

	return $page->ID;
}

function yuhuman_get_edit_blood_request_page_id(): int {
	$page = get_page_by_path( 'edit-blood-request' );

	if ( ! $page ) {
		return 0;
	}

	return $page->ID;
}

function yuhuman_get_edit_blood_request_permalink( int $id ): string {
	$page_id   = yuhuman_get_edit_blood_request_page_id();
	$permalink = get_the_permalink( $page_id );

	return add_query_arg( 'id', $id, $permalink );
}

function yuhuman_get_blood_requests_page_id(): int {
	$page = get_page_by_path( 'blood-requests' );

	if ( ! $page ) {
		return 0;
	}

	return $page->ID;
}

function yuhuman_get_blood_request_input_value( string $name, int $id = 0 ) {
	if ( isset( $_POST[ $name ] ) ) {
		$value = sanitize_text_field( $_POST[ $name ] );
	} else {
		$value = get_post_meta( $id, $name, true );
	}

	return $value;
}

function yuhuman_check_valid_blood_request_id( int $id ): bool {
	$current_user_id = get_current_user_id();

	if ( ! $current_user_id ) {
		return false;
	}

	$post        = get_post( $id );
	$post_author = absint( $post->post_author );
	$post_status = $post->post_status;

	if ( current_user_can( 'administrator' ) ) {
		return true;
	}

	if ( $current_user_id === $post_author && 'publish' === $post_status ) {
		return true;
	}

	return false;
}

add_shortcode( 'yuhuman_blood_requests', 'yuhuman_register_blood_requests_directory_shortcode' );

function yuhuman_register_blood_requests_directory_shortcode() {
	ob_start();

	WPUM()->templates->get_template_part( 'blood-requests/blood-requests' );

	return ob_get_clean();
}

add_shortcode( 'yuhuman_request_for_blood_form', 'yuhuman_register_request_for_blood_form_shortcode' );

function yuhuman_register_request_for_blood_form_shortcode() {
	ob_start();

	WPUM()->templates->get_template_part( 'blood-requests/request-for-blood-form' );

	return ob_get_clean();
}

add_action( 'init', 'yuhuman_process_new_blood_request_form' );

function yuhuman_process_new_blood_request_form() {
	$action = isset( $_POST['action'] ) ? sanitize_text_field( $_POST['action'] ) : '';

	if ( 'blood-request' !== $action ) {
		return;
	}

	$nonce = $_POST['yuhuman_process_blood_request_nonce_field'] ?? '';

	if ( ! wp_verify_nonce( $nonce, 'yuhuman_process_blood_request_nonce' ) ) {
		$_POST['yuhuman-form-error'] = __( 'Invalid nonce', 'yuhuman' );

		return;
	}

	$fields = array(
		'patient-name',
		'patient-blood-group',
		'when-need-blood',
		'blood-units',
		'mobile-number',
		'hospital-name',
		'location',
		'details',
	);

	$required_fields = array(
		'patient-name',
		'patient-blood-group',
		'when-need-blood',
		'blood-units',
		'mobile-number',
		'location',
	);

	$field_required = false;

	foreach ( $required_fields as $field_name ) {
		$field_value = isset( $_POST[ $field_name ] ) ? sanitize_text_field( $_POST[ $field_name ] ) : '';

		if ( ! $field_value ) {
			$field_required = true;
			break;
		}
	}

	if ( $field_required ) {
		$_POST['yuhuman-form-error'] = __( 'Fill the required fields', 'yuhuman' );

		return;
	}

	$post_title = isset( $_POST['patient-name'] ) ? sanitize_text_field( $_POST['patient-name'] ) : '';
	$post_id    = isset( $_POST['blood-request-id'] ) ? sanitize_text_field( $_POST['blood-request-id'] ) : '';

	$current_user_id = get_current_user_id();

	if ( ! $current_user_id ) {
		$_POST['yuhuman-form-error'] = __( 'Permission denied', 'yuhuman' );

		return;
	}

	$post_data = array(
		'post_title'  => $post_title,
		'post_status' => 'publish',
		'post_type'   => 'blood-request',
	);

	$update_blood_request = false;

	if ( $post_id && 'blood-request' === get_post_type( $post_id ) && 'publish' === get_post_status( $post_id ) ) {
		$update_blood_request = true;
	}

	if ( $update_blood_request ) {
		$post_data['ID'] = $post_id;
	} else {
		$post_data['post_author'] = $current_user_id;
	}

	if ( $update_blood_request ) {
		$new_post_id     = wp_update_post( $post_data, true );
		$success_message = __( 'Blood request updated successfully', 'yuhuman' );
		$permalink       = yuhuman_get_edit_blood_request_permalink( $post_id );
	} else {
		$new_post_id     = wp_insert_post( $post_data, true );
		$success_message = __( 'Blood request created successfully', 'yuhuman' );
		$permalink       = get_the_permalink( yuhuman_get_blood_requests_page_id() );
	}

	if ( is_wp_error( $new_post_id ) ) {
		$_POST['yuhuman-form-error'] = $new_post_id->get_error_message();

		return;
	}

	foreach ( $fields as $field_name ) {
		$field_value = isset( $_POST[ $field_name ] ) ? sanitize_text_field( $_POST[ $field_name ] ) : '';

		update_post_meta( $new_post_id, $field_name, $field_value );
	}

	$redirect_to = add_query_arg( 'yh-success', $success_message, $permalink );

	wp_redirect( $redirect_to );
	exit;
}

function yuhuman_register_blood_request_post_type() {
	$labels = array(
		'name'          => _x( 'Blood Requests', 'Post type general name', 'yuhuman' ),
		'singular_name' => _x( 'Blood Request', 'Post type singular name', 'yuhuman' ),
	);

	$args = array(
		'labels'      => $labels,
		'public'      => false,
		'show_ui'     => true,
		'has_archive' => false,
		'menu_icon'   => 'dashicons-book',
		'supports'    => array( 'title', 'author' ),
	);

	register_post_type( 'blood-request', $args );
}

add_action( 'init', 'yuhuman_register_blood_request_post_type' );

add_action( 'init', 'yuhuman_delete_blood_request' );

function yuhuman_delete_blood_request() {
	$action = isset( $_GET['action'] ) ? sanitize_text_field( $_GET['action'] ) : '';

	if ( 'delete-blood-request' !== $action ) {
		return;
	}

	$redirect_to = get_the_permalink( yuhuman_get_blood_requests_page_id() );

	$nonce = $_GET['yuhuman_process_delete_blood_request_nonce_field'] ?? '';

	if ( ! wp_verify_nonce( $nonce, 'yuhuman_process_delete_blood_request_nonce' ) ) {
		$redirect_to = add_query_arg( 'yh-error', __( 'Invalid nonce', 'yuhuman' ), $redirect_to );

		wp_redirect( $redirect_to );
		exit;
	}

	$post_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

	$can_delete = true;

	if ( ! $post_id ) {
		$can_delete = false;
	}

	if ( ! yuhuman_check_valid_blood_request_id( $post_id ) ) {
		$can_delete = false;
	}

	if ( ! $can_delete ) {
		$redirect_to = add_query_arg( 'yh-error', __( 'Invalid blood request', 'yuhuman' ), $redirect_to );

		wp_redirect( $redirect_to );
		exit;
	}

	wp_delete_post( $post_id, true );

	$redirect_to = add_query_arg( 'yh-success', __( 'Blood request deleted successfully', 'yuhuman' ), $redirect_to );

	wp_redirect( $redirect_to );
	exit;
}

function yuhuman_delete_blood_request_link( int $id ): string {
	$link = add_query_arg(
		array(
			'id'     => $id,
			'action' => 'delete-blood-request',
		),
		get_the_permalink( yuhuman_get_blood_requests_page_id() )
	);

	return wp_nonce_url(
		$link,
		'yuhuman_process_delete_blood_request_nonce',
		'yuhuman_process_delete_blood_request_nonce_field'
	);
}

function yuhuman_edit_blood_request_page_template_redirect() {
	$edit_blood_request_page_id = yuhuman_get_edit_blood_request_page_id();

	if ( get_the_ID() === $edit_blood_request_page_id && ! isset( $_GET['id'] ) ) {
		$redirect_to_page_id = yuhuman_get_request_for_blood_page_id();

		wp_redirect( get_the_permalink( $redirect_to_page_id ) );
		exit;
	}
}

add_action( 'template_redirect', 'yuhuman_edit_blood_request_page_template_redirect' );

function yuhuman_register_to_blood_requests_template_redirect() {
	$current_user_id        = get_current_user_id();
	$current_page_id        = get_the_ID();
	$register_page_id       = absint( wpum_get_core_page_id( 'register' ) );
	$blood_requests_page_id = yuhuman_get_blood_requests_page_id();

	if ( $current_user_id && $current_page_id === $register_page_id && $blood_requests_page_id ) {
		wp_redirect( get_the_permalink( $blood_requests_page_id ) );
		exit;
	}
}

add_action( 'template_redirect', 'yuhuman_register_to_blood_requests_template_redirect' );

/**
 * Display pagination for blood requests.
 *
 * @param object $data
 *
 * @return void
 */
function yuhuman_blood_request_pagination( $data ) {
	$total = $data->max_num_pages ?? 0;

	echo '<div class="wpum-directory-pagination">';

	$big          = 9999999;
	$search_for   = array( $big, '#038;' );
	$replace_with = array( '%#%', '&' );

	echo paginate_links( array(
		'base'      => str_replace( $search_for, $replace_with, esc_url( get_pagenum_link( $big ) ) ),
		'current'   => max( 1, get_query_var( 'paged' ) ),
		'total'     => $total,
		'prev_text' => __( 'Previous page', 'yuhuman' ),
		'next_text' => __( 'Next page', 'yuhuman' )
	) );

	echo '</div>';

}
