<?php
	class admin_robots {

        public function __construct($module) {
            $this->module = $module;
            if(cmsController::getInstance()->getCurrentMode() != 'admin')
                return;

            $commonTabs = $this->module->getCommonTabs();

            if($commonTabs) {
                $commonTabs->add('robots');
            }
        }

        public function robots() {
            $this->module->setDataType("settings");
            $this->module->setActionType("modify");

            $domainId = getRequest('domain_id');

            if(!$domainId) {
                $domainId = cmsController::getInstance()->getCurrentDomain()->getId();
            }

            $domainId = intval($domainId);

            $file = sprintf('%s/robots/%d.robots.txt', CURRENT_WORKING_DIR, $domainId);

            $params = Array(
                "robots" => array(
                    "text:robots-content" => ''
                )
            );

            $mode = (string) getRequest('param0');

            if($mode == 'do') {
                $value = trim(getRequest('robots-content'));

                if($value != '') {
                    if(!file_exists($file)) {
                        $dir = sprintf('%s/robots/', CURRENT_WORKING_DIR);

                        if(!is_dir($dir))
                            mkdir($dir);
                    }

                    $fp = fopen($file, 'w+');
                    if($fp) {
                        fputs($fp, $value);
                        fclose($fp);
                    }
                } else {
                    if(file_exists($file))
                        unlink($file);
                }

                $this->module->chooseRedirect();
            }

            $content = '';
            if(file_exists($file)) {
                $content = file_get_contents($file);
            }

            $params['robots']['text:robots-content'] = $content;

            $data = $this->module->prepareData($params, 'settings');
            $this->module->setData($data);
            return $this->module->doData();
        }
	}
?>
