<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Inventor_Jobs_Post_Type_Job
 *
 * @class Inventor_Jobs_Post_Type_Job
 * @package Inventor_Jobs/Classes/Post_Types
 * @author Pragmatic Mates
 */
class Inventor_Jobs_Post_Type_Job {
    /**
     * Initialize custom post type
     *
     * @access public
     * @return void
     */
    public static function init() {
        add_action( 'init', array( __CLASS__, 'definition' ) );
        add_action( 'cmb2_init', array( __CLASS__, 'fields' ) );

        add_filter( 'inventor_claims_allowed_listing_post_types', array( __CLASS__, 'allowed_claiming' ) );
        add_filter( 'inventor_attribute_value', array( __CLASS__, 'attribute_value' ), 10, 2 );
        add_filter( 'inventor_listing_detail_sections', array( __CLASS__, 'job_skills_section' ), 10, 2 );
        add_filter( 'inventor_listing_detail_section_root_dir', array( __CLASS__, 'section_root_dir' ), 10, 2 );
    }

    /**
     * Defines if post type can be claimed
     *
     * @access public
     * @param array $post_types
     * @return array
     */
    public static function allowed_claiming( $post_types ) {
        $post_types[] = 'job';
        return $post_types;
    }

    /**
     * Defines job skills section
     *
     * @access public
     * @param array $sections
     * @param string $post_type
     * @return array
     */
    public static function job_skills_section( $sections, $post_type ) {
        if ( $post_type == 'job' ) {
            $job_skills_section = array( 'job_skills' => esc_attr__( 'Required skills', 'inventor-jobs' ) );
            return array_merge( array_slice( $sections, 0, 3 ), $job_skills_section, array_slice( $sections, 3 ) );
        }
        return $sections;
    }

    /**
     * Returns path for plugin templates
     *
     * @access public
     * @param string $path
     * @param string $section
     * @return string
     */
    public static function section_root_dir( $path, $section ) {
        if ( $section == 'job_skills' ) {
            return INVENTOR_JOBS_DIR;
        }
        return $path;
    }

    /**
     * Renders listing attribute
     *
     * @access public
     * @param mixed $value
     * @param array $field
     * @return string
     */
    public static function attribute_value( $value, $field ) {
        if ( $field[ 'id' ] == INVENTOR_LISTING_PREFIX  . 'job_employer' and ! empty( $value ) ) {
            $business = get_post_meta( get_the_ID(), INVENTOR_LISTING_PREFIX  . 'job_employer', true );
            $url = get_permalink( $business );
            $value = '<a href="' . $url. '">' . $value .'</a>';
        }

        return $value;
    }

    /**
     * Custom post type definition
     *
     * @access public
     * @return void
     */
    public static function definition() {
        $labels = array(
            'name'                  => __( 'Jobs', 'inventor-jobs' ),
            'singular_name'         => __( 'Job', 'inventor-jobs' ),
            'add_new'               => __( 'Add New Job', 'inventor-jobs' ),
            'add_new_item'          => __( 'Add New Job', 'inventor-jobs' ),
            'edit_item'             => __( 'Edit Job', 'inventor-jobs' ),
            'new_item'              => __( 'New Job', 'inventor-jobs' ),
            'all_items'             => __( 'Jobs', 'inventor-jobs' ),
            'view_item'             => __( 'View Job', 'inventor-jobs' ),
            'search_items'          => __( 'Search Job', 'inventor-jobs' ),
            'not_found'             => __( 'No Jobs found', 'inventor-jobs' ),
            'not_found_in_trash'    => __( 'No Jobs Found in Trash', 'inventor-jobs' ),
            'parent_item_colon'     => '',
            'menu_name'             => __( 'Jobs', 'inventor-jobs' ),
        );

        register_post_type( 'job',
            array(
                'labels'            => $labels,
                'show_in_menu'	  => 'listings',
                'supports'          => array( 'title', 'editor', 'thumbnail', 'comments', 'author' ),
                'has_archive'       => true,
                'rewrite'           => array( 'slug' => _x( 'jobs', 'URL slug', 'inventor-jobs' ) ),
                'public'            => true,
                'show_ui'           => true,
                'categories'        => array(),
            )
        );
    }

