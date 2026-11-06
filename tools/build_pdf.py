"""Bouwt het interactieve Re-Art aanvraagformulier als PDF (voor Adobe Acrobat / Reader).

Stap 1: reportlab tekent de pagina's en maakt de formuliervelden.
Stap 2: pikepdf voegt JavaScript toe: vragen tonen/verbergen, datumnotatie,
         controle van verplichte velden en mailen naar info@re-art.com.
"""
import json
from reportlab.pdfgen import canvas
from reportlab.lib.pagesizes import A4
from reportlab.lib.colors import HexColor, white
import pikepdf

OUT_TMP = "stap1.pdf"
OUT = "Re-Art_Aanvraagformulier.pdf"
W, H = A4
NAVY = HexColor("#091925")
SOFT = HexColor("#e6e9ec")
INK = HexColor("#15171c")
MUTED = HexColor("#5b616d")
LINE = HexColor("#dcdfe3")
FIELD_BG = HexColor("#f9f9f9")
CH = "- kies -"          # eerste regel van elke keuzelijst = niets gekozen
N_WORKS = 3
MAILTO = "info@re-art.com"

# ---------------------------------------------------------------- keuzes
PRODUCTS = ["Certified Canvas", "Basic Canvas", "Certified Paper", "Basic Paper", "HD Finish", "HD Decor", "Diasec", "Cruse Fine Art Scan"]
YN = ["Ja", "Nee"]
MATTE = ["Hahnemühle Photo Rag 308 gsm", "Hahnemühle German Etching 310 gsm", "Hahnemühle White Etching 270 gsm"]
PHOTO = ["Hahnemühle Fine Art Baryta Satin 300 gsm", "Hahnemühle Photo Rag Satin 310 gsm"]
METAL = ["Hahnemühle Photo Rag Metallic 340 gsm"]
BASIC = ["Basic Litho White Matt", "Basic Litho Archive Matt"]
FIN = ["Losse print", "Opgeplakt", "Ingelijst"]
PLAK = ["1 mm Aluminium", "3 mm Dibond"]
OPHANG = ["Geen", "15 mm ophangprofiel", "25 mm ophangprofiel"]
LIJST = ["Witte lijst met nerf", "Zwarte lijst met nerf", "Blanke lijst"]

# Optievelden per product: (sleutel, label, opties, kolom, rij, voorwaarde in JS)
# In de voorwaarde staat V('x') voor de waarde van veld x van hetzelfde werk.
def paper_fields(prefix, certified):
    f = []
    if certified:
        f += [(f"{prefix}_cat", "Papiersoort", ["Matte paper", "Photo paper", "Metallic paper"], 0, 0, "true"),
              (f"{prefix}_tm", "Matte paper - type", MATTE, 1, 0, f"V('{prefix}_cat')=='Matte paper'"),
              (f"{prefix}_tp", "Photo paper - type", PHOTO, 1, 0, f"V('{prefix}_cat')=='Photo paper'"),
              (f"{prefix}_tx", "Metallic paper - type", METAL, 1, 0, f"V('{prefix}_cat')=='Metallic paper'")]
    else:
        f += [(f"{prefix}_type", "Papiertype", BASIC, 0, 0, "true")]
    f += [(f"{prefix}_blind", "Blindpreeg", YN, 0, 1, "true"),
          (f"{prefix}_num", "Nummeren", YN, 1, 1, "true"),
          (f"{prefix}_fin", "Afwerking", FIN, 0, 2, "true"),
          (f"{prefix}_plak", "Opplakken op", PLAK, 1, 2, f"V('{prefix}_fin')=='Opgeplakt'"),
          (f"{prefix}_lijst", "Lijst", LIJST, 1, 2, f"V('{prefix}_fin')=='Ingelijst'"),
          (f"{prefix}_oph", "Ophanging", OPHANG, 1, 3, f"V('{prefix}_fin')=='Opgeplakt' && V('{prefix}_plak')!=CH"),
          (f"{prefix}_wit", "Witrand", YN, 0, 3, "true")]
    return f

