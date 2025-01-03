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
use WC_Product;

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
					max_marine_product_categories_enhancements_get_style_asset_dependencies( $asset_file['dependencies'] ),
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
	 * Hide terms with meta `_mm_is_legacy_category` equal to 1 from wp_terms_checklist on the edit product screen.
	 *
	 * This function filters the terms retrieved for the wp_terms_checklist and removes
	 * any term that has `_mm_is_legacy_category` meta set to 1, but only on the edit product screen.
	 *
	 * @since  1.0.0
	 * @global string  $typenow     Current post type global.
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

		global $typenow;

		if ( empty( $typenow ) || 'product' !== $typenow ) {
			return $terms;
		}

		return max_marine_product_categories_enhancements_filter_legacy_categories_from_terms( $terms );
	}

	/**
	 * Remove old categories when a product is duplicated.
	 *
	 * @since  1.1.0
	 * @param  WC_Product|int  $duplicated_product  Product duplicate.
	 * @return void
	 */
	public function woocommerce_product_duplicate( $duplicated_product ) {
		if ( is_numeric( $duplicated_product ) ) {
			$duplicated_product = wc_get_product( $duplicated_product );
		}

		if ( ! $duplicated_product instanceof WC_Product ) {
			return;
		}

		$current_category_ids = $duplicated_product->get_category_ids();

		if ( count( $current_category_ids ) < 1 ) {
			return;
		}

		$filtered_category_ids = max_marine_product_categories_enhancements_filter_legacy_categories_from_term_ids( $current_category_ids );

		$duplicated_product->set_category_ids( $filtered_category_ids );
		$duplicated_product->save();
	}

	/**
	 * Create a root for a react mount on the edit product page.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function edit_product_page_react_root() {
		$current_screen = get_current_screen();

		if ( ! $current_screen instanceof WP_Screen ) {
			return;
		}

		if ( 'product' !== $current_screen->id ) {
			return;
		}

		echo '<div id="max-marine-product-categories-enhancements-edit-product"></div>';
	}

	/**
	 * Overwrite terms during bulk edit instead of appending them.
	 *
	 * @since  1.1.0
	 * @param  int  $post_id  The ID of the post being updated.
	 * @return void
	 */
	public function bulk_edit_terms_replace_old_terms( $post_id ) {
		if ( ! isset( $_REQUEST['bulk_edit'] ) || empty( $_REQUEST['tax_input'] ) ) {
			return;
		}

		$selected_terms = $_REQUEST['tax_input'];

		foreach ( $selected_terms as $taxonomy => $terms ) {
			if ( empty( $terms ) ) {
				continue;
			}

			wp_set_post_terms( $post_id, $terms, $taxonomy );
		}
	}
}
