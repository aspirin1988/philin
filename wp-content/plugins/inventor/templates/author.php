<?php
/**
 * The template for displaying main file
 *
 * @package Inventor Bootstrap
 * @since Inventor Bootstrap 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header(); ?>

<?php global $wp_query; ?>
<?php $user = $wp_query->get_queried_object(); ?>
<?php $name = Inventor_Post_Type_User::get_full_name( $user->ID ); ?>
<?php $image = Inventor_Post_Type_User::get_user_image( $user->ID ); ?>
<?php $phone = get_user_meta( $user->ID, INVENTOR_USER_PREFIX . 'general_phone', true ); ?>
<?php $email = get_user_meta( $user->ID, INVENTOR_USER_PREFIX . 'general_email', true ); ?>
<?php $website = get_user_meta( $user->ID, INVENTOR_USER_PREFIX . 'general_website', true ); ?>
<?php $user_listings_query = Inventor_Query::get_listings_by_user( $user->ID, 'publish' ); ?>
<?php $user_listings = $user_listings_query->posts; ?>

<div class="user-banner">
    <div class="user-banner-content">
        <div class="user-banner-image" data-background-image="<?php echo esc_attr( $image ); ?>">
            <img src="<?php echo esc_attr( $image ); ?>" alt="">
        </div><!-- /.user-banner-user-image -->

        <div class="user-banner-title">
            <span class="user-banner-listings-count">
                <?php printf( _n( '%d listing', '%d listings', count( $user_listings ), 'inventor' ), count( $user_listings ) ); ?>
            </span>

            <div class="user-banner-name">
                <?php echo esc_attr( $name ); ?>
            </div><!-- /.user-banner-user-name -->
        </div>

        <dl class="user-banner-info">
            <?php if ( ! empty( $email ) ) : ?>
                <dt class="user-banner-email"><?php echo __( 'E-mail', 'inventor' ); ?></dt>
                <dd>
                    <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_attr( $email ) ; ?></a>
                </dd>
            <?php endif; ?>

            <?php if ( ! empty( $phone ) ) : ?>
                <dt class="user-banner-phone"><?php echo __( 'Phone', 'inventor' ); ?></dt>
                <dd>
                    <?php echo esc_attr( $phone ) ; ?>
                </dd>
            <?php endif; ?>

            <?php if ( ! empty( $website ) ) : ?>
                <dt class="user-banner-website"><?php echo __( 'Website', 'inventor' ); ?></dt>
                <dd>
                    <a href="<?php echo esc_attr( $website ); ?>" target="_blank"><?php echo esc_attr( $website ) ; ?></a>
                </dd>
            <?php endif; ?>
        </dl>
    </div><!-- /.user-banner-content -->
</div><!-- /.user-banner-->

<div class="row author-content-wrapper">
    <div class="col-sm-12">
        <?php dynamic_sidebar( 'content-top' ); ?>

        <div class="content author-content">

            <h1><?php echo __( 'User listings', 'inventor' ); ?></h1>

            <div class="user-listings <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>items-per-row-3<?php else : ?>items-per-row-4<?php endif; ?>">
                <?php $display = apply_filters( 'inventor_user_listings_display', 'small' ); ?>
                <?php query_posts( $user_listings_query->query_vars ); ?>

                <?php while ( have_posts() ) : the_post(); ?>
                    <div class="listing-container">
                        <?php include Inventor_Template_Loader::locate( 'listings/' . $display ); ?>
                    </div><!-- /.listing-container -->
                <?php endwhile; ?>

                <?php wp_reset_query(); ?>
            </div>
        </div><!-- /.content -->

        <?php dynamic_sidebar( 'content-bottom' ); ?>
    </div><!-- /.col-* -->

    <?php get_sidebar() ?>
</div><!-- /.row -->

<?php get_footer(); ?>