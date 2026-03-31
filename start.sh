#!/bin/bash

# Find MariaDB bin directory dynamically
MARIADB_BIN=$(ls -d /nix/store/*-mariadb-server-10.11*/bin 2>/dev/null | tail -1)
if [ -z "$MARIADB_BIN" ]; then
  MARIADB_BIN=$(ls -d /nix/store/*-mariadb-server-*/bin 2>/dev/null | tail -1)
fi

MARIADB_BASE=$(dirname "$MARIADB_BIN")
MARIADB_SHARE=$(ls -d "$MARIADB_BASE/share/mysql" "$MARIADB_BASE/share" 2>/dev/null | head -1)
DATADIR=/home/runner/workspace/wordpress-data/mysql
WP_ROOT=/home/runner/workspace/wordpress-data/wp
SOCKET=/tmp/mysql.sock

echo "=== Starting Gentle Shoes WordPress ==="
echo "MariaDB bin: $MARIADB_BIN"
echo "MariaDB share: $MARIADB_SHARE"

# Kill any existing MariaDB
pkill -f mariadbd 2>/dev/null || true
sleep 1

# ---------------------------------------------------------------
# 1. Initialize MariaDB data dir if it doesn't exist
# ---------------------------------------------------------------
if [ ! -d "$DATADIR/mysql" ]; then
  echo "Initializing MariaDB data directory..."
  mkdir -p "$DATADIR"
  "$MARIADB_BIN/mariadb-install-db" \
    --basedir="$MARIADB_BASE" \
    --datadir="$DATADIR" \
    --auth-root-authentication-method=normal \
    --skip-test-db \
    2>&1 | tail -5
  echo "MariaDB data directory initialized."
fi

# ---------------------------------------------------------------
# 2. Start MariaDB
# ---------------------------------------------------------------
echo "Starting MariaDB..."
"$MARIADB_BIN/mariadbd" --no-defaults \
  --basedir="$MARIADB_BASE" \
  --datadir="$DATADIR" \
  --socket="$SOCKET" \
  --port=3306 \
  --bind-address=127.0.0.1 \
  --skip-grant-tables \
  --skip-networking=0 \
  --pid-file=/tmp/mariadbd.pid \
  2>/tmp/mariadbd.log &

# Wait for MariaDB socket
echo "Waiting for MariaDB..."
for i in $(seq 1 40); do
  if [ -S "$SOCKET" ]; then
    echo "MariaDB ready (${i}s)"
    break
  fi
  sleep 1
done

if [ ! -S "$SOCKET" ]; then
  echo "ERROR: MariaDB failed to start"
  cat /tmp/mariadbd.log
  exit 1
fi

# Create WordPress database (retry until success)
echo "Setting up WordPress database..."
for attempt in 1 2 3 4 5; do
  "$MARIADB_BIN/mariadb" -S "$SOCKET" -e "CREATE DATABASE IF NOT EXISTS wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null && break
  echo "DB create attempt $attempt failed, retrying..."
  sleep 2
done
# Verify database was created
DB_CHECK=$("$MARIADB_BIN/mariadb" -S "$SOCKET" -e "SHOW DATABASES LIKE 'wordpress';" 2>/dev/null)
if echo "$DB_CHECK" | grep -q wordpress; then
  echo "Database ready."
else
  echo "ERROR: Failed to create wordpress database!"
  cat /tmp/mariadbd.log | tail -10
  exit 1
fi

# ---------------------------------------------------------------
# 3. Download WordPress if not installed
# ---------------------------------------------------------------
if [ ! -f "$WP_ROOT/wp-login.php" ]; then
  echo "Downloading WordPress..."
  mkdir -p "$WP_ROOT"
  curl -L https://wordpress.org/latest.tar.gz -o /tmp/wp.tar.gz 2>/dev/null
  tar -xzf /tmp/wp.tar.gz -C /tmp/
  cp -r /tmp/wordpress/. "$WP_ROOT/"
  rm -rf /tmp/wordpress /tmp/wp.tar.gz
  echo "WordPress downloaded."
fi

