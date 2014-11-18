<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php
// Set default ratio to 16x9 if none selected
if ( empty( $current_ratio ) ) {
	$current_ratio        = 'widescreen';
	$current_ratio_select = 'medium';
}

?>

<section id="<?php echo esc_attr( $this->single_video ); ?>" style="<?php if ( $current_video_layout != 'Single Video' ) echo "display:none;" ?>">

	<label for="<?php echo esc_attr( $this->single_video ); ?>"><?php esc_html_e( 'Playwire Video', 'playwire' ); ?></label>

	<p>

		<select name="<?php echo esc_attr( $this->single_video ); ?>" class="widefat">

			<option value="0"><?php esc_html_e( '&mdash; No video selected &mdash;', 'playwire' ); ?></option>

			<?php foreach ( $videos as $key => $value ) : ?>

				<option data-current-single-video="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $value['id'] ); ?>" <?php selected( $current_single_video, $value['id'] ); ?>>
					<?php echo esc_html( $value['name'] ); ?>
				</option>

			<?php endforeach; ?>

		</select>

	</p>

</section>


<section id="<?php echo esc_attr( $this->video_playlist ); ?>" style="<?php if ( $current_video_layout === 'Single Video' ) echo "display:none;" ?>">

	<label for="<?php echo esc_attr( $this->video_playlist ); ?>">
		<?php esc_html_e( 'Playwire Playlist', 'playwire' ); ?>
	</label>

	<p>

		<select name="<?php echo esc_attr( $this->video_playlist ); ?>" class="widefat">

			<option value="0"><?php esc_html_e( '&mdash; No playlist selected &mdash;', 'playwire' ); ?></option>

			<?php foreach ( $playlists as $playlist ) : ?>

				<option value="<?php echo esc_attr( $playlist['id'] ); ?>" <?php selected( $current_playlist, $playlist['id'] ); ?>><?php echo esc_attr( $playlist['name'] ); ?></option>

			<?php endforeach; ?>

		</select>

	</p>

</section>


<section id="<?php echo esc_attr( $this->video_layout ); ?>">

	<p>

		<label for="<?php echo esc_attr( $this->video_layout ); ?>"><strong><?php esc_html_e( 'Video Layout', 'playwire' ); ?></strong></label>

		<ul>

				<?php foreach ( playwire()->template_types as $key => $value ) : ?>

				<li>

					<label>

						<input data-template="<?php echo esc_attr( $key ); ?>" type="radio" name="<?php echo esc_attr( $this->video_layout ); ?>" id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_video_layout, $value ); ?>>

						</br>

						<img src="<?php echo PLAYWIRE_URL . "assets/img/{$key}.png";?>" alt="single-video" width="200" height="auto" />

						</br>

						<?php echo esc_html( $value ); ?>

					</label>

				</li>

			<?php endforeach; ?>

		</ul>

	</p>

</section>


<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>">

	<p>

		<label><strong><?php esc_html_e( 'Aspect Ratio', 'playwire' ); ?></strong></label>

		<ul>

			<?php foreach ( playwire()->playwire_aspect_ratio['ratios'] as $key => $value ) : ?>

				<li>
					<label><input type="radio" name="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>" id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current_ratio, $key ); ?>><?php echo esc_html( $value['name'] ); ?></label>
				</li>

			<?php endforeach; ?>

		</ul>

	</p>

</section>

<section id="<?php echo esc_attr( playwire()->gallery_pagination_options['option_name'] ); ?>" style="<?php if ( $current_video_layout != 'Gallery' ) echo "display:none;"; ?>">
	<p>

		<label><strong><?php esc_html_e( 'Gallery Pagination Type', 'playwire' ); ?></strong></label>

		<ul>

			<?php foreach ( playwire()->gallery_pagination_options['options'] as $key => $value ) : ?>

				<li>
					<label><input type="radio" name="<?php echo esc_attr( playwire()->gallery_pagination_options['option_name'] ); ?>" id="<?php echo esc_attr( playwire()->gallery_pagination_options['option_name'] ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php checked( $current_gallery_pagination, $key ); ?>><?php echo esc_html( $value ); ?></label>
				</li>

			<?php endforeach; ?>

		</ul>

	</p>

</section>


<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['select_option_name'] ); ?>">

	<label for="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>"><strong><?php esc_html_e( 'Player Size', 'playwire' ); ?></strong></label>

	<p>

		<?php foreach ( playwire()->playwire_aspect_ratio['ratios'] as $key => $value) : ?>

			<?php if ( $key != 'custom' ) : ?>

					<select name="<?php echo esc_attr( playwire()->playwire_aspect_ratio['select_option_name'] ); ?>" class="widefat <?php echo esc_attr( $key ); ?>" style="<?php if ( $current_ratio != $key ) echo "display:none;"; ?>">

						<?php foreach ( $value['size'] as $key => $value ) : ?>

							<option data-width="<?php echo esc_attr( $value['width'] ); ?>" data-height="<?php echo esc_attr( $value['height'] ); ?>" value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_ratio_select, $key ); ?>><?php echo ucfirst( esc_attr( $key ) . '&nbsp;' .  esc_attr( $value['label'] )  ) ; ?></option>

						<?php endforeach; ?>

					</select>

			<?php endif; ?>

		<?php endforeach; ?>

		<ul style="<?php if ( $current_ratio != 'custom' ) echo "display:none;"; ?>">

			<li>

				<input type="number" name="<?php echo esc_attr( playwire()->playwire_aspect_ratio['height_option_name'] ); ?>" placeholder="height" value="<?php echo esc_attr( $current_ratio_height ); ?>">

				<label for="<?php echo esc_attr( playwire()->playwire_aspect_ratio['height_option_name'] ); ?>">
					<?php esc_html_e( 'Player Height', 'playwire' ); ?>
				</label>

			</li>

			<li>

				<input type="number" name="<?php echo esc_attr( playwire()->playwire_aspect_ratio['width_option_name'] ); ?>" placeholder="width" value="<?php echo esc_attr( $current_ratio_width ); ?>" >

				<label for="<?php echo esc_attr( playwire()->playwire_aspect_ratio['width_option_name'] ); ?>"><?php esc_html_e( 'Player Width', 'playwire' ); ?></label>

			</li>

			<li>

				<input type="checkbox" name="<?php echo esc_attr( playwire()->playwire_aspect_ratio['maintain_option_name'] ); ?>" value="1" <?php checked( $maintain_aspect_ratio, 1 ); ?>>

				<label for="<?php echo esc_attr( playwire()->playwire_aspect_ratio['maintain_option_name'] ); ?>"><?php esc_html_e( 'Maintain aspect ratio ( based on width )', 'playwire' ); ?></label>

			</li>

		</ul>

	</p>

</section>
