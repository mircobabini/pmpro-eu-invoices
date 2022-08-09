<?php
/*
Plugin Name: Paid Memberships Pro - European Invoices Add On
Plugin URI: http://www.paidmembershipspro.com/add-ons/pmpro-eu-invoices/
Description: Add invoice incremental counter and other features to comply with european laws.
Version: 1.0.0
Author: Stranger Studios
Author URI: http://www.strangerstudios.com
Text Domain: pmpro-eu-invoices
Domain Path: /languages
*/

define( 'PMPRO_EUINV_VERSION', '1.0.0' );
define( 'PMPRO_EUINV_BASE_FILE', __FILE__ );
define( 'PMPRO_EUINV_DIR', dirname( __FILE__ ) );

require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/bootstrap.php';

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/functions-sequence.php';
require_once __DIR__ . '/includes/functions-pmpropdf.php';

require_once __DIR__ . '/includes/compat/pmpro-pdf-invoices.php';
require_once __DIR__ . '/includes/compat/pmpro-address-for-free-levels.php';

register_activation_hook( __FILE__, 'pmpro_euinv_activation' );
register_deactivation_hook( __FILE__, 'pmpro_euinv_deactivation' );

/**
 * Load text domain
 * pmpro_euinv_load_plugin_text_domain
 */
function pmpro_euinv_load_plugin_text_domain() {
	load_plugin_textdomain( 'pmpro-eu-invoices', false, basename( PMPRO_EUINV_DIR ) . '/languages' );
}
add_action( 'init', 'pmpro_euinv_load_plugin_text_domain' );

/**
 * Function to add links to the plugin row meta
 */
function pmpro_euinv_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'pmpro-eu-invoices.php' ) !== false ) {
		$new_links = array(
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/add-ons/plugins-on-github/pmpro-eu-invoices/' ) . '" title="' . esc_attr( __( 'View Documentation', 'pmpro-eu-invoices' ) ) . '">' . __( 'Docs', 'pmpro-eu-invoices' ) . '</a>',
			'<a href="' . esc_url( 'https://www.paidmembershipspro.com/support/' ) . '" title="' . esc_attr( __( 'Visit Customer Support Forum', 'pmpro-eu-invoices' ) ) . '">' . __( 'Support', 'pmpro-eu-invoices' ) . '</a>',
		);
		$links     = array_merge( $links, $new_links );
	}

	return $links;
}
add_filter( 'plugin_row_meta', 'pmpro_euinv_plugin_row_meta', 10, 2 );
