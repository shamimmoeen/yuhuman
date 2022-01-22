<?php

$query       = $data->query ?? null;
$found_posts = $query->found_posts ?? 0;

$blood_groups          = yuhuman_available_blood_types();
$available_blood_units = yuhuman_available_blood_units();
$available_amounts     = yuhuman_available_amounts();
$available_sort_by     = yuhuman_available_sort_by();

$blood_group = isset( $_GET['blood-group'] ) ? sanitize_text_field( $_GET['blood-group'] ) : '';
$blood_units = isset( $_GET['blood-units'] ) ? absint( $_GET['blood-units'] ) : '';
$amount      = isset( $_GET['amount'] ) ? absint( $_GET['amount'] ) : '';
$sortby      = isset( $_GET['sortby'] ) ? sanitize_text_field( $_GET['sortby'] ) : '';

$blood_request_search    = isset( $_GET['blood-request-search'] ) ? sanitize_text_field( $_GET['blood-request-search'] ) : '';
$archived_blood_requests = isset( $_GET['show-archived-blood-requests'] ) ? sanitize_text_field( $_GET['show-archived-blood-requests'] ) : '';
$my_blood_requests_only  = isset( $_GET['my-blood-requests-only'] ) ? sanitize_text_field( $_GET['my-blood-requests-only'] ) : '';
?>

<div class="yuhuman-filter-toggle-wrapper">
	<span><?php esc_html_e( 'Filter options', 'yuhuman' ); ?></span>
</div>

<div id="wpum-directory-search-form">
	<div class="wpum-row">
		<div class="form-fields wpum-col-xs-10">
			<input
				type="text"
				name="blood-request-search"
				id="wpum-directory-search"
				placeholder="<?php esc_attr_e( 'Search using patient name', 'yuhuman' ); ?>"
				value="<?php echo esc_attr( $blood_request_search ); ?>"
			>
		</div>
		<div class="form-submit wpum-col-xs-2">
			<input type="submit" id="wpum-submit-user-search" class="button wpum-button" value="Search">
		</div>
	</div>
</div>

<div id="yuhuman-donors-filter-bar" class="yuhuman-blood-request-filter-bar">
	<div class="wpum-row">
		<div class="wpum-col-xs">
			<p>
				<?php esc_html_e( 'Blood Group', 'yuhuman' ); ?>:
				<select name="blood-group" id="blood-group">
					<?php foreach ( $blood_groups as $key => $value ) : ?>
						<?php $selected = $key === $blood_group ? ' selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<div class="wpum-col-xs">
			<p>
				<?php esc_html_e( 'Blood Units', 'yuhuman' ); ?>:
				<select name="blood-units" id="blood-units">
					<?php foreach ( $available_blood_units as $key => $value ) : ?>
						<?php $selected = $key === $blood_units ? 'selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<div class="wpum-col-xs yuhuman-search-form-inline-checkbox">
			<p>
				<label>
					<?php esc_html_e( 'Show archived blood requests', 'yuhuman' ); ?>
					<input
						type="checkbox"
						name="show-archived-blood-requests"
						id="show-archived-blood-requests"
						value="1"
						<?php echo '1' === $archived_blood_requests ? 'checked' : ''; ?>
					>
				</label>
			</p>
		</div>
		<?php if ( get_current_user_id() ) : ?>
			<div class="wpum-col-xs yuhuman-search-form-inline-checkbox">
				<p>
					<label>
						<?php esc_html_e( 'Show my blood requests only', 'yuhuman' ); ?>
						<input
							type="checkbox"
							name="my-blood-requests-only"
							id="my-blood-requests-only"
							value="1"
							<?php echo '1' === $my_blood_requests_only ? 'checked' : ''; ?>
						>
					</label>
				</p>
			</div>
		<?php endif; ?>
	</div>
</div>

<div id="wpum-directory-top-bar" class="yuhuman-blood-request-sort-bar">

	<div class="wpum-row">

		<div class="wpum-col-xs">
			<?php printf( __( 'Found %d blood requests.', 'yuhuman' ), $found_posts ); ?>
		</div>

		<div class="wpum-col-xs">
			<p>
				<?php esc_html_e( 'Sort by', 'yuhuman' ); ?>:
				<select name="sortby" id="wpum_sortby" class="wpum-select">
					<?php foreach ( $available_sort_by as $key => $value ) : ?>
						<?php $selected = $key === $sortby ? 'selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>

		<div class="wpum-col-xs">
			<p>
				<?php esc_html_e( 'Results per page', 'yuhuman' ); ?>:
				<select name="amount" id="wpum_amount" class="wpum-select">
					<?php foreach ( $available_amounts as $key => $value ) : ?>
						<?php $selected = $key === $amount ? 'selected="selected"' : ''; ?>
						<option
							value="<?php echo esc_attr( $key ); ?>"
							<?php echo $selected; ?>
						><?php echo esc_html( $value ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>

	</div>

</div>
