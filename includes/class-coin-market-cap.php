<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/includes
 * @author     korivr <korvir78.dev@gmail.com>
 */
class Coin_Market_Cap {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Coin_Market_Cap_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'COIN_MARKET_CAP_VERSION' ) ) {
			$this->version = COIN_MARKET_CAP_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'coin-market-cap';

		$this->load_dependencies();
		$this->set_locale();
		$this->set_shortcode();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->get_coinmarket_data();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Coin_Market_Cap_Loader. Orchestrates the hooks of the plugin.
	 * - Coin_Market_Cap_i18n. Defines internationalization functionality.
	 * - Coin_Market_Cap_Admin. Defines all hooks for the admin area.
	 * - Coin_Market_Cap_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coin-market-cap-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-coin-market-cap-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-coin-market-cap-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-coin-market-cap-public.php';

		$this->loader = new Coin_Market_Cap_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Coin_Market_Cap_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Coin_Market_Cap_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}


	private function set_shortcode() {
		add_shortcode( 'coinmarket', ['Coin_Market_Cap', 'coinmarket_shortcode_render'] );
	}





	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Coin_Market_Cap_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'coin_market_settings' );


		// May be need to use system cron
		//$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'cron_add_5min' );
		//$this->loader->add_action( 'wp', $plugin_admin, 'coin_market_sheduler' );
		//$this->loader->add_action( 'coin_market_sheduler_event', $plugin_admin, 'do_coin_market_event' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Coin_Market_Cap_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		// Cron events
		$this->loader->add_filter( 'cron_schedules', $plugin_public, 'cron_add_5min' );
		$this->loader->add_action( 'wp', $plugin_public, 'coin_market_sheduler' );
		$this->loader->add_action( 'coin_market_sheduler_event', $plugin_public, 'do_coin_market_event' );

		//Save history
		$this->loader->add_action( 'wp_ajax_save_history', $plugin_public, 'coin_market_save_history' );
		$this->loader->add_action( 'wp_ajax_nopriv_save_history', $plugin_public, 'coin_market_save_history' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Coin_Market_Cap_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


	public function get_coinmarket_data(){
		global $wpdb;
		return $wpdb->get_results( "SELECT * FROM $wpdb->prefix . 'coinmarket_currency'" );
	}




	public function coinmarket_shortcode_render() {
		global $wpdb;
		$data = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix.'coinmarket_currency' );

		$history = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix.'coinmarket_operations' . ' LIMIT 10' );

		if ( $data ) : ?>
			<div class="coinmarket-shortcode">
				<div class="coinmarket-shortcode--from">
					<input name="coinmarket_from_number"
					       min="1"
					       id="coinmarket_from_number"
					       type="number"
					       value="1"
					       class="coinmarket_from_number" >
					<select name="coinmarket_from_select" id="coinmarket_from_select" class="coinmarket_from_select">
					<?php foreach ( $data as $key => $value ) : ?>
							<option value="<?php echo $value->id ?>" data-price="<?php echo $value->price ?>" data-symbol="<?php echo $value->symbol ?>">
								<?php echo $value->symbol ?> - <?php echo $value->name ?>
							</option>
					<?php endforeach; ?>
					</select>
				</div>

				<div class="coinmarket-shortcode--to">

					<input name="coinmarket_to_number"
					       min="1"
					       id="coinmarket_to_number"
					       type="number"
					       value="1"
					       class="coinmarket_to_number">
					<select name="coinmarket_to_select" id="coinmarket_to_select" class="coinmarket_to_select">
						<?php foreach ( $data as $key => $value ) : ?>
							<option value="<?php echo $value->id ?>" data-price="<?php echo $value->price ?>" data-symbol="<?php echo $value->symbol ?>">
								<?php echo $value->symbol ?> - <?php echo $value->name ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<?php if ( $history ) : ?>
					<div class="history">
						<?php foreach ( $history as $row) : ?>
						<div class="row">
							<?php
							echo $row->from_amount . ' ' . $row->from_symbol . ' convert to ' . $row->to_amount . ' ' . $row->to_symbol;
							?>
						</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

			</div>
		<?php
		endif;


	}



}
