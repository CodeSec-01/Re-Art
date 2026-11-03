/* Re-Art Orderformulier */
(function () {
"use strict";
const root = document.getElementById("reart-of");
if (!root) return;
const CFG = window.REARTOF || { restUrl: "/wp-json/reart/v1/order", contact: "info@re-art.com" };
const $ = id => document.getElementById("ro-" + id);
const SERVICES = [
  { id: "cert-canvas", name: "Certified Canvas", group: "Canvas" },
  { id: "basic-canvas", name: "Basic Canvas", group: "Canvas" },
  { id: "cert-paper", name: "Certified Paper", group: "Fine art papier" },
  { id: "basic-paper", name: "Basic Paper", group: "Papier" },
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
        const label = typeof o === "string" ? o : o.label;
        const sub = typeof o === "object" && o.sub ? `<small>${esc(o.sub)}</small>` : "";
        const id = `l${l.id}-${field}-${i}`;
        return `<label class="opt"><input type="radio" id="${id}" name="l${l.id}-${field}" data-line="${l.id}" data-field="${field}" value="${esc(val)}" ${l[field] === val ? "checked" : ""}><span>${sub}${esc(label)}</span></label>`;
      }).join("")}
    </div>
    <div class="err">Maak een keuze.</div>
  </fieldset>`;
}

function textField(l, field, label, placeholder, required) {
  const missing = required && showErrors && !l[field].trim();
  return `<div class="${missing ? "invalid" : ""}">
    <label class="lbl" for="l${l.id}-${field}">${label}${required ? ' <span class="req">*</span>' : ""}</label>
    <input type="text" id="l${l.id}-${field}" data-line="${l.id}" data-field="${field}" value="${esc(l[field])}" placeholder="${esc(placeholder)}">
    <div class="err">Dit veld is verplicht.</div>
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
  return `<div class="cert" id="l${l.id}-cert" aria-label="Voorbeeld certificaat">
    <div class="cert-l">
      <div class="cert-artist">${f("artist")}</div>
      <div class="cert-img">Afbeelding kunstwerk</div>
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
    <label class="lbl" for="l${l.id}-size">Titel &amp; formaat kunstwerk <span class="req">*</span></label>
    <input type="text" id="l${l.id}-size" data-line="${l.id}" data-field="size" value="${esc(l.size)}" placeholder="Bijv. Zomerlandschap, 80 × 120 cm (b × h)">
    <div class="err">Vul de titel en het formaat van het kunstwerk in.</div>
  </div>`;
}

