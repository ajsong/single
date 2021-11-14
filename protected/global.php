<?php
//实例化一个模型类
function m($model) {
	return shortcut::model($model);
}

//实例化一个类
function o($app='', $path='api') {
	return shortcut::classs($app, $path);
}

//根据表字段实例化一个字典类
function t($table='', $fields='*') {
	return shortcut::app($table, $fields);
}

//实例化一个插件
function p($plugin, $type='') {
	return shortcut::plugin($plugin, $type);
}

//获取 REQUEST_METHOD, ex: i('method.name.default.type')
function i($mark='') {
	return shortcut::request($mark);
}

//生成序列号
function generate_sn() {
	return date('ymdHis') . rand(10000, 99999);
}

//生成sign
function generate_sign() {
	return md5(md5(rand(100000, 999999)) . time());
}

//生成密码盐值salt
function generate_salt() {
	return rand(100000, 999999);
}

//生成指定位数的随机整数
function generate_code($length=4) {
	return rand(pow(10, ($length-1)), pow(10, $length)-1);
}

//根据盐值生成加密密码
function crypt_password($password, $salt) {
	if (!strlen($password) || !strlen($salt)) return '';
	return md5(md5($password).$salt);
}

//获取头部信息
function get_header($key='') {
	if (!function_exists('getallheaders')) {
		function getallheaders() {
			$headers = array();
			foreach ($_SERVER as $name => $value) {
				if (substr($name, 0, 5) == 'HTTP_') {
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
			if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
				$headers['AUTHORIZATION'] = $_SERVER['PHP_AUTH_DIGEST'];
			} else if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
				$headers['AUTHORIZATION'] = base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $_SERVER['PHP_AUTH_PW']);
			}
			if (isset($_SERVER['CONTENT_LENGTH'])) {
				$headers['CONTENT-LENGTH'] = $_SERVER['CONTENT_LENGTH'];
			}
			if (isset($_SERVER['CONTENT_TYPE'])) {
				$headers['CONTENT-TYPE'] = $_SERVER['CONTENT_TYPE'];
			}
			return $headers;
		}
	}
	$headers = getallheaders();
	if (!is_array($headers)) $headers = array();
	if (strlen($key)) $headers = isset($headers[$key]) ? $headers[$key] : '';
	return $headers;
}

//field:(平均分数量|field)(每份高度||field)(小数!field百分比压缩), upload_switch:上传方法([0|字符串]指定第三方插件,1本地,2大文件分割上传本地)
//return_detail:返回所有信息(数组), file_type:NULL不限制类型, file_name:指定文件名(加上后缀即无后缀时使用指定)
//上传文件,自动判断是否数组形式
function upload_file($mydir, $field=NULL, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
	if (is_array($_FILES[$field]['name'])) {
		return upload_array_file($mydir, $field, $upload_switch, $return_detail, $file_type, $file_name);
	} else {
		return upload_one_file($mydir, $field, $upload_switch, $return_detail, $file_type, $file_name);
	}
}

//上传单个文件
function upload_one_file($mydir, $field=NULL, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
	if (is_string($field)) {
		if (preg_match('/^([\d.]+)!(\w+)$/', $field)) {
			preg_match('/^([\d.]+)!(\w+)$/', $field, $matcher);
			if (!isset($_FILES[$matcher[2]])) exit('FILE ERROR');
		} else if (preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field)) {
			preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field, $matcher);
			if (!isset($_FILES[$matcher[3]])) exit('FILE ERROR');
		}
	} else if (!isset($_FILES[$field])) exit('FILE ERROR');
	$file = upload_obj_file($_FILES, $mydir, $field, $upload_switch, $return_detail, $file_type, $file_name);
	if (!$file || (is_array($file) && !count($file))) $file = isset($_POST["origin_{$field}"]) ? trim($_POST["origin_{$field}"]) : '';
	return $file;
}

//上传数组形式文件
//20151211 by ajsong 为了兼容数组形式的file, e.g.: name="pic[]"
function upload_array_file($mydir, $field=NULL, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
	if (is_string($field)) {
		if (preg_match('/^([\d.]+)!(\w+)$/', $field)) {
			preg_match('/^([\d.]+)!(\w+)$/', $field, $matcher);
			if (!isset($_FILES[$matcher[2]])) exit('FILE ERROR');
		} else if (preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field)) {
			preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field, $matcher);
			if (!isset($_FILES[$matcher[3]])) exit('FILE ERROR');
		}
	} else if (!isset($_FILES[$field])) exit('FILE ERROR');
	$file = $_FILES[$field];
	$name = $file['name'];
	$type = $file['type'];
	$tmp_name = $file['tmp_name'];
	$error = $file['error'];
	$size = $file['size'];
	$files = array();
	for ($i=0; $i<count($name); $i++) {
		if ($name[$i] != '') {
			$fileObj = array();
			$fileObj[$field] = array();
			$fileObj[$field]['name'] = $name[$i];
			$fileObj[$field]['type'] = $type[$i];
			$fileObj[$field]['tmp_name'] = $tmp_name[$i];
			$fileObj[$field]['error'] = $error[$i];
			$fileObj[$field]['size'] = $size[$i];
			$files[] = upload_obj_file($fileObj, $mydir, $field, $upload_switch, $return_detail, $file_type, $file_name);
		} else {
			$files[] = $return_detail ? array() : '';
		}
	}
	return $files;
}

//上传多个文件, e.g.:name="pic1", name="pic2", name="pic3"
function upload_files($mydir, $field=NULL, $num=3, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
	$files = array();
	for ($i=1; $i<=$num; $i++) {
		$files[] = upload_one_file($mydir, $field.$i, $upload_switch, $return_detail, $file_type, $file_name);
	}
	return $files;
}

