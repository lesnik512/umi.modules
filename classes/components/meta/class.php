<?php
	class meta extends def_module {

		public function __construct () {
			parent::__construct ();

            $iTypeId = regedit::getInstance()->getVal("//modules/meta/imported");
            if(!$iTypeId or $iTypeId == 0) $this->importType();

			$this->loadCommonExtension();

			if(cmsController::getInstance()->getCurrentMode() == "admin") {
                $commonTabs = $this->getCommonTabs();
                if($commonTabs) {
                    $commonTabs->add('seo_template');
                    //$commonTabs->add('test');
                }

				$this->__loadLib("admin.php");
				$this->__implement("MetaAdmin");

				$this->loadAdminExtension();

                $this->__loadLib("customAdmin.php");
                $this->__implement("MetaCustomAdmin", true);

                $this->__loadLib("handlers.php");
                $this->__implement("MetaHandlers");
			}

			$this->loadSiteExtension();
		}

        public function importType() {
            $importer = new xmlImporter();
            $importer->loadXmlFile(CURRENT_WORKING_DIR . '/classes/components/meta/umiDump.xml');
            //$importer->setUpdateIgnoreMode();
            $importer->setFilesSource(CURRENT_WORKING_DIR . '/classes/components/meta/');
            $importer->execute();
            regedit::getInstance()->setVal("//modules/meta/imported", 1);
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

            foreach($objectTypes->getChildTypeIds($iRootTypeId) as $iTypeId) {
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

        public function generate_do() {
            if (is_demo()) {
                throw new publicAdminException(getLabel('label-stop-in-demo'));
            }
            $this->setDataType("list");
            $this->setActionType("view");

            $id = getRequest('param0');
            $objects = umiObjectsCollection::getInstance();

            $template = $objects->getObject($id);
            if (!$template instanceof umiObject) {
                throw new publicException(getLabel("meta-err-template_notfound"));
            }

            $aTemplate = $this->templateToArray($template);
            if (!count($aTemplate)) {
                throw new publicException(getLabel("meta-err-template_fields_notfound"));
            }

            $oObjectType = $template->getValue('object_type_id') ? umiObjectTypesCollection::getInstance()->getType($template->getValue('object_type_id')) : false;
            $oHierarchyType = $template->getValue('hierarchy_type_id') ? umiHierarchyTypesCollection::getInstance()->getType($template->getValue('hierarchy_type_id')) : false;
            if (!$oObjectType instanceof umiObjectType and !$oHierarchyType instanceof umiHierarchyType) {
                throw new publicException(getLabel("meta-err-type_notfound"));
            }
            $aCategories = $template->getValue('pages');

            //$generate_offset = (int) getSession("generate_offset_" . $id);
            $generate_offset = (int) session::getInstance()->getAndClose("generate_offset_" . $id);
            $per_page = regedit::getInstance()->getVal("//modules/meta/per_page") ? regedit::getInstance()->getVal("//modules/meta/per_page") : 10;

            $aPages = new selector('pages');
            if ($oObjectType instanceof umiObjectType) $aPages->types('object-type')->id($oObjectType->getId());
            else $aPages->types('hierarchy-type')->id($oHierarchyType->getId());

            if (is_array($aCategories)) {
                foreach ($aCategories as $oCategory) {
                    $oCategory->getId() && $aPages->where('hierarchy')->page($oCategory->getId())->level(100);
                }
            }

            $aPages->where('is_active')->equals(array(0,1));
            $aPages->order('id');
            $aPages->limit($generate_offset, $per_page); //offset,limit

            $bIsComplete = true;
            $iGenerated = 0;
            $sLog = '';

            foreach($aPages as $oPage) {
                $iGenerated++;
                $bIsComplete = false;
                $this->generatePage($oPage, $aTemplate, $template->getValue('force')) && $sLog .= 'Сгенерированы теги для страницы: ' . $oPage->getName() . "\n";
            }

            $progressKey = "generate_offset_" . $id;
            $progress = $generate_offset + $per_page;

            $session = session::getInstance();
            $session->set($progressKey, $progress);

            if ($bIsComplete) {
                $session->del($progressKey);

                $importFinished = new umiEventPoint('metaOnGenerateFinish');
                $importFinished->setMode('after');
                $importFinished->addRef('template', $template);
                $importFinished->call();
            }
            $session->commit();

            $data = array(
                "attribute:complete" => (int) $bIsComplete,
                "attribute:generated" => $iGenerated,
                "nodes:log" => $sLog
            );

            $this->setData($data);
            $this->doData();
        }

        public function templateToArray(umiObject $oTemplate) {
            $oFieldsCollection = umiFieldsCollection::getInstance();
            $aTemplate = array();
            foreach(array_merge($oTemplate->getPropGroupByName('common'), $oTemplate->getPropGroupByName('custom')) as $iFieldId) {
                $sFieldName = $oFieldsCollection->getField($iFieldId)->getName();
                if ($sFieldName && $oTemplate->getValue($sFieldName)) $aTemplate[$sFieldName] = trim($oTemplate->getValue($sFieldName));
            }
            return $aTemplate;
        }

        public function generatePage(umiHierarchyElement $oPage, $aTemplate, $bForceGenerate) {
            $aParents = umiHierarchy::getInstance()->getAllParents($oPage->getId());
            array_pop($aParents);
            $iParentId2 = array_pop($aParents);
            $iParentId3 = array_pop($aParents);
            foreach($aTemplate as $sFieldName => $sTemplate) {
                if (!$sTemplate) continue;
                $sFieldValue = @$oPage->getValue($sFieldName);
                if (!$sFieldValue || $bForceGenerate || ($sFieldName == 'h1' && $sFieldValue == $oPage->getName())) {
                    if ($sTemplate) {
                        $sTemplate = $this->synonymsParser($sTemplate);
                        $sValue = def_module::parseTPLMacroses($sTemplate, $oPage->getId(), false, array('parent_id' => $oPage->getParentId(), 'name' => $oPage->getName(), 'parent_id2' => $iParentId2, 'parent_id3' => $iParentId3));
                        @$oPage->setValue($sFieldName, $sValue);
                    }
                }
            }
            $oPage->commit();
            return true;
        }

        public function synonymsParser($sTemplate) {
            $i = 0;
            while (preg_match_all("/{([^{}]+)}/", $sTemplate, $matches)) {
                foreach ($matches[1] as $match) {
                    $replace = "{" . $match . "}";
                    $elements = explode('|', $match);
                    $sTemplate = str_replace($replace, $elements[array_rand($elements)],$sTemplate);
                }
                if ($i++ > 100) break;
            }
            return $sTemplate;
        }
	};

?>