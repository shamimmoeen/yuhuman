<?php

function yuhuman_user_custom_fields(): array {
	$fields = array();

	$fields['street-address'] = array(
		'label'       => __( 'Street Address', 'yuhuman' ),
		'type'        => 'text',
		'template'    => 'text',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 98,
	);

	$fields['district'] = array(
		'label'       => __( 'District', 'yuhuman' ),
		'type'        => 'dropdown',
		'template'    => 'dropdown',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 98,
		'options'     => yuhuman_available_districts(),
	);

	$fields['upazilla'] = array(
		'label'       => __( 'Upazilla', 'yuhuman' ),
		'type'        => 'dropdown',
		'template'    => 'dropdown',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 98,
		'options'     => array(),
	);

	$fields['mobile-number'] = array(
		'label'       => __( 'Mobile Number', 'yuhuman' ),
		'type'        => 'telephone',
		'template'    => 'telephone',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 98,
	);

	$fields['birthday'] = array(
		'label'       => __( 'Birthday', 'yuhuman' ),
		'type'        => 'datepicker',
		'template'    => 'datepicker',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 99,
	);

	$fields['blood-group'] = array(
		'label'       => __( 'Blood Group', 'yuhuman' ),
		'type'        => 'dropdown',
		'template'    => 'dropdown',
		'required'    => true,
		'placeholder' => '',
		'description' => '',
		'priority'    => 100,
		'options'     => yuhuman_available_blood_types(),
	);

	$fields['want-to-donate-blood'] = array(
		'label'       => '',
		'type'        => 'checkbox',
		'template'    => 'checkbox',
		'required'    => false,
		'placeholder' => '',
		'description' => __( 'Are you willing to donate blood?', 'yuhuman' ),
		'priority'    => 101,
	);

	return $fields;
}


add_filter( 'wpum_get_registration_fields', 'yuhuman_register_form_fields' );

function yuhuman_register_form_fields( $fields ) {
	$custom_fields = yuhuman_user_custom_fields();

	$fields['user_firstname']['required'] = true;

	return wp_parse_args( $fields, $custom_fields );
}

add_action( 'wpum_before_registration_end', 'yuhuman_register_user_custom_fields', 10, 2 );

function yuhuman_register_user_custom_fields( $user_id, $values ) {
	$keys = array_keys( yuhuman_user_custom_fields() );

	foreach ( $keys as $key ) {
		$value = $values['register'][ $key ] ?? '';
		update_user_meta( $user_id, $key, $value );
	}
}

add_filter( 'wpum_get_account_fields', 'yuhuman_account_fields' );

function yuhuman_account_fields( $fields ): array {
	$user_email = $fields['user_email']['value'];
	$user       = get_user_by( 'email', $user_email );
	$user_id    = $user->ID;
	$upazillas  = yuhuman_available_upazillas();

	foreach ( yuhuman_user_custom_fields() as $key => $data ) {
		$value = get_user_meta( $user_id, $key, true );

		if ( 'upazilla' === $key ) {
			$district = $fields['district']['value'];
			$options  = $upazillas[ $district ];

			$data['options'] = $options;
		}

		$data['value']  = $value;
		$fields[ $key ] = $data;
	}

	return $fields;
}

add_action( 'wpum_after_user_update', 'yuhuman_update_user_profile', 10, 3 );

function yuhuman_update_user_profile( $profile, $values, $user_id ) {
	if ( ! $profile ) {
		return;
	}

	$keys = array_keys( yuhuman_user_custom_fields() );

	foreach ( $keys as $key ) {
		$value = $values['account'][ $key ] ?? '';
		update_user_meta( $user_id, $key, $value );
	}
}

add_filter( 'wpum_directory_search_query_args', 'yuhuman_user_directory_search_query_args' );

function yuhuman_user_directory_search_query_args( $args ) {
	$blood_group = isset( $_GET['blood-group'] ) ? sanitize_text_field( $_GET['blood-group'] ) : '';
	$district    = isset( $_GET['district'] ) ? sanitize_text_field( $_GET['district'] ) : '';
	$upazilla    = isset( $_GET['upazilla'] ) ? sanitize_text_field( $_GET['upazilla'] ) : '';

	$meta_query = $args['meta_query'];

	$new_meta_query = array();

	// Don't show the users who are not willing to donate blood.
	$new_meta_query[] = array(
		'key'   => 'want-to-donate-blood',
		'value' => '1',
	);

	// Filter by blood group.
	if ( $blood_group ) {
		$new_meta_query[] = array(
			'key'   => 'blood-group',
			'value' => $blood_group,
		);
	}

	// Filter by district.
	if ( $district ) {
		$new_meta_query[] = array(
			'key'   => 'district',
			'value' => $district,
		);
	}

	// Filter by upazilla.
	if ( $upazilla ) {
		$new_meta_query[] = array(
			'key'   => 'upazilla',
			'value' => $upazilla,
		);
	}

	$updated_meta_query = wp_parse_args( $meta_query, $new_meta_query );
	$args['meta_query'] = $updated_meta_query;

	return $args;
}
