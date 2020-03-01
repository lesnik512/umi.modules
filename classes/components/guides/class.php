<?php

class guides extends def_module {
	public $list_guid = array(),$active_type = false;

	public function __construct() {
		parent::__construct();
        $list_guid = regedit::getInstance()->getVal("//modules/guides/list");
        $this->list_guid = explode(',',$list_guid);
		if(cmsController::getInstance()->getCurrentMode() == "admin") {

			$configTabs = $this->getConfigTabs();
			if ($configTabs) {
				$configTabs->add("config");
			}
			$this->__loadLib("admin.php");
			$this->__implement("GuidesAdmin");
            $active_type = getRequest('object_type');
            if(!$active_type) $active_type = $this->list_guid[0];
            $this->active_type = $active_type;

		}

	}

    public function getTypesList($active_type = false) {
        $types = umiObjectTypesCollection::getInstance();
        $result = array();
        if(!$active_type) $active_type = $this->list_guid[0];
        foreach($this->list_guid as $type_id){
            $line_arr = array();
            $type = $types->getType($type_id);
			if ($type) {
				$line_arr["@id"] = $type->getId();
				$line_arr["@title"] = $type->getName();
				if($type_id == $active_type){
					$line_arr["@active"] = 'active';
				}
				$result[] = $line_arr;
			}
        }
        return array('+type' => $result);
    }

    public function getGuideItems($type_id = false,$extend_properties = '',$extend_groups = '') {
        if(!$type_id) return false;
        $sel = new selector('objects');
        $sel->types('object-type')->id($type_id);
        $objects = array();
        $extend_properties = explode(',',$extend_properties);
        $extend_groups = explode(',',$extend_groups);
        foreach ($sel->result() as $object) {
            if ($object instanceof umiObject) {
                if (count($extend_properties) || count($extend_groups)) {
                    $data = translatorWrapper::get($object)->translate($object);
                    $data['extended'] = array();
                    if (count($extend_properties)) {
                        $data['extended']['properties'] = array();
                        foreach ($extend_properties as $extendedPropery) {
                            $property = $object->getPropByName($extendedPropery);
                            if (!$property instanceof umiObjectProperty) continue;
                            $data['extended']['properties']['nodes:property'][] = translatorWrapper::get($property)->translate($property);
                        }
                    }
                    if (count($extend_groups)) {
                        $data['extended']['groups'] = array();
                        $data['extended']['groups']['nodes:group'] = array();
                        $objectType = $object->getType();
                        foreach ($extend_groups as $extendedGroup) {
                            $group = $objectType->getFieldsGroupByName($extendedGroup);
                            if (!$group instanceof umiFieldsGroup) continue;
                            $data['extended']['groups']['nodes:group'][] = translatorWrapper::get($group)->translateProperties($group, $object);
                        }
                    }
                } else {
                    $data = $object;
                }
                $objects[] = $data;
            }
        }
        return array('+item' => $objects);
    }

    public function getObjectEditLink($objectId, $type = false) {
        return $this->pre_lang . "/admin/guides/edit/" . $objectId . "/";
    }
};
?>
