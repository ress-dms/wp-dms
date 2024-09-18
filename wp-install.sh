#!/bin/bash

# Function to check if WP-CLI is installed
check_wp_cli() {
    if php wp-cli.phar --info >/dev/null 2>&1; then
        echo "WP-CLI is already installed."
    else
        echo "WP-CLI is not installed. Installing now..."
        curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
        chmod +x wp-cli.phar
        sudo mv wp-cli.phar /usr/local/bin/wp
        wp --info
        echo "WP-CLI has been installed."
    fi
}

# Check and install WP-CLI if necessary
check_wp_cli

# Get the current directory name
WP_DIR=$(basename "$PWD")

# Set database details
DB_NAME=$WP_DIR
DB_USER="root"
DB_PASS="root"
DB_HOST="localhost"

# Create the MySQL database
mysql -u $DB_USER -p$DB_PASS -e "CREATE DATABASE \`$DB_NAME\`;"

# Generate the SITE_URL based on the directory name
SITE_URL="$WP_DIR.ress.local"
SITE_TITLE="$WP_DIR By DM&S"

ADMIN_USER="admin"
ADMIN_PASSWORD="admin"
ADMIN_EMAIL="admin@example.com"

# Download WordPress
wp core download --path="$PWD"

# Create the wp-config.php file
wp config create --dbname="$DB_NAME" --dbuser="$DB_USER" --dbpass="$DB_PASS" --dbhost="$DB_HOST" --path="$PWD"

# Set debugging to true
wp config set WP_DEBUG true --raw --path="$PWD"

# Install WordPress
wp core install --url="$SITE_URL" --title="$SITE_TITLE" --admin_user="$ADMIN_USER" --admin_password="$ADMIN_PASSWORD" --admin_email="$ADMIN_EMAIL" --path="$PWD"

# Install plugins
wp plugin install capability-manager-enhanced wpforms-lite debug-bar debug-bar-actions-and-filters-addon classic-editor default-featured-image plugin-inspector log-deprecated-notices query-monitor theme-check wordpress-beta-tester show-current-template theme-inspector view-admin-as --path="$PWD"

# wp plugin install translatepress-multilingual --path="$PWD" --> “Warning: Some code is trying to do a URL redirect.”

# Activate plugins
wp plugin activate --all --path="$PWD"

# Delete default plugins
wp plugin delete akismet hello --path="$PWD"

# echo -e "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum." | wp post generate --post_content --count=20 --path="$PWD"

# Set permalink structure
wp rewrite structure '%postname%' --path="$PWD"

# Create superadmin user
wp user create "$ADMIN_USER" "$ADMIN_EMAIL" --role=administrator --user_pass="$ADMIN_PASSWORD" --path="$PWD"

# delete twenty-three and twenty-two themes
wp theme delete twentytwentythree twentytwentytwo --path="$PWD"

# Install en_GB, nl_NL& fr_FR
wp language core install en_GB nl_NL fr_FR --path="$PWD"

for i in {1..5}; do
    wget -O "image$i.jpg" "https://picsum.photos/800/800"
    # import images to media library
    wp media import "image$i.jpg" --title="Image $i" --path="$PWD"
    # delete the image after importing
    rm "image$i.jpg"
done

# Create symbolic links for the web directory
mkdir web
WEB_DIR="$PWD/web"

ln -s "$PWD/index.php" "$WEB_DIR/index.php"
ln -s "$PWD/wp-config.php" "$WEB_DIR/wp-config.php"
ln -s "$PWD/wp-content" "$WEB_DIR/wp-content"
ln -s "$PWD/wp-includes" "$WEB_DIR/wp-includes"
ln -s "$PWD/wp-admin" "$WEB_DIR/wp-admin"
ln -s "$PWD/wp-settings.php" "$WEB_DIR/wp-settings.php"
ln -s "$PWD/wp-login.php" "$WEB_DIR/wp-login.php"

# Inform the user
echo "Database '$DB_NAME' has been created with the following credentials:"
echo "DB User: $DB_USER"
echo "DB Password: $DB_PASS"
echo "DB Host: $DB_HOST"
echo "Database Name: $DB_NAME"
echo
echo "WordPress has been installed successfully at $SITE_URL - backend at $SITE_URL/wp-admin"
echo "Admin username: $ADMIN_USER"
echo "Admin password: $ADMIN_PASSWORD"
echo "Admin email: $ADMIN_EMAIL"
