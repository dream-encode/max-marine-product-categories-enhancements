<?php
/**
 * Common functions for the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/includes
 */

/**
 * Define a constant if it is not already defined.
 *
 * @since  1.0.0
 * @param  string  $name   Constant name.
 * @param  mixed   $value  Constant value.
 * @return void
 */
function max_marine_product_categories_enhancements_maybe_define_constant( $name, $value ) {
	if ( ! defined( $name ) ) {
		define( $name, $value );
	}
}

/**
 * Get a plugin setting by key.
 *
 * @since  1.0.0
 * @param  string  $key      Setting key.
 * @param  mixed   $default  Optional. Default value. Default false.
 * @return mixed
 */
function max_marine_product_categories_enhancements_get_plugin_setting( $key, $default = false ) {
	static $settings = false;

	if ( false === $settings ) {
		$settings = get_option( 'max_marine_product_categories_enhancements_plugin_settings', array() );
	}

	if ( isset( $settings[ $key ] ) ) {
		return $settings[ $key ];
	}

	return $default;
}

/**
 * Get an array of data that relates enqueued assets to specific admin screens.
 *
 * @since  1.0.0
 * @return array
 */
function max_marine_product_categories_enhancements_get_admin_screens_to_assets() {
	return array(
		'settings_page_max-marine-product-categories-enhancements-settings' => array(
			array(
				'name'         => 'settings-page',
				'localization' => array(
					'REST_URL'    => get_rest_url( null, '' ),
					'WP_REST_URL' => get_rest_url(),
					'NONCES'      => array(
						'REST' => wp_create_nonce( 'wp_rest' ),
					),
					'SETTINGS'    => get_option( 'max_marine_product_categories_enhancements_plugin_settings', array() ),
				),
			),
		),
		'product' => array(
			array(
				'name'         => 'edit-product-page',
				'localization' => array(
					'REST_URL'    => get_rest_url( null, '' ),
					'WP_REST_URL' => get_rest_url(),
					'NONCES'      => array(
						'REST' => wp_create_nonce( 'wp_rest' ),
					),
					'SETTINGS'    => get_option( 'max_marine_product_categories_enhancements_plugin_settings', array() ),
				),
			),
		),
	);
}

/**
 * Get a list of WP style dependencies.
 *
 * @since  1.0.0
 * @return string[]
 */
function max_marine_product_categories_enhancements_get_wp_style_dependencies() {
	return array(
		'wp-components',
	);
}

/**
 * Get a list of WP style dependencies.
 *
 * @since  1.0.0
 * @param  array  $dependencies  Raw dependencies.
 * @return string[]
 */
function max_marine_product_categories_enhancements_get_style_asset_dependencies( $dependencies ) {
	$style_dependencies = max_marine_product_categories_enhancements_get_wp_style_dependencies();

	$new_dependencies = array();

	foreach ( $dependencies as $dependency ) {
		if ( in_array( $dependency, $style_dependencies, true ) ) {
			$new_dependencies[] = $dependency;
		}
	}

	return $new_dependencies;
}

/**
 * Get enqueued assets for the current admin screen.
 *
 * @since  1.0.0
 * @return array
 */
function max_marine_product_categories_enhancements_admin_current_screen_enqueued_assets() {
	$current_screen = get_current_screen();

	if ( ! $current_screen instanceof WP_Screen ) {
		return array();
	}

	$assets = max_marine_product_categories_enhancements_get_admin_screens_to_assets();

	return ! empty( $assets[ $current_screen->id ] ) ? $assets[ $current_screen->id ] : array();
}

/**
 * Check if the current admin screen has any enqueued assets.
 *
 * @since  1.0.0
 * @return int
 */
function max_marine_product_categories_enhancements_admin_current_screen_has_enqueued_assets() {
	return count( max_marine_product_categories_enhancements_admin_current_screen_enqueued_assets() );
}

/**
 * Get enqueued assets for the an admin screen.
 *
 * @since  1.0.0
 * @param  WP_Screen  $screen  Screen to check.
 * @return array
 */
function max_marine_product_categories_enhancements_admin_screen_enqueued_assets( $screen ) {
	if ( ! $screen instanceof WP_Screen ) {
		return array();
	}

	$assets = max_marine_product_categories_enhancements_get_admin_screens_to_assets();

	return ! empty( $assets[ $screen->id ] ) ? $assets[ $screen->id ] : array();
}

/**
 * Check if an admin screen has any enqueued assets.
 *
 * @since  1.0.0
 * @param  WP_Screen  $screen  Screen to check.
 * @return int
 */
function max_marine_product_categories_enhancements_admin_screen_has_enqueued_assets( $screen ) {
	return count( max_marine_product_categories_enhancements_admin_screen_enqueued_assets( $screen ) );
}

/**
 * Get an array of "legacy' categories.
 *
 * @since  1.0.0
 * @param  bool  $cached  Optional. Whether to use cached results. Default false.
 * @return false|int[]
 */
function max_marine_product_categories_enhancements_get_legacy_categories_ids( $cached = false ) {
	static $legacy_category_ids;

	if ( ! $legacy_category_ids ) {
		$legacy_category_ids = $cached ? get_transient( 'mmpce_legacy_category_ids' ) : false;

		// If not cached, query the database and cache it.
		if ( false === $legacy_category_ids ) {
			$categories_terms_args = array(
				'taxonomy'               => 'product_cat',
				'hide_empty'             => false,
				'number'                 => 0,
				'fields'                 => 'ids',
				'update_term_meta_cache' => false,
				'meta_query'             => array(
					array(
						'key'     => '_mm_is_legacy_category',
						'compare' => 'EXISTS',
					),
				),
			);

			$categories_terms_query = new WP_Term_Query( $categories_terms_args );

			$legacy_category_ids = $categories_terms_query->get_terms();

			if ( empty( $legacy_category_ids ) ) {
				return false;
			}

			set_transient( 'mmpce_legacy_category_ids', $legacy_category_ids, YEAR_IN_SECONDS );
		}
	}

	return $legacy_category_ids;
}

/**
 * Given an array of term IDs(product categories), filter out any "legacy' categories.
 *
 * @since  1.0.0
 * @param  int[]  $term_ids  Array of term IDs.
 * @return int[]
 */
function max_marine_product_categories_enhancements_filter_legacy_categories_from_term_ids( $term_ids ) {
	$legacy_category_ids = max_marine_product_categories_enhancements_get_legacy_categories_ids();

	if ( ! $legacy_category_ids ) {
		return $term_ids;
	}

	$filtered_terms = array_filter(
		$term_ids,
		function ( $term_id ) use ( $legacy_category_ids ) {
			if ( ! is_int( $term_id ) ) {
				return false;
			}

			return ! in_array( $term_id, $legacy_category_ids );
		}
	);

	return $filtered_terms;
}

/**
 * Given an array of terms(product categories), filter out any "legacy' categories.
 *
 * @since  1.0.0
 * @param  WP_Term[]  $terms  Array of terms.
 * @return WP_Term[]
 */
function max_marine_product_categories_enhancements_filter_legacy_categories_from_terms( $terms ) {
	$legacy_category_ids = max_marine_product_categories_enhancements_get_legacy_categories_ids();

	if ( ! $legacy_category_ids ) {
		return $terms;
	}

	$filtered_terms = array_filter(
		$terms,
		function ( $term ) use ( $legacy_category_ids ) {
			if ( ! $term instanceof WP_Term ) {
				return false;
			}

			return ! in_array( $term->term_id, $legacy_category_ids );
		}
	);

	return $filtered_terms;
}
