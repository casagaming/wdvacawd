#!/bin/bash

# Find MariaDB bin directory dynamically
MARIADB_BIN=$(ls -d /nix/store/*-mariadb-server-10.11*/bin 2>/dev/null | tail -1)
if [ -z "$MARIADB_BIN" ]; then
  MARIADB_BIN=$(ls -d /nix/store/*-mariadb-server-*/bin 2>/dev/null | tail -1)
fi

MARIADB_BASE=$(dirname "$MARIADB_BIN")
MARIADB_SHARE="$MARIADB_BASE/share/mysql"
DATADIR=/home/runner/workspace/wordpress-data/mysql
WP_ROOT=/home/runner/workspace/wordpress-data/wp
SOCKET=/tmp/mysql.sock

echo "=== Starting Gentle Shoes WordPress ==="
echo "MariaDB bin: $MARIADB_BIN"

# Kill any existing MariaDB
pkill -f mariadbd 2>/dev/null || true
sleep 1

# Start MariaDB
echo "Starting MariaDB..."
$MARIADB_BIN/mariadbd --no-defaults \
  --basedir="$MARIADB_BASE" \
  --datadir=$DATADIR \
  --socket=$SOCKET \
  --port=3306 \
  --bind-address=127.0.0.1 \
  --skip-grant-tables \
  --pid-file=/tmp/mariadbd.pid \
  2>/tmp/mariadbd.log &

# Wait for MariaDB socket
echo "Waiting for MariaDB..."
for i in $(seq 1 30); do
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

# Initialize system tables if needed
if ! $MARIADB_BIN/mariadb -S $SOCKET -e "SELECT 1 FROM mysql.user LIMIT 1;" 2>/dev/null; then
  echo "Initializing MariaDB system tables..."
  $MARIADB_BIN/mariadb -S $SOCKET mysql < $MARIADB_SHARE/mysql_system_tables.sql 2>/dev/null || true
  $MARIADB_BIN/mariadb -S $SOCKET mysql < $MARIADB_SHARE/mysql_system_tables_data.sql 2>/dev/null || true
  echo "System tables done."
fi

# Create WordPress database
echo "Setting up WordPress database..."
$MARIADB_BIN/mariadb -S $SOCKET -e "CREATE DATABASE IF NOT EXISTS wordpress CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
echo "Database ready."

# Ensure theme symlink exists
THEME_DIR=$WP_ROOT/wp-content/themes/gentle-shoes
mkdir -p $WP_ROOT/wp-content/themes
if [ ! -e "$THEME_DIR" ]; then
  ln -sf /home/runner/workspace $THEME_DIR
fi

# Ensure uploads directory exists
mkdir -p $WP_ROOT/wp-content/uploads
chmod 777 $WP_ROOT/wp-content/uploads 2>/dev/null || true

echo "=== Starting PHP server on port 5000 ==="
cd $WP_ROOT
exec php -S 0.0.0.0:5000 router.php
