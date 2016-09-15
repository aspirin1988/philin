<div class="submission-step-title">
	<h1><?php echo $listing_type_title; ?></h1>

	<h2>
		<?php $index = 1; ?>
		<?php foreach ( $steps as $step_id => $step ) : ?>
			<?php if ( $step_id == $current_step ) : ?>
				<span><?php echo __( 'Step', 'inventor-submission' ); ?> <?php echo esc_attr( $index ); ?></span> <?php echo $step['title']; ?>
				<?php if ( ! empty( $step['description'] ) ): ?>
					<p><?php echo $step['description']; ?></p>
				<?php endif; ?>
			<?php endif; ?>
			<?php $index++; ?>
		<?php endforeach; ?>
	</h2>
</div><!-- /.submission-step-title -->