//上传文件操作
function upload_obj_file($fileObj, $mydir, $field=NULL, $upload_switch=0, $return_detail=false, $file_type=array('jpg', 'jpeg', 'png', 'gif', 'bmp'), $file_name='') {
	set_time_limit(0);
	ini_set('memory_limit', '10240M');
	global $upload_type, $client_id;
	if (isset($upload_type) && $upload_type == 'location') $upload_switch = 1;
	if (defined('IS_SAAS') && IS_SAAS && ((defined('IS_AG') && IS_AG) || (defined('IS_OP') && IS_OP) || (isset($upload_type) && $upload_type == 'softstao'))) {
		$upload_type = 'ypyun';
		$GLOBALS['upyun_bucketname'] = 'bangfang';
		$GLOBALS['upyun_operator_name'] = 'bangfang2';
		$GLOBALS['upyun_operator_pwd'] = 'WfZ9jXRJH#Ts';
		$GLOBALS['upyun_domain'] = 'http://bangfang.b0.upaiyun.com';
	}
	if (defined('IS_SAAS') && IS_SAAS && ((isset($client_id) && intval($client_id) <= 0) || !isset($upload_type))) $upload_type = 'ypyun';
	$file_array = array('file' => '', 'type' => '', 'name' => '', 'tmp_name' => '');
	$splitNumber = 0; //图片平均分割
	$splitHeight = 0; //图片高度分割
	$compressPercent = 0; //图片百分比压缩
	if (is_string($field)) {
		if (preg_match('/^([\d.]+)!(\w+)$/', $field)) {
			preg_match('/^([\d.]+)!(\w+)$/', $field, $matcher);
			$compressPercent = floatval($matcher[1]);
			$field = $matcher[2];
		} else if (preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field)) {
			preg_match('/^(\d+)(\|{1,2})(\w+)$/', $field, $matcher);
			if ($matcher[2] == '|') $splitNumber = intval($matcher[1]);
			else $splitHeight = intval($matcher[1]);
			$field = $matcher[3];
		}
	}
	if ( !$field ||
		(isset($fileObj[$field]) &&
			(is_string($fileObj[$field]) ||
				(is_array($fileObj[$field]) &&
					((!is_array($fileObj[$field]['size']) && $fileObj[$field]['size'] > 0) || (is_array($fileObj[$field]['size']) && $fileObj[$field]['size'][0] > 0))))) ) {
		if (is_string($file_type) && strlen($file_type)) $file_type = explode(',', $file_type);
		//if (!is_array($file_type) || !count($file_type)) $file_type = array('jpg', 'jpeg', 'png', 'gif', 'bmp');
		if (!is_array($file_type)) $file_type = array();
		$fileEle = !$field ? $fileObj : $fileObj[$field];
		$clientDir = '';
		if (defined('IS_SAAS') && IS_SAAS && defined('IS_AG') && defined('IS_OP') && !IS_AG && !IS_OP && isset($client_id)) $clientDir = "/client/{$client_id}";
		if (is_null($mydir)) {
			$dir = '';
		} else {
			if (strlen($mydir)) {
				if (substr($mydir, 0, 1) == '/') {
					$upload_switch = 1;
					$dir = $mydir;
				} else {
					$dir = UPLOAD_PATH . $clientDir . '/' . $mydir . '/' . date('Y') . '/' . date('m') . '/' . date('d');
				}
			} else {
				$dir = UPLOAD_PATH . $clientDir . '/' . date('Y') . '/' . date('m') . '/' . date('d');
			}
		}
		if ((!is_string($upload_switch) && $upload_switch == 0) || (is_bool($upload_switch) && $upload_switch)) $upload_switch = $upload_type;
		if (is_bool($upload_switch) && !$upload_switch) $upload_switch = 1;
		$uploadCallback = function($fileObj) use ($field, $dir, $upload_switch, $file_type, $file_name, $upload_type) {
			$fileEle = !$field ? $fileObj : $fileObj[$field];
			$ext = '';
			$upload_file = '';
			if (is_array($fileEle) && isset($fileEle['tmp_name'])) {
				$upload_file = $fileEle['name'];
				$fs = fopen($fileEle['tmp_name'], 'rb');
				$byte = fread($fs, 2);
				fclose($fs);
			} else if (is_string($fileEle)) {
				if (preg_match('/^http/', $fileEle)) {
					$fs = fopen($fileEle, 'rb');
					$byte = fread($fs, 2);
					fclose($fs);
				} else {
					$byte = substr($fileEle, 0, 2);
				}
			} else {
				$byte = 0;
			}
			$info = @unpack('C2chars', $byte);
			$code = intval($info['chars1'].$info['chars2']);
			switch ($code) {
				case 6063: //php,xml
				case 7790: //exe,dll
				case 64101:error('PLEASE SELECT THE FILE OF ' . implode(',', $file_type));break; //bat
				case 7173:$ext = '.gif';break;
				case 255216:$ext = '.jpg';break;
				case 13780:$ext = '.png';break;
				case 6677:$ext = '.bmp';break;
				case 8297:$ext = '.rar';break;
				case 4742:$ext = '.js';break;
				case 5666:$ext = '.psd';break;
				case 10056:$ext = '.torrent';break;
				case 239187:$ext = '.txt';break; //txt,aspx,asp,sql
				case 6033:$ext = '.html';break; //htm,html
				case 208207: //doc,xls,ppt
				case 8075: //docx,xlsx,pptx,zip,mmap
				default:
					$ext = '';
					if ($code == 208207 && function_exists('finfo_open')) {
						$finfo = finfo_open(FILEINFO_MIME_TYPE);
						$type = @finfo_file($finfo, $upload_file);
						if ($type) {
							if (strpos($type, 'msword') !== false) $ext = '.doc';
							else if (strpos($type, 'vnd.ms-office') !== false) $ext = '.xls';
							else if (strpos($type, 'powerpoint') !== false) $ext = '.ppt';
						}
					}
					if (!strlen($ext)) {
						if (strlen($upload_file)) {
							$type = pathinfo($upload_file, PATHINFO_EXTENSION);
							if (strlen($type)) $ext = $type;
						} else if (strpos($fileEle, 'http') !== false && strpos($fileEle, '.') !== false) {
							$arr = explode('.', $fileEle);
							$ext = end($arr);
						}
						if (!strlen($ext)) {
							if (is_array($file_type) && count($file_type) == 1) {
								$ext = end($file_type);
							} else if (isset($fileEle['type'])) {
								$arr = explode('/', $fileEle['type']);
								$ext = end($arr);
							}
						}
					}
					if (strlen($ext) && substr($ext, 0, 1) != '.') $ext = '.' . $ext;
					break;
			}
			$array = array('file' => '', 'type' => '', 'name' => '', 'tmp_name' => '');
			if (isset($fileEle['tmp_name'])) $array['tmp_name'] = $fileEle['tmp_name'];
			$file = '';
			if (isset($_POST['split_name']) && strlen($_POST['split_name'])) {
				$file_name = date('Ymd') . '_' . $_POST['split_name'];
				$filename = $file_name.$ext;
			} else if (is_string($file_name) && strpos($file_name, '.') !== false) {
				$file_name = explode('.', $file_name);
				if (!strlen($ext)) $ext = $file_name[1];
				$file_name = strlen($file_name[0]) ? $file_name[0] : generate_sn();
				$filename = $file_name . '.' . $ext;
			} else {
				$filename = (is_array($fileEle) && isset($fileEle['name'])) ? $fileEle['name'] : generate_sn() . $ext;
			}
			$array['name'] = $filename;
			$names = explode('.', $filename);
			$ext = $names[count($names)-1];
			$array['type'] = $ext;
			if (is_array($fileEle) && isset($fileEle['size'])) $array['size'] = $fileEle['size'];
			if (is_bool($file_name)) {
				$name = '';
				if ($file_name && is_array($fileEle) && isset($fileEle['name'])) {
					unset($names[count($names)-1]);
					$name = implode('.', $names);
				}
				$file_name = $name;
			}
			if (count($file_type) && !in_array(strtolower($ext), $file_type)) error('PLEASE SELECT THE FILE OF ' . implode(',', $file_type) . ', NOW IS ' . $ext);
			$name = strlen($file_name) ? $file_name : generate_sn();
			if (is_string($upload_switch)) {
				//上传到第三方文件存储
				$upload = p('upload', $upload_switch);
				$result = $upload->upload($fileObj, $field, str_replace('/public/', '/', $dir), $name, $ext);
				if (isset($result['width']) && intval($result['width'])>0) $array['width'] = intval($result['width']);
				if (isset($result['height']) && intval($result['height'])>0) $array['height'] = intval($result['height']);
				$file = $result['file'];
			} else if ($upload_switch == 2) {
				//文件分割上传到服务器
				$upload_dir = ROOT_PATH . (!strlen($dir) ? UPLOAD_PATH : $dir);
				if (!is_dir($upload_dir)) makedir($upload_dir);
				$savename = $name . '.' . $ext;
				$pathname = $upload_dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
				if ($field) {
					$tmp_name = $fileEle['tmp_name'];
					if (file_exists($pathname)) unlink($pathname);
					if (function_exists('move_uploaded_file')) {
						move_uploaded_file($tmp_name, $pathname);
					} else if(function_exists('rename')) {
						rename($tmp_name, $pathname);
					} else if (function_exists('copy')) {
						copy($tmp_name, $pathname);
					}
				} else {
					$handle = fopen($upload_dir . '/' . $savename, 'w') or error('UNABLE TO WRITE FILE');
					fwrite($handle, $fileEle);
					fclose($handle);
				}
				$file = $dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
				$splitTotal = intval($_POST['split_total']);
				for ($i=0; $i<$splitTotal; $i++) {
					$names = explode('_', $name);
					$splitName = $names[0] . '_' . $names[1] . '_' . $i;
					$pathname = ROOT_PATH . str_replace($name, $splitName, $file);
					if (!file_exists($pathname)) exit;
				}
				$pname = generate_sn();
				$array['name'] = $pname . '.' . $ext;
				$leader = fopen($upload_dir . (substr($dir, -1) == '/' ? '' : '/') . $pname . '.' . $ext, 'ab');
				for ($i=0; $i<$splitTotal; $i++) {
					$names = explode('_', $name);
					$splitName = $names[0] . '_' . $names[1] . '_' . $i;
					$pathname = ROOT_PATH . str_replace($name, $splitName, $file);
					$handle = fopen($pathname, 'rb');
					fwrite($leader, fread($handle, filesize($pathname)));
					fclose($handle);
					unlink($pathname);
				}
				fclose($leader);
				$file = $dir . (substr($dir, -1) == '/' ? '' : '/') . $pname . '.' . $ext;
				$file = str_replace('/public/', '/', $file);
			} else {
				//上传到服务器
				$upload_dir = ROOT_PATH . (!strlen($dir) ? UPLOAD_PATH : $dir);
				if (!is_dir($upload_dir)) makedir($upload_dir);
				if (is_array($fileEle)) {
					$keys = array_keys($fileEle);
					if (array_sum($keys) == array_sum(array_keys($keys))) {
						require_once(SDK_PATH . '/class/upload/class.upload.php');
						$upload = new Upload($fileEle);
						if ($upload->uploaded) {
							$upload->file_new_name_body = $name;
							$upload->Process($upload_dir);
							if ($upload->processed) {
								if (stripos($fileEle['type'], 'image/') !== false) {
									$array['width'] = $upload->image_src_x;
									$array['height'] = $upload->image_src_y;
								}
								$file = $dir . (substr($dir, -1) == '/' ? '' : '/') . $upload->file_dst_name;
							}
						}
						$upload->Clean();
					} else if (isset($fileEle['tmp_name'])) {
						$tmp_name = $fileEle['tmp_name'];
						$savename = $name . '.' . $ext;
						$pathname = $upload_dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
						if (file_exists($pathname)) unlink($pathname);
						if (function_exists('move_uploaded_file')) {
							move_uploaded_file($tmp_name, $pathname);
						} else if(function_exists('rename')) {
							rename($tmp_name, $pathname);
						} else if (function_exists('copy')) {
							copy($tmp_name, $pathname);
						}
						$file = $dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
					}
				} else {
					$savename = $name . '.' . $ext;
					$pathname = $upload_dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
					$handle = fopen($pathname, 'w') or error('UNABLE TO WRITE FILE');
					fwrite($handle, $fileEle);
					fclose($handle);
					$file = $dir . (substr($dir, -1) == '/' ? '' : '/') . $savename;
				}
				$file = str_replace('/public/', '/', $file);
				if (in_array($ext, ['jpg', 'jpeg'])) {
					image_compress('/public/' . $file, 1, '/public/' . $file);
				}
			}
			$array['file'] = $file;
			return $array;
		};
		if ($compressPercent > 0) {
			//图片宽高百分比压缩
			if (!is_array($fileEle)) error('DATA ERROR');
			if (isset($fileEle['size']) && is_array($fileEle['size'])) {
				$name = $fileEle['name'][0];
				$tmp_name = $fileEle['tmp_name'][0];
			} else {
				$name = $fileEle['name'];
				$tmp_name = $fileEle['tmp_name'];
			}
			$fs = fopen($tmp_name, 'rb');
			$byte = fread($fs, 2);
			fclose($fs);
			$info = @unpack('C2chars', $byte);
			$code = intval($info['chars1'].$info['chars2']);
			$e = '';
			switch ($code) {
				case 255216:$e = 'jpeg';break; //jpg
				case 7173:$e = 'gif';break; //gif
				case 6677:$e = 'bmp';break; //bmp
				case 13780:$e = 'png';break; //png
				default: error('FILE IS NOT A IMAGE');
			}
			if ($e == 'gif') {
				$obj = array(
					'name' => $name,
					'type' => "image/{$e}",
					'tmp_name' => $tmp_name,
					'error' => 0,
					'size' => filesize($tmp_name)
				);
				$file_array = $uploadCallback(array($field => $obj));
			} else {
				if ($e == 'bmp') $e = 'jpeg';
				$savename = generate_sn() . '.' . ($e == 'jpeg' ? 'jpg' : $e);
				$filename = ROOT_PATH . "/temp/{$savename}";
				$pathname = ROOT_PATH . '/temp/' . generate_sn() . '.' . ($e == 'jpeg' ? 'jpg' : $e);
				if (file_exists($pathname)) unlink($pathname);
				if (function_exists('move_uploaded_file')) {
					move_uploaded_file($tmp_name, $pathname);
				} else if(function_exists('rename')) {
					rename($tmp_name, $pathname);
				} else if (function_exists('copy')) {
					copy($tmp_name, $pathname);
				}
				image_compress($pathname, $compressPercent, $filename);
				$obj = array(
					'name' => $name,
					'type' => "image/{$e}",
					'tmp_name' => $filename,
					'error' => 0,
					'size' => filesize($filename)
				);
				$file_array = $uploadCallback(array($field=>$obj));
				unlink($pathname);
				unlink($filename);
			}
		} else if ($splitNumber > 0 || $splitHeight > 0) {
			//图片平均分割或按高度分割
			if (!is_array($fileEle)) error('DATA ERROR');
			if (isset($fileEle['size']) && is_array($fileEle['size'])) {
				$name = $fileEle['name'][0];
				$tmp_name = $fileEle['tmp_name'][0];
			} else {
				$name = $fileEle['name'];
				$tmp_name = $fileEle['tmp_name'];
			}
			$fs = fopen($tmp_name, 'rb');
			$byte = fread($fs, 2);
			fclose($fs);
			$info = @unpack('C2chars', $byte);
			$code = intval($info['chars1'].$info['chars2']);
			$e = '';
			switch ($code) {
				case 255216:$e = 'jpeg';break; //jpg
				case 7173:$e = 'gif';break; //gif
				case 6677:$e = 'bmp'; //bmp
					if (version_compare(PHP_VERSION, '7.0', '<')) error('BMP MUST BE >=7.0');
					break;
				case 13780:$e = 'png';break; //png
				default: error('FILE IS NOT A IMAGE');
			}
			if ($e == 'bmp') $e = 'jpeg';
			$imagecreatefrom = "imagecreatefrom{$e}";
			$imageoutput = "image{$e}";
			$source = $imagecreatefrom($tmp_name);
			$file = array('file' => array(), 'type' => array(), 'name' => array());
			$array = array();
			list($width, $height) = getimagesize($tmp_name); //list(width, height, [1:GIF,2:JPG,3:PNG,6:BMP])
			if ($splitHeight > 0 && $height <= $splitHeight) {
				$obj = array(
					'name' => $name,
					'type' => "image/{$e}",
					'tmp_name' => $tmp_name,
					'error' => 0,
					'size' => filesize($tmp_name)
				);
				$array[] = $uploadCallback(array($field => $obj));
			} else {
				$newWidth = $width;
				if ($splitHeight > 0) {
					$newHeight = $splitHeight;
					$splitNumber = ceil($height / $splitHeight);
					$lastHeight = $height % $splitHeight;
				} else {
					$newHeight = floor($height / $splitNumber);
					$lastHeight = $newHeight + ($height % $splitNumber);
				}
				for ($i=0; $i<$splitNumber; $i++) {
					$p = $newHeight * $i;
					if (($i+1) == $splitNumber) $newHeight = $lastHeight;
					$thumb = imagecreatetruecolor($newWidth, $newHeight);
					imagecopyresized($thumb, $source, 0, 0, 0, $p, $newWidth, $height, $width, $height);
					if ($e == 'jpeg') {
						$path = ROOT_PATH . "/temp/split_image_{$i}.jpg";
						$imageoutput($thumb, $path, 100);
					} else {
						$path = ROOT_PATH . "/temp/split_image_{$i}.{$e}";
						$imageoutput($thumb, $path);
					}
					$obj = array(
						'name' => "split_image_{$i}." . ($e == 'jpeg' ? 'jpg' : $e),
						'type' => "image/{$e}",
						'tmp_name' => $path,
						'error' => 0,
						'size' => filesize($path)
					);
					$array[] = $uploadCallback(array($field => $obj));
					unlink($path);
				}
			}
			foreach ($array as $k => $obj) {
				$file['file'][$k] = $obj['file'];
				$file['type'][$k] = $obj['type'];
				$file['name'][$k] = $obj['name'];
				if (isset($obj['size'])) $file['size'][$k] = $obj['size'];
				if (isset($obj['width'])) $file['width'][$k] = $obj['width'];
				if (isset($obj['height'])) $file['height'][$k] = $obj['height'];
			}
			$file_array = $file;
		} else if (is_array($fileEle) && isset($fileEle['size']) && is_array($fileEle['size'])) {
			//多文件上传
			$file = array('file' => array(), 'type' => array(), 'name' => array());
			$array = array();
			for ($i=0; $i<count($fileEle['size']); $i++) {
				$obj = array(
					'name' => $fileEle['name'][$i],
					'type' => $fileEle['type'][$i],
					'tmp_name' => $fileEle['tmp_name'][$i],
					'error' => $fileEle['error'][$i],
					'size' => $fileEle['size'][$i]
				);
				$array[] = $uploadCallback(array($field => $obj));
			}
			foreach ($array as $k => $obj) {
				$file['file'][$k] = $obj['file'];
				$file['type'][$k] = $obj['type'];
				$file['name'][$k] = $obj['name'];
				if (isset($obj['size'])) $file['size'][$k] = $obj['size'];
				if (isset($obj['width'])) $file['width'][$k] = $obj['width'];
				if (isset($obj['height'])) $file['height'][$k] = $obj['height'];
			}
			$file_array = $file;
		} else {
			//单文件上传
			$file_array = $uploadCallback($fileObj);
		}
	} else if (isset($_POST[$field])) {
		//如果不是文件即以POST形式获取
		$file = trim($_POST[$field]);
		$file_array['file'] = $file;
	}
	return $return_detail ? $file_array : $file_array['file'];
}

