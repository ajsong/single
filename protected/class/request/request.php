<?php
class Request {
	public function __construct() {
		//
	}
	
	public function request($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'request');
	}
	
	public function get($name, $default='', $type='string') {
		return $this->act($name, $default, $type);
	}
	
	public function post($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'post');
	}
	
	public function cookie($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'cookie');
	}
	
	public function server($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'server');
	}
	
	public function session($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'session');
	}
	
	public function header($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'header');
	}
	
	public function put($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'put');
	}
	
	public function delete($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'delete');
	}
	
	public function param($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'param');
	}
	
	public function path($name, $default='', $type='string') {
		return $this->act($name, $default, $type, 'path');
	}
	
	public function file($mydir, $name, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
		return $this->act($name, $upload_switch, $mydir, 'file', $return_detail, $file_type, $file_name);
	}
	
	public function origin($name, $default='') {
		return $this->act($name, $default, 'origin', 'post');
	}
	
	public function object($name, $object, $default='') {
		return $this->act($name, $default, 'string', $object);
	}

	public function act($name, $default='', $type='string', $method='get', $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
		$originField = false;
		$originName = $name;
		if (substr($name, 0, 1) == ':') { //:name为空值即自动获取origin_{name}值
			$originField = true;
			$name = substr($name, 1);
			$originName = $name;
		}
		if (preg_match('/^([\d.]+)!(\w+)$/', $name)) { //0.5!name图片宽高50%压缩
			preg_match('/^([\d.]+)!(\w+)$/', $name, $matcher);
			$name = $matcher[2];
		}
		if (preg_match('/^(\d+)(\|{1,2})(\w+)$/', $name)) { //3|name图片平均分割3份, 300||name图片按300高度分割
			preg_match('/^(\d+)(\|{1,2})(\w+)$/', $name, $matcher);
			$name = $matcher[3];
		}
		$data = array();
		if (is_string($method)) {
			switch (strtoupper($method)) {
				case 'REQUEST':
					$data = $_REQUEST;
					break;
				case 'GET':
					$data = $_GET;
					break;
				case 'POST':
					$data = $_POST;
					break;
				case 'COOKIE':
					$data = $_COOKIE;
					break;
				case 'SERVER':
					$data = $_SERVER;
					break;
				case 'SESSION':
					$data = $_SESSION;
					break;
				case 'HEADER':
					$data = function_exists('get_header') ? get_header() : array();
					break;
				case 'GLOBALS':
					$data = $GLOBALS;
					break;
				case 'PUT':
				case 'DELETE':
					parse_str(file_get_contents('php://input'), $data);
					break;
				case 'PARAM':
					switch (strtoupper($_SERVER['REQUEST_METHOD'])) {
						case 'POST':
							$data = $_POST;
							break;
						case 'PUT':
						case 'DELETE':
							parse_str(file_get_contents('php://input'), $data);
							break;
						default:
							$data = $_GET;
							break;
					}
					break;
				case 'PATH':
					$data = array();
					if (isset($_SERVER['PATH_INFO']) && !empty($_SERVER['PATH_INFO'])) {
						$depr = '/';
						$data = explode($depr, trim($_SERVER['PATH_INFO'], $depr));
					}
					break;
				case 'FILE':
					if (is_string($default) && !strlen($default)) $default = 0;
					$result = function_exists('upload_file') ? upload_file($type, $originName, $default, $return_detail, $file_type, $file_name) : '';
					$data = array("{$name}" => $result);
					if ($return_detail || is_array($result)) $type = 'array';
					$default = '';
					break;
			}
		} else {
			$data = $method;
		}
		if (isset($data[$name]) && is_array($data[$name])) $type = 'array';
		if (is_string($type) && (!strlen($type) || $type == 'string')) {
			if (is_int($default) || is_integer($default)) $type = 'int';
			if (is_float($default) || is_double($default)) $type = 'float';
			if (is_array($default)) $type = 'array';
		}
		$numericRange = array();
		if (is_array($type)) { //数组或范围内数值, ex.: request('param', 0, [1, 10])
			if (count($type) == 2 && (is_int($default) || is_integer($default))) {
				$numericRange = $type;
				$type = 'int';
			} else if (count($type) == 2 && (is_float($default) || is_double($default))) {
				$numericRange = $type;
				$type = 'float';
			} else {
				$type = 'array';
			}
		}
		switch (strtolower($type)) {
			case 'int':
			case 'integer':
			case '0':
				if ($default == '') $default = 0;
				$value = isset($data[$name]) ? intval($data[$name]) : $default;
				if (count($numericRange) == 2) {
					$min = $value >= $numericRange[0];
					$max = $value <= $numericRange[1];
					if (!$min || !$max) $value = $default;
				}
				break;
			case 'float':
			case 'double':
			case 'decimal':
			case '0.0':
				if ($default == '') $default = 0;
				$value = isset($data[$name]) ? floatval($data[$name]) : $default;
				if (count($numericRange) == 2) {
					$min = $value >= $numericRange[0];
					$max = $value <= $numericRange[1];
					if (!$min || !$max) $value = $default;
				}
				break;
			case 'url': //url解码
			case 'urldecode':
				$value = (isset($data[$name]) && strlen(trim($data[$name]))) ? trim(urldecode($data[$name])) : $default;
				break;
			case 'slash': //去斜杠
			case 'xg':
			case '\\':
				$value = (isset($data[$name]) && strlen(trim($data[$name]))) ? stripslashes(trim($data[$name])) : $default;
				break;
			case 'origin': //原样返回
			case '?':
				$value = isset($data[$name]) ? $data[$name] : $default;
				break;
			case 'array': //转数组
			case '[]':
				$value = isset($data[$name]) ? $data[$name] : $default;
				if (is_string($value)) $value = json_decode(stripslashes(trim($value)), true);
				$value = $value ? json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), true) : $default;
				break;
			case 'json': //转json对象
			case 'object':
			case '{}':
			case 'json[]':
			case 'object[]':
			case '{}[]':
				$assoc = false;
				if (strpos($type, '[]') !== false) $assoc = true;
				$value = isset($data[$name]) ? $data[$name] : $default;
				if (is_string($value)) $value = json_decode(stripslashes(trim($value)), true);
				$value = $value ? json_decode(json_encode($value, JSON_UNESCAPED_UNICODE), $assoc) : $default;
				break;
			default:
				$value = (isset($data[$name]) && strlen(trim($data[$name]))) ? strval(trim($data[$name])) : '';
				//$value = str_replace("_", "\_", $value); //过滤_
				//$value = str_replace("%", "\%", $value); //过滤%
				//$value = nl2br($value); //回车转换
				//PHP直接输出html，可用htmlspecialchars函数进行过滤
				if (!strlen($value) && $originField) $value = (isset($data['origin_'.$name]) && strlen(trim($data['origin_'.$name]))) ? strval(trim($data['origin_'.$name])) : '';
				if (!strlen($value)) $value = $default;
				break;
		}
		return $value;
	}
}