def canvas_fields(prefix):
    return [(f"{prefix}_uv", "UV-topcoating", YN, 0, 0, "true"),
            (f"{prefix}_uvt", "Type coating", ["Matt", "Satin", "Gloss"], 1, 0, f"V('{prefix}_uv')=='Ja'"),
            (f"{prefix}_span", "Opspannen", YN, 0, 1, "true"),
            (f"{prefix}_frame", "Type frame", ["Aluminium", "Hout"], 1, 1, f"V('{prefix}_span')=='Ja'"),
            (f"{prefix}_da", "Diepte frame (aluminium)", ["25/65", "45/65"], 1, 2, f"V('{prefix}_span')=='Ja' && V('{prefix}_frame')=='Aluminium'"),
            (f"{prefix}_dh", "Diepte frame (hout)", ["20/65", "45/65"], 1, 2, f"V('{prefix}_span')=='Ja' && V('{prefix}_frame')=='Hout'")]

def hd_fields(prefix):
    return [(f"{prefix}_top", "Type afwerking", ["Wit", "Metal"], 0, 0, "true"),
            (f"{prefix}_topf", "Afwerking wit", ["Gloss", "Matt"], 1, 0, f"V('{prefix}_top')=='Wit'"),
            (f"{prefix}_prof", "Type ophangprofiel", ["Geen", "15 mm", "25 mm"], 0, 1, "true")]

SCAN = [("sc_type", "Type scan", ["Standaard scan", "Texture", "3D"], 0, 0, "true"),
        ("sc_after", "Na de scan", ["Digitaal bestand", "Digitaal bestand + voorbereid", "Kleurproofing"], 1, 0, "true")]

PRODUCT_FIELDS = {
    "Certified Canvas": canvas_fields("cc"), "Basic Canvas": canvas_fields("bc"),
    "Certified Paper": paper_fields("cp", True), "Basic Paper": paper_fields("bp", False),
    "HD Finish": hd_fields("hf"), "HD Decor": hd_fields("hdd"),
    "Diasec": [("dsc_type", "Uitvoering", ["Gloss", "Mat", "TruLife"], 0, 0, "true")], "Cruse Fine Art Scan": SCAN,
}

# ---------------------------------------------------------------- tekenen
c = canvas.Canvas(OUT_TMP, pagesize=A4)
c.setTitle("Re-Art Aanvraagformulier")
c.setAuthor("Re-Art – Special Art productions")
form = c.acroForm
initially_hidden = []     # velden die pas verschijnen na een keuze
required_text = {}        # veldnaam -> label (verplicht, tekst)
all_choice = {}           # veldnaam -> label (keuzelijsten, altijd verplicht als zichtbaar)

def header(subtitle):
    c.setFillColor(NAVY); c.rect(0, H - 70, W, 70, stroke=0, fill=1)
    c.setFillColor(white); c.setFont("Helvetica-Bold", 20); c.drawString(40, H - 38, "Aanvraagformulier")
    c.setFont("Helvetica", 8.5); c.drawString(40, H - 54, subtitle.upper())

def footer(page_no):
    c.setStrokeColor(LINE); c.setLineWidth(0.6); c.line(40, 44, W - 40, 44)
    c.setFillColor(MUTED); c.setFont("Helvetica", 8)
    c.drawString(40, 30, "info@re-art.com   ·   036 536 88 75   ·   www.re-art.com")
    c.drawRightString(W - 40, 30, f"Velden met * zijn verplicht   ·   {page_no}/{N_WORKS + 1}")

def section(num, title, y):
    c.setFillColor(NAVY); c.setFont("Helvetica-Oblique", 15); c.drawString(40, y, num)
    c.setFillColor(INK); c.setFont("Helvetica-Bold", 15); c.drawString(66, y, title)

def static_label(text, x, y, req=False):
    c.setFillColor(MUTED); c.setFont("Helvetica-Bold", 7.5)
    c.drawString(x, y, text.upper() + (" *" if req else ""))

