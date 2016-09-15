<?php if ( apply_filters( 'inventor_metabox_allowed', true, $metabox_key, get_the_author_meta('ID') ) ): ?>
    <div class="listing-detail-section listing-detail-section-generic" id="listing-detail-section-<?php echo $metabox_key; ?>">
        <h2 class="page-header"><?php echo $section_title; ?></h2>

        <div class="listing-detail-section-content-wrapper">
            <?php foreach( $fields as $field ): ?>
                <?php $value = Inventor_Post_Types::get_field_value( $field ); ?>

                <?php if ( ! empty( $value ) && $field['skip'] ): ?>
                    <h3><?php echo $field['name']; ?></h3>
                    <p><?php echo $value; ?></p>
                <?php endif; ?>

            <?php endforeach; ?>
        </div>
    </div><!-- /.listing-detail-section -->
<?php endif; ?>