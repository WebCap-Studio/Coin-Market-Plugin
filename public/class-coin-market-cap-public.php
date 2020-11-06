<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/public
 * @author     korivr <korvir78.dev@gmail.com>
 */
class Coin_Market_Cap_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coin_Market_Cap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coin_Market_Cap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_style( $this->plugin_name.'_select2', plugin_dir_url( __FILE__ ) . 'css/coin-market-cap-select2.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coin-market-cap-public.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Coin_Market_Cap_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Coin_Market_Cap_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		//wp_enqueue_script( $this->plugin_name.'_select2',plugin_dir_url( __FILE__ ) . 'js/coin-market-cap-select2.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coin-market-cap-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'data_js', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('media-form')
		));

	}


	// Cron events
	public function cron_add_5min($schedules)
	{
		$schedules['5min'] = array(
			'interval' => 5 * 60,
			'display' => __('Once a 5 min'),
		);
		return $schedules;
	}

	public function coin_market_sheduler()
	{
		if ( ! wp_next_scheduled('coin_market_sheduler_event')) {
			wp_schedule_event(time(), '5min', 'coin_market_sheduler_event');
		}
	}

	public function do_coin_market_event()
	{

		global $wpdb;
		$wpdb->query('TRUNCATE TABLE ' . $wpdb->prefix.'coinmarket_currency');

		$i = 1;
		do {
			# Get data
			$url = 'https://sandbox-api.coinmarketcap.com/v1/cryptocurrency/listings/latest';
			$parameters = [
				'start' => $i,
				'limit' => '500',
				'convert' => 'USD'
			];

			$headers = [
				'Accepts: application/json',
				'X-CMC_PRO_API_KEY: b54bcf4d-1bca-4e8e-9a24-22ff2c3d462c'
			];
			$qs = http_build_query($parameters);
			$request = "{$url}?{$qs}";


			$curl = curl_init();

			// Set cURL options
			curl_setopt_array($curl, array(
				CURLOPT_URL => $request,
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_RETURNTRANSFER => 1
			));

			$response = curl_exec($curl);
			curl_close($curl);
			# ---

			# Do something with data
			$bigdata = json_decode($response);

			if ( $bigdata->status->error_code === 0 )
			{

				foreach ( $bigdata->data as $key => $value ){
					$result = $wpdb->insert(
						$wpdb->prefix . 'coinmarket_currency',
						array( 'currency_id' 	=> $value->id,
						       'symbol' 		=> $value->symbol,
						       'name' 			=> $value->name,
						       'slug' 			=> $value->slug,
						       'price' 			=> $value->quote->USD->price,
						       'time' 			=> $value->last_updated
						),
						array( '%d', '%s', '%s', '%s', '%f', '%s'  )
					);


				}

			}


			$i += 500;

		} while ( $i <= $bigdata->status->total_count);

	}

	public function coin_market_save_history(){

		global $wpdb;
		$data = $_POST;

		$result = $wpdb->insert(
			$wpdb->prefix . 'coinmarket_operations',
			array( 'from_amount' 	    => $_POST['input_from'],
			       'from_symbol' 		=> $_POST['symbol_from'],
			       'to_amount' 			=> $_POST['input_to'],
			       'to_symbol' 			=> $_POST['symbol_to']
			),
			array( '%s', '%s', '%s', '%s' )
		);

		echo json_encode(array(
			'status' => 200,
			'$result' => $result,
		));
		wp_die();

	}


}
