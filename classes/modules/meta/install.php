<?php

    $INFO = Array();

    $INFO['name'] = "meta";
    $INFO['filename'] = "modules/meta/class.php";
    $INFO['config'] = "1";
    $INFO['ico'] = "meta";
    $INFO['default_method'] = "seo_template";
    $INFO['default_method_admin'] = "seo_template";
    $INFO['per_page'] = "10";
    $INFO['imported'] = 0;

    $INFO['func_perms'] = "";
    $INFO['func_perms/main'] = "Управление шаблонами и генерация";
    $INFO['func_perms/macroses'] = "Дополнительные макросы для шаблонов";


    $COMPONENTS = array();

    $COMPONENTS[0] = "./classes/modules/meta/__admin.php";
	$COMPONENTS[2] = "./classes/modules/meta/__custom.php";
	$COMPONENTS[3] = "./classes/modules/meta/__generate.php";
	$COMPONENTS[5] = "./classes/modules/meta/class.php";
	$COMPONENTS[6] = "./classes/modules/meta/i18n.en.php";
	$COMPONENTS[7] = "./classes/modules/meta/i18n.php";
	$COMPONENTS[8] = "./classes/modules/meta/permissions.php";
    $COMPONENTS[9] = "./classes/modules/meta/events.php";
?>