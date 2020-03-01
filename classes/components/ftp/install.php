<?php
/**
 * Установщик модуля
 */

/**
 * @var array $INFO реестр модуля
 */
$INFO = array();
$INFO['name'] = "ftp";
$INFO['filename'] = "modules/ftp/class.php";
$INFO['config'] = "1";
$INFO['default_method'] = "check";
$INFO['default_method_admin'] = "check";

$INFO['func_perms'] = "";
$INFO['func_perms/admin'] = "Администрирование модуля";

$COMPONENTS = array();
$COMPONENTS[] = "./classes/components/ftp/class.php";
$COMPONENTS[] = "./classes/components/ftp/admin.php";
$COMPONENTS[] = "./classes/components/ftp/events.php";
$COMPONENTS[] = "./classes/components/ftp/handlers.php";
$COMPONENTS[] = "./classes/components/ftp/lang.php";
$COMPONENTS[] = "./classes/components/ftp/i18n.php";
$COMPONENTS[] = "./classes/components/ftp/permissions.php";
?>