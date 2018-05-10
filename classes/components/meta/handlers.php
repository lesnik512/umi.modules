<?php
	/**
	 * Класс обработчиков событий
	 */
	class MetaHandlers {
		/**
		 * @var news $module
		 */
		public $module;

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

					$aTemplate = $this->module->templateToArray($oTemplate);
					$this->module->generatePage($oPage, $aTemplate, $oTemplate->getValue('force'));
					return true;
				}
			}
			return true;
		}
	}
?>