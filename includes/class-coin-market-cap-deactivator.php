<?php

/**
 * Fired during plugin deactivation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 * @author     korivr <korvir78.dev@gmail.com>
 */
class Coin_Market_Cap_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		global $wpdb;
		$tableArray = [
			$wpdb->prefix . "coinmarket_currency",
			$wpdb->prefix . "coinmarket_operations",
		];

		foreach ($tableArray as $tablename) {
			$wpdb->query("DROP TABLE IF EXISTS $tablename");
		}

		delete_option('coinmarket_currency_db_version' );
	}

}
