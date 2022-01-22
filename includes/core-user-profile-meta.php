<?php

add_action( 'show_user_profile', 'yuhuman_user_profile_edit_action' );
add_action( 'edit_user_profile', 'yuhuman_user_profile_edit_action' );
add_action( 'user_new_form', 'yuhuman_user_profile_edit_action' );

function yuhuman_user_profile_edit_action( $user ) {
	$user_id = $user->ID ?? 0;

	$street_address       = get_user_meta( $user_id, 'street-address', true );
	$district             = get_user_meta( $user_id, 'district', true );
	$upazilla             = get_user_meta( $user_id, 'upazilla', true );
	$mobile_number        = get_user_meta( $user_id, 'mobile-number', true );
	$birthday             = get_user_meta( $user_id, 'birthday', true );
	$blood_group          = get_user_meta( $user_id, 'blood-group', true );
	$want_to_donate_blood = get_user_meta( $user_id, 'want-to-donate-blood', true );
	?>
	<h2><?php esc_html_e( 'Donor Profile', 'yuhuman' ); ?></h2>
	<table class="form-table">
		<tbody>
			<tr>
				<th><label for="street-address"><?php esc_html_e( 'Street Address', 'yuhuman' ); ?></label></th>
				<td>
					<input
						type="text"
						name="street-address"
						id="street-address"
						value="<?php echo esc_attr( $street_address ); ?>"
					>
				</td>
			</tr>
			<tr>
				<th><label for="district"><?php esc_html_e( 'District', 'yuhuman' ); ?></label></th>
				<td>
					<select name="district" id="district">
						<?php
						$districts = yuhuman_available_districts();

						foreach ( $districts as $district_name => $district_label ) {
							$selected = $district_name === $district ? ' selected="selected"' : '';
							echo '<option value="' . esc_attr( $district_name ) . '"' . esc_attr( $selected ) . '>' . esc_html( $district_label ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="upazilla"><?php esc_html_e( 'Upazilla', 'yuhuman' ); ?></label></th>
				<td>
					<select
						name="upazilla"
						id="upazilla"
						style="min-width: 100px;"
						<?php echo ! $district ? ' disabled' : ''; ?>
					>
						<?php
						$upazillas = yuhuman_available_upazillas();

						if ( $upazilla && $district ) {
							$_upazillas = $upazillas[ $district ];

							foreach ( $_upazillas as $upazilla_name => $upazilla_label ) {
								$selected = $upazilla_name === $upazilla ? ' selected="selected"' : '';
								echo '<option value="' . esc_attr( $upazilla_name ) . '"' . $selected . '>' . esc_html( $upazilla_label ) . '</option>';
							}
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th><label for="mobile-number"><?php esc_html_e( 'Mobile Number', 'yuhuman' ); ?></label></th>
				<td>
					<input
						type="tel"
						name="mobile-number"
						id="mobile-number"
						value="<?php echo esc_attr( $mobile_number ); ?>"
					>
				</td>
			</tr>
			<tr>
				<th><label for="birthday"><?php esc_html_e( 'Birthday', 'yuhuman' ); ?></label></th>
				<td>
					<input
						type="text"
						name="birthday"
						class="yuhuman-birthday"
						id="birthday"
						value="<?php echo esc_attr( $birthday ); ?>"
					>
				</td>
			</tr>
			<tr>
				<th><label for="blood-group"><?php esc_html_e( 'Blood Group', 'yuhuman' ); ?></label></th>
				<td>
					<select name="blood-group" id="blood-group">
						<?php
						$blood_groups = yuhuman_available_blood_types();

						foreach ( $blood_groups as $blood_group_name => $blood_group_label ) {
							$selected = $blood_group_name === $blood_group ? ' selected="selected"' : '';
							echo '<option value="' . esc_attr( $blood_group_name ) . '"' . esc_attr( $selected ) . '>' . esc_html( $blood_group_label ) . '</option>';
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<th>
					<label for="want-to-donate-blood"><?php esc_html_e( 'Want to donate blood', 'yuhuman' ); ?></label>
				</th>
				<td>
					<input
						type="checkbox"
						name="want-to-donate-blood"
						id="want-to-donate-blood"
						<?php echo $want_to_donate_blood ? ' checked' : ''; ?>
					>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'personal_options_update', 'yuhuman_user_profile_update_action' );
add_action( 'edit_user_profile_update', 'yuhuman_user_profile_update_action' );
add_action( 'user_register', 'yuhuman_user_profile_update_action' );

function yuhuman_user_profile_update_action( $user_id ) {
	$street_address       = isset( $_POST['street-address'] ) ? sanitize_text_field( $_POST['street-address'] ) : '';
	$district             = isset( $_POST['district'] ) ? sanitize_text_field( $_POST['district'] ) : '';
	$upazilla             = isset( $_POST['upazilla'] ) ? sanitize_text_field( $_POST['upazilla'] ) : '';
	$mobile_number        = isset( $_POST['mobile-number'] ) ? sanitize_text_field( $_POST['mobile-number'] ) : '';
	$birthday             = isset( $_POST['birthday'] ) ? sanitize_text_field( $_POST['birthday'] ) : '';
	$blood_group          = isset( $_POST['blood-group'] ) ? sanitize_text_field( $_POST['blood-group'] ) : '';
	$want_to_donate_blood = isset( $_POST['want-to-donate-blood'] );

	update_user_meta( $user_id, 'street-address', $street_address );
	update_user_meta( $user_id, 'district', $district );
	update_user_meta( $user_id, 'upazilla', $upazilla );
	update_user_meta( $user_id, 'mobile-number', $mobile_number );
	update_user_meta( $user_id, 'birthday', $birthday );
	update_user_meta( $user_id, 'blood-group', $blood_group );
	update_user_meta( $user_id, 'want-to-donate-blood', $want_to_donate_blood );
}
