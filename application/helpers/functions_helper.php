<?php

function get_block($template){
	$ci =& get_instance();
	$ci->load->view($template);
}

function save($table, $data, $where = '')
{
	$CI = & get_instance();
	$CI->load->database();

	if (empty($where)) {
		$SQL = $CI->db->insert_string($table, $data);

		if($CI->db->query($SQL))
			return $CI->db->insert_id();
		else
			return false;
	} else {
		$SQL = $CI->db->update_string($table, $data, $where);
		if($CI->db->query($SQL))
			return true;
		else
			return false;
	}
}

function array2object($array)
{
	$object = new stdClass();
	foreach ($array as $key => $value) {
		$object->$key = $value;
	}
	return $object;
}

function getVar($name, $xss_clean = TRUE, $escape_sql = TRUE)
{
	$CI = & get_instance();
	if ($escape_sql) {
		return $CI->db->escape_str($CI->input->get_post($name, $xss_clean));
	} else {
		return $CI->input->get_post($name, $xss_clean);
	}
}

?>
