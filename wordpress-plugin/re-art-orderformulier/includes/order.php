<?php
/**
 * Validatie en opslag van orders.
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Orders als (niet-openbaar) berichttype in het beheer
 * ---------------------------------------------------------------------- */

add_action( 'init', 'reart_of_register_post_type' );
function reart_of_register_post_type() {
	register_post_type(
		'reart_order',
		array(
			'labels'              => array(
				'name'          => 'Orders',
				'singular_name' => 'Order',
				'menu_name'     => 'Orders',
				'all_items'     => 'Alle orders',
				'edit_item'     => 'Order bekijken',
				'search_items'  => 'Orders zoeken',
				'not_found'     => 'Nog geen orders',
			),
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'exclude_from_search' => true,
			'menu_icon'           => 'dashicons-clipboard',
			'menu_position'       => 26,
			'supports'            => array( 'title' ),
			'capability_type'     => 'post',
			'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
			'map_meta_cap'        => true,
		)
	);
}

/* -------------------------------------------------------------------------
 * Validatie: bouwt een schone order op uit de ingezonden gegevens
 * ---------------------------------------------------------------------- */

/**
 * @param array $data   Ingezonden JSON.
 * @param array $errors Wordt gevuld met foutmeldingen.
 * @return array Order met klantgegevens en regels.
 */
function reart_of_build_order( array $data, array &$errors ) {
	$text = function ( $key, $max = 200 ) use ( $data ) {
		$v = isset( $data[ $key ] ) && is_scalar( $data[ $key ] ) ? sanitize_text_field( (string) $data[ $key ] ) : '';
		return mb_substr( $v, 0, $max );
	};

	$order = array(
		'naam'        => $text( 'naam' ),
		'datum'       => $text( 'datum', 10 ),
		'email'       => sanitize_email( $text( 'email' ) ),
		'tel'         => $text( 'tel', 40 ),
		'opmerkingen' => isset( $data['opmerkingen'] ) && is_scalar( $data['opmerkingen'] ) ? mb_substr( sanitize_textarea_field( (string) $data['opmerkingen'] ), 0, 3000 ) : '',
		'lines'       => array(),
	);

	if ( '' === $order['naam'] ) {
		$errors[] = 'naam of bedrijfsnaam ontbreekt';
	}
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $order['datum'] ) ) {
		$errors[] = 'datum ontbreekt';
	}
	if ( ! is_email( $order['email'] ) ) {
		$errors[] = 'e-mailadres is ongeldig';
	}

	$lines = isset( $data['lines'] ) && is_array( $data['lines'] ) ? array_values( $data['lines'] ) : array();
	if ( ! $lines ) {
		$errors[] = 'er staat geen werk in de order';
	}
	if ( count( $lines ) > 50 ) {
		$errors[] = 'maximaal 50 werken per order';
		$lines    = array_slice( $lines, 0, 50 );
	}

	foreach ( $lines as $i => $line ) {
		if ( is_array( $line ) ) {
			$order['lines'][] = reart_of_build_line( $line, $i + 1, $errors );
		}
	}

	return $order;
}

/**
 * Eén werk: controleert de keuzes volgens dezelfde logica als het formulier.
 */
