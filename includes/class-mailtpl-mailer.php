<?php

/**
 * All mail functions will go in here
 *
 * @link       https://wp.timersys.com
 * @since      1.0.0
 *
 * @package    Mailtpl
 * @subpackage Mailtpl/includes
 * @author     Damian Logghe <info@timersys.com>
 */
class Mailtpl_Mailer {

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

		$this->plugin_name  = $plugin_name;
		$this->version      = $version;
		$this->opts         = Mailtpl::opts();

	}

	/**
	 * Send html emails instead of text plain
	 * @since 1.0.0
	 * @return string
	 */
	public function set_content_type() {
		return $content_type = 'text/html';
	}

	/**
	 * Modify php mailer body with final email
	 *
	 * @since 1.0.0
	 * @param object $phpmailer
	 */
	function send_email( $phpmailer ) {

		$message            =  $this->add_template( apply_filters( 'mailtpl/email_content', $phpmailer->Body ) );
		$phpmailer->Body    =  $this->replace_placeholders( $message );

	}

	/**
	 * Mandrill Compatibility
	 * @param $message Array
	 *
	 * @return Array
	 */
	public function send_email_mandrill( $message ) {
		$message            =  $this->add_template( apply_filters( 'mailtpl/email_content', $message['html'] ) );
		$message['html']    =  $this->replace_placeholders( $message );
		return $message;
	}

	/**
	 * Send a test email to admin email
	 * @since 1.0.0
	 */
	public function send_test_email () {
		ob_start();
		include_once( MAILTPL_PLUGIN_DIR . '/admin/templates/partials/default-message.php');
		$message = ob_get_contents();
		ob_end_clean();
		$subject = __( 'Wp Email Templates', $this->plugin_name);

		echo wp_mail( get_bloginfo('admin_email'), $subject, $message);

		die();
	}

	/**
	 * Add template to plain mail
	 * @param $email string Mail to be send
	 * @since 1.0.0
	 * @return string
	 */
	private function add_template( $email ) {
		$template = apply_filters( 'mailtpl/customizer_template', MAILTPL_PLUGIN_DIR . "/admin/templates/default.php");
		ob_start();
		include_once( $template );
		$template = ob_get_contents();
		ob_end_clean();
		return str_replace( '%%MAILCONTENT%%', $email, $template );
	}

	/**
	 * Replace placeholders
	 * @param $email string Mail to be send
	 *
	 * @return string
	 */
	private function replace_placeholders( $email ) {

		$to_replace = apply_filters( 'emailtpl/placeholders', array(
			'%%BLOG_URL%%'         => get_option( 'siteurl' ),
			'%%HOME_URL%%'         => get_option( 'home' ),
			'%%BLOG_NAME%%'        => get_option( 'blogname' ),
			'%%BLOG_DESCRIPTION%%' => get_option( 'blogdescription' ),
			'%%ADMIN_EMAIL%%'      => get_option( 'admin_email' ),
			'%%DATE%%'             => date_i18n( get_option( 'date_format' ) ),
			'%%TIME%%'             => date_i18n( get_option( 'time_format' ) )
		));

		foreach ( $to_replace as $placeholder => $var ) {
			$email = str_replace( $placeholder , $var, $email );
		}

		return $email;

	}

	/**
	 * Sets email's From email
	 * @since 1.0.0
	 * @return string
	 */
	public function set_from_email(){
		return $this->opts['from_email'];
	}

	/**
	 * Sets email's From name
	 * @since 1.0.0
	 * @return string
	 */
	public function set_from_name(){
		return $this->opts['from_name'];
	}

}