def label_field(name, text, x, y, w, hidden, bg=white):
    form.textfield(name="lbl_" + name, value=text.upper() + " *", x=x, y=y - 3, width=w, height=11,
                   fontName="Helvetica-Bold", fontSize=7.5, textColor=MUTED, borderWidth=0,
                   fillColor=bg, borderColor=bg, fieldFlags="readOnly", tooltip=text)
    if hidden:
        initially_hidden.append("lbl_" + name)

def text_input(name, label, x, y, w, h=20, req=False, tip=None, multiline=False):
    form.textfield(name=name, x=x, y=y, width=w, height=h, fontName="Helvetica", fontSize=10 if not multiline else 9.5,
                   textColor=INK, fillColor=FIELD_BG, borderColor=LINE, borderWidth=0.8, forceBorder=False,
                   tooltip=tip or label, fieldFlags="multiline" if multiline else "")
    if req:
        required_text[name] = label

def choice(name, label, options, x, y, w, hidden, tip=None):
    form.choice(name=name, options=[CH] + options, value=CH, x=x, y=y, width=w, height=20,
                fontName="Helvetica", fontSize=9.5, textColor=INK, fillColor=white if hidden else FIELD_BG,
                borderColor=LINE, borderWidth=0.8, forceBorder=False, tooltip=tip or label, fieldFlags="combo")
    all_choice[name] = label
    if hidden:
        initially_hidden.append(name)

# ---- pagina 1: gegevens
header("Special Art productions")
y = H - 112
c.setFillColor(INK); c.setFont("Helvetica", 9.5)
intro = ["Vul je gegevens in en daarna per werk één pagina (maximaal drie werken).",
         "De vragen passen zich aan je keuzes aan. Klaar? Klik op ‘Aanvraag versturen’: de PDF wordt",
         f"gecontroleerd en als bijlage in een nieuwe e-mail aan {MAILTO} gezet."]
for i, line in enumerate(intro):
    c.drawString(40, y - i * 13, line)
y -= 62
section("01", "Je gegevens", y)
col2 = 40 + (W - 80) / 2 + 8
fw = (W - 80) / 2 - 8
rows = [
    [("naam", "Naam / bedrijfsnaam", True, W - 80)],
    [("datum", "Datum (wordt automatisch ingevuld)", False, fw), ("email", "E-mailadres", True, fw)],
    [("tel", "Telefoonnummer", False, W - 80)],
    [("straat", "Straat en huisnummer", False, W - 80)],
    [("postcode", "Postcode", False, fw), ("plaats", "Plaats", False, fw)],
    [("land", "Land", False, W - 80)],
]
y -= 30
for row in rows:
    for i, (name, label, req, w) in enumerate(row):
        x = 40 if i == 0 else col2
        static_label(label, x, y, req)
        text_input(name, label, x, y - 24, w, req=req)
    y -= 46
y -= 14
section("03", "Opmerkingen", y)
y -= 22
static_label("Aanvullende wensen of informatie", 40, y)
text_input("opmerkingen", "Opmerkingen", 40, y - 92, W - 80, h=84, multiline=True,
           tip="Bijv. gewenste leverdatum, ophalen of bezorgen, bijzonderheden over het werk")
y -= 130
# knoppen
def button(name, label, x, y, w, primary):
    form.textfield(name=name, value=label, x=x, y=y, width=w, height=26, fontName="Helvetica-Bold", fontSize=10,
                   textColor=white if primary else NAVY, fillColor=NAVY if primary else white,
                   borderColor=NAVY, borderWidth=1, forceBorder=False, fieldFlags="readOnly", tooltip=label)
button("btn_send_1", "Aanvraag versturen", 40, y, 170, True)
button("btn_reset_1", "Formulier leegmaken", 220, y, 150, False)
c.setFillColor(MUTED); c.setFont("Helvetica", 8)
c.drawString(40, y - 16, "Werkt het versturen niet? Sla de PDF op en mail hem zelf naar info@re-art.com.")
footer(1)
c.showPage()

