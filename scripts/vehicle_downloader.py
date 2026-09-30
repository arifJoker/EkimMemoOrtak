"""
Araç Çizimleri ve 1990-2026 Veritabanı İndirme & Hazırlama Motoru
TamBaskı / BykCut - 3D/2.5D Araç Giydirme & 1:1 Sticker Simülatörü
"""

import os
import json
import re
import urllib.request
from pathlib import Path

BASE_DIR = Path(__file__).resolve().parent.parent
VEHICLES_DIR = BASE_DIR / "tambaski.com.tr" / "assets" / "vehicles"
DATA_DIR = BASE_DIR / "tambaski.com.tr" / "assets" / "vehicles" / "data"

VEHICLES_DIR.mkdir(parents=True, exist_ok=True)
DATA_DIR.mkdir(parents=True, exist_ok=True)

# 1990-2026 Kapsamlı Araç Veritabanı ve Milimetrik Ölçüleri
VEHICLE_DATABASE = {
    "fiat": {
        "name": "Fiat",
        "models": {
            "egea_sedan": {
                "name": "Egea Sedan",
                "years": "2015-2026",
                "category": "Binek Sedan",
                "length_mm": 4532,
                "width_mm": 1792,
                "height_mm": 1497,
                "wheelbase_mm": 2636,
                "angles": ["side_left", "side_right", "front", "rear", "top"]
            },
            "egea_cross": {
                "name": "Egea Cross / HB",
                "years": "2020-2026",
                "category": "Crossover / HB",
                "length_mm": 4386,
                "width_mm": 1802,
                "height_mm": 1556,
                "wheelbase_mm": 2638,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "doblo_combi": {
                "name": "Doblo Combi / Cargo",
                "years": "2010-2023",
                "category": "Hafif Ticari",
                "length_mm": 4406,
                "width_mm": 1832,
                "height_mm": 1845,
                "wheelbase_mm": 2755,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "fiorino": {
                "name": "Fiorino Combi",
                "years": "2007-2026",
                "category": "Hafif Ticari",
                "length_mm": 3957,
                "width_mm": 1716,
                "height_mm": 1721,
                "wheelbase_mm": 2513,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "linea": {
                "name": "Linea",
                "years": "2007-2018",
                "category": "Binek Sedan",
                "length_mm": 4560,
                "width_mm": 1730,
                "height_mm": 1494,
                "wheelbase_mm": 2603,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "punto": {
                "name": "Grande Punto / Evo",
                "years": "2005-2018",
                "category": "Hatchback",
                "length_mm": 4030,
                "width_mm": 1687,
                "height_mm": 1490,
                "wheelbase_mm": 2510,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "uno": {
                "name": "Uno",
                "years": "1990-2002",
                "category": "Klasik Hatchback",
                "length_mm": 3689,
                "width_mm": 1558,
                "height_mm": 1430,
                "wheelbase_mm": 2362,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "tempra": {
                "name": "Tempra",
                "years": "1990-1999",
                "category": "Klasik Sedan",
                "length_mm": 4354,
                "width_mm": 1695,
                "height_mm": 1445,
                "wheelbase_mm": 2540,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "tofas_sahin_dogan": {
                "name": "Tofaş Şahin / Doğan / Kartal",
                "years": "1990-2002",
                "category": "Efsane Kasa",
                "length_mm": 4316,
                "width_mm": 1642,
                "height_mm": 1437,
                "wheelbase_mm": 2490,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "renault": {
        "name": "Renault",
        "models": {
            "clio_5": {
                "name": "Clio 5",
                "years": "2019-2026",
                "category": "Hatchback",
                "length_mm": 4050,
                "width_mm": 1798,
                "height_mm": 1440,
                "wheelbase_mm": 2583,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "clio_4": {
                "name": "Clio 4",
                "years": "2012-2020",
                "category": "Hatchback",
                "length_mm": 4062,
                "width_mm": 1732,
                "height_mm": 1448,
                "wheelbase_mm": 2589,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "megane_sedan": {
                "name": "Megane 4 Sedan",
                "years": "2016-2026",
                "category": "Binek Sedan",
                "length_mm": 4632,
                "width_mm": 1814,
                "height_mm": 1443,
                "wheelbase_mm": 2711,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "symbol": {
                "name": "Symbol / Thalia",
                "years": "2008-2021",
                "category": "Binek Sedan",
                "length_mm": 4348,
                "width_mm": 1733,
                "height_mm": 1517,
                "wheelbase_mm": 2634,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "kangoo": {
                "name": "Kangoo Maxi / Express",
                "years": "2008-2026",
                "category": "Hafif Ticari",
                "length_mm": 4282,
                "width_mm": 1829,
                "height_mm": 1805,
                "wheelbase_mm": 2697,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "master": {
                "name": "Master Panelvan",
                "years": "2010-2026",
                "category": "Ticari Panelvan",
                "length_mm": 5548,
                "width_mm": 2070,
                "height_mm": 2499,
                "wheelbase_mm": 3682,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "r12_toros": {
                "name": "R12 Toros",
                "years": "1990-2000",
                "category": "Klasik Station / Sedan",
                "length_mm": 4345,
                "width_mm": 1616,
                "height_mm": 1435,
                "wheelbase_mm": 2440,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "r9_broadway": {
                "name": "R9 Broadway / Spring",
                "years": "1990-2000",
                "category": "Klasik Sedan",
                "length_mm": 4060,
                "width_mm": 1650,
                "height_mm": 1400,
                "wheelbase_mm": 2480,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "ford": {
        "name": "Ford",
        "models": {
            "transit_custom": {
                "name": "Transit Custom / Tourneo",
                "years": "2012-2026",
                "category": "Ticari Van",
                "length_mm": 4972,
                "width_mm": 1986,
                "height_mm": 1979,
                "wheelbase_mm": 2933,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "transit_v363": {
                "name": "Transit Panelvan (Büyük Boy)",
                "years": "2014-2026",
                "category": "Büyük Ticari",
                "length_mm": 5531,
                "width_mm": 2059,
                "height_mm": 2550,
                "wheelbase_mm": 3750,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "courier": {
                "name": "Tourneo / Transit Courier",
                "years": "2014-2026",
                "category": "Hafif Ticari",
                "length_mm": 4157,
                "width_mm": 1764,
                "height_mm": 1747,
                "wheelbase_mm": 2489,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "connect": {
                "name": "Tourneo Connect",
                "years": "2002-2026",
                "category": "Hafif Ticari",
                "length_mm": 4425,
                "width_mm": 1835,
                "height_mm": 1840,
                "wheelbase_mm": 2664,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "focus": {
                "name": "Focus 4 Sedan/HB",
                "years": "2018-2026",
                "category": "Binek",
                "length_mm": 4647,
                "width_mm": 1825,
                "height_mm": 1452,
                "wheelbase_mm": 2700,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "fiesta": {
                "name": "Fiesta",
                "years": "2008-2023",
                "category": "Hatchback",
                "length_mm": 4040,
                "width_mm": 1735,
                "height_mm": 1476,
                "wheelbase_mm": 2493,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "ranger": {
                "name": "Ranger 4x4 Pick-Up",
                "years": "2012-2026",
                "category": "Off-Road Pick-Up",
                "length_mm": 5359,
                "width_mm": 1860,
                "height_mm": 1815,
                "wheelbase_mm": 3220,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "escort": {
                "name": "Escort",
                "years": "1990-2001",
                "category": "Klasik Sedan/HB",
                "length_mm": 4136,
                "width_mm": 1695,
                "height_mm": 1395,
                "wheelbase_mm": 2525,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "volkswagen": {
        "name": "Volkswagen",
        "models": {
            "transporter_t6": {
                "name": "Transporter T6 / T6.1 / Caravelle",
                "years": "2015-2024",
                "category": "Ticari Van",
                "length_mm": 4904,
                "width_mm": 1904,
                "height_mm": 1990,
                "wheelbase_mm": 3000,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "transporter_t5": {
                "name": "Transporter T5",
                "years": "2003-2015",
                "category": "Ticari Van",
                "length_mm": 4892,
                "width_mm": 1904,
                "height_mm": 1970,
                "wheelbase_mm": 3000,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "caddy": {
                "name": "Caddy 4 / 5",
                "years": "2015-2026",
                "category": "Hafif Ticari",
                "length_mm": 4500,
                "width_mm": 1855,
                "height_mm": 1798,
                "wheelbase_mm": 2755,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "crafter": {
                "name": "Crafter Panelvan",
                "years": "2006-2026",
                "category": "Büyük Ticari",
                "length_mm": 5986,
                "width_mm": 2040,
                "height_mm": 2590,
                "wheelbase_mm": 3640,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "golf_8": {
                "name": "Golf 8",
                "years": "2019-2026",
                "category": "Hatchback",
                "length_mm": 4284,
                "width_mm": 1789,
                "height_mm": 1456,
                "wheelbase_mm": 2636,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "golf_7": {
                "name": "Golf 7",
                "years": "2012-2020",
                "category": "Hatchback",
                "length_mm": 4255,
                "width_mm": 1799,
                "height_mm": 1452,
                "wheelbase_mm": 2637,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "passat_b8": {
                "name": "Passat B8",
                "years": "2014-2023",
                "category": "Binek Sedan",
                "length_mm": 4767,
                "width_mm": 1832,
                "height_mm": 1456,
                "wheelbase_mm": 2791,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "polo": {
                "name": "Polo 6",
                "years": "2017-2026",
                "category": "Hatchback",
                "length_mm": 4053,
                "width_mm": 1751,
                "height_mm": 1461,
                "wheelbase_mm": 2551,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "amarok": {
                "name": "Amarok Pick-Up",
                "years": "2010-2026",
                "category": "Off-Road Pick-Up",
                "length_mm": 5254,
                "width_mm": 1954,
                "height_mm": 1834,
                "wheelbase_mm": 3097,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "toyota": {
        "name": "Toyota",
        "models": {
            "corolla_sedan": {
                "name": "Corolla Sedan (E210)",
                "years": "2019-2026",
                "category": "Binek Sedan",
                "length_mm": 4630,
                "width_mm": 1780,
                "height_mm": 1435,
                "wheelbase_mm": 2700,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "hilux": {
                "name": "Hilux 4x4",
                "years": "2015-2026",
                "category": "Off-Road Pick-Up",
                "length_mm": 5330,
                "width_mm": 1855,
                "height_mm": 1815,
                "wheelbase_mm": 3085,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "yaris": {
                "name": "Yaris",
                "years": "2020-2026",
                "category": "Hatchback",
                "length_mm": 3940,
                "width_mm": 1745,
                "height_mm": 1500,
                "wheelbase_mm": 2560,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "proace_city": {
                "name": "Proace City",
                "years": "2019-2026",
                "category": "Hafif Ticari",
                "length_mm": 4403,
                "width_mm": 1848,
                "height_mm": 1880,
                "wheelbase_mm": 2785,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "corolla_e100": {
                "name": "Corolla Efsane Kasa (E100)",
                "years": "1992-1998",
                "category": "Klasik Sedan",
                "length_mm": 4270,
                "width_mm": 1685,
                "height_mm": 1380,
                "wheelbase_mm": 2465,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "bmw": {
        "name": "BMW",
        "models": {
            "3_series_g20": {
                "name": "3 Serisi (G20)",
                "years": "2019-2026",
                "category": "Premium Sedan",
                "length_mm": 4709,
                "width_mm": 1827,
                "height_mm": 1435,
                "wheelbase_mm": 2851,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "3_series_f30": {
                "name": "3 Serisi (F30)",
                "years": "2011-2019",
                "category": "Premium Sedan",
                "length_mm": 4624,
                "width_mm": 1811,
                "height_mm": 1429,
                "wheelbase_mm": 2810,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "3_series_e90": {
                "name": "3 Serisi (E90)",
                "years": "2005-2013",
                "category": "Premium Sedan",
                "length_mm": 4520,
                "width_mm": 1817,
                "height_mm": 1421,
                "wheelbase_mm": 2760,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "3_series_e46": {
                "name": "3 Serisi (E46)",
                "years": "1998-2006",
                "category": "Efsane Sedan / Coupe",
                "length_mm": 4471,
                "width_mm": 1739,
                "height_mm": 1415,
                "wheelbase_mm": 2725,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "3_series_e36": {
                "name": "3 Serisi (E36)",
                "years": "1990-2000",
                "category": "Efsane Sedan / Coupe",
                "length_mm": 4433,
                "width_mm": 1698,
                "height_mm": 1393,
                "wheelbase_mm": 2700,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "5_series_g30": {
                "name": "5 Serisi (G30)",
                "years": "2017-2023",
                "category": "Executive Sedan",
                "length_mm": 4936,
                "width_mm": 1868,
                "height_mm": 1479,
                "wheelbase_mm": 2975,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "5_series_f10": {
                "name": "5 Serisi (F10)",
                "years": "2010-2017",
                "category": "Executive Sedan",
                "length_mm": 4899,
                "width_mm": 1860,
                "height_mm": 1464,
                "wheelbase_mm": 2968,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "5_series_e60": {
                "name": "5 Serisi (E60)",
                "years": "2003-2010",
                "category": "Executive Sedan",
                "length_mm": 4841,
                "width_mm": 1846,
                "height_mm": 1468,
                "wheelbase_mm": 2888,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "5_series_e39": {
                "name": "5 Serisi (E39)",
                "years": "1995-2004",
                "category": "Efsane Sedan",
                "length_mm": 4775,
                "width_mm": 1800,
                "height_mm": 1435,
                "wheelbase_mm": 2830,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "mercedes": {
        "name": "Mercedes-Benz",
        "models": {
            "c_class_w206": {
                "name": "C Serisi (W206)",
                "years": "2021-2026",
                "category": "Premium Sedan",
                "length_mm": 4751,
                "width_mm": 1820,
                "height_mm": 1438,
                "wheelbase_mm": 2865,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "c_class_w205": {
                "name": "C Serisi (W205)",
                "years": "2014-2021",
                "category": "Premium Sedan",
                "length_mm": 4686,
                "width_mm": 1810,
                "height_mm": 1442,
                "wheelbase_mm": 2840,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "e_class_w213": {
                "name": "E Serisi (W213)",
                "years": "2016-2023",
                "category": "Executive Sedan",
                "length_mm": 4923,
                "width_mm": 1852,
                "height_mm": 1468,
                "wheelbase_mm": 2939,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "vito": {
                "name": "Vito / V-Class (W447)",
                "years": "2014-2026",
                "category": "VIP Minibüs / Van",
                "length_mm": 5140,
                "width_mm": 1928,
                "height_mm": 1910,
                "wheelbase_mm": 3200,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "sprinter": {
                "name": "Sprinter Panelvan",
                "years": "2006-2026",
                "category": "Büyük Ticari",
                "length_mm": 5932,
                "width_mm": 2020,
                "height_mm": 2620,
                "wheelbase_mm": 3665,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "w124": {
                "name": "E Serisi Efsane Kasa (W124)",
                "years": "1990-1996",
                "category": "Klasik Sedan",
                "length_mm": 4740,
                "width_mm": 1740,
                "height_mm": 1430,
                "wheelbase_mm": 2800,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "peugeot": {
        "name": "Peugeot",
        "models": {
            "208": {
                "name": "208",
                "years": "2019-2026",
                "category": "Hatchback",
                "length_mm": 4055,
                "width_mm": 1745,
                "height_mm": 1430,
                "wheelbase_mm": 2540,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "3008": {
                "name": "3008 SUV",
                "years": "2016-2026",
                "category": "SUV",
                "length_mm": 4447,
                "width_mm": 1841,
                "height_mm": 1624,
                "wheelbase_mm": 2675,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "partner_rifter": {
                "name": "Rifter / Partner Tepee",
                "years": "2008-2026",
                "category": "Hafif Ticari",
                "length_mm": 4403,
                "width_mm": 1848,
                "height_mm": 1878,
                "wheelbase_mm": 2785,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "boxer": {
                "name": "Boxer Panelvan",
                "years": "2006-2026",
                "category": "Büyük Ticari",
                "length_mm": 5413,
                "width_mm": 2050,
                "height_mm": 2522,
                "wheelbase_mm": 3450,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "206": {
                "name": "206 / 206+",
                "years": "1998-2013",
                "category": "Efsane Hatchback",
                "length_mm": 3835,
                "width_mm": 1652,
                "height_mm": 1428,
                "wheelbase_mm": 2442,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "hyundai": {
        "name": "Hyundai",
        "models": {
            "i20": {
                "name": "i20",
                "years": "2020-2026",
                "category": "Hatchback",
                "length_mm": 4040,
                "width_mm": 1775,
                "height_mm": 1450,
                "wheelbase_mm": 2580,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "tucson": {
                "name": "Tucson SUV",
                "years": "2020-2026",
                "category": "SUV",
                "length_mm": 4500,
                "width_mm": 1865,
                "height_mm": 1650,
                "wheelbase_mm": 2680,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "h100": {
                "name": "H-100 Kamyonet",
                "years": "1996-2026",
                "category": "Kamyonet",
                "length_mm": 4850,
                "width_mm": 1740,
                "height_mm": 1970,
                "wheelbase_mm": 2430,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "accent_era": {
                "name": "Accent Era",
                "years": "2006-2012",
                "category": "Binek Sedan",
                "length_mm": 4280,
                "width_mm": 1695,
                "height_mm": 1470,
                "wheelbase_mm": 2500,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "accent_milenyum": {
                "name": "Accent Milenyum / Yumurta",
                "years": "1994-2005",
                "category": "Klasik Sedan",
                "length_mm": 4235,
                "width_mm": 1670,
                "height_mm": 1395,
                "wheelbase_mm": 2440,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "honda": {
        "name": "Honda",
        "models": {
            "civic_fe": {
                "name": "Civic 11 (FE)",
                "years": "2021-2026",
                "category": "Sedan",
                "length_mm": 4677,
                "width_mm": 1802,
                "height_mm": 1415,
                "wheelbase_mm": 2735,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_fc5": {
                "name": "Civic 10 (FC5)",
                "years": "2016-2021",
                "category": "Sedan",
                "length_mm": 4648,
                "width_mm": 1799,
                "height_mm": 1407,
                "wheelbase_mm": 2698,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_fb7": {
                "name": "Civic 9 (FB7)",
                "years": "2012-2016",
                "category": "Sedan",
                "length_mm": 4545,
                "width_mm": 1755,
                "height_mm": 1435,
                "wheelbase_mm": 2675,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_fd6": {
                "name": "Civic 8 (FD6)",
                "years": "2006-2012",
                "category": "Efsane Sedan",
                "length_mm": 4545,
                "width_mm": 1750,
                "height_mm": 1435,
                "wheelbase_mm": 2700,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_vtec2": {
                "name": "Civic 7 (VTEC II)",
                "years": "2001-2006",
                "category": "Efsane Sedan",
                "length_mm": 4458,
                "width_mm": 1715,
                "height_mm": 1440,
                "wheelbase_mm": 2620,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_ek": {
                "name": "Civic 6 (EK / EJ - Japon Kasa)",
                "years": "1995-2001",
                "category": "Kült HB / Sedan",
                "length_mm": 4190,
                "width_mm": 1705,
                "height_mm": 1375,
                "wheelbase_mm": 2620,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "civic_eg": {
                "name": "Civic 5 (EG - Yumurta Kasa)",
                "years": "1991-1995",
                "category": "Kült HB",
                "length_mm": 4080,
                "width_mm": 1700,
                "height_mm": 1350,
                "wheelbase_mm": 2570,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    },
    "dacia": {
        "name": "Dacia",
        "models": {
            "duster_3": {
                "name": "Duster 3",
                "years": "2024-2026",
                "category": "SUV 4x4",
                "length_mm": 4343,
                "width_mm": 1813,
                "height_mm": 1661,
                "wheelbase_mm": 2657,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "duster_2": {
                "name": "Duster 2",
                "years": "2018-2024",
                "category": "SUV 4x4",
                "length_mm": 4341,
                "width_mm": 1804,
                "height_mm": 1693,
                "wheelbase_mm": 2674,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "sandero_stepway": {
                "name": "Sandero Stepway",
                "years": "2020-2026",
                "category": "Crossover",
                "length_mm": 4099,
                "width_mm": 1848,
                "height_mm": 1587,
                "wheelbase_mm": 2604,
                "angles": ["side_left", "side_right", "front", "rear"]
            },
            "dokker": {
                "name": "Dokker Combi / Van",
                "years": "2012-2021",
                "category": "Hafif Ticari",
                "length_mm": 4363,
                "width_mm": 1751,
                "height_mm": 1814,
                "wheelbase_mm": 2810,
                "angles": ["side_left", "side_right", "front", "rear"]
            }
        }
    }
}

def generate_database_json():
    """Veritabanını JSON olarak kaydeder"""
    json_path = DATA_DIR / "vehicles.json"
    with open(json_path, "w", encoding="utf-8") as f:
        json.dump(VEHICLE_DATABASE, f, ensure_ascii=False, indent=2)
    print(f"✅ Araç veritabanı kaydedildi: {json_path} (Toplam {sum(len(b['models']) for b in VEHICLE_DATABASE.values())} ana model)")

def generate_svg_templates():
    """
    Her araç ve açı için katmanlı, 0-4 numara cam filmi destekli,
    canlı renk değiştiren ve 1:1 ölçekli SVG şablonlarını üretir.
    """
    templates_dir = VEHICLES_DIR / "svg"
    templates_dir.mkdir(parents=True, exist_ok=True)

    for brand_key, brand in VEHICLE_DATABASE.items():
        brand_folder = templates_dir / brand_key
        brand_folder.mkdir(parents=True, exist_ok=True)
        
        for model_key, model in brand["models"].items():
            for angle in model.get("angles", ["side_left"]):
                svg_file = brand_folder / f"{model_key}_{angle}.svg"
                
                # SVG Şablonu (Gövde, Cam Filmi Katmanı, Tekerlekler, Farlar, Gölge Overlay)
                svg_content = build_layered_svg(brand["name"], model["name"], model["category"], model["length_mm"], angle)
                
                with open(svg_file, "w", encoding="utf-8") as f:
                    f.write(svg_content)
    
    print("✅ Tüm katmanlı SVG şablonları üretildi!")

def build_layered_svg(brand_name, model_name, category, length_mm, angle):
    """
    Dinamik Katmanlı Vektörel SVG Şablonu Oluşturucu
    """
    # Standart Tuval: 1000 x 420 px
    # 1 px = (length_mm / 920) mm
    scale_factor = round(length_mm / 920.0, 4)
    
    # Araç Tipine Göre Gövde ve Cam Hatları
    is_van = "Ticari" in category or "Van" in category or "Panelvan" in category or "Minibüs" in category
    is_suv = "SUV" in category or "Pick-Up" in category or "Crossover" in category
    
    # Gövde Path Koordinatları
    if is_van:
        # Kutu/Ticari hatlar (Transit / Doblo / Transporter vb.)
        body_path = "M 70 300 C 65 240, 85 190, 150 160 L 250 110 C 290 95, 340 90, 420 90 L 890 90 C 930 90, 940 100, 940 140 L 940 300 C 940 330, 930 335, 880 335 L 785 335 C 785 270, 715 270, 715 335 L 305 335 C 305 270, 235 270, 235 335 L 110 335 C 75 335, 70 325, 70 300 Z"
        window_path = "M 165 165 L 255 118 C 285 105, 320 102, 380 102 L 530 102 C 540 102, 545 108, 545 120 L 545 180 C 545 188, 540 190, 530 190 L 175 190 C 160 190, 155 180, 165 165 Z M 560 102 L 730 102 C 740 102, 745 108, 745 120 L 745 180 C 745 188, 740 190, 730 190 L 560 190 Z M 760 102 L 915 102 C 925 102, 930 108, 930 120 L 930 180 C 930 188, 925 190, 915 190 L 760 190 Z"
        wheel1_cx, wheel2_cx = 270, 750
    elif is_suv:
        # Yüksek SUV/Pick-up hatları (Duster / 3008 / Hilux / Ranger)
        body_path = "M 60 290 C 55 240, 80 200, 140 180 L 240 140 C 280 115, 330 105, 410 105 L 680 105 C 740 105, 780 125, 840 160 L 930 200 C 950 210, 955 230, 955 280 L 955 315 C 955 335, 940 340, 890 340 L 805 340 C 805 265, 715 265, 715 340 L 315 340 C 315 265, 225 265, 225 340 L 100 340 C 65 340, 60 325, 60 290 Z"
        window_path = "M 155 182 L 250 142 C 285 125, 320 115, 390 115 L 530 115 L 530 195 L 170 195 Z M 545 115 L 675 115 C 720 115, 750 125, 790 150 L 830 180 C 838 186, 835 195, 825 195 L 545 195 Z"
        wheel1_cx, wheel2_cx = 270, 760
    else:
        # Standart Sedan / Hatchback dinamik hatları (Egea / Megane / Civic / Passat / Clio)
        body_path = "M 50 280 C 45 235, 70 205, 140 190 L 260 160 C 310 130, 360 110, 440 110 L 640 110 C 720 110, 770 130, 840 175 L 930 215 C 960 230, 965 245, 965 285 L 965 315 C 965 335, 945 340, 885 340 L 805 340 C 805 260, 705 260, 705 340 L 305 340 C 305 260, 205 260, 205 340 L 95 340 C 55 340, 50 325, 50 280 Z"
        window_path = "M 160 190 L 270 160 C 315 135, 355 120, 420 120 L 525 120 L 525 195 L 180 195 C 165 195, 155 195, 160 190 Z M 540 120 L 635 120 C 690 120, 735 135, 780 165 L 835 200 C 840 205, 835 210, 825 210 L 540 210 Z"
        wheel1_cx, wheel2_cx = 255, 755

    svg = f"""<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1020 440" width="100%" height="100%" 
     data-brand="{brand_name}" data-model="{model_name}" data-category="{category}" 
     data-length-mm="{length_mm}" data-scale-factor="{scale_factor}" data-angle="{angle}">
  <defs>
    <!-- Zemin Gölgesi -->
    <radialGradient id="ground-shadow" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#000000" stop-opacity="0.6"/>
      <stop offset="70%" stop-color="#000000" stop-opacity="0.2"/>
      <stop offset="100%" stop-color="#000000" stop-opacity="0"/>
    </radialGradient>
    
    <!-- 3D Metalik Kaporta Işık Gradiyenti -->
    <linearGradient id="body-highlight" x1="0%" y1="0%" x2="0%" y2="100%">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.4"/>
      <stop offset="25%" stop-color="#ffffff" stop-opacity="0.1"/>
      <stop offset="65%" stop-color="#000000" stop-opacity="0.0"/>
      <stop offset="100%" stop-color="#000000" stop-opacity="0.45"/>
    </linearGradient>

    <!-- Cam Yansıması -->
    <linearGradient id="glass-reflection" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%" stop-color="#ffffff" stop-opacity="0.35"/>
      <stop offset="40%" stop-color="#ffffff" stop-opacity="0.1"/>
      <stop offset="100%" stop-color="#000000" stop-opacity="0.2"/>
    </linearGradient>
    
    <!-- Jant Gradiyenti -->
    <radialGradient id="rim-gradient" cx="50%" cy="50%" r="50%">
      <stop offset="0%" stop-color="#ffffff"/>
      <stop offset="45%" stop-color="#a0a5aa"/>
      <stop offset="75%" stop-color="#40454a"/>
      <stop offset="100%" stop-color="#151719"/>
    </radialGradient>
  </defs>

  <!-- ================= 0. ZEMİN GÖLGESİ ================= -->
  <g id="layer-ground-shadow">
    <ellipse cx="500" cy="355" rx="440" ry="24" fill="url(#ground-shadow)" />
  </g>

  <!-- ================= 1. DİNAMİK KAPORTA BOYASI (RENK SEÇİCİ) ================= -->
  <!-- fill rengi JS ile dinamik olarak değiştirilir (örn: #ffffff, #1e293b, #dc2626, #2563eb vs.) -->
  <g id="layer-body-paint">
    <path id="vehicle-body-path" d="{body_path}" fill="#e2e8f0" stroke="#1e293b" stroke-width="2.5" stroke-linejoin="round" />
  </g>

  <!-- ================= 2. 3D KAPORTA IŞIK & GÖLGE KATMANI (METALLIC/GLOSS OVERLAY) ================= -->
  <g id="layer-body-lighting" style="pointer-events: none;">
    <path d="{body_path}" fill="url(#body-highlight)" style="mix-blend-mode: overlay;" />
  </g>

  <!-- ================= 3. CAM VE 0-4 NUMARA CAM FİLMİ KATMANI ================= -->
  <g id="layer-windows">
    <!-- Standart Cam Tabanı -->
    <path d="{window_path}" fill="#64748b" stroke="#0f172a" stroke-width="2" />
    
    <!-- Cam Yansıması -->
    <path d="{window_path}" fill="url(#glass-reflection)" />
    
    <!-- DİNAMİK CAM FİLMİ KATMANI (0, 1, 2, 3, 4 Numara) -->
    <!-- Opacity JS ile ayarlanır: 0 No: 0.05, 1 No: 0.35, 2 No: 0.60, 3 No: 0.82, 4 No: 0.96 -->
    <path id="vehicle-window-tint" d="{window_path}" fill="#05070a" opacity="0.35" style="transition: opacity 0.3s ease;" />
  </g>

  <!-- ================= 4. DETAYLAR (FARLAR, IZGARA, ÇITALAR, KAPI KOLLARI) ================= -->
  <g id="layer-vehicle-details" stroke="#0f172a" stroke-width="1.8" fill="none" stroke-linecap="round">
    <!-- Ön Far -->
    <path d="M 60 260 Q 95 240 140 245 L 135 270 Z" fill="#e2e8f0" opacity="0.9" />
    <path d="M 65 262 Q 95 248 130 252" stroke="#38bdf8" stroke-width="3" /> <!-- LED Gündüz Farı -->

    <!-- Arka Stop -->
    <path d="M 945 255 Q 920 245 885 248 L 890 270 Z" fill="#ef4444" opacity="0.95" />
    
    <!-- Kapı Açma Çizgileri & Boşlukları -->
    <path d="M 330 195 L 330 325" stroke="#334155" stroke-width="2" /> <!-- Ön Çamurluk Çizgisi -->
    <path d="M 535 195 L 530 330" stroke="#334155" stroke-width="2" /> <!-- B Sütunu Kapı Ayrımı -->
    <path d="M 720 205 L 720 325" stroke="#334155" stroke-width="2" /> <!-- Arka Kapı Çizgisi -->
    
    <!-- Kapı Kolları -->
    <rect x="360" y="215" width="36" height="8" rx="3" fill="#334155" />
    <rect x="560" y="215" width="36" height="8" rx="3" fill="#334155" />
    
    <!-- Yan Ayna -->
    <path d="M 285 185 Q 260 175 255 195 Q 275 205 295 195 Z" fill="#1e293b" />
    
    <!-- Alt Marşpiyel Çıtası -->
    <path d="M 315 328 L 705 328" stroke="#0f172a" stroke-width="4" />
  </g>

  <!-- ================= 5. DİNAMİK STICKER YERLEŞTİRME VE ÇİZİM ALANI ================= -->
  <!-- Fabric.js Canvas bu katmanın üzerine veya doğrudan araç üstüne monte edilir -->
  <g id="layer-sticker-workspace"></g>

  <!-- ================= 6. TEKERLEKLER VE JANTLAR ================= -->
  <g id="layer-wheels">
    <!-- Ön Tekerlek -->
    <circle cx="{wheel1_cx}" cy="300" r="50" fill="#090a0c" stroke="#1f242d" stroke-width="6" />
    <circle cx="{wheel1_cx}" cy="300" r="35" fill="url(#rim-gradient)" stroke="#222" stroke-width="2" />
    <circle cx="{wheel1_cx}" cy="300" r="14" fill="#111" />
    <!-- 5 Kollu Spor Jant Deseni -->
    <line x1="{wheel1_cx}" y1="268" x2="{wheel1_cx}" y2="332" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    <line x1="{wheel1_cx-30}" y1="290" x2="{wheel1_cx+30}" y2="310" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    <line x1="{wheel1_cx-20}" y1="325" x2="{wheel1_cx+20}" y2="275" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    
    <!-- Arka Tekerlek -->
    <circle cx="{wheel2_cx}" cy="300" r="50" fill="#090a0c" stroke="#1f242d" stroke-width="6" />
    <circle cx="{wheel2_cx}" cy="300" r="35" fill="url(#rim-gradient)" stroke="#222" stroke-width="2" />
    <circle cx="{wheel2_cx}" cy="300" r="14" fill="#111" />
    <!-- 5 Kollu Spor Jant Deseni -->
    <line x1="{wheel2_cx}" y1="268" x2="{wheel2_cx}" y2="332" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    <line x1="{wheel2_cx-30}" y1="290" x2="{wheel2_cx+30}" y2="310" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
    <line x1="{wheel2_cx-20}" y1="325" x2="{wheel2_cx+20}" y2="275" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round" />
  </g>

  <!-- ================= 7. EN ÜST GERÇEKÇİ KONTUR VE PANEL BOŞLUĞU OVERLAY ================= -->
  <g id="layer-top-overlay" style="pointer-events: none; mix-blend-mode: multiply;">
    <!-- Çamurluk Davlumbaz Gölgeleri -->
    <path d="M {wheel1_cx-55} 300 A 55 55 0 0 1 {wheel1_cx+55} 300" stroke="#000000" stroke-width="5" fill="none" opacity="0.4" />
    <path d="M {wheel2_cx-55} 300 A 55 55 0 0 1 {wheel2_cx+55} 300" stroke="#000000" stroke-width="5" fill="none" opacity="0.4" />
  </g>
</svg>"""
    return svg

if __name__ == "__main__":
    import sys
    sys.stdout.reconfigure(encoding='utf-8')
    print("Araç Veritabanı ve SVG Şablon Üretim Motoru Başlatılıyor...")
    generate_database_json()
    generate_svg_templates()
    print("İşlem başarıyla tamamlandı!")
