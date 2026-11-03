#!/usr/bin/env bash
# Bouwt de WordPress-plugin opnieuw op uit index.html en maakt re-art-orderformulier.zip.
# Gebruik: bash tools/build-plugin.sh   (vanuit de hoofdmap van de repository)
set -euo pipefail
P=wordpress-plugin/re-art-orderformulier
python3 tools/split.py index.html "$P"
cp logo.png "$P/assets/logo.png"
rm -f re-art-orderformulier.zip
(cd wordpress-plugin && zip -qr ../re-art-orderformulier.zip re-art-orderformulier -x '*.DS_Store')
echo "Klaar: re-art-orderformulier.zip"
