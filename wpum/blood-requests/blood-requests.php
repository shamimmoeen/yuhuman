<?php
// yuhuman_generate_fake_user_data();
// yuhuman_generate_fake_blood_requests();
// yuhuman_delete_fake_blood_requests();

$success_message = isset( $_GET['yh-success'] ) ? sanitize_text_field( $_GET['yh-success'] ) : '';
$error_message   = isset( $_GET['yh-error'] ) ? sanitize_text_field( $_GET['yh-error'] ) : '';
$posts_per_page  = isset( $_GET['amount'] ) ? absint( $_GET['amount'] ) : 10;
$paged           = get_query_var( 'paged' );

$blood_group = isset( $_GET['blood-group'] ) ? sanitize_text_field( $_GET['blood-group'] ) : '';
$blood_units = isset( $_GET['blood-units'] ) ? absint( $_GET['blood-units'] ) : '';
$sortby      = isset( $_GET['sortby'] ) ? sanitize_text_field( $_GET['sortby'] ) : '';

$blood_request_search    = isset( $_GET['blood-request-search'] ) ? sanitize_text_field( $_GET['blood-request-search'] ) : '';
$archived_blood_requests = isset( $_GET['show-archived-blood-requests'] ) ? sanitize_text_field( $_GET['show-archived-blood-requests'] ) : '';
$my_blood_requests_only  = isset( $_GET['my-blood-requests-only'] ) ? sanitize_text_field( $_GET['my-blood-requests-only'] ) : '';

$meta_query = array(
	array(
		'relation' => 'OR',
		array(
			'key'     => 'archived',
			'compare' => 'NOT EXISTS',
		),
		array(
			'key'     => 'archived',
			'value'   => 'on',
			'compare' => '!=',
		)
	)
);

if ( $blood_group ) {
	$meta_query[] = array(
		'key'     => 'patient-blood-group',
		'value'   => $blood_group,
		'compare' => '=',
	);
}

if ( $blood_units ) {
	$meta_query[] = array(
		'key'     => 'blood-units',
		'value'   => $blood_units,
		'compare' => '=',
	);
}

$today = current_time( 'Y-m-d' );

if ( $archived_blood_requests ) {
	$meta_query[] = array(
		'key'     => 'when-need-blood',
		'value'   => $today,
		'compare' => '<'
	);
} else {
	$meta_query[] = array(
		'key'     => 'when-need-blood',
		'value'   => $today,
		'compare' => '>='
	);
}

$args = array(
	'post_type'      => 'blood-request',
	'post_status'    => 'publish',
	'posts_per_page' => $posts_per_page,
	'paged'          => $paged,
	'meta_query'     => $meta_query,
);

if ( $blood_request_search ) {
	$args['s'] = $blood_request_search;
}

if ( $my_blood_requests_only ) {
	$args['author'] = get_current_user_id();
}

if ( $sortby ) {
	if ( 'when-need-blood-asc' === $sortby ) {
		$args['order'] = 'ASC';
	}

	$args['orderby']   = 'meta_value';
	$args['meta_key']  = 'when-need-blood';
	$args['meta_type'] = 'DATE';
}

$query          = new WP_Query( $args );
$blood_requests = $query->get_posts();
?>

<?php if ( $success_message ) : ?>
	<div class="wpum-message success"><?php echo esc_html( $success_message ); ?></div>
<?php endif; ?>

<?php if ( $error_message ) : ?>
	<div class="wpum-message error"><?php echo esc_html( $error_message ); ?></div>
<?php endif; ?>

