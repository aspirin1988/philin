<?php if ( apply_filters( 'inventor_metabox_allowed', true, 'contact', get_the_author_meta('ID') ) ): ?>
    <?php $email = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'email', true ); ?>
    <?php $website = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'website', true ); ?>
    <?php $phone = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'phone', true ); ?>
    <?php $person = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'person', true ); ?>
    <?php $address = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX . 'address', true ); ?>

    <?php if ( ! empty( $email ) || ! empty( $website ) || ! empty( $phone ) || ! empty( $person ) || ! empty( $address ) ) : ?>
        <div class="listing-detail-section" id="listing-detail-section-contact">
            <h2 class="page-header"><?php echo $section_title; ?></h2>

            <div class="listing-detail-contact">
                <div class="row">
                    <div class="col-md-6">
                        <ul>
                            <?php if ( ! empty( $email ) ): ?>
                                <li class="email">
                                    <strong class="key"><?php echo __( 'E-mail', 'inventor' ); ?></strong>
                                    <span class="value">
                                        <a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_attr( $email ); ?></a>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $website ) ): ?>
                                <?php if ( strpos( $website, 'http' ) !== 0 ) $website = sprintf( 'http://%s', $website ); ?>

                                <li class="website">
                                    <strong class="key"><?php echo __( 'Website', 'inventor' ); ?></strong>
                                    <span class="value">
                                        <a href="<?php echo esc_attr( $website ); ?>" target="_blank"><?php echo esc_attr( $website ); ?></a>
                                    </span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $phone ) ): ?>
                                <li class="phone">
                                    <strong class="key"><?php echo __( 'Phone', 'inventor' ); ?></strong>
                                    <span class="value"><a href="tel:<?php echo wp_kses( str_replace(' ', '', $phone), wp_kses_allowed_html( 'post' ) ); ?>"><?php echo wp_kses( $phone, wp_kses_allowed_html( 'post' ) ); ?></a></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div><!-- /.col-* -->
                    <div class="col-md-6">
                        <ul>
                            <?php if ( ! empty( $person ) ): ?>
                                <li class="person">
                                    <strong class="key"><?php echo __( 'Person', 'inventor' ); ?></strong>
                                    <span class="value"><?php echo wp_kses( $person, wp_kses_allowed_html( 'post' ) ); ?></span>
                                </li>
                            <?php endif; ?>
                            <?php if ( ! empty( $address ) ): ?>
                                <li class="address">
                                    <strong class="key"><?php echo __( 'Address', 'inventor' ); ?></strong>
                                    <span class="value"><?php echo wp_kses( nl2br( $address ), wp_kses_allowed_html( 'post' ) ); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div><!-- /.col-* -->
                </div><!-- /.row -->
            </div><!-- /.listing-detail-contact -->
        </div><!-- /.listing-detail-section -->
    <?php endif; ?>
<?php endif; ?>