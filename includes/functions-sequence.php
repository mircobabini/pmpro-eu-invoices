<?php

/**
 * @param bool $increment
 *
 * @return int
 */
function pmpro_euinv_internalSequence_getNextNumber( $increment = false ) {
	if ( $reset_counter_on_year_change = true ) {
		$seen_year = intval( pmpro_getOption( 'invoice_seen_year', 0 ) );
		$curr_year = intval( date( 'Y' ) );

		if ( $seen_year < $curr_year ) {
			pmpro_euinv_internalSequence_resetNumber();
			pmpro_setOption( 'invoice_seen_year', $curr_year );
		}
	}

	$nextNumber = intval( pmpro_getOption( 'invoice_next_number' ) );

	if ( ! $nextNumber ) {
		$nextNumber = 1;
	}

	if ( $increment ) {
		pmpro_euinv_internalSequence_setNextNumber( $nextNumber + 1 );
	}

	return $nextNumber;
}

/**
 * @param int $nextNumber
 *
 * @return void
 */
function pmpro_euinv_internalSequence_setNextNumber( $nextNumber ) {
	pmpro_setOption( 'invoice_next_number', $nextNumber );
}

/**
 * @return void
 */
function pmpro_euinv_internalSequence_resetNumber() {
	pmpro_euinv_internalSequence_setNextNumber( 1 );
}
