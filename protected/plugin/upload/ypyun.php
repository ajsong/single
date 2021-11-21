<?php
//又拍云
//https://www.upyun.com
class ypyun extends upload_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function upload($fileObj, $field, $dir='', $name='', $ext='jpg') {
		global $upyun_bucketname, $upyun_operator_name, $upyun_operator_pwd, $upyun_domain, $img_domain;
		if (isset($this->configs['GLOBAL_UPLOAD_KEY'])) {
			$global_key = explode('|', $this->configs['GLOBAL_UPLOAD_KEY']);
			$fields = array();
			foreach ($global_key as $g) {
				$s = explode('：', $g);
				$fields[$s[0]] = $s[1];
			}
			extract($fields);
		}
		if (!strlen($upyun_bucketname) || !strlen($upyun_operator_name) || !strlen($upyun_operator_pwd)) {
			write_log('UPLOAD LOST API KEY', '/temp/upload.txt');
			error('UPLOAD LOST API KEY');
			return false;
		}
		require_once(PLUGIN_PATH . '/upload/upyun/upyun.class.php');
		$fileEle = !$field ? $fileObj : $fileObj[$field];
		$file_array = array('file'=>'', 'width'=>0, 'height'=>0);
		if (is_array($fileEle) && isset($fileEle['type']) && stripos($fileEle['type'],'image/')!==false) {
			$size = getimagesize($fileEle['tmp_name']);
			$file_array['width'] = $size[0];
			$file_array['height'] = $size[1];
		}
		if (strlen($dir)) {
			$dir = trim($dir, '/');
			$file = $dir.'/'.$name.'.'.$ext;
		} else {
			$file = $name.'.'.$ext;
		}
		$upyun = new UpYun($upyun_bucketname, $upyun_operator_name, $upyun_operator_pwd);
		try {
			ini_set('memory_limit', '500M');
			if (is_array($fileEle)) {
				$local_file = isset($fileEle['tmp_name']) ? $fileEle['tmp_name'] : $fileEle[0];
				$fh = fopen($local_file, 'rb');
				$upyun->writeFile($file, $fh, true); //上传文件且自动创建目录
				fclose($fh);
			} else {
				$upyun->writeFile($file, $fileEle, true);
			}
			if (!isset($upyun_domain) || !strlen($upyun_domain)) $upyun_domain = $img_domain;
			if (substr($upyun_domain,-1)!='/') $upyun_domain .= '/';
			if (substr($file, 0, 1)=='/') $file = substr($file, 1);
			$file_array['file'] = $upyun_domain . $file;
		}
		catch (Exception $e) {}
		return $file_array;
	}
	
	public function delete($url) {
		global $upyun_bucketname, $upyun_operator_name, $upyun_operator_pwd, $upyun_domain;
		return true;
	}
}
