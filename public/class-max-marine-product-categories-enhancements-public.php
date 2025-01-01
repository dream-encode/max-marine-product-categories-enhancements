<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/public
 */

namespace Max_Marine\Product_Categories_Enhancements\Frontend;

use Max_Marine\Product_Categories_Enhancements\Core\Upgrade\Max_Marine_Product_Categories_Enhancements_Upgrader;
use Max_Marine\Product_Categories_Enhancements\Core\RestApi\Max_Marine_Product_Categories_Enhancements_Core_API;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/public
 * @author     David Baumwald <david@dream-encode.com>
 */
class Max_Marine_Product_Categories_Enhancements_Public {

	/**
	 * Register plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return void
	 */
	public function register_plugin_settings() {
		$default = array(
			'plugin_log_level' => 'off',
		);

		$schema  = array(
			'type'       => 'object',
			'properties' => array(
				'plugin_log_level' => array(
					'type' => 'string',
				),
			),
		);

		register_setting(
			'options',
			'max_marine_product_categories_enhancements_plugin_settings',
			array(
				'type'         => 'object',
				'default'      => $default,
				'show_in_rest' => array(
					'schema' => $schema,
				),
			)
		);
	}

	/**
	 * Modify capabilities for the `product_cat` taxonomy to restrict term editing to administrators.
	 *
	 * @since  1.1.0
	 * @return void
	 */
	public function remove_edit_terms_capability_for_non_admins() {
		$taxonomy = get_taxonomy( 'product_cat' );

		if ( $taxonomy ) {
			$taxonomy->cap->edit_terms   = 'manage_options';
			$taxonomy->cap->delete_terms = 'manage_options';
			$taxonomy->cap->assign_terms = 'manage_options';
		}
	}
}
