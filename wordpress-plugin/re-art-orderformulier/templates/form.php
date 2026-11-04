<?php
/**
 * Template van het Re-Art orderformulier. Wordt geladen door de shortcode [reart_orderformulier].
 *
 * @var string $reart_of_privacy URL van de privacyverklaring (kan leeg zijn).
 * @var string $reart_of_lang    Taal van het formulier: 'nl' of 'en'.
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="reart-of" id="reart-of">
<div class="wrap">
  <header class="brand">
    <div class="logo" role="img" aria-label="Re-Art – Special Art productions"></div>
    <div class="doc-meta">
      <h1 data-t="title">Aanvraagformulier</h1>
    </div>
  </header>

  <form id="ro-orderForm" novalidate>
    <div class="grid">
      <div class="sheet stack">
        <section>
          <div class="section-head"><span class="num">01</span><h2 data-t="s1">Uw gegevens</h2></div>
          <div class="fields">
            <div class="full" id="ro-f-naam">
              <label class="lbl" for="ro-naam" data-t="naam">Naam / bedrijfsnaam <span class="req">*</span></label>
              <input type="text" id="ro-naam" autocomplete="organization" data-tp="naam_ph" placeholder="Bijv. Galerie Noord of Jan de Vries">
              <div class="err" data-t="naam_err">Vul uw naam of bedrijfsnaam in.</div>
            </div>
            <div id="ro-f-datum">
              <label class="lbl" for="ro-datum" data-t="datum">Datum <span class="req">*</span></label>
              <input type="date" id="ro-datum">
              <div class="err" data-t="datum_err">Vul de datum van vandaag in.</div>
            </div>
            <div id="ro-f-email">
              <label class="lbl" for="ro-email" data-t="email">E-mailadres <span class="req">*</span></label>
              <input type="email" id="ro-email" autocomplete="email" data-tp="email_ph" placeholder="naam@voorbeeld.nl">
              <div class="err" data-t="email_err">Vul een geldig e-mailadres in; hierop ontvangt u de opdrachtbevestiging.</div>
            </div>
            <div class="full">
              <label class="lbl" for="ro-tel" data-t="tel">Telefoonnummer</label>
              <input type="tel" id="ro-tel" autocomplete="tel" data-tp="tel_ph" placeholder="06 12345678">
            </div>
            <div class="full">
              <label class="lbl" for="ro-straat" data-t="straat">Straat en huisnummer</label>
              <input type="text" id="ro-straat" autocomplete="street-address" data-tp="straat_ph" placeholder="Bijv. Kunstlaan 12">
            </div>
            <div>
              <label class="lbl" for="ro-postcode" data-t="postcode">Postcode</label>
              <input type="text" id="ro-postcode" autocomplete="postal-code" data-tp="postcode_ph" placeholder="1234 AB">
            </div>
            <div>
              <label class="lbl" for="ro-plaats" data-t="plaats">Plaats</label>
              <input type="text" id="ro-plaats" autocomplete="address-level2" data-tp="plaats_ph" placeholder="Bijv. Almere">
            </div>
            <div class="full">
              <label class="lbl" for="ro-land" data-t="land">Land</label>
              <input type="text" id="ro-land" autocomplete="country-name" data-tp="land_ph" placeholder="Bijv. Nederland">
            </div>
          </div>
        </section>

        <section>
          <div class="section-head"><span class="num">02</span><h2 data-t="s2">Uw aanvraag</h2></div>
          <div class="stack" id="ro-lines" style="gap:16px"></div>
          <div style="margin-top:16px"><button type="button" class="btn add-line" id="ro-addLine" data-t="addLine">+ Nog een werk toevoegen</button></div>
        </section>

        <section>
          <div class="section-head"><span class="num">03</span><h2 data-t="s3">Opmerkingen</h2></div>
          <label class="lbl" for="ro-opmerkingen" data-t="opm">Aanvullende wensen of informatie</label>
          <textarea id="ro-opmerkingen" data-tp="opm_ph" placeholder="Bijv. gewenste leverdatum, ophalen of bezorgen, bijzonderheden over het werk"></textarea>
        </section>
      </div>

      <aside class="summary" aria-live="polite">
        <div class="card">
          <h3 data-t="sum_title">Aanvraagoverzicht</h3>
          <div class="who" id="ro-sumWho">Nog geen naam ingevuld</div>
          <div id="ro-sumLines"></div>
          <div class="totals"><span id="ro-sumCount">0 werken</span><span id="ro-sumQty">0 stuks</span></div>
        </div>
        <button type="submit" class="btn primary" data-t="submit">Aanvraag controleren &amp; afronden</button>
        <div class="form-error" id="ro-formError">Niet alles is ingevuld. De ontbrekende velden zijn rood gemarkeerd.</div>
      </aside>
    </div>
    <div class="hp" aria-hidden="true">
      <label for="ro-website">Laat dit veld leeg</label>
      <input type="text" id="ro-website" name="website" tabindex="-1" autocomplete="off">
    </div>
  </form>

  <footer>
    <span>Re-Art · Almere<?php if ( $reart_of_privacy ) : ?> · <?php echo 'en' === $reart_of_lang ? 'We only use your details to process your request.' : 'Wij gebruiken uw gegevens alleen om uw aanvraag te verwerken.'; ?> <a href="<?php echo esc_url( $reart_of_privacy ); ?>"><?php echo 'en' === $reart_of_lang ? 'Privacy policy' : 'Privacyverklaring'; ?></a><?php endif; ?></span>
    <span data-t="footer_req">Velden met <span class="req">*</span> zijn verplicht</span>
  </footer>
</div>

<div class="overlay info-modal" role="dialog" aria-modal="true">
  <div class="dialog ib"></div>
</div>

<div class="overlay" id="ro-overlay" role="dialog" aria-modal="true" aria-labelledby="ro-dlgTitle">
  <div class="dialog">
    <div class="thanks-mark" aria-hidden="true">✓</div>
    <h3 id="ro-dlgTitle" data-t="dlg_title">Bedankt voor het invullen van het aanvraagformulier</h3>
    <p class="thanks" data-t="dlg_text">Wij verwerken uw aanvraag in ons systeem en sturen u een opdrachtbevestiging met daarin de kosten en de levertijd. Mochten wij nog vragen hebben, dan nemen wij per e-mail contact met u op.</p>
    <p class="sent" style="margin:0;color:var(--muted)"><span data-t="sent_to">Een kopie van uw aanvraag is verstuurd naar</span> <strong id="ro-sentTo"></strong>.</p>
    <div class="legend" style="margin:6px 0 -8px" data-t="dlg_sum">Uw aanvraagoverzicht</div>
    <pre id="ro-orderText"></pre>
    <div class="actions">
      <button type="button" class="btn primary" id="ro-newBtn" data-t="new">Nieuwe aanvraag</button>
      <button type="button" class="btn" id="ro-closeBtn" data-t="close">Sluiten</button>
    </div>
  </div>
</div>
</div>
