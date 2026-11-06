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
    <div class="doc-meta">
      <h1 data-t="title">Aanvraagformulier</h1>
    </div>
  </header>

  <section class="start" id="ro-start" aria-labelledby="ro-startTitle">
    <div class="start-intro">
      <h2 class="start-title" id="ro-startTitle" data-t="st_title">Waarmee kunnen we je helpen?</h2>
      <p class="start-sub" data-t="st_sub">Kies wat bij je past. Het duurt maar een paar minuten.</p>
    </div>
    <div class="start-grid">
      <article class="start-card" data-mode="new">
        <div class="sc-top">
          <span class="sc-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12z"></path><path d="M8.5 11h7"></path><path d="M8.5 14.5h4"></path></svg></span>
          <span class="sc-pill" data-t="st_new_pill">Nieuwe klant</span>
        </div>
        <h3 class="sc-h" data-t="st_new">Ik wil informatie</h3>
        <p class="sc-p" data-t="st_new_p">Nog geen klant van Re-Art? Stel je vraag, dan nemen we binnen 24 uur contact met je op.</p>
        <ul class="sc-list">
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_new_1">Kort contactformulier</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_new_2">Persoonlijk advies</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_new_3">Vrijblijvend</span></li>
        </ul>
        <button type="button" class="sc-btn" data-mode="new"><span data-t="st_go_new">Naar het contactformulier</span> <span aria-hidden="true">→</span></button>
      </article>
      <article class="start-card" data-mode="existing">
        <div class="sc-top">
          <span class="sc-icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"></rect><path d="M9 4V3h6v1"></path><path d="M8.5 10h7"></path><path d="M8.5 13.5h7"></path><path d="M8.5 17h4"></path></svg></span>
          <span class="sc-pill" data-t="st_old_pill">Bestaande klant</span>
        </div>
        <h3 class="sc-h" data-t="st_old">Aanvraag of bestelling</h3>
        <p class="sc-p" data-t="st_old_p">Al klant bij ons? Stel je aanvraag samen per werk en per afwerking, met direct een overzicht.</p>
        <ul class="sc-list">
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_old_1">Canvas, papier, HD, Diasec en scans</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_old_2">Meerdere werken in één aanvraag</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg><span data-t="st_old_3">Live voorbeeld van je certificaat</span></li>
        </ul>
        <button type="button" class="sc-btn" data-mode="existing"><span data-t="st_go_old">Start je aanvraag</span> <span aria-hidden="true">→</span></button>
      </article>
    </div>
  </section>

  <section id="ro-contactView" hidden>
    <button type="button" class="back-link" data-mode="start" data-t="back">← Terug naar keuze</button>
    <form id="ro-contactForm" class="sheet stack contact-sheet" novalidate>
      <section>
        <div class="section-head"><span class="num">01</span><h2 data-t="s1">Je gegevens</h2></div>
        <div class="fields">
          <div class="full" id="ro-f-cnaam">
            <label class="lbl" for="ro-cnaam" data-t="naam">Naam / bedrijfsnaam <span class="req">*</span></label>
            <input type="text" id="ro-cnaam" autocomplete="organization" data-tp="naam_ph" placeholder="Bijv. Galerie Noord of Jan de Vries">
            <div class="err" data-t="naam_err">Vul je naam of bedrijfsnaam in.</div>
          </div>
          <div id="ro-f-cemail">
            <label class="lbl" for="ro-cemail" data-t="email">E-mailadres <span class="req">*</span></label>
            <input type="email" id="ro-cemail" autocomplete="email" data-tp="email_ph" placeholder="naam@voorbeeld.nl">
            <div class="err" data-t="c_email_err">Vul een geldig e-mailadres in.</div>
          </div>
          <div>
            <label class="lbl" for="ro-ctel" data-t="tel">Telefoonnummer</label>
            <input type="tel" id="ro-ctel" autocomplete="tel" data-tp="tel_ph" placeholder="06 12345678">
          </div>
          <div class="full">
            <label class="lbl" for="ro-cstraat" data-t="straat">Straat en huisnummer</label>
            <input type="text" id="ro-cstraat" autocomplete="street-address" data-tp="straat_ph" placeholder="Bijv. Kunstlaan 12">
          </div>
          <div>
            <label class="lbl" for="ro-cpostcode" data-t="postcode">Postcode</label>
            <input type="text" id="ro-cpostcode" autocomplete="postal-code" data-tp="postcode_ph" placeholder="1234 AB">
          </div>
          <div>
            <label class="lbl" for="ro-cplaats" data-t="plaats">Plaats</label>
            <input type="text" id="ro-cplaats" autocomplete="address-level2" data-tp="plaats_ph" placeholder="Bijv. Almere">
          </div>
          <div class="full">
            <label class="lbl" for="ro-cland" data-t="land">Land</label>
            <input type="text" id="ro-cland" autocomplete="country-name" data-tp="land_ph" placeholder="Bijv. Nederland">
          </div>
        </div>
      </section>
      <section>
        <div class="section-head"><span class="num">02</span><h2 data-t="c_s2">Je vraag</h2></div>
        <div id="ro-f-cbericht">
          <label class="lbl" for="ro-cbericht" data-t="c_msg">Waarover wil je informatie ontvangen? <span class="req">*</span></label>
          <textarea id="ro-cbericht" rows="7" data-tp="c_msg_ph" placeholder="Bijv. ik wil graag mijn schilderijen laten scannen en als giclée laten printen."></textarea>
          <div class="err" data-t="c_msg_err">Vertel ons kort waarover je informatie wilt.</div>
        </div>
      </section>
      <div class="hp" aria-hidden="true">
        <label for="ro-cwebsite">Laat dit veld leeg</label>
        <input type="text" id="ro-cwebsite" tabindex="-1" autocomplete="off">
      </div>
      <div class="contact-actions">
        <button type="submit" class="btn primary" id="ro-contactSubmit" data-t="c_send">Verstuur contactformulier</button>
        <div class="form-error" id="ro-contactError"></div>
      </div>
    </form>
    <div class="sheet contact-thanks" id="ro-contactThanks" hidden>
      <div class="thanks-mark" aria-hidden="true">✓</div>
      <h3 data-t="c_thx_title">Bedankt voor je bericht</h3>
      <p class="thanks" data-t="c_thx_text">We hebben je contactverzoek goed ontvangen. We streven ernaar je binnen 24 uur te antwoorden.</p>
      <p class="sent" style="margin:0;color:var(--muted)"><span data-t="c_sent_to">Een bevestiging is verstuurd naar</span> <strong id="ro-contactSentTo"></strong>.</p>
      <div class="actions"><button type="button" class="btn" data-mode="start" data-t="back_start">Terug naar het begin</button></div>
    </div>
  </section>

  <div id="ro-orderView" hidden>
  <button type="button" class="back-link" data-mode="start" data-t="back">← Terug naar keuze</button>
  <form id="ro-orderForm" novalidate>
    <div class="grid">
      <div class="sheet stack">
        <section>
          <div class="section-head"><span class="num">01</span><h2 data-t="s1">Je gegevens</h2></div>
          <div class="fields">
            <div class="full" id="ro-f-naam">
              <label class="lbl" for="ro-naam" data-t="naam">Naam / bedrijfsnaam <span class="req">*</span></label>
              <input type="text" id="ro-naam" autocomplete="organization" data-tp="naam_ph" placeholder="Bijv. Galerie Noord of Jan de Vries">
              <div class="err" data-t="naam_err">Vul je naam of bedrijfsnaam in.</div>
            </div>
            <div id="ro-f-datum">
              <label class="lbl" for="ro-datum" data-t="datum">Datum</label>
              <input type="date" id="ro-datum" readonly tabindex="-1" aria-readonly="true">
            </div>
            <div id="ro-f-email">
              <label class="lbl" for="ro-email" data-t="email">E-mailadres <span class="req">*</span></label>
              <input type="email" id="ro-email" autocomplete="email" data-tp="email_ph" placeholder="naam@voorbeeld.nl">
              <div class="err" data-t="email_err">Vul een geldig e-mailadres in; hierop ontvang je de offerte of opdrachtbevestiging.</div>
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
          <div class="section-head"><span class="num">02</span><h2 data-t="s2">Je aanvraag</h2></div>
          <div class="help-box help-bar"><span class="help-ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"></path></svg></span>
            <span class="help-txt"><b data-t="help_t">Hulp nodig?</b> <span data-t="help_p">Neem contact met ons op</span><span class="help-links"><a href="tel:+31365368875">036 536 88 75</a><a href="mailto:info@re-art.com">info@re-art.com</a></span></span></div>
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
        <div class="help-box help-side"><span class="help-ic"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"></path></svg></span>
            <span class="help-txt"><b data-t="help_t">Hulp nodig?</b> <span data-t="help_p">Neem contact met ons op</span><span class="help-links"><a href="tel:+31365368875">036 536 88 75</a><a href="mailto:info@re-art.com">info@re-art.com</a></span></span></div>
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
  </div>

  <footer>
    <span>Re-Art · Almere<?php if ( $reart_of_privacy ) : ?> · <?php echo 'en' === $reart_of_lang ? 'We only use your details to process your request.' : 'We gebruiken je gegevens alleen om je aanvraag te verwerken.'; ?> <a href="<?php echo esc_url( $reart_of_privacy ); ?>"><?php echo 'en' === $reart_of_lang ? 'Privacy policy' : 'Privacyverklaring'; ?></a><?php endif; ?></span>
    <span data-t="footer_req">Velden met <span class="req">*</span> zijn verplicht</span>
  </footer>