# ---------------------------------------------------------------
# 4. Install WP-CLI if not available
# ---------------------------------------------------------------
if ! command -v wp &>/dev/null; then
  if [ ! -f /tmp/wp-cli.phar ]; then
    echo "Downloading WP-CLI..."
    curl -L https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /tmp/wp-cli.phar 2>/dev/null
    chmod +x /tmp/wp-cli.phar
  fi
  WP_CLI="php /tmp/wp-cli.phar"
else
  WP_CLI="wp"
fi

# ---------------------------------------------------------------
# 5. Configure wp-config.php if not present
# ---------------------------------------------------------------
if [ ! -f "$WP_ROOT/wp-config.php" ]; then
  echo "Creating wp-config.php..."
  SITE_URL="https://${REPLIT_DEV_DOMAIN}"
  
  $WP_CLI config create \
    --path="$WP_ROOT" \
    --dbname=wordpress \
    --dbuser=root \
    --dbpass="" \
    --dbhost="127.0.0.1" \
    --dbprefix=wp_ \
    --allow-root \
    --extra-php <<PHP
define('WP_HOME', '${SITE_URL}');
define('WP_SITEURL', '${SITE_URL}');
define('FS_METHOD', 'direct');
define('WP_DEBUG', false);
PHP
  echo "wp-config.php created."
fi

# No DB_HOST changes needed - using 127.0.0.1 TCP with skip-networking=0

# ---------------------------------------------------------------
# 6. Install WordPress if not yet installed
# ---------------------------------------------------------------
if ! $WP_CLI --path="$WP_ROOT" --allow-root core is-installed 2>/dev/null; then
  echo "Installing WordPress..."
  SITE_URL="https://${REPLIT_DEV_DOMAIN}"
  $WP_CLI --path="$WP_ROOT" --allow-root core install \
    --url="$SITE_URL" \
    --title="Gentle Shoes" \
    --admin_user=admin \
    --admin_password=admin123 \
    --admin_email=admin@gentleshoes.local \
    --skip-email
  echo "WordPress installed."

  # Install Astra parent theme if missing
  if [ ! -d "$WP_ROOT/wp-content/themes/astra" ]; then
    echo "Installing Astra parent theme..."
    $WP_CLI --path="$WP_ROOT" --allow-root theme install astra 2>/dev/null || true
  fi

  # Install WooCommerce
  echo "Installing WooCommerce..."
  $WP_CLI --path="$WP_ROOT" --allow-root plugin install woocommerce --activate 2>/dev/null || true
  echo "WooCommerce done."

  # Activate theme
  $WP_CLI --path="$WP_ROOT" --allow-root theme activate gentle-shoes 2>/dev/null || true
  echo "Theme activated."

  # Set permalink structure
  $WP_CLI --path="$WP_ROOT" --allow-root rewrite structure '/%postname%/' 2>/dev/null || true
fi

# ---------------------------------------------------------------
# 7. Update site URL dynamically (in case domain changed)
# ---------------------------------------------------------------
SITE_URL="https://${REPLIT_DEV_DOMAIN}"
"$MARIADB_BIN/mariadb" -S "$SOCKET" wordpress -e \
  "UPDATE wp_options SET option_value='$SITE_URL' WHERE option_name IN ('siteurl','home');" 2>/dev/null || true

# ---------------------------------------------------------------
# 8. Ensure theme symlink exists
# ---------------------------------------------------------------
THEME_DIR="$WP_ROOT/wp-content/themes/gentle-shoes"
mkdir -p "$WP_ROOT/wp-content/themes"
if [ ! -e "$THEME_DIR" ]; then
  ln -sf /home/runner/workspace "$THEME_DIR"
fi

# Ensure uploads directory exists
mkdir -p "$WP_ROOT/wp-content/uploads"
chmod 777 "$WP_ROOT/wp-content/uploads" 2>/dev/null || true

# ---------------------------------------------------------------
# 9. Symlink router.php if not in WP_ROOT
# ---------------------------------------------------------------
if [ ! -f "$WP_ROOT/router.php" ]; then
  cp /home/runner/workspace/router.php "$WP_ROOT/router.php"
fi

echo "=== Starting PHP server on port 5000 ==="
cd "$WP_ROOT"
exec php -S 0.0.0.0:5000 router.php
