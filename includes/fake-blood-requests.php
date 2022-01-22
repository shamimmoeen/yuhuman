<?php

function yuhuman_generate_fake_blood_requests( $number_of_posts = 15 ) {
	if ( ! class_exists( '\Faker\Factory' ) ) {
		return;
	}

	$all_users = get_users( array( 'fields' => 'ID' ) );

	$blood_groups = yuhuman_available_blood_types();
	array_shift( $blood_groups );

	$available_hospitals = yuhuman_dummy_hospitals();

	// use the factory to create a Faker\Generator instance
	$faker = Faker\Factory::create();

	for ( $i = 0; $i < $number_of_posts; $i ++ ) {
		$post_title  = $faker->firstName . ' ' . $faker->lastName;
		$post_author = $faker->randomElement( $all_users );

		$post_data = array(
			'post_title'  => $post_title,
			'post_status' => 'publish',
			'post_type'   => 'blood-request',
			'post_author' => $post_author,
		);

		$post_id = wp_insert_post( $post_data );

		if ( ! $post_id ) {
			continue;
		}

		$fields = array(
			'patient-name',
			'patient-blood-group',
			'when-need-blood',
			'blood-units',
			'mobile-number',
			'hospital-name',
		);

		foreach ( $fields as $field_key ) {
			$value = '';

			switch ( $field_key ) {
				case 'patient-name':
					$value = $post_title;

					break;

				case 'patient-blood-group':
					$value = $faker->randomElement( $blood_groups );;

					break;

				case 'when-need-blood':
					$date_obj = $faker->dateTimeBetween( '+1 day', '+1 month' );
					$value    = $date_obj->format( 'Y-m-d' );

					break;

				case 'blood-units':
					$value = 1;

					break;

				case 'mobile-number':
					$value = $faker->tollFreePhoneNumber;

					break;

				case 'hospital-name':
					$value = $faker->randomElement( $available_hospitals );

					break;
			}

			if ( $value ) {
				update_post_meta( $post_id, $field_key, $value );
			}

			// echo $field_key . ' :' . $value . '<br>';
		}

		update_post_meta( $post_id, 'fakerpress_flag', '1' );

		// echo '<br><br>';
	}
}

function yuhuman_dummy_hospitals(): array {
	return array(
		"Dhaka Community Hospital",
		"Japan East West Medical College Hospital, Dhaka",
		"Ad-din Akij Medical College Hospital, Khulna",
		"Ad-din Sakina Medical College Hospital, Jessore",
		"Ad-din Women's Medical College Hospital, Dhaka",
		"Aichi Hospital, Dhaka",
		"Arif Memorial Hospital, Barishal",
		"Al Haramain Hospital, Sylhet",
		"Ambia Memorial Hospital, Barisal",
		"Anwer Khan Modern Hospital Ltd, Dhaka",
		"Evercare Hospital Dhaka",
		"Asgar Ali Hospital Gandaria, Dhaka",
		"Aysha Memorial Specialised Hospital, Dhaka",
		"Bangabandhu Memorial Hospital (BBMH), Chittagong",
		"Bangabandhu Sheikh Mujib Medical University",
		"Bangladesh College of Nursing",
		"Bangladesh Eye Hospital Ltd., Dhaka",
		"Bangladesh Medical College Hospital, Dhaka",
		"Bangladesh Specialized Hospital",
		"Bangladesh Spine &amp; Orthopaedic General Hospital Ltd, Panthapath, Dhaka.",
		"Basundhura Hospital (Pvt.) Ltd.",
		"BDR (Bangladesh Rifles) Hospital",
		"Bangladesh Institute of Research and Rehabilitation for Diabetes, Endocrine and Metabolic Disorders (BIRDEM)",
		"BRB Hospital- Panthapath Dhaka",
		"Cardio Care Specialized and General Hospital Ltd, Dhaka",
		"CARe Hospital, Dhaka",
		"Care Zone Hospital, Dhaka",
		"Catharsis Medical Centre Limited, Gazipur",
		"Central Hospital, Dhaka",
		"Chander Hasi Hospital Limited, Habiganj, Sylhet.",
		"Chittagong Eye Infirmary and Training Hospital",
		"Chittagong Maa-O-Shishu Hospital, Chittagong",
		"Chittagong Diabetic General Hospital",
	);
}

function yuhuman_delete_fake_blood_requests() {
	$args = array(
		'post_type'   => 'blood-request',
		'post_status' => 'publish',
		'fields'      => 'ids',
		'nopaging'    => true,
		'meta_query'  => array(
			array(
				'key'     => 'fakerpress_flag',
				'value'   => '1',
				'compare' => '=',
			),
		),
	);

	$posts = get_posts( $args );

	foreach ( $posts as $post_id ) {
		wp_delete_post( $post_id, true );
	}
}
