<?php
	abstract class __guides extends baseModuleAdmin {

		public function config() {
			$regedit = regedit::getInstance();
			$params = array();
			$mode = getRequest("param0");
            $active_guides = $this->list_guid;
			if($mode == "do") {
                if(sizeof($new_types = getRequest('type'))){
                    $res_arr = array();
                    foreach($new_types as $id => $v){
                        $res_arr[] = $id;
                    }
                    $this->list_guid = $res_arr;
                    $regedit->setVar("//modules/guides/list", implode(',',$res_arr));
                } else{
                    $this->list_guid = array();
                    $regedit->setVar("//modules/guides/list",'');
                }
				$this->chooseRedirect($this->pre_lang . '/admin/guides/lists/' );
			}
            $types = umiObjectTypesCollection::getInstance();
            $guides = $types->getGuidesList();
            foreach($guides as $guid_id => $guid_name){
                $val = (in_array($guid_id,$active_guides)) ? '1' : '0';
                $params['config']['boolean:type'.$guid_id] = array(
                    'id' => $guid_id,
                    'name' => $guid_name,
                    'value' => $val
                );
            }

			$this->setDataType("settings");
			$this->setActionType("modify");

			$data = $this->prepareData($params, "settings");

			$this->setData($data);
			return $this->doData();
		}


		public function lists() {
            $this->setDataType("list");
            $this->setActionType("view");
            $type = getRequest('object_type');
            if(is_array($type)) $type = $type[0];
            if(!$type) $type = $this->list_guid[0];
            if(!$type){
                $this->chooseRedirect($this->pre_lang . '/admin/guides/config/' );
            }
            $guide = selector::get('object-type')->id($type);
			if ($guide) $this->setHeaderLabel(getLabel('header-guides-lists') . ' "' . $guide->getName() . '"');
            if($this->ifNotXmlMode()) return $this->doData();

            $limit = getRequest('per_page_limit');
            $curr_page = getRequest('p');
            $offset = $limit * $curr_page;
            $sel = new selector('objects');
            $sel->types('object-type')->id($type);
            $sel->limit($offset, $limit);
            selectorHelper::detectFilters($sel);
            if(getRequest('viewMode') == 'full') {
                $groups = $guide->getFieldsGroupsList();
            }
            $data = array();
            //$objects = umiObjectsCollection::getInstance();
            foreach($sel->result as $object){
                $line_arr = array();
                $line_arr['@id'] = $object->id;
                $line_arr['@type-id'] = $object->getTypeId();
                $line_arr['@name'] = $object->getName();
                $line_arr['edit-link'] = $this->getObjectEditLink($object->id);
                if(getRequest('viewMode') == 'full') {
                    foreach($groups as $group){
                        $group_arr = array();
                        $group_arr['@name'] = $group->getName();
                        foreach($group->getFields() as $field){
                            $group_arr['nodes:property'][] = $object->getPropById($field->getId());
                        }
                        $line_arr['properties']['nodes:group'][] = $group_arr;
                    }
                }
                $data['nodes:page'][] = $line_arr;

            }

            $this->setDataRange($limit, $offset);
            //$data = $this->prepareData($sel->result, "objects");
            $this->setData($data, $sel->length);
            return $this->doData();
		}

		public function add() {
            $type = (string) getRequest("param0");
            $mode = (string) getRequest("param1");
            //Подготавливаем список параметров
            $inputData = Array('type-id' => $type);

            $guide = selector::get('object-type')->id($type);
            $this->setHeaderLabel(getLabel('header-guides-add') . ' "' . $guide->getName() . '"');

            if($mode == "do") {
                $object = $this->saveAddedObjectData($inputData);
                $this->chooseRedirect($this->pre_lang . '/admin/guides/edit/' . $object->getId() . '/');
            }

            $this->setDataType("form");
            $this->setActionType("create");

            $data = $this->prepareData($inputData, "object");

            $this->setData($data);
            return $this->doData();
		}


		public function edit() {
            $object = $this->expectObject("param0");
            $mode = (string) getRequest('param1');

            $this->setHeaderLabel(getLabel("header-guides-edit") . ' "' . $object->getType()->getName(). '"');

            if($mode == "do") {
                $this->saveEditedObjectData($object);
                $this->chooseRedirect();
            }

            $this->setDataType("form");
            $this->setActionType("modify");

            $data = $this->prepareData($object, "object");

            $this->setData($data);
            return $this->doData();
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
                    /*'allowed-element-types' => Array('banner', 'place')*/
                );

                $this->deleteObject($params);
            }
            $this->setDataType("list");
            $this->setActionType("view");
            $data = $this->prepareData($objects, "objects");
            $this->setData($data);

            return $this->doData();
		}


        public function getDatasetConfiguration($param = '') {
            return array(
                'methods' => array(
                    array('title'=>getLabel('smc-load'), 'forload'=>true, 'module'=>'guides', '#__name'=>'lists'),
                    array('title'=>getLabel('smc-delete'), 'module'=>'guides', '#__name'=>'del', 'aliases' => 'tree_delete_element,delete,del'),
                    array('title'=>getLabel('smc-activity'), 'module'=>'guides', '#__name'=>'activity', 'aliases' => 'tree_set_activity,activity')),
                'types' => array(
                    array('common' => 'true', 'id' => $param)
                ),
                'stoplist' => array(),
                'default' => ''
            );

        }
	};
?>