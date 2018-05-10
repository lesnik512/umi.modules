<?php
/**
 * Класс функционала административной панели
 */
class MetaAdmin {

	use baseModuleAdmin;
	/**
	 * @var guides $module
	 */
	public $module;

    public function config() {
        $regedit = regedit::getInstance();
        $params = array('config' => array('int:per_page' => NULL));
        $mode = getRequest("param0");

        if($mode == "do") {
            $params = $this->expectParams($params);
            $regedit->setVar("//modules/meta/per_page", (int) $params['config']['int:per_page']);
            $this->chooseRedirect();
        }

        $params['config']['int:per_page'] = (int) $regedit->getVal("//modules/meta/per_page");

        $this->setDataType("settings");
        $this->setActionType("modify");

        $data = $this->prepareData($params, "settings");

        $this->setData($data);
        $this->doData();
    }

    public function seo_template() {
        $this->setDataType("list");
        $this->setActionType("view");

        $limit = getRequest('per_page_limit');
        $curr_page = (int) getRequest('p');
        $offset = $limit * $curr_page;

        $sel = new selector('objects');
        $sel->types('hierarchy-type')->name('meta', 'seo_template');
        $sel->limit($offset, $limit);

        selectorHelper::detectFilters($sel);

        $this->setDataRange($limit, $offset);
        $data = $this->prepareData($sel->result, "objects");

        $this->setData($data, $sel->length);
        $this->doData();
    }

    public function add() {
        $mode = (string) getRequest('param0');
        $type = 'seo_template';

        $this->setHeaderLabel("header-seo-template-add");

        $inputData = array(
            'type'					=> $type,
            'allowed-element-types'	=> array($type),
        );

        if($mode == "do") {
            $object = $this->saveAddedObjectData($inputData);
            $this->chooseRedirect($this->pre_lang . '/admin/meta/edit/' . $object->getId() . '/');
        }

        $this->setDataType("form");
        $this->setActionType("create");

        $data = $this->prepareData($inputData, "object");

        $this->setData($data);
        $this->doData();
    }

    public function edit() {
        $object = $this->expectObject("param0", true);
        $mode = (string) getRequest('param1');
        $objectId = $object->getId();

        $this->setHeaderLabel("header-seo-template-edit");

        if($mode == "do") {
            $object = $this->saveEditedObjectData($object);
            $this->chooseRedirect();
        }

        $this->setDataType("form");
        $this->setActionType("modify");

        $data = $this->prepareData($object, "object");

        $this->setData($data);
        $this->doData();
    }

    public function del() {
        $objects = getRequest('element');
        if(!is_array($objects)) {
            $objects = Array($objects);
        }

        foreach($objects as $objectId) {
            $object = $this->expectObject($objectId, false, true);

            $params = Array(
                'object'		=> $object,
                //'allowed-element-types' => Array('template')
            );

            $this->deleteObject($params);
        }

        $this->setDataType("list");
        $this->setActionType("view");
        $data = $this->prepareData($objects, "objects");
        $this->setData($data);

        $this->doData();
    }

    public function getDatasetConfiguration($param = '') {
        $typeId	= umiObjectTypesCollection::getInstance()->getBaseType('meta', 'seo_template');

        return array(
            'methods' => array(
                array('title'=>getLabel('smc-load'), 'forload'=>true, 'module'=>'meta', '#__name'=>'seo_template'),
                array('title'=>getLabel('smc-delete'), 				  'module'=>'meta', '#__name'=>'del', 'aliases' => 'tree_delete_element,delete,del')
            ),
            'types' => array(
                array('common' => 'true', 'id' => $typeId)
            ),
            'stoplist' => array(),
            'default' => ''
        );
    }
};
?>