//下载远程文件到本地
function download_file($dir, $url, $origin_name=false, $suffix='') {
	$paths = download_files($dir, array($url), $origin_name, $suffix);
	return count($paths) ? $paths[0] : '';
}
function download_files($dir, $urls, $origin_name=false, $suffix='') {
	set_time_limit(0);
	ini_set('memory_limit', '10240M');
	$dir = str_replace(ROOT_PATH . UPLOAD_PATH . '/', '', $dir);
	$paths = array();
	foreach ($urls as $url) {
		if (!strlen($url)) continue;
		preg_match('/(\.\w+)$/', $url, $matcher);
		if (is_array($matcher) && count($matcher)) $suffix = $matcher[1];
		if (!$origin_name || !(is_array($matcher) && count($matcher))) {
			$filename = generate_sn();
		} else {
			$pathinfo = explode('/', $url);
			$pathinfo = explode('.', $pathinfo[count($pathinfo)-1]);
			$filename = $pathinfo[0];
		}
		if (strlen($suffix) && !preg_match('/^\./', $suffix)) $suffix = '.'.$suffix;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		if (substr($url, 0, 8) == 'https://') {
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		makedir(ROOT_PATH . UPLOAD_PATH . '/' . $dir);
		$filepath = UPLOAD_PATH . '/' . $dir . '/' . $filename . $suffix;
		$res = fopen(ROOT_PATH . $filepath, 'a');
		fwrite($res, $data);
		fclose($res);
		$paths[] = str_replace('/public/', '/', $filepath);
	}
	return $paths;
}

//导出成excel, $return为true即在服务器生成文件, $fields = array('id'=>'ID', 'name'=>'姓名', 'mobile'=>'电话');
function export_excel($rs, $fields, $return=false) {
	$objPHPExcel = new PHPExcel();
	//表格头
	$column = 'A';
	$row_number = 1;
	foreach ($fields as $field => $name) {
		$cell = "{$column}{$row_number}";
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue("{$cell}", "{$name} ");
		$objSheet = $objPHPExcel->getActiveSheet();
		$objSheet->getColumnDimension($column)->setAutoSize(true);
		$objSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
		$objSheet->getStyle("{$cell}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		$objSheet->getStyle("{$cell}")->getAlignment()->setWrapText(false);
		$column++;
	}
	$row_number = 2; //1:based index
	foreach ($rs as $k => $g) {
		$column = 'A';
		foreach ($fields as $field => $name) {
			$cell = "{$column}{$row_number}";
			$objSheet = $objPHPExcel->getActiveSheet();
			$objSheet->getColumnDimension($column)->setAutoSize(true);
			if (array_key_exists($field, $g)) {
				$objSheet->setCellValue("{$cell}", "{$g->{$field}} ");
			} else {
				$objSheet->setCellValue("{$cell}", " ");
			}
			$objSheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
			$objSheet->getStyle("{$cell}")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
			$objSheet->getStyle("{$cell}")->getAlignment()->setWrapText(false);
			$column++;
		}
		$row_number++;
	}
	if ($return) {
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$filepath = '/export/';
		$filename = $filepath . generate_sn() . '.xlsx';
		makedir($filepath);
		$objWriter->save(ROOT_PATH . UPLOAD_PATH . $filename);
		return $filename;
	} else {
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . generate_sn() . '.xlsx"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');
		// If you're serving to IE over SSL, then the following may be needed
		header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header ('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header ('Pragma: public'); // HTTP/1.0
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		exit;
	}
}

//导入excel, 对应根目录, sheet<0即获取所有工作表,否则获取指定索引工作表
function import_excel($file, $sheet=0, $start_row=1, $start_column=0, $clear_number_letter=true, $non_space_row=false, $delete_file=true) {
	set_time_limit(0);
	ini_set('memory_limit', '10240M');
	$file = ROOT_PATH.str_replace(ROOT_PATH, '', $file);
	if (empty($file) || !file_exists($file)) die('EXCEL FILE NOT EXISTS');
	setlocale(LC_ALL, 'zh_CN');
	$objRead = new PHPExcel_Reader_Excel2007(); //建立reader对象
	if (!$objRead->canRead($file)) {
		$objRead = new PHPExcel_Reader_Excel5();
		if (!$objRead->canRead($file)) die('NOT A EXCEL FILE');
	}
	require_once(SDK_PATH . '/class/PHPExcel/Classes/PHPExcel/Shared/Date.php');
	$cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
		'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
	$obj = $objRead->load($file); //建立excel对象
	$getSheet = function($sheet) use ($obj, $cellName, $start_row, $start_column, $clear_number_letter, $non_space_row) {
		$data = array();
		$currSheet = is_numeric($sheet) ? $obj->getSheet($sheet) : $sheet; //获取指定的sheet表
		$columnH = $currSheet->getHighestColumn(); //取得最大的列号
		$columnCnt = array_search($columnH, $cellName);
		$rowCnt = $currSheet->getHighestRow(); //获取总行数
		for ($row = $start_row; $row <= $rowCnt; $row++) { //读取内容
			$cellValues = array();
			for ($column = $start_column; $column <= $columnCnt; $column++) {
				$cellId = $cellName[$column].$row;
				$cell = $currSheet->getCell($cellId);
				if ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NUMERIC) {
					$cellValue = $cell->getValue();
					$cellstyleformat = $cell->getStyle()->getNumberFormat();
					$formatcode = $cellstyleformat->getFormatCode();
					if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode)) {
						$cellValue = date('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP($cellValue));
					} else {
						//$cellValue = PHPExcel_Style_NumberFormat::toFormattedString($cellValue, $formatcode);
						$cellValue = $cell->getFormattedValue();
					}
				} else {
					//$cellValue = $cell->getCalculatedValue(); //获取公式计算的值
					$cellValue = $cell->getFormattedValue();
				}
				if ($cellValue instanceof PHPExcel_RichText) $cellValue = $cellValue->__toString(); //富文本转换字符串
				$cellValues[$cellName[$column]] = $cellValue;
			}
			$notEmptyCell = !$non_space_row;
			foreach ($cellValues as $k => $g) {
				if (!empty($g) && strlen(trim(strval($g)))) {
					$notEmptyCell = true;
					break;
				}
			}
			if ($notEmptyCell) {
				$rows = array();
				foreach ($cellValues as $k => $g) {
					if ($clear_number_letter) {
						$rows[] = $g;
					} else {
						$rows[$k] = $g;
						//$rows[$k] = iconv('gb2312', 'utf-8//IGNORE', $g);
					}
				}
				if ($clear_number_letter) {
					$data[] = $rows;
				} else {
					$data[$row] = $rows;
				}
			}
		}
		return $data;
	};
	if ($sheet >= 0) {
		$res = $getSheet($sheet);
	} else {
		$res = array();
		$sheetNames = $obj->getSheetNames();
		foreach ($sheetNames as $name) {
			$res[$name] = $getSheet($obj->getSheetByName($name));
		}
		
	}
	if ($delete_file) unlink($file);
	return $res;
}

//导出文件或文件夹为zip
function export_zip($fileOrDir, $base_path='', $filename='') {
	$fileOrDir = ROOT_PATH.str_replace(ROOT_PATH, '', $fileOrDir);
	$add2zip = function($path, &$zip, $base_path='') use (&$add2zip) {
		if (is_dir($path)) {
			$handler = opendir($path);
			while (($file = readdir($handler)) !== false) {
				if ($file != '.' && $file != '..') {
					if (is_dir($path . '/' . $file)) {
						$add2zip($path . '/' . $file, $zip, $base_path);
					} else {
						$dir_path = explode('/' . $base_path, $path);
						$zip->addFile($path . '/' . $file, (strlen($dir_path[1])?$dir_path[1].'/':'') . $file);
					}
				}
			}
			closedir($handler);
		} else {
			$filename = substr($path, strrpos($path, '/')+1);
			$zip->addFile($path, $filename);
		}
	};
	if (!strlen($filename)) $filename = generate_sn();
	$file = ROOT_PATH . UPLOAD_PATH . "/{$filename}.zip";
	$zip = new ZipArchive();
	$res = $zip->open($file, ZipArchive::CREATE | ZipArchive::OVERWRITE);
	if ($res === TRUE) {
		$add2zip($fileOrDir, $zip, $base_path);
	}
	$zip->close();
	header('Content-Type: application/zip');
	header('Content-Transfer-Encoding: Binary');
	header('Content-Length: '.filesize($file));
	header('Content-Disposition: attachment; filename="' . basename($file) . '"');
	readfile($file);
	unlink($file);
	exit;
}

//循环创建目录,对应根目录
function makedir($destination, $create_html=false) {
	$target_path = str_replace(ROOT_PATH, '', $destination);
	if (is_dir(ROOT_PATH.$target_path)) return true;
	$each_path = explode('/', $target_path);
	$cur_path = ROOT_PATH; //当前循环处理的路径
	$origin_mask = @umask(0);
	foreach ($each_path as $path) {
		if ($path) {
			$cur_path .= '/' . $path;
			if (!is_dir($cur_path)) {
				if (@mkdir($cur_path, 0777)) {
					@chmod($cur_path, 0777);
					if ($create_html) @fclose(@fopen($cur_path . '/index.html', 'w'));
				} else {
					@umask($origin_mask);
					return false;
				}
			}
		}
	}
	@umask($origin_mask);
	return true;
}
function create_folder($destination, $create_html=false) {
	return makedir($destination, $create_html);
}

