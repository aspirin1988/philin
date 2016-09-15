<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<?php if ( ! empty( $_GET['marker-style'] ) && $_GET['marker-style'] == 'inventor-poi' ) : ?>
	<?php $poi = Inventor_Post_Type_Listing::get_inventor_poi(); ?>
	<div class="marker-inventor-poi"><div class="marker-inventor-poi-inner">
		<?php if ( ! empty( $poi ) ) : ?>
			<i class="inventor-poi <?php echo $poi; ?>"></i>
		<?php else : ?>
			<i class="inventor-poi inventor-poi-information"></i>
		<?php endif; ?>
	</div></div>
<?php elseif ( ! empty( $_GET['marker-style'] ) && $_GET['marker-style'] == 'thumbnail' ) : ?>
	<?php if ( has_post_thumbnail() ) : ?>
		<?php $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' ); ?>
		<?php $image = $thumbnail[0]; ?>
	<?php else: ?>
		<?php $image = esc_attr( plugins_url( 'inventor' ) ) . '/assets/img/default-item.png'; ?>
	<?php endif; ?>

	<div class="marker-thumbnail" style="background-image:url('<?php echo esc_attr( $image ); ?>')">
		<img src="<?php echo esc_attr( $image ); ?>" alt="">
	</div><!-- /.marker-thumbnail -->
<?php else : ?>
	<div class="marker"><div class="marker-inner"></div></div>
<?php endif; ?>
