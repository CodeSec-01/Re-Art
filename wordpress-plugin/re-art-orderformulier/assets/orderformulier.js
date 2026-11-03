/* Re-Art Orderformulier */
(function () {
"use strict";
const root = document.getElementById("reart-of");
if (!root) return;
const CFG = window.REARTOF || { restUrl: "/wp-json/reart/v1/order", contact: "info@re-art.com", lang: "nl" };
const $ = id => document.getElementById("ro-" + id);
// ---- Taal / language ----
let LANG = (window.REARTOF && window.REARTOF.lang) || (location.hash === "#en" ? "en" : "nl");
const I18N = {
  nl: {
    title: "Orderformulier", s1: "Uw gegevens", s2: "Uw bestelling", s3: "Opmerkingen",
    naam: 'Naam / bedrijfsnaam <span class="req">*</span>', naam_ph: "Bijv. Galerie Noord of Jan de Vries", naam_err: "Vul uw naam of bedrijfsnaam in.",
    datum: 'Datum <span class="req">*</span>', datum_err: "Vul de datum van vandaag in.",
    email: 'E-mailadres <span class="req">*</span>', email_ph: "naam@voorbeeld.nl", email_err: "Vul een geldig e-mailadres in; hierop ontvangt u de opdrachtbevestiging.",
    tel: "Telefoonnummer", tel_ph: "06 12345678",
    straat: "Straat en huisnummer", straat_ph: "Bijv. Kunstlaan 12", postcode: "Postcode", postcode_ph: "1234 AB",
    plaats: "Plaats", plaats_ph: "Bijv. Almere", land: "Land", land_ph: "Bijv. Nederland",
    addLine: "+ Nog een werk toevoegen", opm: "Aanvullende wensen of informatie",
    opm_ph: "Bijv. gewenste leverdatum, ophalen of bezorgen, bijzonderheden over het werk",
    sum_title: "Orderoverzicht", sum_noname: "Nog geen naam ingevuld", submit: "Order controleren &amp; afronden",
    form_err: "Niet alles is ingevuld. De ontbrekende velden zijn rood gemarkeerd.",
    footer_req: 'Velden met <span class="req">*</span> zijn verplicht',
    dlg_title: "Bedankt voor het invullen van het orderformulier",
    dlg_text: "Wij verwerken uw order in ons systeem en sturen u een opdrachtbevestiging met daarin de kosten en de levertijd. Mochten wij nog vragen hebben, dan nemen wij per e-mail contact met u op.",
    dlg_sum: "Uw orderoverzicht", copy: "Kopieer orderoverzicht", edit: "Order aanpassen", new: "Nieuwe order", close: "Sluiten",
    copied: "Gekopieerd", copy_fail: "Kopiëren lukt hier niet; selecteer de tekst handmatig.",
    sent_to: "Een kopie van uw order is verstuurd naar", sending: "Bezig met versturen…", send_fail: "Versturen is niet gelukt.", retry: "Probeer het opnieuw of mail naar",
    choose: "Maak een keuze.", required: "Dit veld is verplicht.",
    work: "Werk", works: "werken", work1: "werk", piece: "stuk", pieces: "stuks", remove: "Verwijderen",
    aantal: "Aantal", aantal_err: "Minimaal 1.", dienst: "Type afwerking of dienst", afwerking: "Afwerking",
    uv: "UV-topcoating", uvType: "Type coating", spannen: "Opspannen", frame: "Type frame", diepte: "Diepte frame",
    paperCat: "Papiersoort", paperType: "Papiertype", type_suffix: " – type", surface: "Surface",
    topFinish_q: "Afwerking white surface", profiel_q: "Type ophangprofiel", profiel: "Ophangprofiel",
    scan: "Type scan", proof: "Kleurproofing", cert: "Certificaat",
    certArtist: "Naam kunstenaar", certArtist_ph: "Zoals op het certificaat", certWeb: "Website kunstenaar", certWeb_ph: "Bijv. www.naamkunstenaar.nl",
    certTitel: "Titel werk", certTitel_ph: "Titel zoals op het certificaat", certEditie: "Editie (oplage)", certEditie_ph: "Bijv. 50",
    certEA: "EA (artist proofs, optioneel)", certEA_ph: "Bijv. 5",
    cert_preview: "Voorbeeld certificaat", cert_cap: "Zo ziet uw certificaat eruit. De gegevens hierboven worden direct ingevuld. De layout is een indicatie en kan nog wijzigen.",
    cert_img: "Afbeelding kunstwerk", cert_aria: "Voorbeeld certificaat",
    size: "Titel &amp; formaat kunstwerk", size_short: "Titel & formaat", size_ph: "Bijv. Zomerlandschap, 80 × 120 cm (b × h)", size_err: "Vul de titel en het formaat van het kunstwerk in.",
    kunstenaar: "Kunstenaar", website: "Website", editie: "Editie", ea: "EA", total: "Totale editie",
    todo: "Nog in te vullen:", done: "Compleet",
    t_date: "Datum", t_name: "Naam / bedrijf", t_email: "E-mail", t_tel: "Telefoon", t_addr: "Adres", t_opm: "Opmerkingen", t_work: "WERK",
    g_fineart: "Fine art papier", g_paper: "Papier"
  },
  en: {
    title: "Order form", s1: "Your details", s2: "Your order", s3: "Comments",
    naam: 'Name / company name <span class="req">*</span>', naam_ph: "E.g. Gallery North or John Smith", naam_err: "Please enter your name or company name.",
    datum: 'Date <span class="req">*</span>', datum_err: "Please enter today's date.",
    email: 'Email address <span class="req">*</span>', email_ph: "name@example.com", email_err: "Please enter a valid email address; we will send the order confirmation to it.",
    tel: "Phone number", tel_ph: "+31 6 12345678",
    straat: "Street and house number", straat_ph: "E.g. 12 Art Street", postcode: "Postal code", postcode_ph: "1234 AB",
    plaats: "City", plaats_ph: "E.g. Amsterdam", land: "Country", land_ph: "E.g. the Netherlands",
    addLine: "+ Add another artwork", opm: "Additional requests or information",
    opm_ph: "E.g. preferred delivery date, pick-up or delivery, details about the artwork",
    sum_title: "Order summary", sum_noname: "No name entered yet", submit: "Review &amp; submit order",
    form_err: "Some fields are missing. They are marked in red.",
    footer_req: 'Fields marked <span class="req">*</span> are required',
    dlg_title: "Thank you for filling in the order form",
    dlg_text: "We will process your order in our system and send you an order confirmation including the costs and delivery time. If we have any questions, we will contact you by email.",
    dlg_sum: "Your order summary", copy: "Copy order summary", edit: "Edit order", new: "New order", close: "Close",
    copied: "Copied", copy_fail: "Copying is not possible here; please select the text manually.",
    sent_to: "A copy of your order has been sent to", sending: "Sending…", send_fail: "Sending failed.", retry: "Please try again or email",
    choose: "Please make a choice.", required: "This field is required.",
    work: "Artwork", works: "artworks", work1: "artwork", piece: "piece", pieces: "pieces", remove: "Remove",
    aantal: "Quantity", aantal_err: "At least 1.", dienst: "Type of finish or service", afwerking: "Finish",
    uv: "UV top coating", uvType: "Coating type", spannen: "Stretching", frame: "Frame type", diepte: "Frame depth",
    paperCat: "Paper category", paperType: "Paper type", type_suffix: " – type", surface: "Surface",
    topFinish_q: "White surface finish", profiel_q: "Hanging profile", profiel: "Hanging profile",
    scan: "Scan type", proof: "Colour proofing", cert: "Certificate",
    certArtist: "Artist name", certArtist_ph: "As shown on the certificate", certWeb: "Artist website", certWeb_ph: "E.g. www.artistname.com",
    certTitel: "Artwork title", certTitel_ph: "Title as shown on the certificate", certEditie: "Edition (print run)", certEditie_ph: "E.g. 50",
    certEA: "EA (artist proofs, optional)", certEA_ph: "E.g. 5",
    cert_preview: "Certificate preview", cert_cap: "This is what your certificate will look like. The details above are filled in directly. The layout is an indication and may still change.",
    cert_img: "Artwork image", cert_aria: "Certificate preview",
    size: "Artwork title &amp; size", size_short: "Title & size", size_ph: "E.g. Summer landscape, 80 × 120 cm (w × h)", size_err: "Please enter the title and size of the artwork.",
    kunstenaar: "Artist", website: "Website", editie: "Edition", ea: "EA", total: "Total edition",
    todo: "Still to complete:", done: "Complete",
    t_date: "Date", t_name: "Name / company", t_email: "Email", t_tel: "Phone", t_addr: "Address", t_opm: "Comments", t_work: "ARTWORK",
    g_fineart: "Fine art paper", g_paper: "Paper"
  }
};
const VAL_EN = { Ja: "Yes", Nee: "No", Geen: "None", Hout: "Wood" };
const t = k => (I18N[LANG] && I18N[LANG][k] != null ? I18N[LANG][k] : I18N.nl[k]);
const V = v => (LANG === "en" && VAL_EN[v]) ? VAL_EN[v] : v;
const plain = h => h.replace(/<[^>]+>/g, "").replace(/&amp;/g, "&");
function applyStatic() {
  root.querySelectorAll("[data-t]").forEach(el => { el.innerHTML = t(el.dataset.t); });
  root.querySelectorAll("[data-tp]").forEach(el => { el.placeholder = t(el.dataset.tp); });
  root.querySelectorAll(".lang-switch a").forEach(a => a.setAttribute("aria-current", a.dataset.lang === LANG ? "true" : "false"));
}

const SERVICES = [
  { id: "cert-canvas", name: "Certified Canvas", group: "Canvas" },
  { id: "basic-canvas", name: "Basic Canvas", group: "Canvas" },
  { id: "cert-paper", name: "Certified Paper", group: "g_fineart" },
  { id: "basic-paper", name: "Basic Paper", group: "g_paper" },
  { id: "hd-finish", name: "HD Finish", group: "HD" },
  { id: "hd-decor", name: "HD Decor", group: "HD" },
  { id: "scan", name: "Cruse Fine Art Scan", group: "Scan" }
];
const PAPER = {
  matte: { label: "Matte paper", types: ["Hahnemühle Photo Rag 308 gsm", "Hahnemühle German Etching 310 gsm", "Hahnemühle White Etching 270 gsm"] },
  photo: { label: "Photo paper", types: ["Hahnemühle Fine Art Baryta Satin 300 gsm", "Hahnemühle Photo Rag Satin 310 gsm"] },
  metallic: { label: "Metallic paper", types: ["Hahnemühle Photo Rag Metallic 340 gsm"] }
};
const BASIC_PAPER = ["Basic Litho White Matt", "Basic Litho Archive Matt"];
const SCANS = ["LR", "Texture", "Superscan", "3D"];
const DIEPTE = { Aluminium: ["25/65", "45/65"], Hout: ["20/65", "45/65"] };

let seq = 0;
let lines = [];
let showErrors = false;
const blank = () => ({ id: ++seq, aantal: "1", dienst: "", uv: "", uvType: "", spannen: "", frame: "", diepte: "", paperCat: "", paperType: "", basicType: "", top: "", topFinish: "", profiel: "", scan: "", proof: "", size: "", cert: "", certArtist: "", certWeb: "", certTitel: "", certEditie: "", certEA: "" });

const esc = s => String(s).replace(/[&<>"]/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;" }[c]));
const svc = id => SERVICES.find(s => s.id === id);

function chips(l, field, options, legend, wide) {
  const missing = showErrors && !l[field];
  return `<fieldset class="${missing ? "invalid" : ""}">
    <legend class="legend">${legend} <span class="req">*</span></legend>
    <div class="${wide ? "services" : "chips"}">
      ${options.map((o, i) => {
        const val = typeof o === "string" ? o : o.value;
        const label = typeof o === "string" ? V(o) : o.label;
        const sub = typeof o === "object" && o.sub ? `<small>${esc(o.sub)}</small>` : "";
        const id = `l${l.id}-${field}-${i}`;
        return `<label class="opt"><input type="radio" id="${id}" name="l${l.id}-${field}" data-line="${l.id}" data-field="${field}" value="${esc(val)}" ${l[field] === val ? "checked" : ""}><span>${sub}${esc(label)}</span></label>`;
      }).join("")}
    </div>
    <div class="err">${t("choose")}</div>
  </fieldset>`;
}

function textField(l, field, label, placeholder, required) {
  const missing = required && showErrors && !l[field].trim();
  return `<div class="${missing ? "invalid" : ""}">
    <label class="lbl" for="l${l.id}-${field}">${label}${required ? ' <span class="req">*</span>' : ""}</label>
    <input type="text" id="l${l.id}-${field}" data-line="${l.id}" data-field="${field}" value="${esc(l[field])}" placeholder="${esc(placeholder)}">
    <div class="err">${t("required")}</div>
  </div>`;
}

function certSize(l) {
  const m = l.size.match(/(\d+(?:[.,]\d+)?)\s*[x×*]\s*(\d+(?:[.,]\d+)?)/i);
  return m ? `${m[1]} x ${m[2]} cm` : "";
}
function certMedium(l) {
  const d = l.dienst;
  if (d === "cert-paper") return l.paperType || "Certified Paper";
  if (d === "basic-paper") return l.basicType || "Basic Paper";
  if (d === "cert-canvas" || d === "basic-canvas") return svc(d).name + (l.uv === "Ja" && l.uvType ? ", UV coating " + l.uvType.toLowerCase() : "");
  if (d === "hd-finish" || d === "hd-decor") return svc(d).name + (l.top ? ", " + l.top + (l.topFinish ? " " + l.topFinish.toLowerCase() : "") : "");
  return "";
}
function certTotal(l) {
  const e = parseInt(l.certEditie, 10), a = parseInt(l.certEA, 10) || 0;
  return Number.isFinite(e) ? String(e + a) : "";
}
function certValues(l) {
  const ed = l.certEditie.trim();
  return {
    artist: l.certArtist.trim(), web: l.certWeb.trim(), title: l.certTitel.trim(),
    number: ed ? "__ / " + ed : "", size: certSize(l), medium: certMedium(l),
    edition: ed, ea: l.certEA.trim(), total: certTotal(l)
  };
}
const CERT_PH = { artist: "Artist name", web: "www.website.com", title: "Title artwork", number: "00 / 00", size: "00 x 00 cm", medium: "", edition: "00", ea: "00", total: "00" };
function certHTML(l) {
  const f = k => `<span data-cert="${k}"></span>`;
  return `<div class="cert" id="l${l.id}-cert" aria-label="${t("cert_aria")}">
    <div class="cert-l">
      <div class="cert-artist">${f("artist")}</div>
      <div class="cert-img">${t("cert_img")}</div>
      <div class="cert-web">${f("web")}</div>
      <div class="cert-small">
        <p>This artwork is a Certified Art Giclée fine art collectors print. The artwork is checked, signed and numbered by the artist. This certificate warrants a lifetime durability and guarantees that no more copies are made as indicated on the certificate.</p>
        <p>Certified Art Giclée a trademark from Re-Art.</p>
      </div>
    </div>
    <div class="cert-r">
      <div class="cert-coa">Certificate<br>of Authenticity</div>
      <div class="cert-title">${f("title")}</div>
      <dl class="cert-dl">
        <dt>Number</dt><dd>${f("number")}</dd>
        <dt>Size</dt><dd>${f("size")}</dd>
        <dt>Medium</dt><dd>${f("medium")}</dd>
        <div class="gap"></div>
        <dt>Edition</dt><dd>${f("edition")}</dd>
        <dt>EA</dt><dd>${f("ea")}</dd>
        <dt>Total Edition</dt><dd>${f("total")}</dd>
      </dl>
      <div class="cert-sig">Signature Artist</div>
      <div class="cert-sig">Signature Master Printer</div>
      <div class="cert-foot">
        <span class="cert-seal">Not valid without embossed seal</span>
        <div class="cert-brand"><div class="cert-logo" role="img" aria-label="Re-Art"></div><span>The Netherlands | www.re-art.com</span></div>
      </div>
    </div>
  </div>`;
}
function updateCerts() {
  lines.forEach(l => {
    const el = document.getElementById(`l${l.id}-cert`);
    if (!el) return;
    const v = certValues(l);
    el.querySelectorAll("[data-cert]").forEach(s => {
      const k = s.dataset.cert, val = v[k];
      s.textContent = val || CERT_PH[k];
      s.classList.toggle("ph", !val);
    });
  });
}

function sizeField(l) {
  const missing = showErrors && !l.size.trim();
  return `<div class="${missing ? "invalid" : ""}">
    <label class="lbl" for="l${l.id}-size">${t("size")} <span class="req">*</span></label>
    <input type="text" id="l${l.id}-size" data-line="${l.id}" data-field="size" value="${esc(l.size)}" placeholder="${esc(t("size_ph"))}">
    <div class="err">${t("size_err")}</div>
  </div>`;
}

function subOptions(l) {
  const d = l.dienst;
  if (!d) return "";
  let html = "";
  if (d === "cert-canvas" || d === "basic-canvas") {
    html += chips(l, "uv", ["Ja", "Nee"], t("uv"));
    if (l.uv === "Ja") html += `<div class="nested">${chips(l, "uvType", ["Matt", "Satin", "Gloss"], t("uvType"))}</div>`;
    html += chips(l, "spannen", ["Ja", "Nee"], t("spannen"));
    if (l.spannen === "Ja") {
      html += `<div class="nested stack" style="gap:18px">${chips(l, "frame", ["Aluminium", "Hout"], t("frame"))}`;
      if (l.frame) html += chips(l, "diepte", DIEPTE[l.frame], t("diepte") + " (" + V(l.frame).toLowerCase() + ")");
      html += `</div>`;
    }
  } else if (d === "cert-paper") {
    html += chips(l, "paperCat", Object.entries(PAPER).map(([k, v]) => ({ value: k, label: v.label })), t("paperCat"));
    if (l.paperCat) html += `<div class="nested">${chips(l, "paperType", PAPER[l.paperCat].types, PAPER[l.paperCat].label + t("type_suffix"))}</div>`;
  } else if (d === "basic-paper") {
    html += chips(l, "basicType", BASIC_PAPER, t("paperType"));
  } else if (d === "hd-finish" || d === "hd-decor") {
    html += chips(l, "top", ["White surface", "Metal surface"], t("surface"));
    if (l.top === "White surface") html += `<div class="nested">${chips(l, "topFinish", ["Gloss", "Matt"], t("topFinish_q"))}</div>`;
    html += chips(l, "profiel", ["Geen", "15 mm", "25 mm"], t("profiel_q"));
  } else if (d === "scan") {
    html += chips(l, "scan", SCANS, t("scan"));
    html += chips(l, "proof", ["Ja", "Nee"], t("proof"));
  }
  html += sizeField(l);
  if (d !== "scan") {
    html += chips(l, "cert", ["Ja", "Nee"], t("cert"));
    if (l.cert === "Ja") html += `<div class="nested stack" style="gap:14px">
      <div class="fields">
        ${textField(l, "certArtist", t("certArtist"), t("certArtist_ph"), true)}
        ${textField(l, "certWeb", t("certWeb"), t("certWeb_ph"), false)}
      </div>
      ${textField(l, "certTitel", t("certTitel"), t("certTitel_ph"), true)}
      <div class="fields">
        ${textField(l, "certEditie", t("certEditie"), t("certEditie_ph"), true)}
        ${textField(l, "certEA", t("certEA"), t("certEA_ph"), false)}
      </div>
      <div class="cert-wrap">
        <div class="legend">${t("cert_preview")}</div>
        <div class="cert-cap">${t("cert_cap")}</div>
        ${certHTML(l)}
      </div>
    </div>`;
  }
  return `<div class="suboptions">${html}</div>`;
}

function lineHTML(l, idx) {
  const aMissing = showErrors && !(parseInt(l.aantal, 10) >= 1);
  return `<div class="line" data-id="${l.id}">
    <div class="line-top">
      <div class="line-title"><span class="tag">${t("work")} ${idx + 1}</span></div>
      ${lines.length > 1 ? `<button type="button" class="btn ghost" data-remove="${l.id}">${t("remove")}</button>` : ""}
    </div>
    <div class="line-fields">
      <div class="${aMissing ? "invalid" : ""}">
        <label class="lbl" for="l${l.id}-aantal">${t("aantal")} <span class="req">*</span></label>
        <input type="number" min="1" step="1" inputmode="numeric" id="l${l.id}-aantal" data-line="${l.id}" data-field="aantal" value="${esc(l.aantal)}">
        <div class="err">${t("aantal_err")}</div>
      </div>
    </div>
    ${chips(l, "dienst", SERVICES.map(s => ({ value: s.id, label: s.name, sub: s.group.startsWith("g_") ? t(s.group) : s.group })), t("dienst"), true)}
    ${subOptions(l)}
  </div>`;
}

function renderLines(focusId) {
  const box = $("lines");
  box.innerHTML = lines.map(lineHTML).join("");
  if (focusId) { const el = document.getElementById(focusId); if (el) el.focus({ preventScroll: true }); }
  renderSummary();
}

// Details & completeness per line
function details(l) {
  const out = []; const missing = [];
  const need = (label, val) => { if (val) out.push([label, V(val)]); else missing.push(label); };
  const d = l.dienst;
  if (!d) { missing.push(t("afwerking")); }
  else {
    out.push([t("afwerking"), svc(d).name]);
    if (d === "cert-canvas" || d === "basic-canvas") {
      need(t("uv"), l.uv);
      if (l.uv === "Ja") need(t("uvType"), l.uvType);
      need(t("spannen"), l.spannen);
      if (l.spannen === "Ja") { need(t("frame"), l.frame); if (l.frame) need(t("diepte"), l.diepte); }
    } else if (d === "cert-paper") {
      need(t("paperCat"), l.paperCat ? PAPER[l.paperCat].label : "");
      if (l.paperCat) need(t("paperType"), l.paperType);
    } else if (d === "basic-paper") {
      need(t("paperType"), l.basicType);
    } else if (d === "hd-finish" || d === "hd-decor") {
      need(t("surface"), l.top);
      if (l.top === "White surface") need(t("afwerking"), l.topFinish);
      need(t("profiel"), l.profiel);
    } else if (d === "scan") {
      need(t("scan"), l.scan);
      need(t("proof"), l.proof);
    }
    need(t("size_short"), l.size.trim());
    if (d !== "scan") {
      need(t("cert"), l.cert);
      if (l.cert === "Ja") {
        need(t("kunstenaar"), l.certArtist.trim());
        if (l.certWeb.trim()) out.push([t("website"), l.certWeb.trim()]);
        need(t("certTitel"), l.certTitel.trim());
        need(t("editie"), l.certEditie.trim());
        if (l.certEA.trim()) out.push([t("ea"), l.certEA.trim()]);
        if (certTotal(l)) out.push([t("total"), certTotal(l)]);
      }
    }
  }
  if (!(parseInt(l.aantal, 10) >= 1)) missing.push(t("aantal"));
  return { out, missing };
}

function renderSummary() {
  updateCerts();
  const naam = $("naam").value.trim();
  $("sumWho").textContent = naam || t("sum_noname");
  let qty = 0;
  $("sumLines").innerHTML = lines.map((l, i) => {
    const { out, missing } = details(l);
    const n = parseInt(l.aantal, 10) || 0; qty += n;
    return `<div class="sum-line">
      <div class="sum-head"><span>${esc(l.size.trim() || t("work") + " " + (i + 1))}</span><span class="qty">${n} ×</span></div>
      ${out.length ? `<dl>${out.map(([k, v]) => `<dt>${esc(k)}</dt><dd>${esc(v)}</dd>`).join("")}</dl>` : ""}
      <div>${missing.length ? `<span class="pill todo">${t("todo")} ${missing.length}</span>` : `<span class="pill done">${t("done")}</span>`}</div>
    </div>`;
  }).join("");
  $("sumCount").textContent = lines.length + " " + (lines.length === 1 ? t("work1") : t("works"));
  $("sumQty").textContent = qty + " " + (qty === 1 ? t("piece") : t("pieces"));
}

// Reset dependent fields when a parent choice changes
const RESETS = {
  dienst: ["uv", "uvType", "spannen", "frame", "diepte", "paperCat", "paperType", "basicType", "top", "topFinish", "profiel", "scan", "proof", "cert", "certArtist", "certWeb", "certTitel", "certEditie", "certEA"],
  uv: ["uvType"], cert: ["certArtist", "certWeb", "certTitel", "certEditie", "certEA"], spannen: ["frame", "diepte"], frame: ["diepte"], paperCat: ["paperType"], top: ["topFinish"]
};

$("lines").addEventListener("change", e => {
  const t = e.target;
  if (t.type !== "radio") return;
  const l = lines.find(x => x.id === +t.dataset.line);
  const f = t.dataset.field;
  l[f] = t.value;
  (RESETS[f] || []).forEach(k => l[k] = "");
  if (f === "paperCat" && PAPER[t.value].types.length === 1) l.paperType = PAPER[t.value].types[0];
  renderLines(t.id);
});
$("lines").addEventListener("input", e => {
  const t = e.target;
  if (!t.dataset.field || t.type === "radio") return;
  const l = lines.find(x => x.id === +t.dataset.line);
  l[t.dataset.field] = t.value;
  if (showErrors) {
    const wrap = t.closest("div");
    const ok = t.dataset.field === "aantal" ? parseInt(t.value, 10) >= 1 : t.value.trim() !== "";
    if (ok) wrap.classList.remove("invalid");
  }
  renderSummary();
});
$("lines").addEventListener("click", e => {
  const id = e.target.dataset && e.target.dataset.remove;
  if (!id) return;
  lines = lines.filter(l => l.id !== +id);
  renderLines();
});
$("addLine").addEventListener("click", () => {
  const l = blank(); lines.push(l); renderLines(`l${l.id}-aantal`);
});
$("datum").addEventListener("input", e => {
  if (e.target.value) $("f-datum").classList.remove("invalid");
});
$("email").addEventListener("input", e => {
  if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(e.target.value.trim())) $("f-email").classList.remove("invalid");
});
$("naam").addEventListener("input", e => {
  if (e.target.value.trim()) $("f-naam").classList.remove("invalid");
  renderSummary();
});

