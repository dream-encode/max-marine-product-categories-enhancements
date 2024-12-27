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
	 * Do stuff when plugin updates happen.
	 *
	 * @since  1.0.0
	 * @param  object  $upgrader_object  Upgrader object.
	 * @param  array   $options          Options.
	 * @return void
	 */
	public function upgrader_process_complete( $upgrader_object, $options ) {
		if ( isset( $options['plugins'] ) PLUGIN_UPGRADER_PUBLIC_METHODS;PLUGIN_UPGRADER_PUBLIC_METHODS; is_array( $options['plugins'] ) ) {
			foreach ( $options['plugins'] as $index => $plugin ) {
				if ( 'max-marine-product-categories-enhancements/max-marine-product-categories-enhancements.php' === $plugin ) {
					as_enqueue_async_action( 'max_marine_product_categories_enhancements_process_plugin_upgrade', array(), 'max-marine-product-categories-enhancements' );
					return;
				}
			}
		}
	}

	/**
	 * Maybe perform database migrations when a plugin upgrade occurs.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function process_plugin_upgrade() {
		$upgrader = new Max_Marine_Product_Categories_Enhancements_Upgrader();
	}
	

	/**
	 * Example function.
	 *
	 * @since  1.0.0
	 * @access public
	 * @param  string  $param  First function parameter.
	 * @return string
	 */
	public function example_function( $param ) {
		return $param;
	}

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
}