//复制文件夹,对应根目录
function copy_folder($source, $destination, $station=true) {
	$source_path = str_replace(ROOT_PATH, '', $source);
	$destination_path = str_replace(ROOT_PATH, '', $destination);
	if ($station) {
		$source_path = ROOT_PATH . $source_path;
		$destination_path = ROOT_PATH . $destination_path;
	}
	if (!is_dir($source_path)) return false;
	if (!is_dir($destination_path)) makedir($destination_path);
	$handle = dir($source_path);
	while ($entry = $handle->read()) {
		if ($entry != '.' && $entry != '..') {
			if (stripos($entry, '.DS_Store') !== false || stripos($entry, '.git') !== false || stripos($entry, '.svn') !== false || stripos($entry, '.idea') !== false) continue;
			if (is_dir($source_path . '/' . $entry)) {
				copy_folder($source_path . '/'.$entry, $destination_path . '/' . $entry, $station);
			} else {
				if (!file_exists($destination_path . '/' . $entry)) {
					copy($source_path . '/' . $entry, $destination_path . '/' . $entry);
				}
			}
		}
	}
	return true;
}

//删除文件夹和文件夹下的所有文件
function delete_folder($path, $station=true) {
	$path = str_replace(ROOT_PATH, '', $path);
	if ($station) $path = ROOT_PATH.$path;
	if (!is_dir($path)) return true;
	$handle = dir($path);
	while ($entry = $handle->read()) {
		if ($entry != '.' && $entry != '..') {
			if (is_dir($path . '/' . $entry)) {
				delete_folder($path . '/' . $entry, $station);
			} else {
				unlink($path . '/' . $entry);
			}
		}
	}
	rmdir($path);
	return true;
}

//替换文件夹内所有文件内容,对应根目录
function folder_file_content_replace($path, $callback, $station=true) {
	$path = str_replace(ROOT_PATH, '', $path);
	if ($station) $path = ROOT_PATH . $path;
	if (!is_dir($path)) return false;
	$handle = dir($path);
	while ($entry = $handle->read()) {
		if ($entry != '.' && $entry != '..') {
			if (is_dir($path . '/' . $entry)) {
				folder_file_content_replace($path . '/' . $entry, $callback, $station);
			} else {
				file_content_replace($path . '/' . $entry, $callback, $station);
			}
		}
	}
	return true;
}