<div id="wpum-user-directory">

	<form action="<?php the_permalink(); ?>" method="GET" name="wpum-blood-request-search-form">
		<?php
		WPUM()->templates
			->set_template_data( [ 'query' => $query ] )
			->get_template_part( 'blood-requests/blood-request-search-bar' );
		?>
	</form>

	<div id="wpum-directory-users-list" class="yuhuman-blood-requests-list">
		<?php if ( $query->have_posts() ) : ?>
			<?php while ( $query->have_posts() ) : ?>
				<?php $query->the_post(); ?>
				<?php
				$post_id             = get_the_ID();
				$patient_name        = get_post_meta( $post_id, 'patient-name', true );
				$patient_blood_group = get_post_meta( $post_id, 'patient-blood-group', true );
				$when_need_blood     = get_post_meta( $post_id, 'when-need-blood', true );
				$blood_units         = get_post_meta( $post_id, 'blood-units', true );
				$mobile_number       = get_post_meta( $post_id, 'mobile-number', true );
				$hospital_name       = get_post_meta( $post_id, 'hospital-name', true );
				$location            = get_post_meta( $post_id, 'location', true );
				$details             = get_post_meta( $post_id, 'details', true );
				$edit_url            = yuhuman_get_edit_blood_request_permalink( $post_id );
				$delete_url          = yuhuman_delete_blood_request_link( $post_id );
				?>

				<div class="wpum-directory-single-user">
					<div class="yuhuman-hidden">
						<div class="yuhuman-blood-request-modal-content">
							<p>
								<i class="fas fa-hospital-user"></i>
								<?php esc_html_e( 'Patient Name', 'yuhuman' ); ?>:
								<?php echo esc_html( $patient_name ); ?>
							</p>
							<p>
								<i class="fas fa-hand-holding-medical"></i>
								<?php esc_html_e( 'Blood Group', 'yuhuman' ); ?>:
								<?php echo esc_html( $patient_blood_group ); ?>
							</p>
							<p>
								<i class="far fa-calendar-alt"></i>
								<?php esc_html_e( 'When need blood', 'yuhuman' ); ?>:
								<?php echo esc_html( $when_need_blood ); ?>
							</p>
							<p>
								<i class="fas fa-sort-amount-up"></i>
								<?php esc_html_e( 'Blood Units', 'yuhuman' ); ?>:
								<?php echo esc_html( $blood_units ); ?>
							</p>
							<p>
								<i class="fas fa-phone-volume"></i>
								<?php esc_html_e( 'Mobile Number', 'yuhuman' ); ?>:
								<?php echo esc_html( $mobile_number ); ?>
							</p>
							<?php if ( $hospital_name ) : ?>
								<p>
									<i class="fas fa-hospital-symbol"></i>
									<?php esc_html_e( 'Hospital Name', 'yuhuman' ); ?>:
									<?php echo esc_html( $hospital_name ); ?>
								</p>
							<?php endif; ?>
							<?php if ( $location ) : ?>
								<p>
									<i class="fas fa-map-marker-alt"></i>
									<?php esc_html_e( 'Location', 'yuhuman' ); ?>:
									<?php echo esc_html( $location ); ?>
								</p>
							<?php endif; ?>
							<?php if ( $details ) : ?>
								<p>
									<i class="fas fa-info-circle"></i>
									<?php esc_html_e( 'Details', 'yuhuman' ); ?>:
									<?php echo esc_html( $details ); ?>
								</p>
							<?php endif; ?>
						</div>
					</div>

					<div class="wpum-row wpum-middle-xs">
						<div class="wpum-col-xs-4">
							<p class="wpum-donor-description">
								<i class="fas fa-hospital-user"></i>
								<?php echo esc_html( $patient_name ); ?>
								<br>
								<i class="fas fa-hand-holding-medical"></i>
								<?php echo esc_html( $patient_blood_group ); ?>
							</p>
						</div>
						<div class="wpum-col-xs-4">
							<p class="wpum-donor-description">
								<i class="fas fa-phone-volume"></i>
								<?php echo esc_html( $mobile_number ); ?>
								<br>
								<i class="far fa-calendar-alt"></i>
								<?php echo esc_html( $when_need_blood ); ?>
							</p>
						</div>
						<div class="wpum-col-xs-4 wpum-meta">
							<?php if ( yuhuman_check_valid_blood_request_id( $post_id ) ) : ?>
								<a href="<?php echo esc_url( $edit_url ); ?>" class="edit-blood-request-button">
									<span class="dashicons dashicons-edit"></span>
								</a>
								<a href="<?php echo esc_url( $delete_url ); ?>" class="delete-blood-request-button">
									<span class="dashicons dashicons-trash"></span>
								</a>
							<?php endif; ?>
							<a href="#" class="button view-blood-request-btn">
								<?php esc_html_e( 'View Request', 'yuhuman' ); ?>
							</a>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
			<?php wp_reset_postdata(); ?>

			<?php yuhuman_blood_request_pagination( $query ); ?>
		<?php else: ?>
			<?php
			WPUM()->templates
				->set_template_data( [
					'message' => esc_html__( 'No blood requests have been found.', 'yuhuman' ),
				] )
				->get_template_part( 'messages/general', 'warning' );
			?>
		<?php endif; ?>

	</div>

	<div class="remodal blood-request-modal" data-remodal-id="blood-request-modal">
		<button data-remodal-action="close" class="remodal-close"></button>
		<h2><?php esc_html_e( 'Blood Request', 'yuhuman' ); ?></h2>
		<div class="yuhuman-blood-request-modal-content-wrapper"></div>
	</div>

</div>
