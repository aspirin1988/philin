<div class="submission-choose-type">
	<?php $post_types = apply_filters( 'inventor_submission_allowed_listing_post_types', Inventor_Post_Types::get_listing_post_types() ); ?>

	<?php if ( ! empty( $post_types ) ) : ?>
		<p>
			<?php echo __( 'Please define type of your submission', 'inventor-submission' ); ?>:
		</p>

		<ul>
			<?php $index = 0; ?>
			<?php foreach( $post_types as $post_type ) : ?>
				<?php $obj = get_post_type_object( $post_type ); ?>

				<li>
					<a href="?type=<?php echo esc_attr( $post_type );?><?php if ( ! empty( $_GET['id'] ) ) { echo esc_attr( '&id=' . $_GET['id'] ); }; ?>"><?php echo esc_attr( $obj->labels->singular_name ); ?></a><?php if ( count( $post_types ) != $index + 1 ) : ?><span>,</span><?php endif; ?>
				</li>
				<?php $index++; ?>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<?php echo __( "You don't have privilege to create any submission.", 'inventor-submission' ); ?>
	<?php endif; ?>
</div><!-- /.submission-choose-type -->