# ---- pagina's 2..4: werken
GRID_X = [40, col2]
OPT_TOP = H - 214
ROW_H = 46
for n in range(1, N_WORKS + 1):
    header(f"Werk {n}")
    y = H - 112
    section("02", f"Je aanvraag – werk {n}", y)
    c.setFillColor(MUTED); c.setFont("Helvetica", 8.5)
    c.drawString(66, y - 16, "Laat deze pagina leeg als je geen werk " + str(n) + " aanvraagt." if n > 1 else "Vul minimaal dit eerste werk in.")
    y -= 44
    static_label("Aantal", 40, y, True)
    text_input(f"w{n}_aantal", "Aantal", 40, y - 24, 70, req=False)
    static_label("Type afwerking of dienst", 126, y, True)
    choice(f"w{n}_prod", "Type afwerking of dienst", PRODUCTS, 126, y - 24, W - 166, False)
    # optievlak
    c.setFillColor(SOFT); c.setStrokeColor(NAVY)
    box_top = OPT_TOP + 22; box_bottom = OPT_TOP - 4 * ROW_H + 10
    c.rect(32, box_bottom, W - 64, box_top - box_bottom, stroke=0, fill=1)
    c.setFillColor(NAVY); c.rect(32, box_bottom, 2.5, box_top - box_bottom, stroke=0, fill=1)
    c.setFillColor(MUTED); c.setFont("Helvetica-Oblique", 8)
    for prod, fields in PRODUCT_FIELDS.items():
        for key, label, opts, col, row, cond in fields:
            name = f"w{n}_{key}"
            x = GRID_X[col] + (4 if col == 0 else 0); yy = OPT_TOP - row * ROW_H
            label_field(name, label, x, yy, fw - 4, True, SOFT)
            choice(name, label, opts, x, yy - 24, fw - 4, True,
                   tip="Tip van Re-Art: we adviseren 5 cm witrand, zodat er ruimte is om te nummeren, te signeren en een blindpreeg te plaatsen." if key.endswith("_wit") else None)
    # titel & formaat
    y = box_bottom - 26
    label_field(f"w{n}_size", "Titel & formaat kunstwerk", 40, y, W - 80, True)
    text_input(f"w{n}_size", "Titel & formaat kunstwerk", 40, y - 24, W - 80,
               tip="Bijv. Zomerlandschap, 80 x 120 cm (b x h)")
    initially_hidden.append(f"w{n}_size")
    y -= 50
    label_field(f"w{n}_cert", "Certificaat", 40, y, fw, True)
    choice(f"w{n}_cert", "Certificaat", YN, 40, y - 24, fw, True)
    y -= 50
    cert_rows = [[("cname", "Naam kunstenaar", True), ("catel", "Naam atelier (optioneel)", False)],
                 [("cweb", "Website kunstenaar", False), ("ctitle", "Titel werk", True)],
                 [("ced", "Editie (oplage)", True), ("cea", "EA (artist proofs, optioneel)", False)]]
    for row in cert_rows:
        for i, (key, label, req) in enumerate(row):
            name = f"w{n}_{key}"; x = 40 if i == 0 else col2
            form.textfield(name="lbl_" + name, value=label.upper() + (" *" if req else ""), x=x, y=y - 3, width=fw, height=11,
                           fontName="Helvetica-Bold", fontSize=7.5, textColor=MUTED, borderWidth=0,
                           fillColor=white, borderColor=white, fieldFlags="readOnly")
            initially_hidden.append("lbl_" + name)
            text_input(name, label, x, y - 24, fw)
            initially_hidden.append(name)
        y -= 46
    form.textfield(name=f"w{n}_ctot", value="", x=40, y=y - 4, width=W - 80, height=14, fontName="Helvetica-Bold",
                   fontSize=8.5, textColor=NAVY, borderWidth=0, fillColor=white, borderColor=white, fieldFlags="readOnly")
    initially_hidden.append(f"w{n}_ctot")
    if n == N_WORKS:
        button("btn_send_2", "Aanvraag versturen", 40, 64, 170, True)
    footer(n + 1)
    c.showPage()
c.save()

# ---------------------------------------------------------------- JavaScript
spec = {}
for n in range(1, N_WORKS + 1):
    items = []
    for prod, fields in PRODUCT_FIELDS.items():
        for key, label, opts, col, row, cond in fields:
            items.append([f"w{n}_{key}", prod, cond.replace("V('", f"V('w{n}_"), label])
    spec[n] = items

