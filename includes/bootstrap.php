<?php
function pmpro_euinv_activation() {
}

function pmpro_euinv_deactivation() {
	global $wpdb;

	$tables = array(
		'pmpro_invoices',
	);

	foreach ( $tables as $table ) {
		$delete_table = $wpdb->prefix . $table;
		// setup sql query
		$sql = "DROP TABLE `$delete_table`";
		// run the query
		$wpdb->query( $sql );
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	//delete options
	global $wpdb;
	$sqlQuery = "DELETE FROM $wpdb->options WHERE option_name LIKE 'pmpro_invoices_%'";
	$wpdb->query( $sqlQuery );
}
