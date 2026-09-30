import os
from pathlib import Path

STICKER_DIR = Path(__file__).resolve().parent.parent / "tambaski.com.tr" / "assets" / "vehicles" / "stickers"
STICKER_DIR.mkdir(parents=True, exist_ok=True)

stickers = {
    "stripe_double.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 80" width="100%" height="100%">
  <polygon points="20,10 580,10 560,35 0,35" fill="#e11d48"/>
  <polygon points="30,45 590,45 570,70 10,70" fill="#1e293b"/>
</svg>""",

    "stripe_sport.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 700 60" width="100%" height="100%">
  <polygon points="0,35 480,35 460,55 0,55" fill="#f59e0b"/>
  <polygon points="500,35 550,35 530,55 480,55" fill="#0f172a"/>
  <polygon points="570,35 620,35 600,55 550,55" fill="#0f172a"/>
  <polygon points="640,35 700,35 680,55 620,55" fill="#0f172a"/>
  <text x="50" y="26" font-family="Arial Black, Impact, sans-serif" font-weight="900" font-style="italic" font-size="24" fill="#0f172a" letter-spacing="4">PERFORMANCE // SPORT EDITION</text>
</svg>""",

    "stripe_geometric.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 120" width="100%" height="100%">
  <polygon points="50,10 120,10 80,110 10,110" fill="#3b82f6"/>
  <polygon points="140,10 210,10 170,110 100,110" fill="#1d4ed8"/>
  <polygon points="230,10 300,10 260,110 190,110" fill="#1e293b"/>
  <polygon points="320,10 390,10 350,110 280,110" fill="#0f172a"/>
</svg>""",

    "compass_mountain.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" width="100%" height="100%">
  <circle cx="150" cy="150" r="130" fill="none" stroke="#0f172a" stroke-width="6" stroke-dasharray="16, 8"/>
  <circle cx="150" cy="150" r="110" fill="none" stroke="#0f172a" stroke-width="2"/>
  <!-- Dağ Silüeti -->
  <polygon points="70,180 120,110 150,145 190,95 240,180" fill="#0f172a"/>
  <polygon points="120,110 105,135 135,135" fill="#ffffff"/>
  <polygon points="190,95 170,125 210,125" fill="#ffffff"/>
  <!-- Pusula Oku -->
  <polygon points="150,20 165,150 150,135 135,150" fill="#dc2626"/>
  <polygon points="150,280 165,150 150,165 135,150" fill="#0f172a"/>
  <text x="150" y="45" font-family="Arial Black" font-size="20" font-weight="bold" fill="#dc2626" text-anchor="middle">N</text>
  <text x="150" y="270" font-family="Arial Black" font-size="20" font-weight="bold" fill="#0f172a" text-anchor="middle">S</text>
  <text x="265" y="157" font-family="Arial Black" font-size="20" font-weight="bold" fill="#0f172a" text-anchor="middle">E</text>
  <text x="35" y="157" font-family="Arial Black" font-size="20" font-weight="bold" fill="#0f172a" text-anchor="middle">W</text>
</svg>""",

    "offroad_4x4.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 450 200" width="100%" height="100%">
  <text x="20" y="130" font-family="Impact, sans-serif" font-size="140" font-weight="900" font-style="italic" fill="#dc2626" stroke="#000" stroke-width="4">4X4</text>
  <text x="250" y="80" font-family="Arial Black" font-size="34" font-weight="bold" font-style="italic" fill="#0f172a">OFF-ROAD</text>
  <text x="250" y="125" font-family="Arial Black" font-size="24" font-weight="bold" fill="#64748b">MUD &amp; ROCK</text>
  <!-- Çamur Sıçraması Efekti -->
  <circle cx="210" cy="40" r="8" fill="#0f172a"/>
  <circle cx="230" cy="25" r="5" fill="#dc2626"/>
  <circle cx="390" cy="150" r="10" fill="#0f172a"/>
  <circle cx="420" cy="130" r="6" fill="#0f172a"/>
  <polygon points="10,155 440,155 420,175 0,175" fill="#0f172a"/>
</svg>""",

    "wind_rose.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" width="100%" height="100%">
  <circle cx="150" cy="150" r="135" fill="none" stroke="#0f172a" stroke-width="4"/>
  <polygon points="150,15 165,150 150,135" fill="#0f172a"/>
  <polygon points="150,15 135,150 150,135" fill="#64748b"/>
  <polygon points="285,150 150,165 165,150" fill="#0f172a"/>
  <polygon points="285,150 150,135 165,150" fill="#64748b"/>
  <polygon points="150,285 135,150 150,165" fill="#0f172a"/>
  <polygon points="150,285 165,150 150,165" fill="#64748b"/>
  <polygon points="15,150 150,135 135,150" fill="#0f172a"/>
  <polygon points="15,150 150,165 135,150" fill="#64748b"/>
  <circle cx="150" cy="150" r="18" fill="#dc2626"/>
  <circle cx="150" cy="150" r="6" fill="#ffffff"/>
</svg>""",

    "flame_fender.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 200" width="100%" height="100%">
  <path d="M 0 100 Q 120 40 220 80 Q 320 20 500 10 Q 380 90 420 120 Q 280 110 320 160 Q 180 130 150 180 Q 80 140 0 100 Z" fill="#ef4444"/>
  <path d="M 0 100 Q 100 60 180 90 Q 260 50 400 30 Q 300 100 330 120 Q 220 115 240 150 Q 140 130 110 160 Q 60 130 0 100 Z" fill="#f59e0b"/>
  <path d="M 0 100 Q 80 75 140 95 Q 200 70 300 50 Q 230 105 250 120 Q 160 120 170 140 Q 90 125 0 100 Z" fill="#fef08a"/>
</svg>""",

    "tribal_side.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 180" width="100%" height="100%">
  <path d="M 10 90 Q 120 10 240 60 Q 360 10 480 70 Q 560 20 590 10 Q 540 60 560 100 Q 480 80 440 130 Q 340 90 300 160 Q 220 100 160 150 Q 90 110 10 90 Z" fill="#0f172a"/>
  <path d="M 80 85 Q 160 30 250 70 Q 340 30 420 80 Q 360 110 320 140 Q 240 95 180 130 Q 130 100 80 85 Z" fill="#dc2626"/>
</svg>""",

    "dragon_claw.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 250" width="100%" height="100%">
  <path d="M 60 10 Q 75 110 40 230 Q 70 150 90 10 Z" fill="#0f172a"/>
  <path d="M 130 20 Q 150 120 110 240 Q 145 150 165 20 Z" fill="#dc2626"/>
  <path d="M 200 40 Q 220 130 180 230 Q 215 150 235 40 Z" fill="#0f172a"/>
  <path d="M 260 70 Q 275 140 245 220 Q 275 150 290 70 Z" fill="#0f172a"/>
</svg>""",

    "ataturk_signature.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 160" width="100%" height="100%">
  <path d="M 40 110 C 60 70, 80 40, 110 30 C 130 25, 140 45, 130 75 C 120 105, 100 125, 80 135 C 60 145, 45 130, 65 100 C 85 70, 150 35, 210 55 C 240 65, 250 90, 230 115 C 210 140, 175 145, 160 130 C 145 115, 170 95, 200 90 C 240 85, 280 110, 310 105 C 330 100, 350 70, 370 45" fill="none" stroke="#0f172a" stroke-width="6" stroke-linecap="round" stroke-linejoin="round"/>
  <circle cx="105" cy="45" r="4" fill="#0f172a"/>
</svg>""",

    "gokturk_turk.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 350 120" width="100%" height="100%">
  <!-- Göktürkçe TÜRK Harfleri (Sağdan Sola: T - Ü - R - K) -->
  <g fill="#0f172a">
    <!-- Harf 1: K (Sağda) -->
    <path d="M 60 20 L 75 20 L 75 100 L 60 100 Z M 75 55 L 110 20 L 125 20 L 85 60 Z M 75 65 L 125 100 L 110 100 L 75 75 Z"/>
    <!-- Harf 2: R -->
    <path d="M 145 20 L 160 20 L 160 100 L 145 100 Z M 160 20 L 205 60 L 195 70 L 160 40 Z"/>
    <!-- Harf 3: Ü -->
    <path d="M 225 20 L 240 20 L 240 100 L 225 100 Z M 240 55 L 280 20 L 295 20 L 250 60 Z M 240 65 L 295 100 L 280 100 L 240 75 Z"/>
    <!-- Harf 4: T (Solda) -->
    <path d="M 315 20 L 330 20 L 330 100 L 315 100 Z M 300 20 L 345 20 L 345 35 L 300 35 Z"/>
  </g>
</svg>""",

    "ay_yildiz.svg": """<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 300" width="100%" height="100%">
  <!-- Hilal -->
  <path d="M 150 30 A 120 120 0 1 0 150 270 A 96 96 0 1 1 150 30 Z" fill="#dc2626"/>
  <!-- Yıldız -->
  <polygon points="215,105 230,135 260,135 235,155 245,185 215,165 185,185 195,155 170,135 200,135" fill="#dc2626"/>
</svg>"""
}

import sys
sys.stdout.reconfigure(encoding='utf-8')

for filename, content in stickers.items():
    file_path = STICKER_DIR / filename
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)

print(f"{len(stickers)} adet yuksek cozunurluklu SVG sticker varligi uretildi!")
