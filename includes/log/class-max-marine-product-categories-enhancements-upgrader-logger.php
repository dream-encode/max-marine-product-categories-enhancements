<?php
/**
 * Simple wrapper class for custom logs.
 *
 * @uses \WC_Logger();
 *
 * @link       https://dream-encode.com
 * @since      1.0.0
 *
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/includes
 */

namespace Max_Marine\Product_Categories_Enhancements\Core\Log;

use Max_Marine\Product_Categories_Enhancements\Core\Abstracts\Max_Marine_Product_Categories_Enhancements_Abstract_WC_Logger;

/**
 * Logger class.
 *
 * Log stuff to files.
 *
 * @since      1.0.0
 * @package    Max_Marine_Product_Categories_Enhancements
 * @subpackage Max_Marine_Product_Categories_Enhancements/includes
 * @author     David Baumwald <david@dream-encode.com>
 */
final class Max_Marine_Product_Categories_Enhancements_Upgrader_Logger extends Max_Marine_Product_Categories_Enhancements_Abstract_WC_Logger {
	/**
	 * Log namespace.
	 *
	 * @since   1.0.0
	 * @access  protected
	 * @var     string  $namespace  Log namespace.
	 */
	public static $namespace = 'max-marine-product-categories-enhancements-upgrader';
}
