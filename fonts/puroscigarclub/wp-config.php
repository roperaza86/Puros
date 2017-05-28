<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'puroscigarclub');

/** MySQL database username */
define('DB_USER', 'puros_db');

/** MySQL database password */
define('DB_PASSWORD', 'Puros_2015!');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FORCE_SSL_ADMIN', true);

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'os~2LC5Oup>X=HiR1/[%zG GD>O|GgoDFM8}vE6>5K+b@UI1_aIk66[7~-(@y@I_');
define('SECURE_AUTH_KEY',  'ds-X$8Ng_gL1bEk%Ys&(uKt:cN=dZ$z;Dop?@Y_,8[h6T{Ula(o<fr|yO>WITx/Z');
define('LOGGED_IN_KEY',    '3s5DrR*pt$DrH1USP=VR xk/Em)#~62MG7;Z8IF5;`V#_S8?o{9,:Kymu*Hx!7tZ');
define('NONCE_KEY',        'k6r^sk547w3?pGaizNMid3-ZXTLQzW)dNF5M>bQeF]q&Fn}X#=h,Zq*STn`A8gu-');
define('AUTH_SALT',        'l9H|-pg&vPZ}`z/3y;?}@L[bs/&q:QsS[A(R_B+xcClME_rx+#;3npFXLB:`!9LN');
define('SECURE_AUTH_SALT', 'C!pda9>]%6ea@^A,9Ihv(wM.Wiu)CwqdIk:bAQ`%yb0=Py<LfkHizNW!s>7%%#5a');
define('LOGGED_IN_SALT',   '.Dofh*ejk(owdK))bF|qokO&n.k*Ik>+ZyfvD&},7/7Y9}P33J1w$tx1)o:r+E!h');
define('NONCE_SALT',       '?aO*;6Z0A!3]d&EW2CB:e-adr(n/vV%gfONO<9OMUb5/xVZp>;~.x6wIxQiP<vu_');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

define( 'WP_MEMORY_LIMIT', '96M' );

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
