<?php

	abstract class __meta_generate extends baseModuleAdmin {

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

			$generate_offset = (int) getSession("generate_offset_" . $id);
            $per_page = regedit::getInstance()->getVal("//modules/meta/per_page") ? regedit::getInstance()->getVal("//modules/meta/per_page") : 10;

            $aPages = new selector('pages');
			if ($oObjectType instanceof umiObjectType) $aPages->types('object-type')->id($oObjectType->getId());
            else $aPages->types('hierarchy-type')->id($oHierarchyType->getId());

            if (is_array($aCategories)) {
                foreach ($aCategories as $oCategory) {
                    $oCategory->getId() && $aPages->where('hierarchy')->page($oCategory->getId())->childs(100);
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

            $_SESSION["generate_offset_" . $id] = $generate_offset + $per_page;

			if ($bIsComplete) {
				unset($_SESSION["generate_offset_" . $id]);

                $importFinished = new umiEventPoint('metaOnGenerateFinish');
                $importFinished->setMode('after');
                $importFinished->addRef('template', $template);
                $importFinished->call();
			}

			$data = array(
				"attribute:complete" => (int) $bIsComplete,
				"attribute:generated" => $iGenerated,
				"nodes:log" => $sLog
			);

			$this->setData($data);
			return $this->doData();
		}

        public function onEditPageMeta(iUmiEventPoint $oEventPoint) {
            /** @var umiHierarchyElement $oPage */
            if ($oEventPoint->getMode() === "after"){
                $oPage = $oEventPoint->getRef("element");
                $iObjectTypeId = $oPage->getObjectTypeId();
                $iHierarchyTypeId = $oPage->getHierarchyType()->getId();
                $aParents = umiHierarchy::getInstance()->getAllParents($oPage->getId());

                $aPages = new selector('objects');
                $aPages->types('hierarchy-type')->name('meta','seo_template');
                $aPages->where('object_type_id')->equals($iObjectTypeId);

                if (!$aPages->length) {
                    $aPages = new selector('objects');
                    $aPages->types('hierarchy-type')->name('meta','seo_template');
                    $aPages->where('hierarchy_type_id')->equals($iHierarchyTypeId);
                }
                if (!$aPages->length) return true;

                foreach($aPages->result as $oTemplate) {
                    if (!$oTemplate->getValue('autogen')) continue;

                    $aCategories = $oTemplate->getValue('pages');
                    $bReady = false;
                    if (is_array($aCategories)) {
                        foreach ($aCategories as $oCategory) {
                            if (in_array($oCategory->getId(), $aParents)) {
                                $bReady = true;
                                break;
                            }
                        }
                    }
                    if (!$bReady) continue;

                    $aTemplate = $this->templateToArray($oTemplate);
                    $this->generatePage($oPage, $aTemplate, $oTemplate->getValue('force'));
                    return true;
                }
            }
            return true;
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
                        //if ($sFieldName == 'meta_keywords') $sValue = mb_strtolower($sValue);
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
	}
?>