//替换文件内容,对应根目录
function file_content_replace($file, $callback, $station=true) {
	$origin_file = $file;
	$file = str_replace(ROOT_PATH, '', $file);
	if ($station) $file = ROOT_PATH.$file;
	if (file_exists($file)) {
		clearstatcache();
		$fp = fopen($file, 'r');
		flock($fp, LOCK_EX);
		$content = fread($fp, filesize($file));
		if (strlen($content) && $callback && !is_string($callback) && is_callable($callback)) $content = $callback($content, $origin_file);
		flock($fp, LOCK_UN);
		fclose($fp);
		$fp = fopen($file, 'w');
		flock($fp, LOCK_EX);
		fwrite($fp, $content);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

//截断过长的字符
function cut_str($string, $length=80, $suffix='') {
	$repeat_pattern = function($pattern, $length) {
		return str_repeat("$pattern{0,65535}", $length / 65535)."$pattern{0,".($length % 65535)."}";
	};
	if (!preg_match("(^(".$repeat_pattern("[\t\r\n -\x{10FFFF}]", $length).")($)?)u", $string, $match)) {
		preg_match("(^(".$repeat_pattern("[\t\r\n -~]", $length).")($)?)", $string, $match);
	}
	return $match[1] . $suffix . (isset($match[2]) ? '' : '<i>...</i>');
}

//是否utf8字符
function is_utf8($str) {
	return preg_match('%^(?:
             [\x09\x0A\x0D\x20-\x7E]                 # ASCII
         | [\xC2-\xDF][\x80-\xBF]                 # non-overlong 2-byte
         |     \xE0[\xA0-\xBF][\x80-\xBF]             # excluding overlongs
         | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}     # straight 3-byte
         |     \xED[\x80-\x9F][\x80-\xBF]             # excluding surrogates
         |     \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
         | [\xF1-\xF3][\x80-\xBF]{3}             # planes 4-15
         |     \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
     )*$%xs', $str);
}

//将时间转换成刚刚、分钟、小时
function get_time_word($date) {
	if (!is_numeric($date)) $date = strtotime($date);
	$between = time() - $date;
	if ($between < 60) return '刚刚';
	if ($between < 3600) return floor($between/60) . '分钟前';
	if ($between < 86400) return floor($between/3600) . '小时前';
	if ($between <= 864000) return floor($between/86400) . '天前';
	if ($between > 864000) return date('Y-m-d', $date);
	return $between;
}

//将手机号码中间设为星号
function get_mobile_mark($mobile) {
	return substr_replace($mobile, '****', 3, -4);
}

//生成又拍云缩略图url
function get_upyun_thumb_url($url, $size='') {
	//if ($url && $size) {
	if ($url && $size && !is_my_domain($url)) {
		//产品缩略图
		if (strpos($url, UPLOAD_PATH) !== false) {
			$url = $url . '!' . $size;
		}
	}
	return $url;
}

//是否本站域名
function is_my_domain($url) {
	global $img_domain;
	if (!strlen($img_domain)) $img_domain = https() . $_SERVER['HTTP_HOST'];
	if (strpos($url, $img_domain) !== false) {
		return true;
	} else {
		return false;
	}
}

//正则语法替换颜色
function changeColor($str) {
	$str = preg_replace_callback('/#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})([^#]+)#/', function($matcher) {
		return '<font color="#' . $matcher[1] . '">' . $matcher[2] . '</font>';
	}, $str);
	$str = preg_replace_callback('/#([RGBOPY])([^#]+)#/', function($matcher) {
		$html = '<font color="';
		switch ($matcher[1]) {
			case 'R':$html .= 'red';break;
			case 'G':$html .= 'green';break;
			case 'B':$html .= 'blue';break;
			case 'O':$html .= 'orange';break;
			case 'P':$html .= 'purple';break;
			case 'Y':$html .= '#ffc700';break;
		}
		$html .= '">' . $matcher[2] . '</font>';
		return $html;
	}, $str);
	return $str;
}

//坐标的指定公里范围矩形
function locationGeo($lng, $lat, $distance=5) {
	$lng = floatval($lng);
	$lat = floatval($lat);
	$earthhalf = 6371; //地球半径
	$dlng = 2 * asin(sin($distance / (2 * $earthhalf)) / cos(deg2rad($lat)));
	$dlng = rad2deg($dlng);
	$dlat = $distance / $earthhalf;
	$dlat = rad2deg($dlat);
	return array(
		array('lat' => $lat+$dlat, 'lng' => $lng-$dlng), //left_top
		array('lat' => $lat+$dlat, 'lng' => $lng+$dlng), //right_top
		array('lat' => $lat-$dlat, 'lng' => $lng-$dlng), //left_bottom
		array('lat' => $lat-$dlat, 'lng' => $lng+$dlng) //right_bottom
	);
}

//获取ip地址
function ip() {
	if (isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != 'unknown') {
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != 'unknown') {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	if ($ip == '' || $ip == '::1' || $ip == '127.0.0.1') {
		return '127.0.0.1';
	}
	return $ip;
}

//通过第三方获取ip地址
function get_ip($allInfo=false) {
	$json = requestCurl('post', 'http://ip.taobao.com/outGetIpInfo', 'ip=myip&accessKey=alibaba-inc', true);
	if(intval($json['code']) != 0)return $allInfo ? NULL : '127.0.0.1';
	return $allInfo ? $json['data'] : $json['data']['ip'];
	/*
	//免费查询10000次/月
	$url = 'http://api.ipstack.com/check?language=zh&access_key=990add5c9e2d8ad47a20dee7299068a5'; //可获取IP、经纬度
	//$url = 'http://api.ipstack.com/134.201.250.155?language=zh&access_key=990add5c9e2d8ad47a20dee7299068a5'; //指定ip
	$json = json_decode(file_get_contents($url), true);
	return ($allInfo ? $json : $json['ip']);
	*/
}

//获取省市
function city() {
	$data = file_get_contents('http://ip.ws.126.net/ipquery');
	$data = iconv('GB2312', 'UTF-8//IGNORE', $data);
	preg_match('/({[^}]+})/', $data, $matcher);
	$res = $matcher[1];
	$res = preg_replace('/\b(\w+):/', '"$1":', $res);
	return json_decode($res, true);
	//{"city":"广州市", "province":"广东省"}
}

//返回http协议
function https() {
    $is_https = false;
    if (!isset($_SERVER['HTTPS'])) return 'http://';
    if ($_SERVER['HTTPS'] === 1) { //Apache
        $is_https = true;
    } else if (strtoupper($_SERVER['HTTPS']) == 'ON') { //IIS
        $is_https = true;
    } else if ($_SERVER['SERVER_PORT'] == 443) { //Other
        $is_https = true;
    }
    return $is_https ? 'https://' : 'http://';
}

//当前网址
function domain() {
	//return https() . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '?' . $_SERVER['QUERY_STRING'];
	return https() . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
}

//格式化URL,suffix增加网址后缀, 如七牛?imageMogr2/thumbnail/200x200, 又拍云(需自定义)!logo
function add_domain($url, $origin_third_host='', $replace_third_host='', $suffix='') {
	global $img_domain;
	if (is_string($url) && strlen($url) && !preg_match('/^https?:\/\//', $url)) {
		if (substr($url,0,2) == '//') {
			$url = https() . substr($url, 2);
		} else {
			if (stripos($url, '%domain%') !== false && strpos($url, https() . $_SERVER['HTTP_HOST']) === false) {
				$url = str_replace('%domain%', https() . $_SERVER['HTTP_HOST'], $url);
			} else {
				$url = str_replace('%domain%', '', $url);
				if (substr($url, 0, 1) == '/') {
					$url = (strlen($img_domain) ? $img_domain : https() . $_SERVER['HTTP_HOST']) . $url;
				} else {
					if (preg_match('/^((http|https|ftp):\/\/)?[\w\-_]+(\.[\w\-_]+)+([\w\-.,@?^=%&:\/~+#]*[\w\-@?^=%&\/~+#])?$/', https() . $_SERVER['HTTP_HOST'] . '/' . $url)) {
						$url = (strlen($img_domain) ? $img_domain : https() . $_SERVER['HTTP_HOST']).'/'.$url;
					} else {
						$url = str_replace('"/uploads/', '"' . https() . $_SERVER['HTTP_HOST'] . '/uploads/', $url);
					}
				}
			}
		}
	}
	if (is_string($url) && strlen($url) && strpos($url, '/images/') === false && strlen($suffix) && stripos($url, $suffix) === false) $url .= $suffix;
	if (is_string($url)) $url = str_replace('%domain%', '', $url);
	if (strlen($origin_third_host) && strlen($replace_third_host) && preg_match('/^https?:\/\//', $url)) {
		$replace = 'str_replace';
		if (substr($origin_third_host, 0, 1) == '/') $replace = 'preg_replace';
		$url = $replace($origin_third_host, $replace_third_host, $url);
	}
	return $url;
}

//递归一个数组/对象的属性加上域名
function add_domain_deep($obj, $fields=array(), $origin_third_host='', $replace_third_host='') {
	if (is_object($obj) || is_array($obj)) {
		foreach ($obj as $key => $val) {
			if (is_object($val) || is_array($val)) {
				if (is_object($obj)) {
					$obj->{$key} = add_domain_deep($val, $fields, $origin_third_host, $replace_third_host);
				} else if (is_array($obj)) {
					$obj[$key] = add_domain_deep($val, $fields, $origin_third_host, $replace_third_host);
				}
			} else {
				if ((is_array($fields) && in_array($key, $fields)) || (is_bool($fields) && $fields) || (is_string($fields) && $key == $fields)) {
					if (is_object($obj)) {
						$obj->{$key} = add_domain($val, $origin_third_host, $replace_third_host, is_avatar($key));
					} else if (is_array($obj)) {
						$obj[$key] = add_domain($val, $origin_third_host, $replace_third_host, is_avatar($key));
					}
				}
			}
		}
	} else if (!is_null($obj)) {
		$obj = add_domain($obj, $origin_third_host, $replace_third_host);
	}
	return $obj;
}

//递归一个数组/对象的属性替换域名
function replace_domain_deep($obj, $origin_third_host, $replace_third_host) {
	$replace = 'str_replace';
	if (substr($origin_third_host, 0, 1) == '/') $replace = 'preg_replace';
	if (is_object($obj) || is_array($obj)) {
		foreach ($obj as $key => $val) {
			if (is_object($val) || is_array($val)) {
				if (is_object($obj)) {
					$obj->{$key} = replace_domain_deep($val, $origin_third_host, $replace_third_host);
				} else if (is_array($obj)) {
					$obj[$key] = replace_domain_deep($val, $origin_third_host, $replace_third_host);
				}
			} else {
				if (is_object($obj)) {
					if (is_string($val)) $obj->{$key} = $replace($origin_third_host, $replace_third_host, $val);
				} else if (is_array($obj)) {
					if (is_string($val)) $obj[$key] = $replace($origin_third_host, $replace_third_host, $val);
				}
			}
		}
	} else if (is_string($obj)) {
		$obj = $replace($origin_third_host, $replace_third_host, $obj);
	}
	return $obj;
}

//字符串是否包含avatar字样, 且判断是否本地图片(本地需要返回空,否则按照又拍云规则增加后缀)
function is_avatar($key) {
	global $img_domain;
	if (strlen($key)) {
		if (strpos($key, 'avatar') !== false) {
			if (!strlen($img_domain)) $img_domain = https() . $_SERVER['HTTP_HOST'];
			if ( !(strpos($key, $img_domain) !== false || (strpos($key, 'http://') === false && strpos($key, 'https://') === false)) ) {
				return '!logo';
			}
		}
	}
	return '';
}

//转义斜杠
function addslashes_deep($value) {
	if (empty($value)) {
		return $value;
	} else {
		return is_array($value) ? array_map('addslashes_deep', $value) : addslashes($value);
	}
}

//解码经escape编码的字符串
function unescape($str, $charset='utf8') {
	if ($str == '') return '';
	if ($charset == 'utf8') {
		$ret = '';
		$len = strlen($str);
		for ($i=0; $i<$len; $i++) {
			if ($str[$i] == '%' && $str[$i+1] == 'u') {
				$val = hexdec(substr($str, $i+2, 4));
				if ($val<0x7f) $ret .= chr($val);
				else if ($val<0x800) $ret .= chr(0xc0|($val>>6)).chr(0x80|($val&0x3f));
				else $ret .= chr(0xe0|($val>>12)).chr(0x80|(($val>>6)&0x3f)).chr(0x80|($val&0x3f));
				$i += 5;
			} else if ($str[$i] == '%') {
				$ret .= urldecode(substr($str, $i, 3));
				$i += 2;
			} else $ret .= $str[$i];
		}
		return $ret;
	} else {
		$str = rawurldecode($str);
		preg_match_all("/%u.{4}|&#x.{4};|&#d+;|.+/U", $str, $r);
		$ar = $r[0];
		foreach ($ar as $k => $v) {
			if (function_exists('mb_convert_encoding')) {
				if (substr($v,0,2) == '%u') $ar[$k] = mb_convert_encoding(pack('H4',substr($v,-4)), 'GB2312', 'UCS-2');
				else if (substr($v,0,3) == '&#x') $ar[$k] = mb_convert_encoding(pack('H4',substr($v,3,-1)), 'GB2312', 'UCS-2');
				else if (substr($v,0,2) == '&#') $ar[$k] = mb_convert_encoding(pack('H4',substr($v,2,-1)), 'GB2312', 'UCS-2');
			} else {
				if (substr($v,0,2) == '%u') $ar[$k] = iconv('UCS-2', 'GB2312', pack('H4',substr($v,-4)));
				else if (substr($v,0,3) == '&#x') $ar[$k] = iconv('UCS-2', 'GB2312', pack('H4',substr($v,3,-1)));
				else if (substr($v,0,2) == '&#') $ar[$k] = iconv('UCS-2', 'GB2312', pack('H4',substr($v,2,-1)));
			}
		}
		return implode('', $ar);
	}
}

//输出xml格式
function xml_encode($data) {
	$xml = new SimpleXMLElement('<?xml version="1.0"?><rest></rest>');
	foreach ($data as $key => $value) {
		if (is_array($value)) {
			foreach ($value as $k => $v) {
				$xml->addChild($k, $v);
			}
		} else {
			$xml->addChild($key, $value);
		}
	}
	return $xml->asXML();
}

//解析xml数据
function xml_decode($xml, $assoc=false) {
	$res = simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	if ($assoc) $res = json_decode(json_encode($res), true);
	return $res;
}

//将对象/数组进行urlencode
function url_encode($obj) {
	$o = deep_clone($obj);
	if (is_array($o)) {
		foreach ($o as $key => $value) $o[urlencode($key)] = url_encode($value);
	} else if (is_object($o)) {
		foreach ($o as $name => $value) $o->{$name} = url_encode($o->{$name});
	} else if (is_string($o)) {
		$o = urlencode($o);
	}
	return $o;
}

//解决序列化字段中有中文导致出错
function unserialize_mb($str) {
	$str = preg_replace_callback('#s:(\d+):"(.*?)";#s', function($match) {
		return 's:' . strlen($match[2]) . ':"' . $match[2] . '";';
	}, $str);
	return unserialize($str);
}

//深度复制(防止PHP直接赋值到新变量后仍然指向同一内存指针)
function deep_clone($obj) {
	if (is_array($obj)) {
		$o = array();
		foreach ($obj as $key => $value) $o[urlencode($key)] = unserialize_mb(serialize($value));
	} else if (is_object($obj)) {
		$o = new stdClass();
		foreach ($obj as $name => $value) $o->{$name} = unserialize_mb(serialize($obj->{$name}));
	} else {
		$o = unserialize_mb(serialize($obj));
	}
	return $o;
}

//判断字符串包含中英文, 返回0全英文或数字或下划线, 1全中文, 2中英混合
function is_en_cn($str) {
	$allen = preg_match('/^\w+$/', $str); //判断是否是英文或数字或下划线
	$allcn = preg_match('/^[\x7f-\xff]+$/', $str); //判断是否是中文
	if ($allen) {
		return 0;
	} else if ($allcn) {
		return 1;
	} else {
		return 2;
	}
}

//判断移动端浏览器打开
function is_mobile_web() {
	if (isset($_SERVER['HTTP_USER_AGENT'])) {
		$keywords = array(
			'nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'blackberry',
			'meizu', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile',
			'smartphone', 'windows ce', 'windows phone', 'ipod', 'iphone', 'ipad', 'android'
		);
		if (preg_match('/('.implode('|', $keywords).')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) return true;
	}
	return false;
}

//验证手机号
function is_mobile($mobile) {
	return preg_match('/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[03678]{1}\d{8}$|^18[\d]{9}$/', $mobile) ? true : false;
}

//验证座机
function is_tel($tel) {
	return preg_match('/^((\d{3,4}-)?\d{8}(-\d+)?|(\(\d{3,4}\))?\d{8}(-\d+)?)$/', $tel) ? true : false;
}

//验证电话号码(包括手机号与座机)
function is_phone($phone) {
	$result = is_mobile($phone);
	if (!$result) $result = is_tel($phone);
	return $result;
}

//验证邮箱
function is_email($email) {
	return preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $email) ? true : false;
}

//验证日期
function is_date($date) {
	return preg_match('/^(?:(?!0000)[0-9]{4}[\/-](?:(?:0?[1-9]|1[0-2])[\/-](?:0?[1-9]|1[0-9]|2[0-8])|(?:0?[13-9]|1[0-2])[\/-](?:29|30)|(?:0?[13578]|1[02])[\/-]31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)[\/-]0?2[\/-]29)$/', $date) ? true : false;
}

//严格验证身份证
function is_idcard($idcard) {
	$idcard_verify_number = function($idcard_base) {
		//计算身份证校验码, 根据国家标准GB 11643-1999
		if (strlen($idcard_base) != 17) return false;
		$factor = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2); //加权因子
		$verify_number_list = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2'); //校验码对应值
		$checksum = 0;
		for ($i=0; $i<strlen($idcard_base); $i++) {
			$checksum += substr($idcard_base, $i, 1) * $factor[$i];
		}
		$mod = $checksum % 11;
		$verify_number = $verify_number_list[$mod];
		return $verify_number;
	};
	$idcard_15to18 = function($idcard) use ($idcard_verify_number) {
		//将15位身份证升级到18位
		if (strlen($idcard) != 15) return false;
		//如果身份证顺序码是996 997 998 999, 这些是为百岁以上老人的特殊编码
		if (array_search(substr($idcard, 12, 3), array('996', '997', '998', '999')) !== false) {
			$idcard = substr($idcard, 0, 6) . '18' . substr($idcard, 6, 9);
		} else {
			$idcard = substr($idcard, 0, 6) . '19' . substr($idcard, 6, 9);
		}
		$idcard = $idcard . $idcard_verify_number($idcard);
		return $idcard;
	};
	$idcard_checksum18 = function($idcard) use ($idcard_verify_number) {
		//18位身份证校验码有效性检查
		if (strlen($idcard) != 18) return false;
		$idcard_base = substr($idcard, 0, 17);
		if ($idcard_verify_number($idcard_base) != strtoupper(substr($idcard, 17, 1))) {
			return false;
		} else {
			return true;
		}
	};
	if (strlen($idcard) == 18) {
		return $idcard_checksum18($idcard);
	} else if (strlen($idcard) == 15) {
		$idcard = $idcard_15to18($idcard);
		return $idcard_checksum18($idcard);
	} else {
		return false;
	}
}

//是否图片文件
function is_image($path) {
	if (!strlen($path)) return false;
	if (strpos($path, 'http://') === false && strpos($path, 'https://') === false && !file_exists($path)) return false;
	$fs = fopen($path, 'rb');
	$byte = fread($fs, 2);
	fclose($fs);
	$info = @unpack('C2chars', $byte);
	$code = intval($info['chars1'].$info['chars2']);
	switch ($code) {
		case 255216: //jpg
		case 7173: //gif
		case 6677: //bmp
		case 13780:$is_image = true;break; //png
		default:$is_image = false;
	}
	return $is_image;
}

//生成随机字母、数字
function random_str($length=4, $factor=array()) {
	//生成一个包含 大写英文字母, 小写英文字母, 数字 的数组
	if (count($factor)) {
		$arr = $factor;
	} else {
		$arr = array_merge(range(0, 9), range('a', 'z'), range('A', 'Z'));
	}
	//$string = str_shuffle(implode('', $arr)); //随机打乱字符串
	$str = '';
	$arr_len = count($arr);
	for ($i=0; $i<$length; $i++) {
		$rand = mt_rand(0, $arr_len-1);
		$str .= $arr[$rand];
	}
	return $str;
}

//随机范围内整数
function random_num($min=0, $max=PHP_INT_MAX) {
	return mt_rand($min, $max);
}

//随机范围内小数
function random_float($min=0, $max=1, $length=2) {
	$num =  $min + mt_rand() / mt_getrandmax() * ($max - $min);
	return substr(strval($num), 0, $length+2);
}

//随机指定数量的范围内不重复数字, 可包含小数, restrict:强制包含小数
function random_norepeat_num($count, $min=0, $max=PHP_INT_MAX, $is_sort=false, $min_float=0, $max_float=0, $length_float=2, $restrict=false) {
	$result = array();
	while (count($result) < $count) {
		$num = mt_rand($min, $max);
		if ($min_float != $max_float) {
			if ($restrict) {
				$num = bcadd($num, random_float($min_float, $max_float, $length_float), $length_float);
			} else {
				if (in_array($num, $result)) $num = bcadd($num, random_float($min_float, $max_float, $length_float), $length_float);
			}
		}
		$result[] = strval($num);
		//数组去重
		//$result = array_unique($result); //效率较低
		$result = array_flip($result);
		$result = array_flip($result);
	}
	if ($is_sort) sort($result);
	return $result;
}

//写log文件
function write_log($content='', $file='', $trace=false, $echo=false) {
	global $client_id;
	if (!strlen($file)) $file = '/temp/log.txt';
	if (defined('IS_SAAS') && IS_SAAS && defined('IS_AG') && defined('IS_OP') && !IS_AG && !IS_OP && isset($client_id) && intval($client_id)>0) $file = '/temp/file/'.$client_id.'/log.txt';
	$filename = ROOT_PATH . str_replace(ROOT_PATH, '', $file);
	$traceStr = '';
	if ($trace) {
		$e = new Exception;
		$trace = $e->getTraceAsString();
		$traceStr = "\n\n" . $trace;
	}
	if (is_array($content) || is_object($content)) $content = json_encode($content, JSON_UNESCAPED_UNICODE);
	file_put_contents($filename, date('Y-m-d H:i:s') . PHP_EOL . $content . $traceStr . PHP_EOL . (is_bool($echo)?'==============================':'') . PHP_EOL . PHP_EOL, FILE_APPEND);
	if (is_bool($echo) && $echo) echo $content . '<br />';
}
//写error文件
function write_error($content='', $file='', $trace=false, $echo=false) {
	write_log($content, strlen($file) ? $file : (defined('ERROR_FILE')?ERROR_FILE:'/temp/error.txt'), $trace, $echo);
}

//批量unset, fields可为数组或空格分割的字符串
function unsets(&$obj, $fields) {
	if (is_string($fields)) $fields = explode(' ', $fields);
	if (is_array($obj)) {
		if (array_values($obj) === $obj) { //数字索引数组
			for ($i=0; $i<count($fields); $i++) {
				foreach ($obj as $j => $o) {
					if ($fields[$i] === $o) {
						unset($obj[$j]);
						break;
					}
				}
			}
			$obj = array_values($obj);
		} else {
			foreach ($fields as $field) {
				if (isset($obj[$field])) unset($obj[$field]);
				else {
					foreach ($obj as $k => $g) unsets($obj[$k], $fields);
				}
			}
		}
	} else if (is_object($obj)) {
		foreach ($fields as $field) {
			if (isset($obj->{$field})) unset($obj->{$field});
			else {
				foreach ($obj as $k => $g) unsets($g, $fields);
			}
		}
	} else {
		unset($obj);
	}
}

//跳转网址后终止
function location($url) {
	if (substr(strtolower(trim($url)), 0, 9) == 'location:') $url = substr(trim($url), 9);
	header("Location:{$url}");
	exit;
}

//原样输出
function debug($obj) {
	if (IS_API) {
		echo json_encode($obj);
	} else {
		echo '<pre>';
		echo print_r($obj, true);
		echo '</pre>';
	}
	exit;
}

//图片转base64
function image_base64($file) {
	if (stripos($file, 'http') === false) $file = ROOT_PATH.str_replace(ROOT_PATH, '', $file);
	$info = getimagesize($file);
	$data = file_get_contents($file);
	return 'data:' . $info['mime'] . ';base64,' . base64_encode($data);
}

//图片压缩, percent缩放百分比, saveName保存图片名(没有后缀就用源图后缀)用于保存, 为空即直接显示
function image_compress($file, $percent=1, $saveName='') {
	if (preg_match('/^https?:\/\//', $file)) exit('ONLY COMPRESS LOCAL IMAGES');
	$file = ROOT_PATH . str_replace(ROOT_PATH, '', $file);
	if (!file_exists($file)) exit('IMAGE IS NOT EXIST');
	$fs = fopen($file, 'rb');
	$byte = fread($fs, 2);
	fclose($fs);
	$info = @unpack('C2chars', $byte);
	$code = intval($info['chars1'].$info['chars2']);
	if (!in_array($code, array(255216, 7173, 6677, 13780))) exit('FILE IS NOT A IMAGE');
	$imageInfo = getimagesize($file);
	$imageInfo = ['width'=>$imageInfo[0], 'height'=>$imageInfo[1], 'type'=>image_type_to_extension($imageInfo[2], false)];
	$fun = 'imagecreatefrom' . $imageInfo['type'];
	$image = $fun($file);
	$new_width = $imageInfo['width'] * $percent;
	$new_height = $imageInfo['height'] * $percent;
	$image_tmp = imagecreatetruecolor($new_width, $new_height);
	imagecopyresampled($image_tmp, $image, 0, 0, 0, 0, $new_width, $new_height, $imageInfo['width'], $imageInfo['height']);
	imagedestroy($image);
	$image = $image_tmp;
	if (strval($saveName)) $saveName = ROOT_PATH . str_replace(ROOT_PATH, '', $saveName);
	if (strval($saveName)) {
		if (file_exists($saveName)) unlink($saveName);
		$allowImg = ['.jpg', '.jpeg', '.png', '.bmp', '.gif', '.wbmp'];
		$dstExt = strrchr($saveName, '.');
		$sourceExt = strrchr($file, '.');
		if (strval($dstExt)) $dstExt = strtolower($dstExt);
		if (strval($sourceExt)) $sourceExt = strtolower($sourceExt);
		if (strval($dstExt) && in_array($dstExt, $allowImg)) {
			$dstName = $saveName;
		} else if (strval($sourceExt) && in_array($sourceExt, $allowImg)) {
			$dstName = $saveName . $sourceExt;
		} else {
			$dstName = $saveName . $imageInfo['type'];
		}
		$func = 'image' . $imageInfo['type'];
		$quality = 75;
		if ($func == 'imagejpeg') {
			$func($image, $dstName, round($quality));
		} else if ($func == 'imagepng') {
			$func($image, $dstName, round(9*$quality/100));
		} else {
			$func($image, $dstName);
		}
		imagedestroy($image);
	} else {
		header('Content-Type: image/' . $imageInfo['type']);
		$func = 'image' . $imageInfo['type'];
		$func($image);
		imagedestroy($image);
		exit;
	}
}

//解析二维码
function qrcode_decode($url, $type='cli') {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	switch ($type) {
		case 'zxing':
			$target_url = 'https://zxing.org/w/decode?u=' . urlencode($url);
			curl_setopt($ch, CURLOPT_URL, $target_url);
			$html = curl_exec($ch);
			curl_close($ch);
			if (strpos($html, '<table id="result">') === false) error('UNABLE TO SCAN THE CONTENT OF THIS IMAGE');
			preg_match("/<table id=\"result\">(.*)<\/table>/isU", $html, $mather);
			if (!is_array($mather)) error('UNABLE TO SCAN THE CONTENT OF THIS IMAGE');
			preg_match("/<pre>(.*)<\/pre>/isU", $mather[1], $arr);
			return $arr[1];
		default:
			$target_url = 'https://cli.im/apis/up/deqrimg';
			curl_setopt($ch, CURLOPT_URL, $target_url);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('x-requested-with: XMLHttpRequest'));
			curl_setopt($ch, CURLOPT_POSTFIELDS, 'img=' . urlencode($url));
			$html = curl_exec($ch);
			curl_close($ch);
			$data = json_decode($html, true);
			if (is_null($data)) error('UNABLE TO SCAN THE CONTENT OF THIS IMAGE');
			if (intval($data['status']) == 0) error($data['info']);
			return $data['info']['data'][0];
	}
}

//合并图片
/*$poster = merge_image(array(
	'bg'=>'/images/poster.jpg',
	'images'=>array(
		array('image'=>$avatar, 'left'=>30, 'top'=>568, 'width'=>68, 'height'=>68),
		array('image'=>$qrcode, 'left'=>184, 'top'=>780, 'width'=>270, 'height'=>270)
	)
), false);*/
function merge_image($options=array(), $show=true) {
	$bg = $options['bg']; //背景
	if (!strlen($bg)) return NULL;
	$default = array('left' => 0, 'top' => 0, 'width' => 100, 'height' => 100, 'opacity' => 100);
	if (strpos($bg, 'http') === false) $bg = ROOT_PATH . $bg;
	if (class_exists('Imagick')) {
		$im = new Imagick($bg);
		$bgWidth = $im->getImageWidth();
		$bgHeight = $im->getImageHeight();
		if (isset($options['images']) && is_array($options['images'])) {
			foreach ($options['images'] as $val) {
				$g = array_merge($default, $val);
				$g['left'] = $g['left'] < 0 ? $bgWidth-abs($g['left'])-$g['width'] : $g['left'];
				$g['top'] = $g['top'] < 0 ? $bgHeight-abs($g['top'])-$g['height'] : $g['top'];
				$imagePath = $g['image'];
				if (!strlen($imagePath)) continue;
				if (strpos($imagePath, 'http') === false && (!isset($g['stream']) || !$g['stream'])) $imagePath = ROOT_PATH . $imagePath;
				$image = new Imagick($imagePath);
				$image->thumbnailImage($g['width'], $g['height']);
				$im->setImageCompressionQuality(100);
				$im->compositeImage($image, Imagick::COMPOSITE_OVER, $g['left'], $g['top']);
				$image->destroy();
			}
		}
		$dir = UPLOAD_PATH . '/' . date('Y') . '/' . date('m') . '/' . date('d');
		$upload_dir = ROOT_PATH;
		if (!is_dir($upload_dir.$dir)) makedir($upload_dir.$dir);
		$ext = 'png';
		switch ($im->getImageMimeType()) {
			case 'image/jpeg':$ext = 'jpg';break;
			case 'image/gif':$ext = 'gif';break;
			case 'image/png':$ext = 'png';break;
		}
		$filename = $dir . '/' . generate_sn() . '.' . $ext;
		if (!$show) {
			$im->writeImage($upload_dir . $filename);
		} else {
			header('Content-Type: image/png');
			echo $im;
		}
		$im->destroy();
		return $filename;
	} else {
		$bgInfo = getimagesize($bg);
		$bgFn = 'imagecreatefrom' . image_type_to_extension($bgInfo[2], false);
		$bg = $bgFn($bg);
		$bgWidth = imagesx($bg);
		$bgHeight = imagesy($bg);
		$res = imagecreatetruecolor($bgWidth, $bgHeight);
		$color = imagecolorallocate($res, 1000, 1000, 1000);//此处3个1000可以使背景设为白色，3个255可以使背景变成透明色
		imagefill($res, 0, 0, $color);
		imagecopyresampled($res, $bg, 0, 0, 0, 0, $bgWidth, $bgHeight, $bgWidth, $bgHeight);
		imagedestroy($bg);
		//叠加图片
		if (isset($options['images']) && is_array($options['images'])) {
			foreach ($options['images'] as $val) {
				$g = array_merge($default, $val);
				$imagePath = $g['image'];
				if (!strlen($imagePath)) continue;
				if (strpos($imagePath, 'http') === false && (!isset($g['stream']) || !$g['stream'])) $imagePath = ROOT_PATH . $imagePath;
				if (isset($g['stream']) && $g['stream']) { //如果传的是字符串图像流,例如file_get_contents获取的
					$info = getimagesizefromstring($imagePath);
					$fn = 'imagecreatefromstring';
				} else {
					$info = getimagesize($imagePath);
					$fn = 'imagecreatefrom'.image_type_to_extension($info[2], false);
				}
				$imageRes = $fn($imagePath);
				$width = $info[0];
				$height = $info[1];
				//建立画板, 缩放图片至指定尺寸
				$canvas = imagecreatetruecolor($g['width'], $g['height']);
				//关键函数, 参数(目标资源, 源, 目标资源的开始坐标x,y, 源资源的开始坐标x,y, 目标资源的宽高w,h, 源资源的宽高w,h)
				imagecopyresampled($canvas, $imageRes, 0, 0, 0, 0, $g['width'], $g['height'], $width, $height);
				$g['left'] = $g['left'] < 0 ? $bgWidth-abs($g['left'])-$g['width'] : $g['left'];
				$g['top'] = $g['top'] < 0 ? $bgHeight-abs($g['top'])-$g['height'] : $g['top'];
				imagedestroy($imageRes);
				//合并图像
				imagecopymerge($res, $canvas, $g['left'], $g['top'], 0, 0, $g['width'], $g['height'], $g['opacity']);
				imagedestroy($canvas);
			}
		}
		//生成图片
		$fn = 'image'.image_type_to_extension($bgInfo[2], false);
		if (!$show) {
			$dir = UPLOAD_PATH . '/' . date('Y') . '/' . date('m') . '/' . date('d');
			$upload_dir = ROOT_PATH . $dir;
			if (!is_dir($upload_dir)) makedir($upload_dir);
			$ext = 'png';
			switch (intval($bgInfo[2])) {
				case 2:$ext = 'jpg';break;
				case 1:$ext = 'gif';break;
				case 6:$ext = 'bmp';break;
				case 3:$ext = 'png';break;
			}
			$filename = $dir . '/' . generate_sn() . '.' . $ext;
			$result = $fn($res, ROOT_PATH.$filename);
			imagedestroy($res);
			if (!$result) return '';
			return $filename;
		} else {
			header('content-type: image/png');
			$fn($res);
			imagedestroy($res);
		}
	}
	return '';
}

//输出script
function script($msg='', $url='') {
	$html = '<meta charset="UTF-8"><script>';
	if (strlen($msg)) $html .= "alert('{$msg}');";
	if (strlen($url)) {
		if (substr($url, 0, 11) == 'javascript:') {
			$html .= substr($url, 11);
		} else if (substr($url, 0, 3) == 'js:') {
			$html .= substr($url, 3);
		} else if (stripos($url, 'commit') !== false) {
			$html .= 'history.go(-2);';
		} else {
			$html .= "location.href = '{$url}';";
		}
	}
	$html .= '</script>';
	exit($html);
}
function historyBack($msg='') {
	script($msg, 'javascript:history.back()');
}

//rewrite转换
function rewrite_change($qs='') {
	$qs = trim($qs, '?');
	if (strlen($qs)) {
		$GET = array();
		$rule = explode('&', $qs);
		for ($i=0; $i<count($rule); $i++) {
			$r = $rule[$i];
			if (!strlen($r) || strpos($r, '=') === false) continue;
			$key = substr($r, 0, strpos($r, '='));
			$value = substr($r, strpos($r, '=')+1);
			if (strlen($value)) {
				if ($key == '_param') {
					$rl = explode('/', trim($value, '/'));
					for ($j=0; $j<count($rl); $j+=2) {
						if ($j+1 >= count($rl) || !isset($rl[$j+1])) break;
						$GET[$rl[$j]] = urldecode($rl[$j+1]);
					}
				} else {
					$GET[$key] = urldecode($value);
				}
			}
		}
		if (count($GET)) $_GET = $GET;
	} else {
		$uri = $_SERVER['REQUEST_URI'];
		if (strpos($uri, '.php') !== false) return $_GET;
		if (strpos($uri, '?') !== false) { //api/goods/detail?id=348
			$u = substr($uri, strpos($uri, '?')+1);
			if (strlen($u)) {
				$rule = explode('&', $u);
				for ($i=0; $i<count($rule); $i++) {
					$r = $rule[$i];
					if (!strlen($r) || strpos($r, '=') === false) continue;
					$key = substr($r, 0, strpos($r, '='));
					$value = substr($r, strpos($r, '=')+1);
					if (strlen($value)) {
						$_GET[$key] = urldecode($value);
					}
				}
			}
		} else { //api/goods/detail/id/348
			$qs = explode('&', $_SERVER['QUERY_STRING']);
			if (substr(end($qs), 0, 1) == '/') {
				array_pop($_GET);
				$rule = explode('/', trim(end($qs), '/'));
				for ($i=0; $i<count($rule); $i+=2) {
					if ($i+1 >= count($rule) || !isset($rule[$i+1])) break;
					$_GET[$rule[$i]] = urldecode($rule[$i+1]);
				}
			}
		}
		if (isset($_GET['app'])) $_GET['app'] = str_replace('.', '_', $_GET['app']);
		if (isset($_GET['act'])) $_GET['act'] = str_replace('.', '_', $_GET['act']);
	}
	$_REQUEST = array_merge($_GET, $_POST);
	return $_GET;
}

//加密网址参数
//$crypt_key = 'MARIO_@AES_@20200604';
//echo encrypt_param('id=1&page=5&timeout='.time(), $crypt_key);
//http://localhost/11.php?AmwCZV5tUDANLFImUDMJPQRhBDQCYQAiUyFXagU7UDEFbVchXiQDbwA2VmdSYgNiVWQFOQRhCzpTbAdi
//getParams($_SERVER['QUERY_STRING'], $crypt_key);
//echo json_encode($_GET);
function encrypt_param($str, $key, $secret='', $secretIndex=10) {
	$encrypt_key = md5(mt_rand(0, 100));
	$ctr = 0;
	$tmp = '';
	for ($i=0; $i<strlen($str); $i++) {
		if ($ctr == strlen($encrypt_key)) $ctr = 0;
		$tmp .= substr($encrypt_key, $ctr, 1) . (substr($str, $i, 1) ^ substr($encrypt_key, $ctr, 1));
		$ctr++;
	}
	$encrypt_key = md5($key);
	$ctr = 0;
	$result = '';
	for ($i=0; $i<strlen($tmp); $i++) {
		if ($ctr == strlen($encrypt_key)) $ctr = 0;
		$result .= substr($tmp, $i, 1) ^ substr($encrypt_key, $ctr, 1);
		$ctr++;
	}
	$result = base64_encode($result);
	$result = str_replace('/', '|', $result);
	$result = rawurlencode($result);
	if (strlen($secret)) {
		$pages = str_split(strtoupper($secret));
		$secretCount = count($pages);
		$index = $secretIndex;
		$page = substr($result, 0, $index);
		for ($i=0; $i<count($pages); $i++) {
			if ($i<count($pages)-1) {
				$page .= $pages[$i] . substr($result, $index*($i+1), $index);
			} else {
				$page .= $pages[$i] . substr($result, $index*($i+1));
			}
		}
		$result = "{$page}_{$secretCount}";
	}
	return $result;
}
//解密网址参数
function decrypt_param($str, $key, $secretCount=0, $secretIndex=10) {
	$secret = '';
	if ($secretCount>0) {
		$index = $secretIndex;
		$page = '';
		for ($i=0; $i<$secretCount; $i++) {
			$page .= substr($str, 0, $index);
			$secret .= substr($str, $index, 1);
			$str = substr($str, $index+1);
			if ($i == $secretCount-1) $page .= $str;
		}
		$str = $page;
		$secret = strtolower($secret);
	}
	$decode = rawurldecode($str);
	$decode = str_replace('|', '/', $decode);
	$decode = base64_decode($decode);
	$encrypt_key = md5($key);
	$ctr = 0;
	$tmp = '';
	for ($i=0; $i<strlen($decode); $i++) {
		if ($ctr == strlen($encrypt_key)) $ctr = 0;
		$tmp .= substr($decode, $i, 1) ^ substr($encrypt_key, $ctr, 1);
		$ctr++;
	}
	$result = '';
	for ($i=0; $i<strlen($tmp); $i++) {
		$md5 = substr($tmp, $i, 1);
		$i++;
		$result .= substr($tmp, $i, 1) ^ $md5;
	}
	return array('result'=>$result, 'secret'=>$secret);
}

//签名, $sign参数为 提供商 验证用
//1.参数按key=value格式自然排序后转http_build_query(注：网址类型需编码)
//2.把结果MD5后从第6位开始截取18位，拼接上appSecret
//3.把结果MD5后从第10位开始截取16位，得到最终签名
function sign($appId, $appSecret, $timestamp, $data=array(), $sign='') {
	$param = array('appid'=>$appId, 'timestamp'=>$timestamp);
	$param = array_merge($param, $data);
	ksort($param);
	$res = substr(md5(substr(md5(http_build_query($param)), 6, 18) . $appSecret), 10, 16);
	if (strlen($sign)) return $res === $sign;
	return $res;
}

//获取表单数据, 兼容普通提交、RSA提交
function getData() {
	global $tbp;
	if (defined('RSA_POST') && RSA_POST && defined('IS_POST') && IS_POST) {
		$data = file_get_contents('php://input');
		if (strlen($data)) {
			$_POST = array();
			if (strlen($data) >= 684 && strpos($data, '&') === false && substr($data, strlen($data)-1) == '=') {
				$GLOBALS['isRSA'] = true;
				$rsa = new Rsa(SDK_PATH . '/class/encrypt/keys', "{$tbp}private", "{$tbp}public");
				$data = $rsa->privDecode($data);
			}
			$json = json_decode($data, true);
			if (is_null($json)) {
				$arr = explode('&', $data);
				foreach ($arr as $g) {
					$gs = explode('=', $g);
					if (count($gs)>1) $_POST[$gs[0]] = $gs[1];
				}
			} else {
				foreach ($json as $key => $val) $_POST[$key] = $val;
			}
		}
	}
	return $_POST;
}

//CURL方式请求
function requestCurl($method, $url, $params=array(), $returnJson=false, $postJson=false, $headers=array(), $getHeader=false) {
	set_time_limit(0);
	ini_set('memory_limit', '10240M');
	$method = strtoupper($method);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60*60); //请求超时
	curl_setopt($ch, CURLOPT_TIMEOUT, 60*60); //执行超时
	if ($getHeader) curl_setopt($ch, CURLOPT_HEADER, 1);
	if (isset($_SERVER['HTTP_USER_AGENT'])) curl_setopt($ch, CURLOPT_USERAGENT, implode(' ', array_filter(array($_SERVER['HTTP_USER_AGENT'], 'SDK/'.API_VERSION.' PHP/'.PHP_VERSION))));
	switch ($method) {
		case 'POST':curl_setopt($ch, CURLOPT_POST, 1);break;
		case 'PUT':
		case 'PATCH':
		case 'DELETE':curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);break;
		default:curl_setopt($ch, CURLOPT_HTTPGET, 1);break;
	}
	if (is_array($headers) && count($headers)) {
		$headers[] = "X-HTTP-Method-Override: {$method}"; //HTTP头信息
	} else {
		$headers = array("X-HTTP-Method-Override: {$method}");
	}
	if (is_array($headers) && count($headers)) {
		//使用JSON提交
		if ($postJson) {
			$headers[] = 'Content-type: application/json;charset=UTF-8';
			if (!empty($params) && is_array($params)) $params = json_encode($params, JSON_UNESCAPED_UNICODE);
		}
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	if (substr($url, 0, 8) == 'https://') {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
		//curl_setopt($ch, CURLOPT_SSLVERSION, 3); //SSL版本
	}
	if (!empty($params)) {
		if (is_array($params)) {
			if (class_exists('\CURLFile')) {
				foreach ($params as $key => $param) {
					if (is_string($param) && preg_match('/^@/', $param)) $params[$key] = new CURLFile(realpath(trim($param, '@')));
				}
			} else {
				if (defined('CURLOPT_SAFE_UPLOAD')) curl_setopt($ch, CURLOPT_SAFE_UPLOAD, 0); //指定PHP5.5及以上兼容@语法,否则需要使用CURLFile
			}
		}
		//如果data为数组即使用multipart/form-data提交, 为字符串即使用application/x-www-form-urlencoded
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	}
	$res = curl_exec($ch);
	if ($getHeader) exit($res);
	$header_info = curl_getinfo($ch); //获取请求头信息
	if (intval($header_info['http_code']) == 301 || intval($header_info['http_code']) == 302) {
		$url = $header_info['redirect_url'];
		$fn = __FUNCTION__;
		$res = $fn($method, $url, $params, false, $postJson, $headers, $getHeader);
	}
	$result = $res;
	if ($res === false) {
		echo 'Curl error: ' . curl_error($ch);
		exit;
	}
	curl_close($ch);
	if ($returnJson) {
		$res = json_decode($res, true);
		if (is_null($res)) $res = NULL;
	}
	if (is_null($res)) write_log(print_r($result, true));
	return $res;
}
function requestData($method, $url, $params=array(), $returnJson=false, $postJson=false, $headers=array(), $getHeader=false) {
	set_time_limit(0);
	ini_set('memory_limit', '10240M');
	$method = strtoupper($method);
	$urls = is_array($url) ? array_merge(array(), $url) : array($url);
	$ch = array();
	$res = array();
	$cm = curl_multi_init(); //创建批处理cURL句柄
	foreach ($urls as $k => $_url) {
		$_params = is_array($params) ? array_merge(array(), $params) : array();
		$_headers = is_array($headers) ? array_merge(array(), $headers) : array();
		$ch[$k] = curl_init();
		curl_setopt($ch[$k], CURLOPT_URL, $_url);
		curl_setopt($ch[$k], CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch[$k], CURLOPT_CONNECTTIMEOUT, 60*60); //请求超时
		curl_setopt($ch[$k], CURLOPT_TIMEOUT, 60*60); //执行超时
		if ($getHeader) curl_setopt($ch[$k], CURLOPT_HEADER, 1);
		if (isset($_SERVER['HTTP_USER_AGENT'])) curl_setopt($ch[$k], CURLOPT_USERAGENT, implode(' ', array_filter(array($_SERVER['HTTP_USER_AGENT'], 'SDK/'.API_VERSION.' PHP/'.PHP_VERSION))));
		switch ($method) { //请求方式
			case 'POST':curl_setopt($ch[$k], CURLOPT_POST, 1);break;
			case 'PUT':
			case 'PATCH':
			case 'DELETE':curl_setopt($ch[$k], CURLOPT_CUSTOMREQUEST, $method);break;
			default:curl_setopt($ch[$k], CURLOPT_HTTPGET, 1);break;
		}
		if (is_array($headers) && count($headers)) {
			$_headers[] = "X-HTTP-Method-Override: {$method}"; //HTTP头信息
		} else {
			$_headers = array("X-HTTP-Method-Override: {$method}");
		}
		if (is_array($headers) && count($headers)) {
			//使用JSON提交
			if ($postJson) {
				$_headers[] = 'Content-type: application/json;charset=UTF-8';
				if (!empty($params) && is_array($_params)) $_params = json_encode($_params, JSON_UNESCAPED_UNICODE);
			}
			curl_setopt($ch[$k], CURLOPT_HTTPHEADER, $_headers);
		}
		if (substr($_url, 0, 8) == 'https://') {
			curl_setopt($ch[$k], CURLOPT_SSL_VERIFYPEER, 0); //对认证证书来源的检查
			curl_setopt($ch[$k], CURLOPT_SSL_VERIFYHOST, 0); //从证书中检查SSL加密算法是否存在
			//curl_setopt($ch, CURLOPT_SSLVERSION, 3); //SSL版本
		}
		if (!empty($params)) {
			if (is_array($_params)) {
				if (class_exists('\CURLFile')) {
					foreach ($_params as $key => $param) {
						if (is_string($param) && preg_match('/^@/', $param)) $_params[$key] = new CURLFile(realpath(trim($param, '@')));
					}
				} else {
					if (defined('CURLOPT_SAFE_UPLOAD')) curl_setopt($ch[$k], CURLOPT_SAFE_UPLOAD, 0); //指定PHP5.5及以上兼容@语法,否则需要使用CURLFile
				}
			}
			//如果data为数组即使用multipart/form-data提交, 为字符串即使用application/x-www-form-urlencoded
			curl_setopt($ch[$k], CURLOPT_POSTFIELDS, $_params);
		}
		//附加 Authorization: Basic <Base64(id:key)>
		//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		//curl_setopt($ch, CURLOPT_USERPWD, "api:{$key}");
		curl_multi_add_handle($cm, $ch[$k]); //加入多处理句柄
	}
	$active = NULL; //连接数
	do { //防卡死写法,执行批处理句柄
		//这里$active会被改写成当前未处理数,全部处理成功$active会变成0
		$mrc = curl_multi_exec($cm, $active);
		//这个循环的目的是尽可能的读写，直到无法继续读写为止(返回CURLM_OK)
		//返回(CURLM_CALL_MULTI_PERFORM)就表示还能继续向网络读写
	} while ($mrc == CURLM_CALL_MULTI_PERFORM);
	//curl_multi_select的作用在等待过程中，如果有就返回目前可以读写的句柄数量,以便
	//继续读写操作,0则没有可以读写的句柄(完成了)
	while ($active && $mrc == CURLM_OK) { //直到出错或者全部读写完毕
		while (curl_multi_exec($cm, $active) === CURLM_CALL_MULTI_PERFORM);
		if (curl_multi_select($cm) != -1) {
			do {
				$mrc = curl_multi_exec($cm, $active);
			} while ($mrc == CURLM_CALL_MULTI_PERFORM);
		}
	}
	foreach ($urls as $k => $_url) {
		//$info = curl_multi_info_read($cm); //获取当前解析的cURL的相关传输信息
		$res[$k] = curl_multi_getcontent($ch[$k]); //获取输出的文本流
		if ($getHeader) exit($res[$k]);
		$header_info = curl_getinfo($ch[$k]); //获取请求头信息
		if (intval($header_info['http_code']) == 301 || intval($header_info['http_code']) == 302) {
			$_url = $header_info['redirect_url'];
			$fn = __FUNCTION__;
			$res[$k] = $fn($method, $_url, $params, false, $postJson, $headers, $getHeader);
		}
		$result = $res[$k];
		if ($res[$k] === false) {
			echo 'Curl error: ' . curl_error($ch[$k]);
			exit;
		}
		curl_multi_remove_handle($cm, $ch[$k]); //移除curl批处理句柄资源中的某个句柄资源
		curl_close($ch[$k]);
		if ($returnJson) {
			$res[$k] = is_array($res[$k]) ? $res[$k] : json_decode($res[$k], true);
			if (is_null($res[$k])) $res[$k] = NULL;
		}
		if (is_null($res[$k])) write_log(print_r($result, true));
	}
	curl_multi_close($cm);
	if (is_array($url)) {
		return $res;
	} else {
		$values = array_values($res);
		return $values[0];
	}
}

