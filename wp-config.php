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

define( 'DB_NAME', "bebestam_wp_best" );


/** MySQL database username */

define( 'DB_USER', "bebestam_Vanush" );


/** MySQL database password */

define( 'DB_PASSWORD', "ayqezban2019" );


/** MySQL hostname */

define( 'DB_HOST', "localhost" );


/** Database Charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8 -- UTF-8 Unicode' );


/** The Database Collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication Unique Keys and Salts.

 *

 * Change these to different unique phrases!

 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}

 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         'Ja<5[ZMyzL|a}?:xb@%et7W@ReC.xGDp6_a]Op8cu,70FwK!fC0e&EwFlUNxKj({' );

define( 'SECURE_AUTH_KEY',  '( NUd[E]<Qf*xQ*sS@?>~1nP0gRfi1Zn]l.F:BeM99uiRSI]]RG(8Ux%StJ|ExC*' );

define( 'LOGGED_IN_KEY',    '.rvuwEjTlntya(=w`g)8w4kF{kmxRFDj?%}$LPnc!zc<vElPN$$%6W[IGo}zS@jk' );

define( 'NONCE_KEY',        '5AN4U/,kNS.9#+KocFTm$ux<lc>tR|[)}fdCeZ341@/,{74#@16Xl3%Anp;K]]%8' );

define( 'AUTH_SALT',        '`v6?_/8xv`3P_f`~?(5KU6cLL6V$<Guam7Jz|?*u;R.s@@b KJ_2nCdt:_S?Es<2' );

define( 'SECURE_AUTH_SALT', '*4si}ilO=wON0{%%rZDgK07AjaU[[jZBE{^Xmfys=:]@@d4#Y/unZ6vi@{8(h$6*' );

define( 'LOGGED_IN_SALT',   '$7;xbqz#(1,X.d{szP{QnF%u.BVoBR!~1W603Tiqz6Kaq$hJ7;H<t>J;^lT/J%BY' );

define( 'NONCE_SALT',       'n@*/={.1*^T6EcvCa!wFscOY#u59 p%J]|-)!:O&^rS$.Buk4y(?3Tz!_?PV<Z-7' );


/**#@-*/


/**

 * WordPress Database Table prefix.

 *

 * You can have multiple installations in one database if you give each

 * a unique prefix. Only numbers, letters, and underscores please!

 */

$table_prefix = 'wp_';


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

define( 'WP_DEBUG', true );


/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

}


/** Sets up WordPress vars and included files. */

require_once( ABSPATH . 'wp-settings.php' );



// define('FORCE_SSL_ADMIN', true);

// if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')

//  $_SERVER['HTTPS']='on';