<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/admin
 */

namespace Max_Marine\Product_Categories_Enhancements\Admin;

use WP_Screen;
use WP_Term;
use WP_Term_Query;
use WP_User;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/admin
 * @author     David Baumwald <david@dream-encode.com>
 */
class Max_Marine_Product_Categories_Enhancements_Admin {

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		if ( ! max_marine_product_categories_enhancements_admin_current_screen_has_enqueued_assets() ) {
			return;
		}

		$current_screen = \get_current_screen();

		if ( ! $current_screen instanceof WP_Screen ) {
			return;
		}

		$screens_to_assets = max_marine_product_categories_enhancements_get_admin_screens_to_assets();

		foreach ( $screens_to_assets as $screen => $assets ) {
			if ( $current_screen->id !== $screen ) {
				continue;
			}

			foreach ( $assets as $asset ) {
				$asset_base_url = MAX_MARINE_PRODUCT_CATEGORIES_ENHANCEMENTS_PLUGIN_URL . 'admin/';

				$asset_file = include( MAX_MARINE_PRODUCT_CATEGORIES_ENHANCEMENTS_PLUGIN_PATH . "admin/assets/dist/js/admin-{$asset['name']}.min.asset.php" );

				wp_enqueue_style(
					"max-marine-product-categories-enhancements-admin-{$asset['name']}",
					$asset_base_url . "assets/dist/css/admin-{$asset['name']}.min.css",
					$asset_file['dependencies'],
					$asset_file['version'],
					'all'
				);
			}
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function enqueue_scripts() {
		if ( ! max_marine_product_categories_enhancements_admin_current_screen_has_enqueued_assets() ) {
			return;
		}

		$current_screen = get_current_screen();

		if ( ! $current_screen instanceof WP_Screen ) {
			return;
		}

		$screens_to_assets = max_marine_product_categories_enhancements_get_admin_screens_to_assets();

		foreach ( $screens_to_assets as $screen => $assets ) {
			if ( $current_screen->id !== $screen ) {
				continue;
			}

			foreach ( $assets as $asset ) {
				$asset_base_url = MAX_MARINE_PRODUCT_CATEGORIES_ENHANCEMENTS_PLUGIN_URL . 'admin/';

				$asset_file = include( MAX_MARINE_PRODUCT_CATEGORIES_ENHANCEMENTS_PLUGIN_PATH . "admin/assets/dist/js/admin-{$asset['name']}.min.asset.php" );

				wp_register_script(
					"max-marine-product-categories-enhancements-admin-{$asset['name']}",
					$asset_base_url . "assets/dist/js/admin-{$asset['name']}.min.js",
					$asset_file['dependencies'],
					$asset_file['version'],
					array(
						'in_footer' => true,
					)
				);

				if ( ! empty( $asset['localization'] ) ) {
					wp_localize_script( "max-marine-product-categories-enhancements-admin-{$asset['name']}", 'MMPCE', $asset['localization'] );
				}

				wp_enqueue_script( "max-marine-product-categories-enhancements-admin-{$asset['name']}" );

				wp_set_script_translations( "max-marine-product-categories-enhancements-admin-{$asset['name']}", 'max-marine-product-categories-enhancements' );
			}
		}
	}

	/**
	 * Restrict the edit_terms capability for the `product_cat` taxonomy to administrators only.
	 *
	 * This function dynamically modifies the user's capabilities at runtime to prevent
	 * non-administrator users from editing, adding, or deleting terms in the `product_cat` taxonomy.
	 *
	 * @since  1.0.0
	 * @param  array    $allcaps  An array of all the capabilities for the user.
	 * @param  array    $cap      The specific capability being checked.
	 * @param  array    $args     Additional arguments passed to the capability check.
	 * @param  WP_User  $user     The WP_User object for the current user.
	 * @return array
	 */
	public function restrict_edit_terms_for_product_cat( $allcaps, $cap, $args, $user ) {
		if ( ! $user instanceof WP_User ) {
			return $allcaps;
		}

		if ( isset( $args[0] ) && 'edit_terms' === $args[0] && isset( $args[2] ) && 'product_cat' === $args[2] ) {
			if ( ! user_can( $user, 'administrator' ) ) {
				$allcaps[ $cap[0] ] = false;
			}
		}

		return $allcaps;
	}

	/**
	 * Hide ability to create new category for non-admins on various pages.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function restrict_add_category_admin_page() {
		$current_screen = get_current_screen();

		if ( ! $current_screen instanceof WP_Screen || current_user_can( 'administrator' ) ) {
			return;
		}

		switch ( $current_screen->id ) {
			case 'edit-product_cat':
				?>

				<style>
					#col-left:has( #addtag ) {
						display: none;
					}

					#col-right {
						float: none;
						width: 100%;
					}
				</style>

				<?php
				break;

			case 'product':
				?>

				<style>
					#product_cat-adder {
						display: none;
					}
				</style>

				<?php
				break;
		}
	}

	/**
	 * Hide terms with meta `_mm_is_legacy_category` equal to 1 from wp_terms_checklist on the edit product screen.
	 *
	 * This function filters the terms retrieved for the wp_terms_checklist and removes
	 * any term that has `_mm_is_legacy_category` meta set to 1, but only on the edit product screen.
	 *
	 * @since  1.0.0
	 * @global object  $wpdb        Global WordPress Core database instance.
	 * @param  array   $terms       Array of term objects being displayed in the checklist.
	 * @param  array   $taxonomies  Array of taxonomy names.
	 * @param  array   $args        Arguments passed to wp_terms_checklist.
	 * @return array
	 */
	public function hide_legacy_categories_from_product_checklist( $terms, $taxonomies, $args ) {
		if ( ! is_admin() ) {
			return $terms;
		}

		if ( ! function_exists( 'get_current_screen' ) ) {
			require_once ABSPATH . '/wp-admin/includes/screen.php';
		}

		// Run only on the edit product screen.
		$current_screen = get_current_screen();

		if ( ! $current_screen || ! in_array( $current_screen->id, array( 'edit-product', 'product' ), true ) ) {
			return $terms;
		}

		$legacy_category_ids = get_transient( 'mmpce_legacy_category_ids' );

		// If not cached, query the database and cache it.
		if ( false === $legacy_category_ids ) {
			$categories_terms_args = array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'number'     => 0,
				'fields'     => 'ids',
				'update_term_meta_cache' => false,
				'meta_query' => array(
					array(
						'key'     => '_mm_is_legacy_category',
						'compare' => 'EXISTS',
					),
				),
			);

			$categories_terms_query = new WP_Term_Query( $categories_terms_args );

			$legacy_category_ids = $categories_terms_query->get_terms();

			set_transient( 'mmpce_legacy_category_ids', $legacy_category_ids, DAY_IN_SECONDS );
		}

		if ( ! $legacy_category_ids || count( $legacy_category_ids ) < 1 ) {
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
}
