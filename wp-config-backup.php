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

# Switched site to be https:// and this forces the wp-admin to go throught that.
define('FORCE_SSL_ADMIN', true);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

/** MySQL database username */

/** MySQL database password */

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

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
define('FS_METHOD', 'direct');
define('AUTH_KEY',         ']@]8Fr~d]v<2jY>e4_mHZ15S uVmu#1p$Ikc}3x?JGM?CJ<-=<6xi iAT)>+MV+D');
define('SECURE_AUTH_KEY',  't-/[<4d(FTlgT=BDzDe;Sz#}+5Ofjj<*GBGzj9bW|N+;(^B_:-Ct-FYIJ_-A;T?a');
define('LOGGED_IN_KEY',    'FzZS ?J3E0`RQw@;KU2q4hU#X9#/c?72vT^:_1f@7hMGHcx.q:yj<L^*%a[@)sl_');
define('NONCE_KEY',        '0]+v>u&&[QcB|o]`$>Y8IB+R&J1gKIJ?Dyf+YagJmz[8<]#,?bv&/DFkp>f7>Qp}');
define('AUTH_SALT',        'X:+inpsDy+qVM,2~~9G+Hxw1b<UBgkUzdf`-P<[(r^[yIY9dNKWe/Ypp}[%O]@6M');
define('SECURE_AUTH_SALT', 'DvTan35 O`kezLiE}2k@kQn-9_#++5tqe&)vKb6{-.FXc8TeC9Y9+eOVMYrFV]k6');
define('LOGGED_IN_SALT',   'X)l-/Klb:hBD,nlq*Ul/53+9p~cM=}/%&3o-v},GbTo)dbTs@KGv 6/Uq-^R+r-0');
define('NONCE_SALT',       '-yIUmI8-tRv{!nz26Z)lRNM?($gh=#4+z;}{$+E1MPVs|94^v|9M/N%R<z_iZCRD');
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', '34k5jh32kj');

/* Original, compromised info (accidentally pushed ot github) */
/*define('AUTH_KEY',         '8u0Bq(K05Y6gR( O+biQ(66?L$;#wG2Cmya0X?EO-`E]C)K0lZa@S~9b_.h|}404');
define('SECURE_AUTH_KEY',  'R4>sKdhp}096jHq$VBilz4)?|Q,z+U.j58nN7JSgP7vnl29`kIM-3z:Q>wq1hT~c');
define('LOGGED_IN_KEY',    '`Ef< QRS68G^XO}p|JHa*! kK]h.? .zebCoz]`+FO:D:$D/OsrZru%+)>h9865h');
define('NONCE_KEY',        ',kC:| C1|50$Vb)jSo?Hu+=2E{60&NGV>uejZ~y@|v$+$iG+7^|_+>aiz?mtl=eD');
define('AUTH_SALT',        ':cYkl{@t6Jw3qD(]G VX3rRvMu|c:mn~+3sEOvB,>V1G!6VD|G=uO.OX/1kAi<|A');
define('SECURE_AUTH_SALT', 'Q_x|$:r.{-.-%w BqF:iv<*JeXh[-6unA@/-57Rm9|w}HzwCx86^&).D,/JGa)EK');
define('LOGGED_IN_SALT',   'y}x3SSG.s-BnX6{jW+x&F~6zO0|f`;DAplq~-;S,YD=)3DwVK1#*$ZkTO|mV~S*R');
define('NONCE_SALT',       'LhL%W7X(:&L<B5@AA5:G<yTIKqrkAv#I_bqP=3u+n*:FV1E+zIH-#73t_^WiR$mN');
define('DB_NAME', 'wordpress');
define('DB_USER', 'wordpress');
define('DB_PASSWORD', '4rzPPLb172');
*/
require_once(ABSPATH . 'wp-settings.php');
