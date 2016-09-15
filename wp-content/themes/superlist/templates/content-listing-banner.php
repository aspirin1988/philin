<?php
/**
 * Template file
 *
 * @package Superlist
 * @subpackage Templates
 */
?>

<?php $listing_banner = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'banner', true ); ?>

<?php // Default - Simple image ?>
<?php if ( empty( $listing_banner ) || 'banner_simple' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/simple' ); ?>
<?php endif; ?>

<?php // Featured image ?>
<?php if ( 'banner_featured_image' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/featured-image' ); ?>
<?php endif; ?>

<?php // Custom image ?>
<?php if ( 'banner_image' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/custom-image' ); ?>
<?php endif ?>

<?php // Video ?>
<?php if ( 'banner_video' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/video' ); ?>
<?php endif; ?>

<?php // Google map ?>
<?php if ( 'banner_map' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/map' ); ?>
<?php endif; ?>

<?php // Google street view ?>
<?php if ( 'banner_street_view' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/street-view' ); ?>
<?php endif; ?>

<?php // Google inside view ?>
<?php if ( 'banner_inside_view' == $listing_banner ) : ?>
    <?php get_template_part( 'templates/banner-types/inside-view' ); ?>
<?php endif; ?>