doc_js = r"""
var CH = "%CH%";
var N_WORKS = %N%;
var SPEC = %SPEC%;
function V(n) { var f = this.getField(n); return f ? f.valueAsString : ""; }
function vis(n) { var f = this.getField(n); return f && f.display == display.visible; }
function setVis(n, on) {
  var d = on ? display.visible : display.hidden;
  var f = this.getField(n); if (f && f.display != d) f.display = d;
  var l = this.getField("lbl_" + n); if (l && l.display != d) l.display = d;
}
function reartUpdate() {
  for (var n = 1; n <= N_WORKS; n++) {
    var p = V("w" + n + "_prod");
    var items = SPEC[n];
    for (var i = 0; i < items.length; i++) {
      var it = items[i];
      setVis(it[0], p == it[1] && eval(it[2]));
    }
    var chosen = p != CH && p != "";
    setVis("w" + n + "_size", chosen);
    var certOn = chosen && p != "Cruse Fine Art Scan";
    setVis("w" + n + "_cert", certOn);
    var c = certOn && V("w" + n + "_cert") == "Ja";
    var keys = ["cname", "catel", "cweb", "ctitle", "ced", "cea"];
    for (var k = 0; k < keys.length; k++) setVis("w" + n + "_" + keys[k], c);
    var tot = this.getField("w" + n + "_ctot");
    var ed = parseInt(V("w" + n + "_ced"), 10), ea = parseInt(V("w" + n + "_cea"), 10) || 0;
    var txt = (c && !isNaN(ed)) ? "Totale editie (editie + EA): " + (ed + ea) : "";
    if (tot) { if (tot.value != txt) tot.value = txt; tot.display = txt ? display.visible : display.hidden; }
  }
}
function reartCheck() {
  var miss = [];
  var req = [["naam", "Naam / bedrijfsnaam"], ["email", "E-mailadres"]];
  for (var i = 0; i < req.length; i++) if (V(req[i][0]).replace(/\s/g, "") == "") miss.push(req[i][1]);
  var em = V("email");
  if (em && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em)) miss.push("E-mailadres is ongeldig");
  var any = false;
  for (var n = 1; n <= N_WORKS; n++) {
    var p = V("w" + n + "_prod");
    if (p == CH || p == "") { if (n == 1) miss.push("Werk 1: type afwerking of dienst"); continue; }
    any = true;
    var w = "Werk " + n + ": ";
    var a = parseInt(V("w" + n + "_aantal"), 10);
    if (isNaN(a) || a < 1) miss.push(w + "aantal");
    var items = SPEC[n];
    for (var i = 0; i < items.length; i++) if (vis(items[i][0]) && V(items[i][0]) == CH) miss.push(w + items[i][3].toLowerCase());
    if (V("w" + n + "_size").replace(/\s/g, "") == "") miss.push(w + "titel & formaat kunstwerk");
    if (vis("w" + n + "_cert")) {
      if (V("w" + n + "_cert") == CH) miss.push(w + "certificaat");
      else if (V("w" + n + "_cert") == "Ja") {
        if (V("w" + n + "_cname") == "") miss.push(w + "naam kunstenaar");
        if (V("w" + n + "_ctitle") == "") miss.push(w + "titel werk");
        if (V("w" + n + "_ced") == "") miss.push(w + "editie");
      }
    }
  }
  return miss;
}
function reartSend() {
  var miss = reartCheck();
  if (miss.length) {
    app.alert({ cMsg: "Niet alles is ingevuld:\n\n• " + miss.slice(0, 15).join("\n• ") + (miss.length > 15 ? "\n• …" : ""), cTitle: "Re-Art aanvraagformulier", nIcon: 1 });
    return;
  }
  var naam = V("naam");
  try {
    this.mailDoc({ bUI: true, cTo: "%MAILTO%", cSubject: "Aanvraag Re-Art – " + naam,
      cMsg: "Beste Re-Art,\n\nIn de bijlage vindt u mijn ingevulde aanvraagformulier.\n\nMet vriendelijke groet,\n" + naam });
  } catch (e) {
    app.alert("Het formulier kon niet automatisch worden gemaild. Sla de PDF op en stuur hem naar %MAILTO%.");
  }
}
function reartReset() {
  if (app.alert({ cMsg: "Weet u zeker dat u het hele formulier wilt leegmaken?", nIcon: 2, nType: 2 }) == 4) {
    this.resetForm(); reartToday(); reartUpdate();
  }
}
function reartToday() {
  var f = this.getField("datum");
  if (f && f.valueAsString == "") f.value = util.printd("dd-mm-yyyy", new Date());
}
reartToday();
reartUpdate();
""".replace("%CH%", CH).replace("%N%", str(N_WORKS)).replace("%SPEC%", json.dumps(spec)).replace("%MAILTO%", MAILTO)

