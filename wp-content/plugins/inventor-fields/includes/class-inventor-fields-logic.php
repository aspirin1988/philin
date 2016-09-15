<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Fields_Logic
 *
 * @class Inventor_Fields_Logic
 * @package Inventor_Fields/Classes/Logic
 * @author Pragmatic Mates
 */
class Inventor_Fields_Logic {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_filter( 'inventor_filter_fields', array( __CLASS__, 'filter_fields' ) );
        add_filter( 'inventor_filter_field_plugin_dir', array( __CLASS__, 'filter_field_plugin_dir' ), 10, 3 );
        add_filter( 'inventor_filter_query_taxonomies', array( __CLASS__, 'filter_query_taxonomies' ), 10, 2 );
        add_filter( 'inventor_filter_query', array( __CLASS__, 'filter_query' ), 10, 2 );
    }

    /**
     * Returns field by its identifier
     *
     * @access public
     * @param $field_id
     * @return WP_Post
     */
    public static function get_field( $field_id ) {
        $query = new WP_Query( array(
            'post_type'         => 'field',
            'posts_per_page'    => -1,
            'meta_key'          => INVENTOR_FIELDS_FIELD_PREFIX . 'identifier',
            'meta_value'        => $field_id
        ) );

        return $query->post_count == 1 ? $query->posts[0] : null;
    }

    /**
     * Returns all custom filter fields
     *
     * @access public
     * @param $types array
     * @param $types_compare string
     * @return array
     */
    public static function get_filter_fields( $types = array(), $types_compare = '=' ) {
        $filter_fields = array();

        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'          => INVENTOR_FIELDS_FIELD_PREFIX . 'filter_field',
                'value'        => 'on'
            )
        );

        $types_meta_query = array(
            'relation' => 'OR'
        );

        if ( is_array( $types ) ) {
            foreach ( $types as $type ) {
                $types_meta_query[] = array(
                    'key'          => INVENTOR_FIELDS_FIELD_PREFIX . 'type',
                    'value'        => $type,
                    'compare'      => $types_compare
                );
            }
        }

        if ( count( $types_meta_query ) > 1 ) {
            $meta_query[] = $types_meta_query;
        }

        $query = new WP_Query( array(
            'post_type'         => 'field',
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'meta_query'        => $meta_query
        ) );

        foreach ( $query->posts as $field ) {
            $identifier = get_post_meta( $field->ID, INVENTOR_FIELDS_FIELD_PREFIX  . 'identifier', true );
            $filter_fields[$identifier] = get_the_title( $field->ID );
        }

        return $filter_fields;
    }

    /**
     * Adds filter fields to filter
     *
     * @access public
     * @param $fields array
     * @return array
     */
    public static function filter_fields( $fields ) {
        $custom_filter_fields = self::get_filter_fields();
        return count( $custom_filter_fields ) == 0 ? $fields : array_merge( $fields, $custom_filter_fields );
    }

    /**
     * Sets template directory for filter fields
     *
     * @access public
     * @param $plugin_dir string
     * @param $template string
     * @param $field_id string
     * @return string
     */
    public static function filter_field_plugin_dir( $plugin_dir, $template, $field_id ) {
        $custom_filter_fields = self::get_filter_fields();
        return array_key_exists( $field_id, $custom_filter_fields ) ? INVENTOR_FIELDS_DIR : $plugin_dir;
    }

    /**
     * Filters listings by taxonomy filter fields
     *
     * @access public
     * @param $taxonomies array
     * @param $params array
     * @return array
     */
    public static function filter_query_taxonomies( $taxonomies, $params ) {
        $custom_filter_fields = self::get_filter_fields( array( 'taxonomy' ), 'LIKE' );
        $searched_fields = array_intersect( array_keys( $custom_filter_fields ), array_keys( $params ) );

        foreach ( $searched_fields as $searched_field ) {
            $value = $params[ $searched_field ];

            if ( ! empty( $value ) ) {
                $field = self::get_field( $searched_field );
                $taxonomy = get_post_meta( $field->ID, INVENTOR_FIELDS_FIELD_PREFIX  . 'taxonomy', true );

                $taxonomies[] = array(
                    'taxonomy'  => $taxonomy,
                    'field'     => 'slug',
                    'terms'     => $value
                );
            }
        }

        return $taxonomies;
    }

    /**
     * Filters query by custom fields
     *
     * @access public
     * @return array
     */
    public static function filter_query( $ids, $params ) {
        $custom_filter_fields = self::get_filter_fields(
            array(
                'radio', 'radio_inline', 'select', 'colorpicker', 'text', 'text_small', 'text_medium',
                'text_email', 'text_url', 'text_money', 'text_time', 'textarea', 'textarea_small', 'textarea_code',
                'wysiwyg', 'select_timezone', 'text_date_timestamp', 'text_datetime_timestamp', 'text_datetime_timestamp_timezone'
            ),
            '='
        );

        $searched_fields = array_intersect( array_keys( $custom_filter_fields ), array_keys( $params ) );

        $meta_query = array(
            'relation'  => 'AND'
        );

        foreach ( $searched_fields as $searched_field ) {
            $field = self::get_field( $searched_field );

            $field_ids = Inventor_Fields_Post_Type_Field::get_possible_ids( $field->ID );

            if ( count( $field_ids ) > 0 ) {
                $filter_lookup = get_post_meta( $field->ID, INVENTOR_FIELDS_FIELD_PREFIX  . 'filter_lookup', true );
                $value = $params[ $searched_field ];

                if( ! empty( $value ) ) {
                    $child_meta_query = array(
                        'relation'  => 'OR'
                    );

                    foreach( $field_ids as $field_id ) {
                        $child_meta_query[] = array(
                            'key'       => $field_id,
                            'value'     => $value,
                            'compare'   => $filter_lookup
                        );
                    }

                    if ( count( $child_meta_query ) > 1 ) {
                        $meta_query[] = $child_meta_query;
                    }
                }
            }
        }

        if ( count( $meta_query ) <= 1 ) {
            return $ids;
        }

        $query = new WP_Query( array(
            'post_type'         => Inventor_Post_Types::get_listing_post_types(),
            'posts_per_page'    => -1,
            'post_status'       => 'publish',
            'meta_query'        => $meta_query
        ) );

        $searched_ids = array();

        if ( $query->post_count > 0 ) {
            foreach( $query->posts as $post ) {
                $searched_ids[] = $post->ID;
            }
        };

        $ids = Inventor_Filter::build_post_ids( $ids, $searched_ids );

        return $ids;
    }
}

Inventor_Fields_Logic::init();