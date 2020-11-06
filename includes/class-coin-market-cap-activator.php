<?php

/**
 * Fired during plugin activation
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 * @author     korivr <korvir78.dev@gmail.com>
 */
class Coin_Market_Cap_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table_name1 = $wpdb->prefix . 'coinmarket_currency';
		$table_name2 = $wpdb->prefix . 'coinmarket_operations';


		// Table 'coinmarket_currency'
		if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name1) != $table_name1) {

			$sql1 = "CREATE TABLE $table_name1 (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				currency_id mediumint(9) NOT NULL,
				symbol varchar(255) NOT NULL,
				name varchar(255) NOT NULL,
				slug varchar(255) NOT NULL,
				price varchar(255) NOT NULL,
				time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql1 );

			add_option("coinmarket_currency_db_version", COIN_MARKET_CAP_VERSION);
		}



		// Table 'coinmarket_operations'
		if ($wpdb->get_var('SHOW TABLES LIKE '.$table_name2) != $table_name2) {

			$sql2 = "CREATE TABLE $table_name2 (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				from_amount varchar(255) NOT NULL,
				from_symbol varchar(255) NOT NULL,
				to_amount varchar(255) NOT NULL,
				to_symbol varchar(255) NOT NULL,
				PRIMARY KEY  (id)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql2 );
		}

	}


}
