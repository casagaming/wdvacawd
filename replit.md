# Gentle Shoes WordPress Theme

## Overview
This is the **Gentle Shoes** WordPress child theme, built on top of the **Astra** parent theme. It's designed for a WooCommerce e-commerce site with:
- Arabic (RTL) layout and Cairo/Outfit fonts
- Custom Cash-on-Delivery (COD) checkout flow
- WooCommerce integration with custom templates
- Custom header, footer, and product page templates

## Architecture

### Tech Stack
- **CMS**: WordPress (latest)
- **Parent Theme**: Astra 4.12.6
- **E-commerce**: WooCommerce
- **Language**: PHP 8.2, CSS, JavaScript
- **Database**: MariaDB 10.11 (local)

### Project Structure
```
/home/runner/workspace/          # This repo - the Gentle Shoes child theme
├── functions.php                # Core theme logic, WooCommerce overrides
├── style.css                    # Theme metadata + custom styles
├── header.php / footer.php      # Layout wrappers
├── front-page.php               # Homepage template
├── custom-checkout.php          # Custom COD checkout page
├── custom-thankyou.php          # Custom thank you page
├── assets/js/cod-checkout.js    # Checkout JS logic
├── woocommerce/                 # WooCommerce template overrides
└── template-parts/              # Modular layout components

/home/runner/workspace/wordpress-data/   # NOT in git
├── mysql/                       # MariaDB data directory
└── wp/                          # WordPress core installation
    ├── wp-content/themes/gentle-shoes -> /home/runner/workspace (symlink)
    ├── wp-content/plugins/woocommerce/
    └── wp-config.php
```

## Startup
The `start.sh` script handles everything:
1. Starts MariaDB on port 3306 (localhost)
2. Initializes the `wordpress` database
3. Starts PHP built-in server on port 5000 (0.0.0.0)

## WordPress Setup
- **Admin**: https://[domain]/wp-admin
- **User**: admin / admin123
- **Database**: wordpress (MariaDB, no password, skip-grant-tables)

## Required Plugins
- WooCommerce (installed automatically via WP-CLI on first run)

## Notes
- The `wordpress-data/` directory is NOT committed to git
- Theme is symlinked from WP themes directory to this workspace root
- MariaDB uses `--skip-grant-tables` for simplicity in development
- WordPress URL is dynamically set from `REPLIT_DEV_DOMAIN` env var
