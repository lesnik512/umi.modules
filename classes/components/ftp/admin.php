<?php

/**
 * Класс функционала административной панели
 */
class FtpAdmin
{
    use baseModuleAdmin;
    /**
     * @var guides $module
     */
    public $module;

    public function config() {
        $regEdit = regedit::getInstance();

        $params = array(
            'config' => array(
                'string:host' => NULL,
                'string:login' => NULL,
                'string:password' => NULL,
                'int:port' => NULL,
                'string:directory' => NULL,
                'string:fields' => NULL
            )
        );

        $mode = getRequest("param0");

        if ($mode == "do") {
            $params = $this->expectParams($params);
            $regEdit->setVar("//modules/ftp/host", (string) $params['config']['string:host']);
            $regEdit->setVar("//modules/ftp/login", (string) $params['config']['string:login']);
            $regEdit->setVar("//modules/ftp/password", (string) $params['config']['string:password']);
            $regEdit->setVar("//modules/ftp/port", (int) $params['config']['int:port']);
            $regEdit->setVar("//modules/ftp/directory", (string) $params['config']['string:directory']);
            $regEdit->setVar("//modules/ftp/fields", (string) $params['config']['string:fields']);
            $this->chooseRedirect();
        }

        $params['config']['string:host'] = (string) $regEdit->getVal("//modules/ftp/host");
        $params['config']['string:login'] = (string) $regEdit->getVal("//modules/ftp/login");
        $params['config']['string:password'] = (string) $regEdit->getVal("//modules/ftp/password");
        $params['config']['int:port'] = (int) $regEdit->getVal("//modules/ftp/port");
        $params['config']['string:directory'] = (string) $regEdit->getVal("//modules/ftp/directory");
        $params['config']['string:fields'] = (string) $regEdit->getVal("//modules/ftp/fields");

        $this->setDataType("settings");
        $this->setActionType("modify");

        $data = $this->prepareData($params, "settings");

        $this->setData($data);
        $this->doData();
    }
}
?>