</div>

<div class="overlay info-modal" role="dialog" aria-modal="true">
  <div class="dialog ib"></div>
</div>

<div class="overlay" id="ro-review" role="dialog" aria-modal="true" aria-labelledby="ro-reviewTitle">
  <div class="dialog">
    <h3 id="ro-reviewTitle" data-t="rv_title">Controleer je aanvraag</h3>
    <p class="thanks" data-t="rv_text">Kloppen alle gegevens? Verstuur dan je aanvraag. Wil je nog iets wijzigen, ga dan terug naar het formulier.</p>
    <div class="sum-doc" id="ro-reviewBody"></div>
    <div class="form-error" id="ro-sendError"></div>
    <div class="actions">
      <button type="button" class="btn primary" id="ro-reviewSend" data-t="rv_send">Aanvraag versturen</button>
      <button type="button" class="btn" id="ro-reviewEdit" data-t="rv_edit">Terug naar formulier</button>
    </div>
  </div>
</div>

<div class="overlay" id="ro-overlay" role="dialog" aria-modal="true" aria-labelledby="ro-dlgTitle">
  <div class="dialog">
    <div class="thanks-mark" aria-hidden="true">✓</div>
    <h3 id="ro-dlgTitle" data-t="dlg_title">Bedankt voor het invullen van het aanvraagformulier</h3>
    <p class="thanks" data-t="dlg_text">We verwerken je aanvraag in ons systeem en sturen je een offerte of opdrachtbevestiging met daarin de kosten en de levertijd. Hebben we nog vragen, dan nemen we per e-mail contact met je op.</p>
    <p class="sent" style="margin:0;color:var(--muted)"><span data-t="sent_to">Een kopie van je aanvraag is verstuurd naar</span> <strong id="ro-sentTo"></strong>.</p>
    <div class="sum-doc" id="ro-thanksBody"></div>
    <div class="actions">
      <button type="button" class="btn primary" id="ro-newBtn" data-t="new">Nieuwe aanvraag</button>
      <button type="button" class="btn" id="ro-closeBtn" data-t="close">Sluiten</button>
    </div>
  </div>
</div>
</div>
