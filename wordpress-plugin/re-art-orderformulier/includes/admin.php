<?php
/**
 * Beheer: instellingenpagina en orderweergave.
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * Instellingen → Re-Art Orderformulier
 * ---------------------------------------------------------------------- */

add_action( 'admin_init', 'reart_of_register_settings' );
function reart_of_register_settings() {
	register_setting(
		'reart_of_settings',
		'reart_of_recipient',
		array(
			'type'              => 'string',
			'default'           => REART_OF_DEFAULT_RECIPIENT,
			'sanitize_callback' => function ( $value ) {
				$value = sanitize_email( $value );
				return is_email( $value ) ? $value : REART_OF_DEFAULT_RECIPIENT;
			},
		)
	);
}

add_action( 'admin_menu', 'reart_of_settings_menu' );
function reart_of_settings_menu() {
	add_options_page( 'Re-Art Aanvraagformulier', 'Re-Art Aanvraagformulier', 'manage_options', 'reart-orderformulier', 'reart_of_settings_page' );
}

function reart_of_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>Re-Art Aanvraagformulier</h1>
		<form method="post" action="options.php">
			<?php settings_fields( 'reart_of_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="reart_of_recipient">Aanvragen mailen naar</label></th>
					<td>
						<input type="email" class="regular-text" id="reart_of_recipient" name="reart_of_recipient" value="<?php echo esc_attr( reart_of_recipient() ); ?>">
						<p class="description">Op dit adres komt elke nieuwe aanvraag binnen. De mails van het formulier worden ook vanaf dit adres verstuurd. De klant krijgt zelf een bevestiging op het adres dat hij invult.</p>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Opslaan' ); ?>
		</form>
		<h2>Gebruik</h2>
		<p>Zet de shortcode <code>[reart_orderformulier]</code> op een pagina (blok <em>Shortcode</em>). Alle aanvragen staan in het menu <a href="<?php echo esc_url( admin_url( 'edit.php?post_type=reart_order' ) ); ?>">Aanvragen</a>.</p>
		<p>Komen de mails niet aan? Installeer dan een SMTP-plugin zoals <em>WP Mail SMTP</em> en koppel die aan de mailbox van Re-Art.</p>
	</div>
	<?php
}

add_filter( 'plugin_action_links_' . plugin_basename( REART_OF_FILE ), 'reart_of_action_links' );
function reart_of_action_links( $links ) {
	array_unshift( $links, '<a href="' . esc_url( admin_url( 'options-general.php?page=reart-orderformulier' ) ) . '">Instellingen</a>' );
	return $links;
}

/* -------------------------------------------------------------------------
 * Order bekijken in het beheer
 * ---------------------------------------------------------------------- */

add_action( 'add_meta_boxes_reart_order', 'reart_of_meta_box' );
function reart_of_meta_box() {
	add_meta_box( 'reart_of_order', 'Aanvraaggegevens', 'reart_of_render_meta_box', 'reart_order', 'normal', 'high' );
}

function reart_of_render_meta_box( $post ) {
	$order = get_post_meta( $post->ID, '_reart_of_order', true );
	if ( ! is_array( $order ) ) {
		echo '<p>Geen aanvraaggegevens gevonden.</p>';
		return;
	}
	$order['no'] = get_post_meta( $post->ID, '_reart_of_no', true );
	echo reart_of_summary_html( $order ); // phpcs:ignore WordPress.Security.EscapeOutput -- alle waarden worden ge-escaped in reart_of_summary_html().

	$mail = get_post_meta( $post->ID, '_reart_of_mail', true );
	if ( is_array( $mail ) ) {
		echo '<p style="margin-top:20px;color:#646970">Mail aan Re-Art: ' . ( $mail['admin'] ? 'verstuurd' : '<strong style="color:#b32d2e">mislukt</strong>' )
			. ' · Bevestiging aan klant: ' . ( $mail['klant'] ? 'verstuurd' : '<strong style="color:#b32d2e">mislukt</strong>' ) . '</p>';
	}
}

// Kolom "Klant" in het overzicht van orders.
add_filter( 'manage_reart_order_posts_columns', 'reart_of_columns' );
function reart_of_columns( $cols ) {
	$new = array();
	foreach ( $cols as $key => $label ) {
		$new[ $key ] = $label;
		if ( 'title' === $key ) {
			$new['reart_email'] = 'E-mail';
			$new['reart_werken'] = 'Werken';
		}
	}
	return $new;
}

add_action( 'manage_reart_order_posts_custom_column', 'reart_of_column_content', 10, 2 );
function reart_of_column_content( $col, $post_id ) {
	$order = get_post_meta( $post_id, '_reart_of_order', true );
	if ( ! is_array( $order ) ) {
		return;
	}
	if ( 'reart_email' === $col ) {
		echo '<a href="mailto:' . esc_attr( $order['email'] ) . '">' . esc_html( $order['email'] ) . '</a>';
	} elseif ( 'reart_werken' === $col ) {
		echo (int) count( $order['lines'] );
	}
}
