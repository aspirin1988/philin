<div class="detail-banner">
    <?php $banner_video_loop = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'banner_video_loop', true ); ?>
    <video autoplay muted
        <?php echo esc_attr( empty( $banner_video_loop ) ? '' : 'loop' ); ?>>
        <source src="<?php echo esc_attr( get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'banner_video', true ) ); ?>" type="video/mp4">
        <div class="alert alert-danger"><?php esc_attr__( 'Your browser does not support the video html tag.', 'superlist' ); ?></div>
    </video>

    <?php get_template_part( 'templates/content-listing-banner-info' ); ?>
</div><!-- /.detail-banner -->