<?php
class FtpHandlers {
    /**
     * @var menu $module
     */
    public $module;

    /**
     * Upload files to remote ftp-server in listening fields
     * @param UmiEventPoint $oEventPoint
     * @return bool
     */
    public function listenFields(UmiEventPoint $oEventPoint) {
        /**@var $element umiHierarchyElement*/
        if ($oEventPoint->getMode() != "after")
            return true;

        $element = $oEventPoint->getRef('element');
        $regEdit = regedit::getInstance();
        $fields = explode(',', $regEdit->getVal("//modules/ftp/fields"));
        foreach($fields as $field_name) {
            $value = $element->getValue($field_name);
            if (!$value instanceof umiFile && !$value instanceof umiImageFile)
                continue;
            $path = $value->getFilePath(true);
            if ($this->module->moveFile('.'.$path)) {
                $element->setValue($field_name,false);
                $element->setValue($field_name.'_ftp', $path);
            }
        }
        return true;
    }
}
?>