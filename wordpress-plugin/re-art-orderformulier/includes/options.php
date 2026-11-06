<?php
/**
 * Alle keuzes van het formulier. Moet gelijk lopen met assets/orderformulier.js;
 * de server accepteert alleen waarden uit deze lijsten.
 */

defined( 'ABSPATH' ) || exit;

function reart_of_catalog() {
	return array(
		'services'    => array(
			'cert-canvas'  => 'Certified Canvas',
			'basic-canvas' => 'Basic Canvas',
			'cert-paper'   => 'Certified Paper',
			'basic-paper'  => 'Basic Paper',
			'hd-finish'    => 'HD Finish',
			'hd-decor'     => 'HD Decor',
			'diasec'       => 'Diasec',
			'scan'         => 'Cruse Fine Art Scan',
		),
		'paper'       => array(
			'matte'    => array(
				'label' => 'Matte paper',
				'types' => array( 'Hahnemühle Photo Rag 308 gsm', 'Hahnemühle German Etching 310 gsm', 'Hahnemühle White Etching 270 gsm' ),
			),
			'photo'    => array(
				'label' => 'Photo paper',
				'types' => array( 'Hahnemühle Fine Art Baryta Satin 300 gsm', 'Hahnemühle Photo Rag Satin 310 gsm' ),
			),
			'metallic' => array(
				'label' => 'Metallic paper',
				'types' => array( 'Hahnemühle Photo Rag Metallic 340 gsm' ),
			),
		),
		'basic_paper' => array( 'Basic Litho White Matt', 'Basic Litho Archive Matt' ),
		'scans'       => array( 'Standaard scan', 'Texture', '3D' ),
		'diepte'      => array(
			'Aluminium' => array( '25/65', '45/65' ),
			'Hout'      => array( '20/65', '45/65' ),
		),
		'yes_no'      => array( 'Ja', 'Nee' ),
		'coating'     => array( 'Matt', 'Satin', 'Gloss' ),
		'surface'     => array( 'Wit', 'Metal' ),
		'gloss_matt'  => array( 'Gloss', 'Matt' ),
		'profiel'     => array( 'Geen', '15 mm', '25 mm' ),
		'diasec'      => array( 'Gloss', 'Mat', 'TruLife' ),
		'paper_fin'   => array( 'Losse print', 'Opgeplakt', 'Ingelijst' ),
		'plak'        => array( '1 mm Aluminium', '3 mm Dibond' ),
		'ophang'      => array( 'Geen', '15 mm ophangprofiel', '25 mm ophangprofiel' ),
		'lijst'       => array( 'Witte lijst met nerf', 'Zwarte lijst met nerf', 'Blanke lijst' ),
		'after_scan'  => array( 'Digitaal bestand', 'Digitaal bestand + voorbereid', 'Kleurproofing' ),
	);
}