//异步调用PHP, 例如处理完客户端需要的数据就返回, 再调用该函数异步在服务器执行耗时的操作
function requestAsync($method, $url, $param=array(), $header=array()) {
	//当执行过程中,客户端连接断开或连接超时,都会有可能造成执行不完整,因此目标网址程序需要加上
	//ini_set('ignore_user_abort', true);
	//ignore_user_abort(true); //忽略客户端断开
	//set_time_limit(0); //设置执行不超时
	$method = strtoupper($method);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 1);
	if ($method == 'POST') {
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, empty($param) ? '' : http_build_query($param));
	}
	if (is_array($header)) {
		$headers = array('Content-type: application/x-www-form-urlencoded');
		foreach ($header as $h) $headers[] = $h;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	}
	if (substr($url, 0, 8) == 'https://') {
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	}
	curl_exec($ch);
	curl_close($ch);
}

//获取redis
function redisd($host='127.0.0.1', $port=6379) {
	//https://www.cnblogs.com/peteremperor/p/6635778.html
	//命令 /usr/local/redis/bin/redis-cli
	//查看所有key(keys *)
	//获取指定key的值(get KEY)
	//清空(flushall)
	if (!class_exists('Redis')) exit('MISSING CLASS REDIS');
	$instance = new Redis();
	$instance->connect($host, $port);
	return $instance;
}