// Submit
let orderNo = "";
let startedAt = Date.now();
function buildText() {
  const v = id => $(id).value.trim();
  const rows = [
    "RE-ART ORDER " + orderNo,
    t("t_date") + ": " + new Date(v("datum") + "T12:00").toLocaleDateString(LANG === "en" ? "en-GB" : "nl-NL", { day: "numeric", month: "long", year: "numeric" }),
    "",
    t("t_name") + ": " + v("naam")
  ];
  if (v("email")) rows.push(t("t_email") + ": " + v("email"));
  if (v("tel")) rows.push(t("t_tel") + ": " + v("tel"));
  const adres = [v("straat"), [v("postcode"), v("plaats")].filter(Boolean).join(" "), v("land")].filter(Boolean).join(", ");
  if (adres) rows.push(t("t_addr") + ": " + adres);
  lines.forEach((l, i) => {
    rows.push("", `${t("t_work")} ${i + 1}`, `  ${t("aantal")}: ${parseInt(l.aantal, 10)}`);
    details(l).out.forEach(([k, val]) => rows.push(`  ${k}: ${val}`));
  });
  if (v("opmerkingen")) rows.push("", t("t_opm") + ":", v("opmerkingen"));
  return rows.join("\n");
}

$("orderForm").addEventListener("submit", async e => {
  e.preventDefault();
  showErrors = true;
  const naamOk = $("naam").value.trim() !== "";
  $("f-naam").classList.toggle("invalid", !naamOk);
  const datumOk = $("datum").value !== "";
  $("f-datum").classList.toggle("invalid", !datumOk);
  const emailOk = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test($("email").value.trim());
  $("f-email").classList.toggle("invalid", !emailOk);
  renderLines();
  const linesOk = lines.every(l => details(l).missing.length === 0);
  const err = $("formError");
  err.textContent = t("form_err");
  if (!naamOk || !datumOk || !emailOk || !linesOk) {
    err.classList.add("show");
    const first = root.querySelector(".invalid");
    if (first) { first.scrollIntoView({ behavior: "smooth", block: "center" }); const inp = first.querySelector("input"); if (inp) inp.focus({ preventScroll: true }); }
    return;
  }
  err.classList.remove("show");
  const btn = root.querySelector('button[type="submit"]');
  const label = t("submit");
  btn.disabled = true; btn.textContent = t("sending");
  const payload = {
    naam: $("naam").value, datum: $("datum").value, email: $("email").value, tel: $("tel").value,
    straat: $("straat").value, postcode: $("postcode").value, plaats: $("plaats").value, land: $("land").value, lang: LANG,
    opmerkingen: $("opmerkingen").value, website: $("website").value, elapsed: Date.now() - startedAt,
    lines: lines.map(l => { const o = Object.assign({}, l); delete o.id; return o; })
  };
  try {
    const res = await fetch(CFG.restUrl, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data.ok) throw new Error(data.message || t("send_fail"));
    orderNo = data.orderNo || "";
    $("orderText").textContent = buildText();
    $("sentTo").textContent = $("email").value.trim();
    $("overlay").classList.add("show");
    $("newBtn").focus();
  } catch (ex) {
    err.textContent = ex.message + " " + t("retry") + " " + CFG.contact + ".";
    err.classList.add("show");
  } finally {
    btn.disabled = false; btn.innerHTML = label;
  }
});

function resetForm() {
  showErrors = false;
  ["naam", "datum", "email", "tel", "straat", "postcode", "plaats", "land", "opmerkingen"].forEach(id => $(id).value = "");
  ["f-naam", "f-email", "f-datum"].forEach(id => $(id).classList.remove("invalid"));
  $("formError").classList.remove("show");
  lines = [blank()]; renderLines();
  startedAt = Date.now();
}
const closeDlg = () => { $("overlay").classList.remove("show"); resetForm(); root.scrollIntoView({ behavior: "smooth", block: "start" }); };
$("closeBtn").addEventListener("click", closeDlg);
$("newBtn").addEventListener("click", closeDlg);
document.addEventListener("keydown", e => { if (e.key === "Escape" && $("overlay").classList.contains("show")) closeDlg(); });

applyStatic();
lines = [blank()];
renderLines();
})();