    /**
     * Defines custom fields
     *
     * @access public
     * @return array
     */
    public static function fields() {
        $post_type = 'job';
        Inventor_Post_Types::add_metabox( $post_type, array( 'general' ) );

        $cmb = new_cmb2_box( array(
            'id'            => INVENTOR_LISTING_PREFIX . 'job_details',
            'title'         => __( 'Details', 'inventor-jobs' ),
            'object_types'  => array( 'job' ),
            'context'       => 'normal',
            'priority'      => 'high',
        ) );

        if ( in_array( 'business', Inventor_Post_Types::get_listing_post_types() ) ) {
            $business_query = Inventor_Query::get_listings_by_post_type( 'business' );
            $businesses = $business_query->posts;

            if( count( $businesses ) > 0 ) {
                $business_choices = array();

                foreach( $businesses as $business ) {
                    $business_choices[ $business->ID ] = get_the_title( $business );
                }

                $cmb->add_field( array(
                    'name'              => __( 'Employer', 'inventor-jobs' ),
                    'id'                => INVENTOR_LISTING_PREFIX  . 'job_employer',
                    'type'              => 'select',
                    'default'           => apply_filters( 'inventor_metabox_field_default', null, INVENTOR_LISTING_PREFIX . 'job_details', INVENTOR_LISTING_PREFIX  . 'job_employer', $post_type ),
                    'show_option_none'  => true,
                    'options'           => $business_choices
                ) );
            }
        }

        $cmb->add_field( array(
            'name'              => __( 'Starting date', 'inventor-jobs' ),
            'id'                => INVENTOR_LISTING_PREFIX  . 'job_starting_date',
            'type'              => 'text_date_timestamp',
            'default'           => apply_filters( 'inventor_metabox_field_default', null, INVENTOR_LISTING_PREFIX . 'job_details', INVENTOR_LISTING_PREFIX  . 'job_starting_date', $post_type ),
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Job position', 'inventor-jobs' ),
            'id'                => INVENTOR_LISTING_PREFIX  . 'job_position',
            'type'              => 'taxonomy_select_hierarchy',
            'taxonomy'          => 'job_positions',
            'default'           => apply_filters( 'inventor_metabox_field_default', null, INVENTOR_LISTING_PREFIX . 'job_details', INVENTOR_LISTING_PREFIX  . 'job_position', $post_type ),
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Contract type', 'inventor-jobs' ),
            'id'                => INVENTOR_LISTING_PREFIX  . 'job_contract_type',
            'type'              => 'radio',
            'options'           => array(
                'FULL_TIME'              => __( 'Full-time', 'inventor-jobs' ),
                'PART_TIME'              => __( 'Part-time', 'inventor-jobs' ),
                'BRIGADE'                => __( 'Brigade', 'inventor-jobs' ),
                'AGREEMENT'              => __( 'Agreement', 'inventor-jobs' ),
                'SELF_EMPLOYED_PERSON'   => __( 'Self-employed persons', 'inventor-jobs' ),
            ),
            'default'           => apply_filters( 'inventor_metabox_field_default', null, INVENTOR_LISTING_PREFIX . 'job_details', INVENTOR_LISTING_PREFIX  . 'job_contract_type', $post_type ),
        ) );

        $cmb->add_field( array(
            'name'              => __( 'Required skills', 'inventor-jobs' ),
            'id'                => INVENTOR_LISTING_PREFIX  . 'job_skill',
            'type'              => 'taxonomy_multicheck_hierarchy',
            'taxonomy'          => 'job_skills',
            'default'           => apply_filters( 'inventor_metabox_field_default', null, INVENTOR_LISTING_PREFIX . 'job_details', INVENTOR_LISTING_PREFIX  . 'job_skill', $post_type ),
            'skip'              => true
        ) );

        Inventor_Post_Types::add_metabox( 'job', array( 'banner', 'price', 'location', 'flags', 'contact', 'social', 'listing_category' ) );
    }
}

Inventor_Jobs_Post_Type_Job::init();