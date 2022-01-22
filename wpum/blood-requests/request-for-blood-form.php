<?php
$blood_groups          = yuhuman_available_blood_types();
$available_blood_units = yuhuman_available_blood_units();

$blood_request_id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;

if ( $blood_request_id && ! yuhuman_check_valid_blood_request_id( $blood_request_id ) ) {
	?>
	<div class="wpum-message error">
		<?php esc_html_e( 'Invalid blood request', 'yuhuman' ); ?>
	</div>
	<?php
	return;
}

$patient_name        = yuhuman_get_blood_request_input_value( 'patient-name', $blood_request_id );
$patient_blood_group = yuhuman_get_blood_request_input_value( 'patient-blood-group', $blood_request_id );
$when_need_blood     = yuhuman_get_blood_request_input_value( 'when-need-blood', $blood_request_id );
$blood_units         = yuhuman_get_blood_request_input_value( 'blood-units', $blood_request_id );
$mobile_number       = yuhuman_get_blood_request_input_value( 'mobile-number', $blood_request_id );
$hospital_name       = yuhuman_get_blood_request_input_value( 'hospital-name', $blood_request_id );
$location            = yuhuman_get_blood_request_input_value( 'location', $blood_request_id );
$details             = yuhuman_get_blood_request_input_value( 'details', $blood_request_id );

$yuhuman_form_error = isset( $_POST['yuhuman-form-error'] ) ? sanitize_text_field( $_POST['yuhuman-form-error'] ) : '';
$success_message    = isset( $_GET['yh-success'] ) ? sanitize_text_field( $_GET['yh-success'] ) : '';

$current_user_id = get_current_user_id();
$login_page_url  = wpum_login_link( array( 'redirect' => get_the_permalink( yuhuman_get_request_for_blood_page_id() ) ) );
?>

<div class="wpum-template wpum-form request-for-blood-form">

	<?php if ( ! $current_user_id ) : ?>
		<div class="wpum-message info">
			<?php printf( __( 'You need to be logged-in to request for blood. %s', 'yuhuman' ), $login_page_url ); ?>
		</div>
		<?php return; ?>
	<?php endif; ?>

	<?php if ( $success_message ) : ?>
		<div class="wpum-message success"><?php echo esc_html( $success_message ); ?></div>
	<?php endif; ?>

	<?php if ( $yuhuman_form_error ) : ?>
		<div class="wpum-message error"><?php echo esc_html( $yuhuman_form_error ); ?></div>
	<?php endif; ?>

	<form action="" method="post">
		<fieldset class="fieldset-patient-name">
			<label for="patient-name">
				<?php esc_html_e( 'Patient Name', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<input
					type="text"
					name="patient-name"
					id="patient-name"
					value="<?php echo esc_attr( $patient_name ); ?>"
				>
			</div>
		</fieldset>

		<fieldset class="fieldset-patient-blood-group">
			<label for="patient-blood-group">
				<?php esc_html_e( 'Patient Blood Group', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<select name="patient-blood-group" id="patient-blood-group">
					<?php foreach ( $blood_groups as $key => $value ) : ?>
						<?php $selected = $key === $patient_blood_group ? ' selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</fieldset>

		<fieldset class="fieldset-when-need-blood">
			<label for="when-need-blood">
				<?php esc_html_e( 'When Need Blood', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<input
					type="text"
					class="input-datepicker yuhuman-datepicker"
					name="when-need-blood"
					id="when-need-blood"
					value="<?php echo esc_attr( $when_need_blood ); ?>"
				>
			</div>
		</fieldset>

		<fieldset class="fieldset-blood-unit">
			<label for="blood-units">
				<?php esc_html_e( 'Blood Units', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<select name="blood-units" id="blood-units">
					<?php foreach ( $available_blood_units as $key => $value ) : ?>
						<?php $selected = $key == $blood_units ? ' selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</fieldset>

		<fieldset class="fieldset-mobile-number">
			<label for="mobile-number">
				<?php esc_html_e( 'Mobile Number', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<input
					type="tel"
					name="mobile-number"
					id="mobile-number"
					value="<?php echo esc_attr( $mobile_number ); ?>"
				>
			</div>
		</fieldset>

		<fieldset class="fieldset-hospital-name">
			<label for="hospital-name"><?php esc_html_e( 'Hospital Name', 'yuhuman' ); ?></label>
			<div class="field">
				<input
					type="text"
					name="hospital-name"
					id="hospital-name"
					value="<?php echo esc_attr( $hospital_name ); ?>"
				>
			</div>
		</fieldset>

		<fieldset class="fieldset-location">
			<label for="location">
				<?php esc_html_e( 'Location', 'yuhuman' ); ?>
				<span class="wpum-required">*</span>
			</label>
			<div class="field">
				<input type="text" name="location" id="location" value="<?php echo esc_attr( $location ); ?>">
			</div>
		</fieldset>

		<fieldset class="fieldset-details">
			<label for="details"><?php esc_html_e( 'Details', 'yuhuman' ); ?></label>
			<div class="field">
				<textarea
					name="details"
					id="details"
					cols="20"
					rows="3"
				><?php echo esc_textarea( $details ); ?></textarea>
			</div>
		</fieldset>

		<?php
		wp_nonce_field(
			'yuhuman_process_blood_request_nonce',
			'yuhuman_process_blood_request_nonce_field'
		);
		?>

		<input type="submit" value="<?php esc_attr_e( 'Submit', 'yuhuman' ); ?>">
		<input type="hidden" name="action" value="blood-request">
		<?php if ( $blood_request_id ) : ?>
			<input type="hidden" name="blood-request-id" value="<?php echo esc_attr( $blood_request_id ); ?>">
		<?php endif; ?>
	</form>
</div>
