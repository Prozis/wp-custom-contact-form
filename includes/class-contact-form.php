<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://example.com
 * @since      1.0.0
 *
 * @package    Contact_Form
 * @subpackage Contact_Form/includes
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
 * @package    Contact_Form
 * @subpackage Contact_Form/includes
 * @author     Andrew Harashchenia <prozius11@gmail.com>
 */
class Contact_Form {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Contact_Form_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

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

	protected $reg_errors;

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
		if ( defined( 'CONTACT_FORM_VERSION' ) ) {
			$this->version = CONTACT_FORM_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'contact-form';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->reg_errors = new WP_Error;

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Contact_Form_Loader. Orchestrates the hooks of the plugin.
	 * - Contact_Form_i18n. Defines internationalization functionality.
	 * - Contact_Form_Admin. Defines all hooks for the admin area.
	 * - Contact_Form_Public. Defines all hooks for the public side of the site.
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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-contact-form-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-contact-form-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-contact-form-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-contact-form-public.php';

		$this->loader = new Contact_Form_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Contact_Form_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Contact_Form_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Contact_Form_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Contact_Form_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
		$this->add_short_code();
	}

	protected function add_contact_form() {
		$firstname = $_POST['firstname'] ?? null;
		$lastName  = $_POST['lastname'] ?? null;
		$subject   = $_POST['subject'] ?? null;
		$message   = $_POST['message'] ?? null;
		$email     = $_POST['email'] ?? null;

		echo '
    <div class="cf-contact-from">
    <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
    <div>
    <label for="firstname">First Name<strong>*</strong></label>
    <input type="text" name="firstname" value="' . $firstname . '">
    </div>
    
    <div>
    <label for="lastname">Last Name<strong>*</strong></label>
    <input type="text" name="lastname" value="' . $lastName . '">
    </div>

    <div>
    <label for="subject">Subject <strong>*</strong></label>
    <input type="text" name="subject" value="' . $subject . '">
    </div>

    <div>
    <label for="message">Message <strong>*</strong></label>
    <textarea name="message">' . $message . ' </textarea>
    </div>
    
    <div>
    <label for="email">E-mail <strong>*</strong></label>
    <input type="text" name="email" value="' . $email . '">
    </div>
    
    <input type="submit" name="submit" value="Send mail"/>
    </form>
    </div>
    ';
	}

	protected function contact_form_validation( $firstname, $lastName, $subject, $message, $email ) {
		if ( empty( $firstname ) || empty( $lastName ) || empty( $subject ) || empty( $message ) || empty( $email ) ) {
			$this->reg_errors->add( 'field', 'Required form field is missing' );
		}

		if ( 4 > strlen( $message ) ) {
			$this->reg_errors->add( 'message_length', 'Message too short. At least 4 characters is required' );
		}

		if ( ! empty( $email ) ) {
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$this->reg_errors->add( 'email', 'Email is not a valid' );
			}
		}

		if ( is_wp_error( $this->reg_errors ) ) {

			foreach ( $this->reg_errors->get_error_messages() as $error ) {

				echo '<div>';
				echo '<strong>ERROR</strong>:';
				echo $error . '<br/>';
				echo '</div>';

			}
		}


	}

	protected function complete_contact_form( $firstname, $lastName, $subject, $message, $email ) {
		if ( 1 > count( $this->reg_errors->get_error_messages() ) ) {
			$admin_email = get_option( 'admin_email' );
			wp_mail( $admin_email, $subject, $message );

			//add message to log
			$logMessage = 'Email sent from ' . $email;
			$this->log( $logMessage );

			echo 'Email sended';
		}
	}


	protected function custom_contact_form_main() {
		if ( isset( $_POST['submit'] ) ) {
			$this->contact_form_validation(
				$_POST['firstname'],
				$_POST['lastname'],
				$_POST['subject'],
				$_POST['message'],
				$_POST['email']
			);

			$firstname = sanitize_text_field( $_POST['firstname'] );
			$lastName  = sanitize_text_field( $_POST['lastname'] );
			$subject   = sanitize_text_field( $_POST['subject'] );
			$message   = sanitize_text_field( $_POST['message'] );
			$email     = sanitize_email( $_POST['email'] );

			$this->complete_contact_form(
				$firstname, $lastName, $subject, $message, $email
			);
		}

		$this->add_contact_form();
	}

	protected function add_short_code() {
		add_shortcode( 'cf_custom_contact_form', function () {
			ob_start();
			$this->custom_contact_form_main();

			return ob_get_clean();
		} );
	}

	/**
	 * Prints a message to the debug file that can easily be called by any subclass.
	 *
	 * @param mixed $message an object, array, string, number, or other data to write to the debug log
	 *
	 */
	protected function log( $message ) {
		error_log( print_r( $message, true ) );
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 * @since     1.0.0
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    Contact_Form_Loader    Orchestrates the hooks of the plugin.
	 * @since     1.0.0
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return    string    The version number of the plugin.
	 * @since     1.0.0
	 */
	public function get_version() {
		return $this->version;
	}

}
