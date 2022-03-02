<?php
/**
 * Configurações do sistema
 */
defined("DEFAULT_CONTROLLER") || define("DEFAULT_CONTROLLER", "Home");
defined("DEFAULT_ACTION") || define("DEFAULT_ACTION", "Index");
defined("ROOT") || define("ROOT", $_SERVER['DOCUMENT_ROOT']);
defined("SITE_ROOT") || define("SITE_ROOT", ROOT.'/rooms');


defined("MAINTENANCE") || define("MAINTENANCE", false);
defined("ENVIRONMENT") || define("ENVIRONMENT", 'development');


/**
 * Confgurações do banco de dados
 */

defined("DB_SETTINGS") || define("DB_SETTINGS", array(
    "default" => array(
        "DB_HOST" => "localhost",
        "DB_NAME" => "db_rooms",
        "DB_USER" => "root",
        "DB_PASS" => ""
    )
));



/**
 * Site configs
 */
defined("SITE_URL") || define("SITE_URL", "http://localhost/rooms");
defined("MEDIA_URL") || define("MEDIA_URL", SITE_URL."/media");
