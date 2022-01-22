<?php

function yuhuman_generate_fake_user_data() {
	if ( ! class_exists( '\Faker\Factory' ) ) {
		return;
	}

	$meta_query = array();

	$meta_query[] = array(
		'key'     => 'fakerpress_flag',
		'value'   => '1',
		'compare' => '=',
	);

	$args = array(
		'fields'     => 'ID',
		'role'       => 'Subscriber',
		'meta_query' => $meta_query,
	);

	$users = get_users( $args );

	$districts = yuhuman_available_districts();
	array_shift( $districts );

	$upazillas = yuhuman_available_upazillas();

	$blood_groups = yuhuman_available_blood_types();
	array_shift( $blood_groups );

	// use the factory to create a Faker\Generator instance
	$faker = Faker\Factory::create();

	foreach ( $users as $user_id ) {
		$district_key = $faker->randomKey( $districts );
		$district = $districts[ $district_key ];

		$district_upazillas = $upazillas[ $district_key ];
		array_shift( $district_upazillas );
		$upazilla = $faker->randomElement( $district_upazillas );

		$mobile_number = $faker->tollFreePhoneNumber;

		$birth_day_obj = $faker->dateTimeBetween( '-40 years', '-18 years' );
		$birth_day = $birth_day_obj->format( 'Y-m-d' );

		$blood_group = $faker->randomElement( $blood_groups );

		$willing_to_donate_blood = $faker->boolean( 70 );

		// $user_data = get_userdata( $user_id );
		// echo '<h3>' . $user_data->display_name . '</h3>';
		//
		// echo 'District: ' . $district . '<br>';
		// echo 'Upazilla: ' . $upazilla . '<br>';
		// echo 'Mobile Number: ' . $mobile_number . '<br>';
		// echo 'Birth Day: ' . $birth_day . '<br>';
		// echo 'Blood Group: ' . $blood_group . '<br>';
		// echo 'Willing to donate blood: ' . $willing_to_donate_blood . '<br>';

		// Attach the fake data to users.
		update_user_meta( $user_id, 'district', $district_key );
		update_user_meta( $user_id, 'upazilla', $upazilla );
		update_user_meta( $user_id, 'mobile-number', $mobile_number );
		update_user_meta( $user_id, 'birthday', $birth_day );
		update_user_meta( $user_id, 'blood-group', $blood_group );
		update_user_meta( $user_id, 'want-to-donate-blood', $willing_to_donate_blood );
	}
}
