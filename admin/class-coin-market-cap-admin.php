<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Coin_Market_Cap
 * @subpackage Coin_Market_Cap/admin
 * @author     korivr <korvir78.dev@gmail.com>
 */
class Coin_Market_Cap_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/coin-market-cap-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/coin-market-cap-admin.js', array( 'jquery' ), $this->version, false );

	}




	/**
	 * Creates the submenu item to render
	 * the actual contents of the page.
	 */
	public function add_admin_menu() {
		add_options_page(
			'Coin Market Settings',
			'Coin Market Settings',
			'manage_options',
			'coin-market-page',
			[ $this, 'coin_market_render_menu']
		);
	}

	public function coin_market_render_menu() {
		?>
		<div class="wrap">
			<h2><?php echo get_admin_page_title() ?></h2>

			<form action="options.php" method="POST">
				<?php
				settings_fields( 'option_group' );     // скрытые защитные поля
				do_settings_sections( 'primer_page' ); // секции с настройками (опциями). У нас она всего одна 'section_id'
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function coin_market_settings(){
		register_setting( 'option_group', 'option_name', 'sanitize_callback' );
		add_settings_section( 'section_id', 'Main Settings', '', 'primer_page' );
		add_settings_field('primer_field1', 'API Key', [ $this, 'fill_primer_field1'], 'primer_page', 'section_id' );
	}

	public function fill_primer_field1(){
		$val = get_option('option_name');
		$val = $val ? $val['input'] : null;
		?>
		<input type="text" name="option_name[input]" value="<?php echo esc_attr( $val ) ?>" style="width: 350px" />
		<?php
	}

	public function fill_primer_field2(){
		$val = get_option('option_name');
		$val = $val ? $val['checkbox'] : null;
		?>
		<label><input type="checkbox" name="option_name[checkbox]" value="1" <?php checked( 1, $val ) ?> /> отметить</label>
		<?php
	}

	public function sanitize_callback( $options ){
		foreach( $options as $name => & $val ){
			if( $name == 'input' )
				$val = strip_tags( $val );
		}
		return $options;
	}


}
