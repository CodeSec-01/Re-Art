<?php
/**
 * Template van het Re-Art orderformulier. Wordt geladen door de shortcode [reart_orderformulier].
 *
 * @var string $reart_of_privacy URL van de privacyverklaring (kan leeg zijn).
 */
defined( 'ABSPATH' ) || exit;
?>
<div class="reart-of" id="reart-of">
<div class="wrap">
  <header class="brand">
    <div class="logo" role="img" aria-label="Re-Art – Special Art productions"></div>
    <div class="doc-meta">
      <h1>Orderformulier</h1>
    </div>
  </header>

  <form id="ro-orderForm" novalidate>
    <div class="grid">
      <div class="sheet stack">
        <section>
          <div class="section-head"><span class="num">01</span><h2>Uw gegevens</h2></div>
          <div class="fields">
            <div class="full" id="ro-f-naam">
              <label class="lbl" for="ro-naam">Naam / bedrijfsnaam <span class="req">*</span></label>
              <input type="text" id="ro-naam" autocomplete="organization" placeholder="Bijv. Galerie Noord of Jan de Vries">
              <div class="err">Vul uw naam of bedrijfsnaam in.</div>
            </div>
            <div id="ro-f-datum">
              <label class="lbl" for="ro-datum">Datum <span class="req">*</span></label>
              <input type="date" id="ro-datum">
              <div class="err">Vul de datum van vandaag in.</div>
            </div>
            <div id="ro-f-email">
              <label class="lbl" for="ro-email">E-mailadres <span class="req">*</span></label>
              <input type="email" id="ro-email" autocomplete="email" placeholder="naam@voorbeeld.nl">
              <div class="err">Vul een geldig e-mailadres in; hierop ontvangt u de opdrachtbevestiging.</div>
            </div>
            <div class="full">
              <label class="lbl" for="ro-tel">Telefoonnummer</label>
              <input type="tel" id="ro-tel" autocomplete="tel" placeholder="06 12345678">
            </div>
          </div>
        </section>

        <section>
          <div class="section-head"><span class="num">02</span><h2>Uw bestelling</h2></div>
          <div class="stack" id="ro-lines" style="gap:16px"></div>
          <div style="margin-top:16px"><button type="button" class="btn add-line" id="ro-addLine">+ Nog een werk toevoegen</button></div>
        </section>

        <section>
          <div class="section-head"><span class="num">03</span><h2>Opmerkingen</h2></div>
          <label class="lbl" for="ro-opmerkingen">Aanvullende wensen of informatie</label>
          <textarea id="ro-opmerkingen" placeholder="Bijv. gewenste leverdatum, ophalen of bezorgen, bijzonderheden over het werk"></textarea>
        </section>
      </div>

      <aside class="summary" aria-live="polite">
        <div class="card">
          <h3>Orderoverzicht</h3>
          <div class="who" id="ro-sumWho">Nog geen naam ingevuld</div>
          <div id="ro-sumLines"></div>
          <div class="totals"><span id="ro-sumCount">0 werken</span><span id="ro-sumQty">0 stuks</span></div>
        </div>
        <button type="submit" class="btn primary">Order controleren &amp; afronden</button>
        <div class="form-error" id="ro-formError">Niet alles is ingevuld. De ontbrekende velden zijn rood gemarkeerd.</div>
      </aside>
    </div>
    <div class="hp" aria-hidden="true">
      <label for="ro-website">Laat dit veld leeg</label>
      <input type="text" id="ro-website" name="website" tabindex="-1" autocomplete="off">
    </div>
  </form>

  <footer>
    <span>Re-Art · Almere<?php if ( $reart_of_privacy ) : ?> · Wij gebruiken uw gegevens alleen om uw order te verwerken. <a href="<?php echo esc_url( $reart_of_privacy ); ?>">Privacyverklaring</a><?php endif; ?></span>
    <span>Velden met <span class="req">*</span> zijn verplicht</span>
  </footer>
</div>

<div class="overlay" id="ro-overlay" role="dialog" aria-modal="true" aria-labelledby="ro-dlgTitle">
  <div class="dialog">
    <div class="thanks-mark" aria-hidden="true">✓</div>
    <h3 id="ro-dlgTitle">Bedankt voor het invullen van het orderformulier</h3>
    <p class="thanks">Wij verwerken uw order in ons systeem en sturen u een opdrachtbevestiging met daarin de kosten en de levertijd. Mochten wij nog vragen hebben, dan nemen wij per e-mail contact met u op.</p>
    <p class="sent" style="margin:0;color:var(--muted)">Een kopie van uw order is verstuurd naar <strong id="ro-sentTo"></strong>.</p>
    <div class="legend" style="margin:6px 0 -8px">Uw orderoverzicht</div>
    <pre id="ro-orderText"></pre>
    <div class="actions">
      <button type="button" class="btn primary" id="ro-newBtn">Nieuwe order</button>
      <button type="button" class="btn" id="ro-closeBtn">Sluiten</button>
    </div>
  </div>
</div>
</div>