//获取memcached
function mcached($host='127.0.0.1', $port=11211) {
	if (!class_exists('Memcached')) exit('MISSING CLASS MEMCACHED');
	$instance = new Memcached();
	if (method_exists($instance, 'connect')) $connect = 'connect';
	else $connect = 'addServer';
	$instance->{$connect}($host, $port);
	return $instance;
}

//404错误
function error404() {
	header('HTTP/1.1 404 Not Found.', TRUE, 404);
	exit(1);
}

//503错误
function error503() {
	header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
	exit(1);
}

//友好提示
function error_tip($tips='') {
	$icon = $iconStyle = $textStyle = '';
	$iconWidth = $iconHeight = '1.5rem';
	$bgColor = '#ebfaff';
	$countDown = true;
	if (is_array($tips)) {
		if (isset($tips['icon'])) $icon = $tips['icon'];
		if (isset($tips['iconWidth'])) $iconWidth = $tips['iconWidth'];
		if (isset($tips['iconHeight'])) $iconHeight = $tips['iconHeight'];
		if (isset($tips['bgColor'])) $bgColor = $tips['bgColor'];
		if (isset($tips['iconStyle'])) $iconStyle = $tips['iconStyle'];
		if (isset($tips['textStyle'])) $textStyle = $tips['textStyle'];
		if (isset($tips['countDown'])) $countDown = $tips['countDown'];
		$tips = $tips['tips'];
	}
	if (defined('IS_API') && IS_API) error($tips);
	$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
	$html = '<!doctype html>
<html lang="zh-CN" style="font-size:100px;">
<head>
<meta name="viewport" content="width=320,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=0" />
<meta name="format-detection" content="telephone=no" />
<meta name="format-detection" content="email=no" />
<meta name="format-detection" content="address=no" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta charset="UTF-8">
<title>'.$tips.'</title>
</head>
<body style="background:'.$bgColor.';">
<style>
html, body{height:100%; margin:0; padding:0; position:relative; text-align:center; font-family:Arial; -webkit-font-smoothing:antialiased;}
.tip-view{position:absolute; left:0; top:50%; width:100%; height:auto; margin-top:-0.5rem; -webkit-transform:translateY(-50%); transform:translateY(-50%);}
.tip-view i{display:block; margin:0 auto; width:1.5rem; height:1.5rem; background:no-repeat center center;'.(!strlen($icon)?" background-image:url(\"data:image/png;base64,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\");":'').' background-size:cover;}
.tip-view span{display:block; width:100%; height:0.34rem; line-height:0.34rem; font-size:0.18rem;}
.tip-view font{display:block; width:100%; height:0.2rem; line-height:0.2rem; font-size:0.14rem; color:#ccc;}
.tip-view strong{font-weight:normal;}
</style>
<div class="tip-view">
	<i'.(strlen($icon)?" style=\"background-image:url({$icon});width:{$iconWidth};height:{$iconHeight};{$iconStyle}\"":'').'></i>
	<span'.(strlen($textStyle)?" style=\"{$textStyle}\"":'').'>'.$tips.'</span>
	'.($countDown && (strlen($referer))?'<font>That will be return after at <strong>5</strong>s</font>':'').'
</div>
</body>
</html>';
if (($countDown && strlen($referer))) $html .= PHP_EOL.'<script>
var count = 5, timer = setInterval(function(){
	if(count<=0){
		clearInterval(timer);timer = null;
		history.back();
		return;
	}
	count--;
	let strong = document.getElementsByTagName("strong");
	if(strong.length)strong[0].innerHTML = count;
}, 1000);
</script>';
	echo $html;
	exit;
}
