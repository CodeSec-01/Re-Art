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
				'name'          => 'Aanvragen',
				'singular_name' => 'Aanvraag',
				'menu_name'     => 'Aanvragen',
				'all_items'     => 'Alle aanvragen',
				'edit_item'     => 'Aanvraag bekijken',
				'search_items'  => 'Aanvragen zoeken',
				'not_found'     => 'Nog geen aanvragen',
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
		'datum'       => wp_date( 'Y-m-d' ), // altijd de dag van insturen
		'email'       => sanitize_email( $text( 'email' ) ),
		'tel'         => $text( 'tel', 40 ),
		'straat'      => $text( 'straat' ),
		'postcode'    => $text( 'postcode', 20 ),
		'plaats'      => $text( 'plaats', 100 ),
		'land'        => $text( 'land', 100 ),
		'lang'        => ( isset( $data['lang'] ) && 'en' === $data['lang'] ) ? 'en' : 'nl',
		'opmerkingen' => isset( $data['opmerkingen'] ) && is_scalar( $data['opmerkingen'] ) ? mb_substr( sanitize_textarea_field( (string) $data['opmerkingen'] ), 0, 3000 ) : '',
		'lines'       => array(),
	);

	if ( '' === $order['naam'] ) {
		$errors[] = 'naam of bedrijfsnaam ontbreekt';
	}
	if ( ! is_email( $order['email'] ) ) {
		$errors[] = 'e-mailadres is ongeldig';
	}

	$lines = isset( $data['lines'] ) && is_array( $data['lines'] ) ? array_values( $data['lines'] ) : array();
	if ( ! $lines ) {
		$errors[] = 'er staat geen werk in de aanvraag';
	}
	if ( count( $lines ) > 50 ) {
		$errors[] = 'maximaal 50 werken per aanvraag';
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

	// Blindpreeg, nummeren en afwerking: geldt voor beide papiersoorten.
	$paper_extra = function () use ( $choice, $c ) {
		$choice( 'blind', 'Blindpreeg', $c['yes_no'] );
		$choice( 'nummer', 'Nummeren', $c['yes_no'] );
		$fin = $choice( 'paperFin', 'Afwerking', $c['paper_fin'] );
		if ( 'Opgeplakt' === $fin ) {
			if ( $choice( 'plakMat', 'Opplakken op', $c['plak'] ) ) {
				$choice( 'ophang', 'Ophanging', $c['ophang'] );
			}
		} elseif ( 'Ingelijst' === $fin ) {
			$choice( 'lijst', 'Lijst', $c['lijst'] );
		}
		$choice( 'witrand', 'Witrand', $c['yes_no'] );
	};

	$aantal = (int) $get( 'aantal', 6 );
	if ( $aantal < 1 || $aantal > 9999 ) {
		$errors[] = "werk $n: aantal is ongeldig";
		$aantal   = max( 1, min( 9999, $aantal ) );
	}

	$dienst = $choice(
		'dienst',
		'Product',
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
		$paper_extra();
	} elseif ( 'basic-paper' === $dienst ) {
		$choice( 'basicType', 'Papiertype', $c['basic_paper'] );
		$paper_extra();
	} elseif ( in_array( $dienst, array( 'hd-finish', 'hd-decor' ), true ) ) {
		if ( 'Wit' === $choice( 'top', 'Type afwerking', $c['surface'] ) ) {
			$choice( 'topFinish', 'Afwerking', $c['gloss_matt'] );
		}
		$choice( 'profiel', 'Ophangprofiel', $c['profiel'] );
	} elseif ( 'diasec' === $dienst ) {
		$choice( 'diasecType', 'Uitvoering', $c['diasec'] );
	} elseif ( 'scan' === $dienst ) {
		$choice( 'scan', 'Type scan', $c['scans'] );
		$choice( 'proof', 'Na de scan', $c['after_scan'] );
	}

	if ( $dienst ) {
		$required_text( 'size', 'Titel & formaat' );

		if ( 'scan' !== $dienst && 'Ja' === $choice( 'cert', 'Certificaat', $c['yes_no'] ) ) {
			$required_text( 'certArtist', 'Kunstenaar' );
			$atelier = trim( $get( 'certAtelier' ) );
			if ( '' !== $atelier ) {
				$details[] = array( 'Atelier', $atelier );
			}
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
 * Volgend aanvraagnummer: RE-jjmmdd-0001, oplopend per dag (begint elke dag weer bij 0001).
 */
function reart_of_next_number() {
	$day = wp_date( 'ymd' );
	$seq = get_option( 'reart_of_seq' );
	if ( ! is_array( $seq ) || ! isset( $seq['day'], $seq['n'] ) || $seq['day'] !== $day ) {
		$seq = array( 'day' => $day, 'n' => 0 );
	}
	$seq['n'] = (int) $seq['n'] + 1;
	update_option( 'reart_of_seq', $seq, false );
	return sprintf( 'RE-%s-%04d', $day, $seq['n'] );
}

/**
 * Slaat de order op en geeft het bericht-ID terug. De titel wordt het ordernummer.
 */
function reart_of_save_order( array $order ) {
	$post_id = wp_insert_post(
		array(
			'post_type'   => 'reart_order',
			'post_status' => 'publish',
			'post_title'  => 'Nieuwe aanvraag',
		),
		true
	);
	if ( is_wp_error( $post_id ) || ! $post_id ) {
		return 0;
	}

	$no          = reart_of_next_number();
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
