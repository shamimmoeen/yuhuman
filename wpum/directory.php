<?php
/**
 * The Template for displaying the directory
 *
 * This template can be overridden by copying it to yourtheme/wpum/directory.php
 *
 * HOWEVER, on occasion WPUM will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$blood_group = isset( $_GET['blood-group'] ) ? sanitize_text_field( $_GET['blood-group'] ) : '';
$district    = isset( $_GET['district'] ) ? sanitize_text_field( $_GET['district'] ) : '';
$upazilla    = isset( $_GET['upazilla'] ) ? sanitize_text_field( $_GET['upazilla'] ) : '';

$blood_groups = yuhuman_available_blood_types();
$districts    = yuhuman_available_districts();
$upazillas    = yuhuman_available_upazillas();
?>

<div id="wpum-user-directory">

	<?php do_action( 'wpum_before_user_directory', $data ); ?>

	<form action="<?php the_permalink(); ?>" method="GET" name="wpum-directory-search-form">

		<div class="yuhuman-filter-toggle-wrapper">
			<span><?php esc_html_e( 'Filter options', 'yuhuman' ); ?></span>
		</div>

		<?php
			WPUM()->templates
				->set_template_data( $data )
				->get_template_part( 'directory/search-form' );
		?>
		<div id="yuhuman-donors-filter-bar">
			<div class="wpum-row">
				<div class="wpum-col-xs">
					<p>
						<?php esc_html_e( 'Blood Group', 'yuhuman' ); ?>:
						<select name="blood-group" id="blood-group">
							<?php foreach ( $blood_groups as $key => $value ) : ?>
								<?php $selected = $key === $blood_group ? ' selected="selected"' : ''; ?>
								<option value="<?php echo esc_attr( $key ); ?>"<?php echo $selected; ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				</div>
				<div class="wpum-col-xs">
					<p>
						<?php esc_html_e( 'District', 'yuhuman' ); ?>:
						<select name="district" id="district">
							<?php foreach ( $districts as $key => $value ) : ?>
								<?php $selected = $key === $district ? ' selected="selected"' : ''; ?>
								<option value="<?php echo esc_attr( $key ); ?>"<?php echo $selected; ?>><?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					</p>
				</div>
				<div class="wpum-col-xs">
					<p>
						<?php esc_html_e( 'Upazilla', 'yuhuman' ); ?>:
						<select name="upazilla" id="upazilla">
							<?php if ( $district ) : ?>
								<?php $_upazillas = $upazillas[ $district ]; ?>
								<?php foreach ( $_upazillas as $key => $value ) : ?>
									<?php $selected = $key === $upazilla ? ' selected="selected"' : ''; ?>
									<option value="<?php echo esc_attr( $key ); ?>"<?php echo $selected; ?>><?php echo esc_html( $value ); ?></option>
								<?php endforeach; ?>
							<?php endif; ?>
						</select>
					</p>
				</div>
			</div>
		</div>
		<?php
			WPUM()->templates
				->set_template_data( $data )
				->get_template_part( 'directory/top-bar' );
		?>
	</form>
	<!-- start directory -->
	<div id="wpum-directory-users-list">

		<?php if( is_array( $data->results ) && ! empty( $data->results ) ) : ?>

			<?php foreach( $data->results as $user ) : ?>
				<?php

					$user_template = ( $data->user_template !== 'default' || ! $data->user_template ) ? $data->user_template : 'user';

					WPUM()->templates
						->set_template_data( $user )
						->get_template_part( 'directory/single', $user_template );
				?>
			<?php endforeach; ?>

			<?php wpum_user_directory_pagination( $data ); ?>

		<?php else : ?>
			<?php

				WPUM()->templates
					->set_template_data( [
						'message' => esc_html__( 'No donors have been found.', 'wp-user-manager' ),
					] )
					->get_template_part( 'messages/general', 'warning' );

			?>

		<?php endif; ?>

	</div>
	<!-- end directory -->
	<?php do_action( 'wpum_after_user_directory', $data ); ?>

</div>
