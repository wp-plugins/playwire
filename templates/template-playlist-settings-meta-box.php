<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php
// Set default ratio to 16x9 if none selected
if ( empty( $current_ratio ) ) {
	$current_ratio        = 'widescreen';
	$current_ratio_select = 'medium';
}

global $pagenow;

$publisher_id = PlaywirePublisher::get_pub_id();
$url = PlaywirePublisher::pub_id_playlists();

if ( ( 'post.php' == $pagenow || 'post-new.php' == $pagenow ) && get_post_type() === $this->playlists_post_type  ) {
	?>
	
	<div class="playlist-steps">
		<h3 class="playlist-heading">Follow these steps to <span class="underline">create</span> Video Galleries from your Playwire playlists:</h3>

		<ol>
			<li>Name your Video Gallery above</li>
			<li>Select a playlist from your Playwire Account (right below the instructions)</li>
			<li>Select your desired layout - Native, Film Strip, Gallery</li>
			<li>Click Publish to save your video gallery.</li>
			<li>You're now ready to embed, follow the next steps below.</li>
		</ol>

		<hr>

		<h3 class="playlist-heading">Follow these steps to <span class="underline">embed</span> Video Galleries into you posts and pages:</h3>

		<img class="metabox-image" src="<?php echo PLAYWIRE_URL . "assets/img/videoGalleryStep1.jpg";?>" alt="Creating Video Galleries - Step One" />

		<img class="metabox-image" src="<?php echo PLAYWIRE_URL . "assets/img/videoGalleryStep2.jpg";?>" alt="Creating Video Galleries - Step Two" />

		<img class="metabox-image" src="<?php echo PLAYWIRE_URL . "assets/img/videoGalleryStep3.jpg";?>" alt="Creating Video Galleries - Step Three" />

	</div>
	
<?php } ?>

<section id="<?php echo esc_attr( $this->video_playlist ); ?>">

	<?php if ( $playlists ) : ?>
		<hr>
		<h3 class="playlist-heading">Configure your Video Gallery options below:</h3>
		
		<label for="<?php echo esc_attr( $this->video_playlist ); ?>"><strong>
			<?php esc_html_e( 'Select a Playlist from your Playwire.com Account:', 'playwire' ); ?></strong>
		</label>

		<p>
			<select name="<?php echo esc_attr( $this->video_playlist ); ?>" class="widefat">

				<option value="0"><?php esc_html_e( '&mdash; No playlist selected, please click here to select a playlist &mdash;', 'playwire' ); ?></option>

				<?php foreach ( $playlists as $playlist ) : ?>

					<option value="<?php echo esc_attr( $playlist['id'] ); ?>" <?php selected( $current_playlist, $playlist['id'] ); ?>><?php echo esc_attr( $playlist['name'] ); ?></option>

				<?php endforeach; ?>
			</select>
		</p>

	<?php else : ?>

		<?php if ( $pagenow == "edit.php" || $pagenow == "post.php" || $pagenow == "post-new.php" ) : ?>
			<div class="playwire-error">
				<?php esc_html_e( 'You must create playlists on Playwire.com before you can create Video Galleries', 'playwire' ); ?></br></br>
				<a href="<?php echo $url ?>" target="_blank"> &Rightarrow; Click here to Create Playlists on Playwire</a>
			</div>
			
		<?php endif; ?>

	<?php endif; ?>

</section>

<?php if ( $playlists ) : ?>
	<section id="<?php echo esc_attr( $this->video_layout ); ?>">
<?php else : ?>

	<span class="no-select-text">
		<span class="dashicons dashicons-dismiss big-icon"></span>
		Disabled until you upload Playlists to Playwire
	</span>
	<section id="<?php echo esc_attr( $this->video_layout ); ?>" class="no-select">	
	<p>

		<select name="<?php echo esc_attr( $this->video_playlist ); ?>" class="widefat">

			<option value="0"><?php esc_html_e( '&mdash; No playlists available  &mdash;', 'playwire' ); ?></option>

		</select>

	</p>
<?php endif; ?>

	<p>

		<label for="<?php echo esc_attr( $this->video_layout ); ?>"><strong><?php esc_html_e( 'Video Layout', 'playwire' ); ?></strong></label>

		<ul>

				<?php foreach ( playwire()->template_types as $key => $value ) : 
					// hide single template for now
					// TODO remove from playlists altogether
					if ($key == 'single') continue;
				?>

				<li>

					<label>

					<span class="radio-btn-helper"><?php echo esc_html( $value . '&nbsp;&gt;'); ?></span>

						<input data-template="<?php echo esc_attr( $key ); ?>" type="radio" name="<?php echo esc_attr( $this->video_layout ); ?>" id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $current_video_layout, $value ); ?>>

						</br>

						<img src="<?php echo PLAYWIRE_URL . "assets/img/{$key}.png";?>" alt="single-video" width="200" height="auto" />

						</br>

					</label>

				</li>

			<?php endforeach; ?>

		</ul>

	</p>

</section>


<?php if ( $playlists ) : ?>
	<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>">
<?php else : ?>
	<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['option_name'] ); ?>" class="no-select">
<?php endif; ?>

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

<?php if ( $playlists ) : ?>
	<section id="<?php echo esc_attr( playwire()->gallery_pagination_options['option_name'] ); ?>" style="<?php if ( $current_video_layout != 'Gallery' ) echo "display:none;"; ?>">
<?php else : ?>
	<section id="<?php echo esc_attr( playwire()->gallery_pagination_options['option_name'] ); ?>" style="<?php if ( $current_video_layout != 'Gallery' ) echo "display:none;"; ?>" class="no-select">
<?php endif; ?>


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


<?php if ( $playlists ) : ?>
	<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['select_option_name'] ); ?>">
<?php else : ?>
	<section id="<?php echo esc_attr( playwire()->playwire_aspect_ratio['select_option_name'] ); ?>" class="no-select">
<?php endif; ?>

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