function subOptions(l) {
  const d = l.dienst;
  if (!d) return "";
  let html = "";
  if (d === "cert-canvas" || d === "basic-canvas") {
    html += chips(l, "uv", ["Ja", "Nee"], "UV-topcoating");
    if (l.uv === "Ja") html += `<div class="nested">${chips(l, "uvType", ["Matt", "Satin", "Gloss"], "Type coating")}</div>`;
    html += chips(l, "spannen", ["Ja", "Nee"], "Opspannen");
    if (l.spannen === "Ja") {
      html += `<div class="nested stack" style="gap:18px">${chips(l, "frame", ["Aluminium", "Hout"], "Type frame")}`;
      if (l.frame) html += chips(l, "diepte", DIEPTE[l.frame], "Diepte frame (" + l.frame.toLowerCase() + ")");
      html += `</div>`;
    }
  } else if (d === "cert-paper") {
    html += chips(l, "paperCat", Object.entries(PAPER).map(([k, v]) => ({ value: k, label: v.label })), "Papiersoort");
    if (l.paperCat) html += `<div class="nested">${chips(l, "paperType", PAPER[l.paperCat].types, PAPER[l.paperCat].label + " – type")}</div>`;
  } else if (d === "basic-paper") {
    html += chips(l, "basicType", BASIC_PAPER, "Papiertype");
  } else if (d === "hd-finish" || d === "hd-decor") {
    html += chips(l, "top", ["White surface", "Metal surface"], "Surface");
    if (l.top === "White surface") html += `<div class="nested">${chips(l, "topFinish", ["Gloss", "Matt"], "Afwerking white surface")}</div>`;
    html += chips(l, "profiel", ["Geen", "15 mm", "25 mm"], "Type ophangprofiel");
  } else if (d === "scan") {
    html += chips(l, "scan", SCANS, "Type scan");
    html += chips(l, "proof", ["Ja", "Nee"], "Kleurproofing");
  }
  html += sizeField(l);
  if (d !== "scan") {
    html += chips(l, "cert", ["Ja", "Nee"], "Certificaat");
    if (l.cert === "Ja") html += `<div class="nested stack" style="gap:14px">
      <div class="fields">
        ${textField(l, "certArtist", "Naam kunstenaar", "Zoals op het certificaat", true)}
        ${textField(l, "certWeb", "Website kunstenaar", "Bijv. www.naamkunstenaar.nl", false)}
      </div>
      ${textField(l, "certTitel", "Titel werk", "Titel zoals op het certificaat", true)}
      <div class="fields">
        ${textField(l, "certEditie", "Editie (oplage)", "Bijv. 50", true)}
        ${textField(l, "certEA", "EA (artist proofs, optioneel)", "Bijv. 5", false)}
      </div>
      <div class="cert-wrap">
        <div class="legend">Voorbeeld certificaat</div>
        <div class="cert-cap">Zo ziet uw certificaat eruit. De gegevens hierboven worden direct ingevuld. De layout is een indicatie en kan nog wijzigen.</div>
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
      <div class="line-title"><span class="tag">Werk ${idx + 1}</span></div>
      ${lines.length > 1 ? `<button type="button" class="btn ghost" data-remove="${l.id}">Verwijderen</button>` : ""}
    </div>
    <div class="line-fields">
      <div class="${aMissing ? "invalid" : ""}">
        <label class="lbl" for="l${l.id}-aantal">Aantal <span class="req">*</span></label>
        <input type="number" min="1" step="1" inputmode="numeric" id="l${l.id}-aantal" data-line="${l.id}" data-field="aantal" value="${esc(l.aantal)}">
        <div class="err">Minimaal 1.</div>
      </div>
    </div>
    ${chips(l, "dienst", SERVICES.map(s => ({ value: s.id, label: s.name, sub: s.group })), "Type afwerking of dienst", true)}
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
  const need = (label, val) => { if (val) out.push([label, val]); else missing.push(label); };
  const d = l.dienst;
  if (!d) { missing.push("Afwerking"); }
  else {
    out.push(["Afwerking", svc(d).name]);
    if (d === "cert-canvas" || d === "basic-canvas") {
      need("UV-topcoating", l.uv);
      if (l.uv === "Ja") need("Type coating", l.uvType);
      need("Opspannen", l.spannen);
      if (l.spannen === "Ja") { need("Type frame", l.frame); if (l.frame) need("Diepte frame", l.diepte); }
    } else if (d === "cert-paper") {
      need("Papiersoort", l.paperCat ? PAPER[l.paperCat].label : "");
      if (l.paperCat) need("Papiertype", l.paperType);
    } else if (d === "basic-paper") {
      need("Papiertype", l.basicType);
    } else if (d === "hd-finish" || d === "hd-decor") {
      need("Surface", l.top);
      if (l.top === "White surface") need("Afwerking", l.topFinish);
      need("Ophangprofiel", l.profiel);
    } else if (d === "scan") {
      need("Type scan", l.scan);
      need("Kleurproofing", l.proof);
    }
    need("Titel & formaat", l.size.trim());
    if (d !== "scan") {
      need("Certificaat", l.cert);
      if (l.cert === "Ja") {
        need("Kunstenaar", l.certArtist.trim());
        if (l.certWeb.trim()) out.push(["Website", l.certWeb.trim()]);
        need("Titel werk", l.certTitel.trim());
        need("Editie", l.certEditie.trim());
        if (l.certEA.trim()) out.push(["EA", l.certEA.trim()]);
        if (certTotal(l)) out.push(["Totale editie", certTotal(l)]);
      }
    }
  }
  if (!(parseInt(l.aantal, 10) >= 1)) missing.push("Aantal");
  return { out, missing };
}

function renderSummary() {
  updateCerts();
  const naam = $("naam").value.trim();
  $("sumWho").textContent = naam || "Nog geen naam ingevuld";
  let qty = 0;
  $("sumLines").innerHTML = lines.map((l, i) => {
    const { out, missing } = details(l);
    const n = parseInt(l.aantal, 10) || 0; qty += n;
    return `<div class="sum-line">
      <div class="sum-head"><span>${esc(l.size.trim() || "Werk " + (i + 1))}</span><span class="qty">${n} ×</span></div>
      ${out.length ? `<dl>${out.map(([k, v]) => `<dt>${esc(k)}</dt><dd>${esc(v)}</dd>`).join("")}</dl>` : ""}
      <div>${missing.length ? `<span class="pill todo">Nog in te vullen: ${missing.length}</span>` : `<span class="pill done">Compleet</span>`}</div>
    </div>`;
  }).join("");
  $("sumCount").textContent = lines.length + (lines.length === 1 ? " werk" : " werken");
  $("sumQty").textContent = qty + (qty === 1 ? " stuk" : " stuks");
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
const ERR_DEFAULT = $("formError").textContent;
function buildText() {
  const v = id => $(id).value.trim();
  const rows = [
    "RE-ART ORDER " + orderNo,
    "Datum: " + new Date(v("datum") + "T12:00").toLocaleDateString("nl-NL", { day: "numeric", month: "long", year: "numeric" }),
    "",
    "Naam / bedrijf: " + v("naam")
  ];
  if (v("email")) rows.push("E-mail: " + v("email"));
  if (v("tel")) rows.push("Telefoon: " + v("tel"));
  lines.forEach((l, i) => {
    rows.push("", `WERK ${i + 1}`, `  Aantal: ${parseInt(l.aantal, 10)}`);
    details(l).out.forEach(([k, val]) => rows.push(`  ${k}: ${val}`));
  });
  if (v("opmerkingen")) rows.push("", "Opmerkingen:", v("opmerkingen"));
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
  err.textContent = ERR_DEFAULT;
  if (!naamOk || !datumOk || !emailOk || !linesOk) {
    err.classList.add("show");
    const first = root.querySelector(".invalid");
    if (first) { first.scrollIntoView({ behavior: "smooth", block: "center" }); const inp = first.querySelector("input"); if (inp) inp.focus({ preventScroll: true }); }
    return;
  }
  err.classList.remove("show");
  const btn = root.querySelector('button[type="submit"]');
  const label = btn.textContent;
  btn.disabled = true; btn.textContent = "Bezig met versturen…";
  const payload = {
    naam: $("naam").value, datum: $("datum").value, email: $("email").value, tel: $("tel").value,
    opmerkingen: $("opmerkingen").value, website: $("website").value, elapsed: Date.now() - startedAt,
    lines: lines.map(l => { const o = Object.assign({}, l); delete o.id; return o; })
  };
  try {
    const res = await fetch(CFG.restUrl, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
    const data = await res.json().catch(() => ({}));
    if (!res.ok || !data.ok) throw new Error(data.message || "Versturen is niet gelukt.");
    orderNo = data.orderNo || "";
    $("orderText").textContent = buildText();
    $("sentTo").textContent = $("email").value.trim();
    $("overlay").classList.add("show");
    $("newBtn").focus();
  } catch (ex) {
    err.textContent = ex.message + " Probeer het opnieuw of mail naar " + CFG.contact + ".";
    err.classList.add("show");
  } finally {
    btn.disabled = false; btn.textContent = label;
  }
});

function resetForm() {
  showErrors = false;
  ["naam", "datum", "email", "tel", "opmerkingen"].forEach(id => $(id).value = "");
  ["f-naam", "f-email", "f-datum"].forEach(id => $(id).classList.remove("invalid"));
  $("formError").classList.remove("show");
  lines = [blank()]; renderLines();
  startedAt = Date.now();
}
const closeDlg = () => { $("overlay").classList.remove("show"); resetForm(); root.scrollIntoView({ behavior: "smooth", block: "start" }); };
$("closeBtn").addEventListener("click", closeDlg);
$("newBtn").addEventListener("click", closeDlg);
document.addEventListener("keydown", e => { if (e.key === "Escape" && $("overlay").classList.contains("show")) closeDlg(); });

lines = [blank()];
renderLines();
})();
