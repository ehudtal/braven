<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpress');

/** MySQL database password */
define('DB_PASSWORD', 'wordpress');

/** MySQL hostname */
define('DB_HOST', 'bravendb');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/** The domain of the bebraven site to signup and apply to the program */
define('BRAVEN_JOIN_DOMAIN', 'join.docker');

/** The domain of the bebraven portal (LMS) */
define('BRAVEN_PORTAL_DOMAIN', 'canvas.docker');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '1234567890aosghjdflkhj,8]K5zS1TPnY0#Cf`>liE?az$:Qp:Uw$no=9x-AIMP');
define('SECURE_AUTH_KEY',  '12334567890aflgfhW*l0391548alkgsjR8pRRJ.%*AcWd[w;?QBW1|QulW[UvqW');
define('LOGGED_IN_KEY',    '123456adffshdgh>TfDCI0_/O@fQbhRx )G&L{Q_FTcm22Ojc+uME0VV~Q73CbST');
define('NONCE_KEY',        'i1234567890asdfsdfhgfsghlkj:kdke1h?hyj#Q,!qdt(7&(zEH=K>:}@xt{}3t');
define('AUTH_SALT',        '12345573908kjdfgjhfsglkjkajdfglb0!<n|Q^4Rzha {>T+95{}88Lo!..HT|+');
define('SECURE_AUTH_SALT', '12345askldgjfsdgwlV(u,?5F-(HO:Dsg$G6P7qRBq::Pt`LsCB:Rr> CE?N[`ZH');
define('LOGGED_IN_SALT',   '1234567890asdlfkhQD0g#Sk/>$$xph$yRy]8y)?QVhDQ[-|cib$;p7x_[M[#:t^');
define('NONCE_SALT',       '12345678laksdfjf)9oaoWN:]F6wX!$Xq()%gJIIjuz>Z(<)pJ6cogbThvkQU+. ');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
