<?php $field = Inventor_Fields_Logic::get_field( $field_id ); ?>

<?php $field_type = get_post_meta( $field->ID, INVENTOR_FIELDS_FIELD_PREFIX . 'type', true ); ?>

<?php if ( class_exists( 'Inventor_Template_Loader' ) ) : ?>
    <?php if ( strpos( $field_type, 'taxonomy' ) !== false ): ?>
        <?php include Inventor_Template_Loader::locate( 'widgets/filter-fields/taxonomy', INVENTOR_FIELDS_DIR ); ?>
    <?php endif; ?>

    <?php if ( ( strpos( $field_type, 'radio' ) !== false && strpos( $field_type, '_radio' ) === false ) || $field_type == 'select' ): ?>
        <?php include Inventor_Template_Loader::locate( 'widgets/filter-fields/select', INVENTOR_FIELDS_DIR ); ?>
    <?php endif; ?>
<?php endif; ?>