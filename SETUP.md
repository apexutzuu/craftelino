# PromoBrand - Magazin Online Produse Promoționale (OpenCart 3.x)

## Cerințe Sistem

- **PHP** >= 7.3 (recomandat 8.0+)
- **MySQL** >= 5.6 sau **MariaDB** >= 10.0
- **Apache** cu mod_rewrite SAU **Nginx**
- **XAMPP** / **WAMP** / **Laragon** pe Windows

## Instalare

### 1. Setup Server Local

Instalează **XAMPP** sau **Laragon** (recomandat pe Windows).

### 2. Configurare Bază de Date

1. Creează o bază de date numită `promobrand` în phpMyAdmin
2. Accesează `http://localhost/` în browser
3. Urmează wizard-ul de instalare OpenCart:
   - Database: `promobrand`
   - Username DB: `root`
   - Password DB: (gol pentru XAMPP)
   - Admin username: `admin`
   - Admin password: alege o parolă sigură

### 3. Activare Temă PromoBrand

După instalarea OpenCart:

1. Conectează-te în **Admin Panel** (`/admin`)
2. Mergi la **System → Settings → Edit Store**
3. Tab-ul **General** → **Theme**: selectează `promobrand`
4. **Salvează**

### 4. Instalare Extensie Design Upload

1. Importă fișierul SQL din `system/design_upload_install.sql` în phpMyAdmin
2. Copiază `system/design_upload.ocmod.xml` în dashboard-ul Extensions → Installer
3. Mergi la **Extensions → Modifications** → Click **Refresh**
4. Activează modulul din **Extensions → Modules → Design Upload**

### 5. Configurare Categorii Recomandate

Creează aceste categorii din Admin → Catalog → Categories:
- Termosuri & Căni
- Agende & Carnete
- Tricouri & Textile
- Pixuri & Instrumente
- Genți & Rucsacuri
- Seturi cadou
- Umbrele & Accesorii

### 6. Configurare Producători (Branduri)

Creează din Admin → Catalog → Manufacturers:
- Stanley, Moleskine, Parker, Gildan, B&C, Kariban, Halfar, Impliva, Result, Roly, Elevate

## Structura Temei

```
catalog/view/theme/promobrand/
├── stylesheet/
│   └── promobrand.css          # Stilul principal (design custom)
├── template/
│   ├── common/
│   │   ├── header.twig         # Header: topbar, logo, search, nav
│   │   ├── footer.twig         # Footer: info, servicii, plată
│   │   └── home.twig           # Homepage: hero, categorii, produse
│   └── product/
│       ├── product.twig        # Pagina produs cu UPLOAD DESIGN + OBSERVAȚII
│       ├── category.twig       # Pagina categorie cu grid produse
│       ├── search.twig         # Pagina căutare
│       ├── special.twig        # Pagina oferte speciale
│       ├── compare.twig        # Comparare produse
│       └── manufacturer_list.twig  # Lista branduri
```

## Funcționalitate Upload Design

Pe pagina fiecărui produs există secțiunea **"Încarcă designul tău"** care permite:

1. **Drag & Drop upload** sau click pentru selectare fișier
2. Formate acceptate: PNG, JPG, PDF, AI, CDR, SVG (max 10MB)
3. Preview cu numele fișierului și opțiune de ștergere
4. **Câmp Observații** - textarea unde clientul poate descrie:
   - Poziția logo-ului pe produs
   - Dimensiunea dorită
   - Culorile preferate
   - Text suplimentar
   - Alte instrucțiuni de personalizare

Designul și observațiile se salvează automat la comanda produsului.

## Culori Brand

| Element | Culoare |
|---------|---------|
| Primary (dark blue) | `#1a3a5c` |
| Accent (gold) | `#e8a020` |
| Background | `#f4f2ee` |
| Border | `#e8e5df` |
| Green | `#2c5f2e` |
| Purple | `#6b2d6b` |
| Highlight | `#f5c842` |

## Fonturi

- **Barlow Condensed** — titluri, prețuri, butoane
- **Barlow** — text general, descrieri

## Icoane

- **Tabler Icons** (CDN) — înlocuiesc Font Awesome din tema default

## Licență

OpenCart este software open-source sub licență GPL v3.