function reart_of_build_line( array $line, $n, array &$errors ) {
	$c       = reart_of_catalog();
	$details = array();

	$get = function ( $key, $max = 200 ) use ( $line ) {
		$v = isset( $line[ $key ] ) && is_scalar( $line[ $key ] ) ? sanitize_text_field( (string) $line[ $key ] ) : '';
		return mb_substr( $v, 0, $max );
	};
	// Keuze uit een vaste lijst; voegt toe aan de details of meldt wat ontbreekt.
	$choice = function ( $key, $label, array $allowed, $display = null ) use ( $get, $n, &$errors, &$details ) {
		$v = $get( $key );
		if ( ! in_array( $v, $allowed, true ) ) {
			$errors[] = "werk $n: " . strtolower( $label ) . ' ontbreekt';
			return '';
		}
		$details[] = array( $label, null === $display ? $v : $display( $v ) );
		return $v;
	};
	$required_text = function ( $key, $label ) use ( $get, $n, &$errors, &$details ) {
		$v = trim( $get( $key ) );
		if ( '' === $v ) {
			$errors[] = "werk $n: " . strtolower( $label ) . ' ontbreekt';
			return '';
		}
		$details[] = array( $label, $v );
		return $v;
	};

	$aantal = (int) $get( 'aantal', 6 );
	if ( $aantal < 1 || $aantal > 9999 ) {
		$errors[] = "werk $n: aantal is ongeldig";
		$aantal   = max( 1, min( 9999, $aantal ) );
	}

	$dienst = $choice(
		'dienst',
		'Afwerking',
		array_keys( $c['services'] ),
		function ( $v ) use ( $c ) {
			return $c['services'][ $v ];
		}
	);

	if ( in_array( $dienst, array( 'cert-canvas', 'basic-canvas' ), true ) ) {
		if ( 'Ja' === $choice( 'uv', 'UV-topcoating', $c['yes_no'] ) ) {
			$choice( 'uvType', 'Type coating', $c['coating'] );
		}
		if ( 'Ja' === $choice( 'spannen', 'Opspannen', $c['yes_no'] ) ) {
			$frame = $choice( 'frame', 'Type frame', array_keys( $c['diepte'] ) );
			if ( $frame ) {
				$choice( 'diepte', 'Diepte frame', $c['diepte'][ $frame ] );
			}
		}
	} elseif ( 'cert-paper' === $dienst ) {
		$cat = $choice(
			'paperCat',
			'Papiersoort',
			array_keys( $c['paper'] ),
			function ( $v ) use ( $c ) {
				return $c['paper'][ $v ]['label'];
			}
		);
		if ( $cat ) {
			$choice( 'paperType', 'Papiertype', $c['paper'][ $cat ]['types'] );
		}
	} elseif ( 'basic-paper' === $dienst ) {
		$choice( 'basicType', 'Papiertype', $c['basic_paper'] );
	} elseif ( in_array( $dienst, array( 'hd-finish', 'hd-decor' ), true ) ) {
		if ( 'White surface' === $choice( 'top', 'Surface', $c['surface'] ) ) {
			$choice( 'topFinish', 'Afwerking', $c['gloss_matt'] );
		}
		$choice( 'profiel', 'Ophangprofiel', $c['profiel'] );
	} elseif ( 'scan' === $dienst ) {
		$choice( 'scan', 'Type scan', $c['scans'] );
		$choice( 'proof', 'Kleurproofing', $c['yes_no'] );
	}

	if ( $dienst ) {
		$required_text( 'size', 'Titel & formaat' );

		if ( 'scan' !== $dienst && 'Ja' === $choice( 'cert', 'Certificaat', $c['yes_no'] ) ) {
			$required_text( 'certArtist', 'Kunstenaar' );
			$web = trim( $get( 'certWeb' ) );
			if ( '' !== $web ) {
				$details[] = array( 'Website', $web );
			}
			$required_text( 'certTitel', 'Titel werk' );
			$editie = $required_text( 'certEditie', 'Editie' );
			$ea     = trim( $get( 'certEA' ) );
			if ( '' !== $ea ) {
				$details[] = array( 'EA', $ea );
			}
			if ( is_numeric( $editie ) ) {
				$details[] = array( 'Totale editie', (string) ( (int) $editie + (int) $ea ) );
			}
		}
	}

	return array(
		'aantal'  => $aantal,
		'details' => $details,
	);
}

/* -------------------------------------------------------------------------
 * Opslaan
 * ---------------------------------------------------------------------- */

/**
 * Slaat de order op en geeft het bericht-ID terug. De titel wordt het ordernummer.
 */
function reart_of_save_order( array $order ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'reart_order',
			'post_status' => 'publish',
			'post_title'  => 'Nieuwe order',
		),
		true
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	$no          = 'RA-' . wp_date( 'ymd' ) . '-' . $post_id;
	$order['no'] = $no;
	wp_update_post(
		array(
			'ID'         => $post_id,
			'post_title' => $no . ' – ' . $order['naam'],
		)
	);
	update_post_meta( $post_id, '_reart_of_no', $no );
	update_post_meta( $post_id, '_reart_of_order', $order );

	return $post_id;
}
