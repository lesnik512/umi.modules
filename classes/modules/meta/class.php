<?php

	class meta extends def_module {

		public function __construct () {
			parent::__construct ();

            $iTypeId = regedit::getInstance()->getVal("//modules/meta/imported");
            if(!$iTypeId or $iTypeId == 0) $this->importType();

			$commonTabs = $this->getCommonTabs();
			if($commonTabs) {
				$commonTabs->add('seo_templates');
			}

			$this->loadCommonExtension();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
				$this->__loadLib("__admin.php");
				$this->__implement("__meta");

                $this->__loadLib("__events.php");
                $this->__implement("__meta_events");

				$this->__loadLib("__generate.php");
				$this->__implement("__meta_generate");

				$this->loadAdminExtension();

				$this->__loadLib("__custom_adm.php");
				$this->__implement("__meta_custom_admin");
			}

			$this->loadSiteExtension();

			$this->__loadLib("__custom.php");
			$this->__implement("__custom_meta");
		}

        public function importType() {
            $importer = new xmlImporter();
            $importer->loadXmlFile(CURRENT_WORKING_DIR . '/classes/modules/meta/umiDump.xml');
            //$importer->setUpdateIgnoreMode();
            $importer->setFilesSource(CURRENT_WORKING_DIR . '/classes/modules/meta/');
            $importer->execute();
            regedit::getInstance()->setVal("//modules/meta/imported",1);
            return true;
        }

		public function getObjectEditLink($objectId, $type = false) {
			return $this->pre_lang . "/admin/meta/edit/" . $objectId . "/";
		}

        public function value($page_id = false, $field_name = false, $lowercase = false, $obj_field_name = false) {
            if (!$page_id || !$field_name) return '';
            $oPage = umiHierarchy::getInstance()->getElement($page_id);
            $sResult = '';
            if ($oPage instanceof umiHierarchyElement) {
                $objects = umiObjectsCollection::getInstance();
                if ($field_name == 'name') $sResult = $oPage->getName();
                else $sResult = $oPage->getValue($field_name);
                if (is_array($sResult)) {
                    $aResult = $sResult;
                    $sResult = '';
                    $i = 0;
                    foreach($aResult as $mItem) {
                        $i++;
                        if (isset($mItem['rel'])) $sResult .= (($i !== 1) ? ', ' : '') . ($obj_field_name? $objects->getObject($mItem['rel'])->getValue($obj_field_name) : @$objects->getObject($mItem['rel'])->getName());
                    }
                }
                if (is_numeric($sResult)) {
                    $sTmp = @$objects->getObject($sResult)->getName();
                    $sTmp && $sResult = $sTmp;
                }
                $lowercase && $sResult = mb_strtolower($sResult);
            }
            return $sResult;
        }

        public function random() {
            $iNumberParams = func_num_args();
            if (!$iNumberParams) return '';
            return func_get_arg(rand(0,$iNumberParams - 1));
        }

        public function test($expr, $statement, $else = '') {
            if ($expr) return $statement ? $statement : $expr;
            else return $else;
        }

        public function objectsTypesList($iRootTypeId) {
            $items = array();
            $objectTypes = umiObjectTypesCollection::getInstance();

            foreach($objectTypes->getChildClasses($iRootTypeId) as $iTypeId) {
                $sTypeName = $objectTypes->getType($iTypeId)->getName();
                $items[] = def_module::parseTemplate("", array(
                    'attribute:id'		=> $iTypeId,
                    'node:title'		=> $sTypeName
                ));
            }

            return def_module::parseTemplate("", array(
                'subnodes:items'	=> $items
            ));
        }

	};

?>