<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Inventor_Query
 *
 * @class Inventor_Query
 * @package Inventor/Classes
 * @author Pragmatic Mates
 */
class Inventor_Query {
	/**
	 * Gets user listings query
	 *
	 * @access public
	 * @param int $user_id
	 * @param string $post_status
	 * @param bool $count_only
	 * @return mixed
	 */
	public static function get_listings_by_user( $user_id = null, $post_status = 'any', $count_only = false ) {
		$user_id = $user_id == null ? get_current_user_id() : $user_id;

		$query = new WP_Query( array(
			'author'            => $user_id,
			'post_type'         => Inventor_Post_Types::get_listing_post_types(),
			'posts_per_page'    => -1,
			'post_status'       => $post_status,
		) );

		return $count_only ? $query->post_count : $query;
	}

	/**
	 * Returns listings query by post type
	 *
	 * @access public
	 * @param string $post_type
	 * @return WP_Query
	 */
	public static function get_listings_by_post_type( $post_type ) {
		if ( ! in_array( $post_type, Inventor_Post_Types::get_listing_post_types() ) ) {
			return null;
		}

		return new WP_Query( array(
			'post_type'         => array( $post_type ),
			'posts_per_page'    => -1,
			'post_status'       => 'any',
		) );
	}

	/**
	 * Returns all listings
	 *
	 * @access public
	 * @return WP_Query
	 */
	public static function get_all_listings() {
		return self::get_listings();
	}

	/**
	 * Returns listings
	 *
	 * @access public
	 * @param $count
	 * @param $filter_params
	 * @param $post_types
	 * @param $post_status
	 * @return WP_Query
	 */
	public static function get_listings( $count = -1, $filter_params = null, $post_types = null, $post_status = 'any' ) {
		$post_types = empty( $post_types ) ? Inventor_Post_Types::get_listing_post_types() : $post_types;

		$query = new WP_Query( array(
			'post_type'         => $post_types,
			'posts_per_page'    => $count,
			'post_status'       => $post_status,
		) );

		if ( $filter_params != null ) {
			return Inventor_Filter::filter_query( $query, $filter_params );
		}

		return $query;
	}

	/**
	 * Sets similar listings into loop
	 *
	 * @access public
	 * @param null|int $post_id
	 * @param int $count
	 * @return void
	 */
	public static function loop_listings_similar( $post_id = null, $count = 3 ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		$categories = wp_get_post_terms( $post_id, 'listing_categories' );
		$categories_ids = array();

		if ( ! empty( $categories ) && is_array( $categories ) ) {
			foreach ( $categories as $category ) {
				$categories_ids[] = $category->term_id;
			}
		}

		$args = array(
			'post_type' 		=> Inventor_Post_Types::get_listing_post_types(),
			'posts_per_page' 	=> $count,
			'orderby'			=> 'rand',
			'post__not_in'		=> array( $post_id ),
		);

		if ( ! empty( $categories_ids ) && is_array( $categories_ids ) && count( $categories_ids ) > 0 ) {
			$args['tax_query'] = array(
				array(
					'taxonomy'  => 'listing_categories',
					'field'     => 'id',
					'terms'     => $categories_ids,
				),
			);
		}

		query_posts( $args );
	}

	/**
	 * Gets listing location name
	 *
	 * @access public
	 * @param null   $post_id
	 * @param string $separator
	 * @param bool $hierarchical
	 * @return bool|string
	 */
	public static function get_listing_location_name( $post_id = null, $separator = '/', $hierarchical = true ) {
		if ( null == $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! empty( $listing_locations[ $post_id ] ) ) {
			return $listing_locations[ $post_id ];
		}

		$locations = wp_get_post_terms( $post_id, 'locations', array(
			'orderby'   => 'parent',
			'order'     => 'ASC',
		) );

		if ( is_array( $locations ) && count( $locations ) > 0 ) {
			$output = '';

            if ( true === $hierarchical ) {
			    foreach ( $locations as $key => $location ) {
                    $output .= '<a href="' . get_term_link( $location, 'locations' ). '">' . $location->name . '</a>';

                    if ( array_key_exists( $key + 1, $locations ) ) {
                        $output .= ' <span class="separator">' . $separator . '</span> ';
                    }
                }
			} else {
                $output = '<a href="' . get_term_link( end( $locations ), 'locations' ). '">' . end( $locations )->name . '</a>';
            }

			$listing_locations[ $post_id ] = $output;
			return $output;
		}

		return false;
	}

	/**
	 * Gets listing category name
	 *
	 * @access public
	 * @param null $post_id
	 * @param string $separator
	 * @param bool $hierarchical
	 * @return bool
	 */
	public static function get_listing_category_name( $post_id = null, $separator = '/', $hierarchical = false ) {
		if ( empty ( $post_id ) ) {
			$post_id = get_the_ID();
		}

		if ( ! empty( $listing_categories[ $post_id ] ) ) {
			return $listing_categories[ $post_id ];
		}

		$categories = wp_get_post_terms( $post_id, 'listing_categories', array(
			'orderby'   => 'parent',
			'order'     => 'ASC',
		) );

		if ( is_array( $categories ) && count( $categories ) > 0 ) {
			$output = '';

			if ( true === $hierarchical ) {
				foreach ( $categories as $key => $category ) {
					$output .= '<a href="' . get_term_link( $category, 'listing_categories' ). '">' . $category->name . '</a>';

					if ( array_key_exists( $key + 1, $categories ) ) {
						$output .= ' <span class="separator">' . $separator . '</span> ';
					}
				}
			} else {
//				$output = '<a href="' . get_term_link( end( $categories ), 'listing_categories' ). '">' . end( $categories )->name . '</a>';

//				$categories = wp_get_post_terms( $post_id, 'listing_categories' );
				$result_category = null;
				if ( is_array( $categories ) && count( $categories ) > 0 ) {
		            $depth = -1;
		            foreach ( $categories as $category ) {
						$current_depth = count( get_ancestors( $category->term_id, 'listing_categories' ) );
		                if ( $current_depth > $depth ) {
		                    $result_category = $category;
		                    $depth = $current_depth;
		                }
		            }

					if ( $result_category ) {
						$output = '<a href="' . get_term_link( $result_category ) . '">' . $result_category->name . '</a>';
					}
				}
			}

			$listing_categories[ $post_id ] = $output;
			return $output;
		}

		return false;
	}

	/**
	 * Checks if there is another post in query
	 *
	 * @access public
	 * @return bool
	 */
	public static function loop_has_next() {
		global $wp_query;

		if ( $wp_query->current_post + 1 < $wp_query->post_count ) {
			return true;
		}

		return false;
	}
}