<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @package    Plugin_Name_Name_Space
 * @author     Mehdi Soltani <soltani.n.mehdi@gmail.com>
 * @license    https://www.gnu.org/licenses/gpl-3.0.txt GNU/GPLv3
 * @link       https://yoursite.com
 * @since      1.0.0
 */

namespace Plugin_Name_Name_Space\Includes\Init;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Plugin_Name_Name_Space\Includes\Abstracts\{
	Admin_Menu, Admin_Sub_Menu, Ajax, Meta_box
};

use Plugin_Name_Name_Space\Includes\Interfaces\{
	Action_Hook_Interface, Filter_Hook_Interface
};
use Plugin_Name_Name_Space\Includes\Admin\{
	Admin_Menu1, Admin_Sub_Menu1, Admin_Sub_Menu2
};
use Plugin_Name_Name_Space\Includes\Config\{
	Register_Post_Type, Sample_Post_Type, Initial_Value
};
use Plugin_Name_Name_Space\Includes\Functions\{
	Init_Functions, Utility, Check_Type
};

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.1
 * @package    Plugin_Name_Name_Space
 * @author     Mehdi Soltani <soltani.n.mehdi@gmail.com>
 */
class Core {
	use Utility;
	use Check_Type;
	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * @var Public_Hook $hook_object Object  to keep all of hooks in your theme
	 */
	protected $hooks;

	/**
	 * @var Admin_Menu[] $admin_menus
	 */
	protected $admin_menus;

	/**
	 * @var Admin_Sub_Menu[] $admin_sub_menus
	 */
	protected $admin_sub_menus;

	/**
	 * @var Ajax[] $ajax_calls
	 */
	protected $ajax_calls;

	/**
	 * @var Initial_Value $initial_values An object  to keep all of initial values for theme
	 */
	protected $initial_values;

	/**
	 * @var Meta_box[] $meta_boxes
	 */
	protected $meta_boxes;

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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		if ( defined( 'PLUGIN_NAME_MAIN_NAME' ) ) {
			$this->plugin_name = PLUGIN_NAME_MAIN_NAME;
		} else {
			$this->plugin_name = 'plugin-name';
		}

	}

	/**
	 * Run the Needed methods for plugin
	 *
	 * In run method, you can run every methods that you need to run every time that your plugin is loaded.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\Loader
	 */
	public function run() {
		$this->load_dependencies();
		$this->set_locale();
		if ( is_admin() ) {
			$this->set_admin_menu();
			$this->define_admin_hooks();
		} else {
			$this->define_public_hooks();
			$this->check_url();
		}
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * You can Include related files or init functions that you need when
	 * your plugin is executed. The first thing is creating an object from
	 * Loader class that can run all of actions and filters in your plugin
	 * in an organized way.
	 * Then e.g. you can load init functions that you need in starting of your
	 * plugin (in this sample, we use from Init_Function class and related static
	 * methods)
	 * Notice that create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\Loader
	 * @see      \Plugin_Name_Name_Space\Includes\Functions\Init_Functions
	 */
	private function load_dependencies() {

		$plugin_name_hooks_loader = new Init_Functions();
		add_action( 'init', array( $plugin_name_hooks_loader, 'app_output_buffer' ) );
		/*To add your custom post type*/
		Sample_Post_Type::instance();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_I18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\I18n
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();
		add_action( 'plugins_loaded', array( $plugin_i18n, 'load_plugin_textdomain' ) );
	}

	/**
	 * Define admin menu for your plugin
	 *
	 * If you need some admin menus in WordPress admin panel, you can use
	 * from this method.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Admin\Admin_Menu
	 * @see      \Plugin_Name_Name_Space\Includes\Config\Initial_Value
	 */
	private function set_admin_menu() {
		/*$plugin_name_sample_admin_menu = new Admin_Menu( Initial_Value::sample_menu_page() );
		add_action( 'admin_menu', array( $plugin_name_sample_admin_menu, 'add_admin_menu_page' ) );

		$plugin_name_sample_admin_sub_menu1 = new Admin_Sub_Menu( Initial_Value::sample_sub_menu_page1() );
		add_action( 'admin_menu', array( $plugin_name_sample_admin_sub_menu1, 'add_admin_sub_menu_page' ) );

		$plugin_name_sample_admin_sub_menu2 = new Admin_Sub_Menu( Initial_Value::sample_sub_menu_page2() );
		add_action( 'admin_menu', array( $plugin_name_sample_admin_sub_menu2, 'add_admin_sub_menu_page' ) );*/
	}

	/**
	 * Define hooks these are needed in admin panel of WordPress
	 *
	 * If you need to some hooks these are needed in WordPress admin panel
	 * you can use from this method. In this boilerplate, I only use it to
	 * register and enqueueing styles and scripts in admin panel
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\Admin_Hook
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin_Hook( $this->get_plugin_name(), $this->get_version() );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );

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
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\Public_Hook
	 */
	private function define_public_hooks() {

		$plugin_public = new Public_Hook( $this->get_plugin_name(), $this->get_version() );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );

	}

	/**
	 * Define router to handle url request
	 *
	 * If you need to check url and redirect user to other page except admin
	 * panel of WordPress (or you need to have specific panel for your WordPress
	 * site), you need to handle request by routers. To do that, you can use from
	 * Router class to manage your routes inside of your plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @see      \Plugin_Name_Name_Space\Includes\Init\Router
	 */
	private function check_url() {
		$check_url_object = new Router();
		add_action( 'init', array( $check_url_object, 'boot' ) );
	}

}

