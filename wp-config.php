<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define( 'DB_NAME', 'Homy' );

/** Utilisateur de la base de données MySQL. */
define( 'DB_USER', 'laye' );

/** Mot de passe de la base de données MySQL. */
define( 'DB_PASSWORD', 'laye2019@' );

/** Adresse de l’hébergement MySQL. */
define( 'DB_HOST', 'localhost' );

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'kqmQBK]+g2Pk?HH.2[fwn$rK3T9N!4fi[//fNtrO+1.3]cleg0?65up izoWkc$E' );
define( 'SECURE_AUTH_KEY',  '>|6~(^:qscHxK|3 ied#:2K7Z=h*RWILLB *:~`D[#V7>X09Iqs )vca0WITf,|g' );
define( 'LOGGED_IN_KEY',    'R)QfphCE3wOXk9BQIT`Wc+P.OBO,b<d{qGEW]E.p-jZF$)dwFXaJRuZDZg?cN}os' );
define( 'NONCE_KEY',        'g|=[zdTH8C~CxG|+FA{#r8J6S}P4?fJ%lvN=(5{Q_vUnJ6yoz-@26mI)f@y$_,VE' );
define( 'AUTH_SALT',        ';:fU_4!ud^@/f$cU<}<1d=#B<[3p:J(eB|+LZCzRPJPXrOKT4q>)0S>0tu+6t*e2' );
define( 'SECURE_AUTH_SALT', 'n~nnGuxEQWzS4.ZFk^l=S7,<j{4{+B.%3ooVcH+!A70[r6Lt)mdNHBSJ^G1:k??9' );
define( 'LOGGED_IN_SALT',   ')w/UT{VkRGrfuDTB*O<0ddY(hext[(DP#JAil`r]L6oz2$*5xoDB^gT6@ Ada3vL' );
define( 'NONCE_SALT',       'mPP<zU@tXOIU9dG:[(u#!}d#~OyCjUSE&3iW`>a3F|cJm8.qGm,E.GLVeZ6,&Q#A' );
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d’information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* C’est tout, ne touchez pas à ce qui suit ! Bonne publication. */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
define('FS_METHOD', 'direct');

