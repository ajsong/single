<?php
//搜狗图床
//https://pic.sogou.com/
class sogou extends upload_base {
	public function __construct() {
		parent::__construct();
	}
	
	public function upload($fileObj, $field=NULL, $dir='', $name='', $ext='jpg') {
		$fileEle = !$field ? $fileObj : $fileObj[$field];
		$file_array = array('file'=>'', 'width'=>0, 'height'=>0);
		$is_image = false;
		if (is_array($fileEle) && isset($fileEle['type']) && stripos($fileEle['type'],'image/')!==false) {
			$is_image = true;
			$size = getimagesize($fileEle['tmp_name']);
			$file_array['width'] = $size[0];
			$file_array['height'] = $size[1];
		}
		try {
			if (!$field) {
				$ext = '';
				if (preg_match('/^http/', $fileEle)) {
					$fs = fopen($fileEle, 'rb');
					$byte = fread($fs, 2);
					fclose($fs);
				} else {
					$byte = substr($fileEle, 0, 2);
				}
				$info = @unpack('C2chars', $byte);
				$code = intval($info['chars1'].$info['chars2']);
				switch ($code) {
					case 7173:$ext = '.gif';$is_image = true;break;
					case 255216:$ext = '.jpg';$is_image = true;break;
					case 13780:$ext = '.png';$is_image = true;break;
					case 6677:$ext = '.bmp';$is_image = true;break;
				}
				$filename = ROOT_PATH . UPLOAD_PATH . '/' . generate_sn() . $ext;
				file_put_contents($filename, $fileEle);
			} else {
				$filename = ((is_array($fileObj[$field]) && isset($fileObj[$field]['tmp_name'])) ? $fileObj[$field]['tmp_name'] : $fileObj[$field]);
			}
			$res = requestData('POST', 'http://pic.sogou.com/ris_upload', array(
				'pic_path' => '@'.$filename
			));
			if (!$field) unlink($filename);
			$res = urldecode($res);
			preg_match('/query=(.*?)&oname/i', $res, $matcher);
			if (!isset($matcher[1])) error('上传失败！代码200');
			$file_array['file'] = $matcher[1];
			if ($is_image) {
				$size = getimagesize($matcher[1]);
				if ($size) {
					$file_array['width'] = $size[0];
					$file_array['height'] = $size[1];
				}
			}
		}
		catch (Exception $e) {}
		return $file_array;
	}
	
	public function delete($url) {
		return true;
	}
}
