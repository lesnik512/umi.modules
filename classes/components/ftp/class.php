<?php
class ftp extends def_module {
    public $connection;
    public function __construct() {
        parent::__construct();
        if (cmsController::getInstance()->getCurrentMode() == "admin") {
            $commonTabs = $this->getCommonTabs();
            if ($commonTabs) {
                $commonTabs->add('config');
                if (cmsController::getInstance()->getCurrentMethod() != 'config') {
                    $this->redirect('/admin/ftp/config/');
                }
            }
            $this->__loadLib("admin.php");
            $this->__implement("FtpAdmin");

            $this->__loadLib("handlers.php");
            $this->__implement("FtpHandlers");

            $this->loadAdminExtension();

            $this->connection = $this->getConnection();
        }
    }

    private function getConnection() {
        $regEdit = regedit::getInstance();
        $host = (string) $regEdit->getVal("//modules/ftp/host");
        $login = (string) $regEdit->getVal("//modules/ftp/login");
        $password = (string) $regEdit->getVal("//modules/ftp/password");
        $port = (string) $regEdit->getVal("//modules/ftp/port");
        $directory = (string) $regEdit->getVal("//modules/ftp/directory");
        if (!$host)
            return null;
        $connection = ftp_connect($host,$port ? $port : 21);
        if (ftp_login($connection, $login, $password)) {
            if ($directory)
                ftp_chdir($connection, $directory);
            ftp_pasv($connection, true);
            return $connection;
        }
        return null;
    }

    private function basedir() {
        $con = $this->connection;
        if (!$con)
            return false;
        $directory = (string) regedit::getInstance()->getVal("//modules/ftp/directory");
        if ($directory)
            ftp_chdir($con, $directory);
        else
            ftp_chdir($con, '/');
        return null;
    }

    public function moveFile($src, $new_path = '', $new_name = '') {
        $con = $this->connection;
        if (!$con || !$src)
            return false;
        $fp = fopen($src, 'r');
        if (!$fp)
            return false;
        $this->ftp_mksubdirs($new_path ? : dirname($src));
        if ($result = ftp_fput($con, $new_name ? : basename($src), $fp, FTP_BINARY)) unlink($src);
        fclose($fp);
        $this->basedir();
        return $result;
    }

    public function removeFile($src) {
        $con = $this->connection;
        if (!$con || !$src)
            return false;
        return ftp_delete($con, $src);
    }

    public function renameFile($src, $new_src) {
        $con = $this->connection;
        if (!$con || !$src || !$new_src)
            return false;
        $this->ftp_mksubdirs(dirname($new_src));
        $this->basedir();
        return ftp_rename($con, $src, $new_src);
    }

    private function ftp_mksubdirs($src){
        $parts = explode('/',$src);
        $con = $this->connection;
        foreach($parts as $part){
            if(!ftp_chdir($con, $part)){
                ftp_mkdir($con, $part);
                ftp_chmod($con, 0777, $part);
                ftp_chdir($con, $part);
            }
        }
    }

    public function __destruct() {
        if ($this->connection)
            ftp_close($this->connection);
    }
}

;
?>
