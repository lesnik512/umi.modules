<?php
$INFO = Array();
$INFO['name'] = "meta";
$INFO['config'] = "1";
$INFO['default_method'] = "seo_template";
$INFO['default_method_admin'] = "seo_template";
$INFO['per_page'] = "10";

$COMPONENTS = array();
$COMPONENTS[] = "./classes/components/meta/admin.php";
$COMPONENTS[] = "./classes/components/meta/class.php";
$COMPONENTS[] = "./classes/components/meta/customAdmin.php";
$COMPONENTS[] = "./classes/components/meta/handlers.php";
$COMPONENTS[] = "./classes/components/meta/i18n.php";
$COMPONENTS[] = "./classes/components/meta/permissions.php";
$COMPONENTS[] = "./classes/components/meta/events.php";
?>