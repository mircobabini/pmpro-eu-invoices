<?php
function pmpro_euinv_database_init() {
	global $wpdb;

	$wpdb->hide_errors();
	$wpdb->pmpro_invoices = $wpdb->prefix . 'pmpro_invoices';

	// TODO run this only on pmpro settings page
	pmpro_euinv_check_upgrade();
}
add_action( 'plugins_loaded', 'pmpro_euinv_database_init' );

function pmpro_euinv_check_upgrade() {
	$pmpro_invoices_db_version = pmpro_getOption( "pmpro_euinv_db_version" );

	global $wpdb;
	$table_exists = $wpdb->query( "SHOW TABLES LIKE '" . $wpdb->pmpro_invoices . "'" );
	if ( ! $table_exists ) {
		// if we can't find the DB tables, reset db version to 0
		$pmpro_invoices_db_version = 0;
	}

	if ( $pmpro_invoices_db_version < PMPRO_EUINV_VERSION ) {
		pmpro_euinv_dbdelta();
		pmpro_setOption( 'pmpro_euinv_db_version', PMPRO_EUINV_VERSION );

		$pmpro_invoices_db_version = PMPRO_EUINV_VERSION;
	}

	return $pmpro_invoices_db_version;
}

function pmpro_euinv_dbdelta() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	global $wpdb;
	$wpdb->hide_errors();
	$wpdb->pmpro_invoices = $wpdb->prefix . 'pmpro_invoices';

	// we need the following references:
	// - the order_id (from which we can get the user, the membership level...)
	// - the code (which must be incremental pmpro-1, pmpro-2... in Italy, but can be anything in other countries)
	//   varchar(42) which is varchar(32+10), where varchar(32) is the orders' code length
	//   this way it would be possible to use the order's code, plus a prefix/suffix of 10 chars.
	// - the date (which is NOT the order date. the invoice might be added few days later)
	//   for instance, a BACS order can be paid after 5 days. meanwhite there are other invoices
	//   emitted for other orders. in Italy, the incremental invoice code MUST BE in order with the dates
	//   2020/1 on March 10th and 2020/2 on March 20th is ok
	//   2020/2 on March 20th and 2020/1 on March 10th is NOT ok
	//   the rules to keep in mind are
	//   - can't mix the codes and the dates (as explained above);
	//   - can't skip a code (2020/2 can't exist if 2020/1 is not emitted);
	//   - can't have multiple invoices for the same code;
	//   - can avoid collisions from services using prefix/suffix:
	//     - i.e. online and offline invoices with different prefix/suffix and their own incremental number
	//     - i.e. pmpro and wc invoices with different prefix/suffix and their own incremental number

	// we need (at least) the following options:
	// - next number, which is pointer to the next, when using internal sequence (mandatory in Italy)
	// - prefix (optional)
	// - suffix (optional)
	// - separator, between prefix/suffix and number
	// - numbering method: order code (default), order code + prefix/suffix, internal sequence + prefix/suffix
	//   - maybe better to use a formattable field (i.e. %prefix%/%year%-%internal_sequence% to generate PMPRO/2020-1 code)
	$sqlQuery = "
			CREATE TABLE `" . $wpdb->pmpro_invoices . "` (
				`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`membership_order_id` int(10) unsigned NOT NULL,
				`code` varchar(42) NOT NULL,
				`date` datetime NOT NULL,
				`modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`notes` TEXT NOT NULL,
				PRIMARY KEY  (`id`),
				UNIQUE KEY `code` (`code`),
				KEY `membership_order_id` (`membership_order_id`),
				KEY `date` (`date`)
			);
		";

	dbDelta( $sqlQuery );
}
