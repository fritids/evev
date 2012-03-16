<?php
class node {
    public function __construct() {
        $this->ci =& get_instance();
        $this->db = $this->ci->db;
        $this->tablename = 'node';
    }

    protected function pre_query() {
        $this->db->where('language', 'es');
    }

    public function load_node($id) {
        $this->pre_query();
        $this->db->where('id', $id);

        $n = $this->db->get($this->tablename)->row();

        $n->fields = $this->_load_fields($n);
        return new NodeResult($n);
    }

    public function objects($params=array()) {
        $this->pre_query();
        $types = !empty($params['type']) ? $params['type'] : false;
        $this->db->order_by('id', 'DESC');
        if (!empty($types)) {
            $this->db->where_in('type', $types);
        }

        $result = $this->db->get($this->tablename)->result();
        $output = array();
        foreach ($result as $item) $output[] = $this->load_node($item->id);
        return $output;
    }

    private function _load_fields($node) {
        $table = 'fields_'.$node->type;
        $this->db->where('node_id', $node->id);
        $fields = $this->db->get($table)->row();
        return $fields;
    }

    public function delete($id) {
        $node = node_load($id);
        $table = 'fields_'.$node->type;
        $this->db->where('id', $id);
        $this->db->delete($this->tablename);
        $this->db->where('node_id', $id);
        $this->db->delete($table);
    }
    
    public function node_save($params, $id=null) {
    	$node_fields = $this->db->list_fields($this->tablename);
    	$fields_table = 'fields_'.$params['type'];
    	$fields_fields = $this->db->list_fields($fields_table);
    	
    	if (!empty($id)) {
    		$this->db->where('id', $id);
    		$node_params = array();
    		foreach (array_keys($params) as $k) {
    			if (in_array($k, $node_fields)) $node_params[$k] = $params[$k];
    		}
    		unset($node_params['id']);
    		$this->db->update($this->tablename, $node_params);
    		
    		$field_params = array();
    		foreach (array_keys($params) as $k) {
    			if (in_array($k, $fields_fields)) $field_params[$k] = $params[$k];
    		}
    		$this->db->where('node_id', $id);
    		$this->db->update($fields_table, $field_params);
    	} else {
    		$node_params = array();
    		foreach (array_keys($params) as $k) {
    			if (in_array($k, $node_fields)) $node_params[$k] = $params[$k];
    		}
    		$this->db->insert($this->tablename, $node_params);
    		$node_id = $this->db->insert_id();
    		$field_params = array();
    		foreach (array_keys($params) as $k) {
    			if (in_array($k, $fields_fields)) $field_params[$k] = $params[$k];
    		}
    		$field_params['node_id'] = $node_id;
    		$this->db->insert($fields_table, $field_params);
    		
    	}
    }
}

class NodeResult {
    private $fields = array();
    public function __construct($node) {
        $this->fields['id'] = $node->id;
        $this->fields['title'] = $node->title;
        $this->fields['created'] = $node->created;
        $this->fields['updated'] = $node->updated;
        $this->fields['status'] = $node->status;
        $this->fields['language'] = $node->language;
        $this->fields['type'] = $node->type;

        foreach ($node->fields as $k => $v) $this->$k = $v;
    }

    public function __get($key) {
        if (!empty($this->fields[$key])) return $this->fields[$key];
    }

    public function __set($key, $value) {
        $this->fields[$key] = $value;
    }
}
 