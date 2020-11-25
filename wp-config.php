<?php 
define( 'DB_NAME', 'wordpress');
define( 'DB_USER', 'wordpress');
define( 'DB_PASSWORD', 'somethingstupid');
define( 'DB_HOST', 'localhost');
define( 'DB_CHARSET',  'utf8mb4' );

define( 'ALLOW_UNFILTERED_UPLOADS' , true);

$table_prefix = 'wp_';
if ( !defined('ABSPATH') )
        define('ABSPATH', dirname(__FILE__) . '/');
require_once(ABSPATH . 'wp-settings.php');
