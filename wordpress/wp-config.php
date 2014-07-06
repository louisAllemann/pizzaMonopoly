<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'pizzaMonopoly');

/** MySQL database username */
define('DB_USER', 'admin');

/** MySQL database password */
define('DB_PASSWORD', 'admin');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '|p`ce~oi<I8|e.T_WW+PAncKo`X)$#C0u; s:>*7+/THm3vwd-s-t3S9mYp4^A>%');
define('SECURE_AUTH_KEY',  '|JB[V2/c|kyJ3u{17z$`NopUnU?A+g|K03p,yRa*nZ}zy-r?zN**4m7jMx+e)i-p');
define('LOGGED_IN_KEY',    'gTay|1U16K7?0]{xX{U~uR4{D#=Um5Pz`R-#hu85~J.0]VHUp >-Pvt9+Wp-Q-#K');
define('NONCE_KEY',        'jJtzpGbeP7@`zKRj}VLEOQ-2UCl!97.*B;B_;s/V4&T11tnz!yK&~E|AT=g81|ZB');
define('AUTH_SALT',        '@8l<B!0P4X5> ~]-B;fir&?XhqK}T]moqlU=]ugR{!Orz$-@&KEi4en|gE#nmiP9');
define('SECURE_AUTH_SALT', ']O=-n3`>-j`)HT+w=5aq4X _lo#l: ZTs1RJL_g6emasIHuC]$5r<r1{gGf oF.N');
define('LOGGED_IN_SALT',   'yl{e`ypffbr.^R6nk3DH]Ay!;1P0H%,q<dU>7:: ;^|^Y/LOa0}>Ba,vlX^Os~aW');
define('NONCE_SALT',       '=ecV3+$U/94NVDE$]fg`$6)ux|^WNVn^!P9jxFHwOI_uXFs}Mg?];|QQ@^2yDi55');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'fr_FR');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