pdf = pikepdf.open(OUT_TMP)
acro = pdf.Root.AcroForm
fields = {str(f.get("/T")): f for f in acro.Fields}

def js(code):
    return pdf.make_indirect(pikepdf.Dictionary(S=pikepdf.Name.JavaScript, JS=pikepdf.String(code)))

# document-JavaScript
names = pikepdf.Dictionary(JavaScript=pikepdf.Dictionary(Names=pikepdf.Array([pikepdf.String("ReArt"), js(doc_js)])))
pdf.Root.Names = names
pdf.Root.OpenAction = js("reartToday(); reartUpdate();")

for name, f in fields.items():
    flags = int(f.get("/F", 4))
    if name in initially_hidden:
        f.F = (flags | 2 | 4)          # verborgen, maar wel printbaar zodra zichtbaar
    if f.get("/FT") == pikepdf.Name.Ch:
        f.Ff = int(f.get("/Ff", 0)) | (1 << 17) | (1 << 26)   # keuzelijst + direct vastleggen bij keuze
        f.AA = pikepdf.Dictionary(V=js("event.rc = true;"))
    if name.startswith("btn_"):
        # leesbare tekstvelden omzetten naar knoppen met actie
        f.FT = pikepdf.Name.Btn
        f.Ff = 1 << 16                  # pushbutton
        label = str(f.get("/V", ""))
        for k in ("/V", "/DV", "/MaxLen"):
            if k in f: del f[k]
        mk = f.get("/MK", pikepdf.Dictionary())
        mk.CA = pikepdf.String(label)
        f.MK = mk
        f.A = js("reartSend();" if "send" in name else "reartReset();")
    if name == "datum":
        f.Ff = int(f.get("/Ff", 0)) | 1   # alleen-lezen: datum wordt automatisch ingevuld
    if name.endswith("_aantal"):
        f.AA = pikepdf.Dictionary(K=js("AFNumber_Keystroke(0, 0, 0, 0, '', true);"), F=js("AFNumber_Format(0, 0, 0, 0, '', true);"))

# Verborgen rekenveld: draait reartUpdate() na elke wijziging
trig_rect = pikepdf.Array([0, 0, 0, 0])
page0 = pdf.pages[0]
trig = pdf.make_indirect(pikepdf.Dictionary(
    Type=pikepdf.Name.Annot, Subtype=pikepdf.Name.Widget, FT=pikepdf.Name.Tx, T=pikepdf.String("calc_trigger"),
    Rect=trig_rect, F=2, P=page0.obj, V=pikepdf.String(""), DA=pikepdf.String("/Helv 0 Tf 0 g"),
    AA=pikepdf.Dictionary(C=js("reartUpdate();"))))
page0.obj.Annots.append(trig)
acro.Fields.append(trig)
acro.CO = pikepdf.Array([trig])
acro.NeedAppearances = True
dr_fonts = acro.DR.Font
if "/HeBo" not in dr_fonts:
    dr_fonts.HeBo = pdf.make_indirect(pikepdf.Dictionary(Type=pikepdf.Name.Font, Subtype=pikepdf.Name.Type1,
        BaseFont=pikepdf.Name("/Helvetica-Bold"), Encoding=pikepdf.Name.WinAnsiEncoding, Name=pikepdf.Name.HeBo))
pdf.save(OUT)
print("ok", OUT, len(fields) + 1, "velden")
