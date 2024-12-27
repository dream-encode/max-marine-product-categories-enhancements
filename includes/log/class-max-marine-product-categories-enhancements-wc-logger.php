<?php
/**
 * Simple logger class that relies on the WC_Logger instance.
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Max_Marine_Product_Categories_Enhancements/includes/log
 */

namespace Max_Marine\Product_Categories_Enhancements\Core\Log;

use Max_Marine\Product_Categories_Enhancements\Core\Abstracts\Max_Marine_Product_Categories_Enhancements_Abstract_WC_Logger;

/**
 * Simple logger class to log data to custom files.
 *
 * Relies on the bundled logger class in WooCommerce.
 *
 * @package  Max_Marine\Product_Categories_Enhancements\Core\Log\Max_Marine_Product_Categories_Enhancements_WC_Logger
 * @author   David Baumwald <david@dream-encode.com>
 */
final class Max_Marine_Product_Categories_Enhancements_WC_Logger extends Max_Marine_Product_Categories_Enhancements_Abstract_WC_Logger {

	/**
	 * Log namespace.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $namespace  Log namespace.
	 */
	protected static $namespace = 'max-marine-product-categories-enhancements';
}
