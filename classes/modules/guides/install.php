<?php

$INFO = Array();


$INFO['name'] = "guides";
$INFO['filename'] = "modules/guides/class.php";
$INFO['config'] = "1";
$INFO['ico'] = "ico_guides";
$INFO['default_method_admin'] = "lists";
$INFO['is_indexed'] = "1";
$INFO['list'] = "";


$COMPONENTS = array();

$COMPONENTS[0] = "./classes/modules/guides/__admin.php";
$COMPONENTS[1] = "./classes/modules/guides/class.php";
$COMPONENTS[2] = "./classes/modules/guides/i18n.en.php";
$COMPONENTS[3] = "./classes/modules/guides/i18n.php";
$COMPONENTS[4] = "./classes/modules/guides/lang.php";
$COMPONENTS[5] = "./classes/modules/guides/permissions.php";

?>