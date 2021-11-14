<?php
//SM.MS
//https://sm.ms/doc/
class smms extends upload_base {
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
			$json = requestData('POST', 'https://sm.ms/api/v2/upload', array(
				'smfile' => '@'.$filename
			), true, false, array(
				'Content-Type: multipart/form-data',
				'Authorization: 14ac5499cfdd2bb2859e4476d2e5b1d2bad079bf'
			));
			if (!$field) unlink($filename);
			if ($json['code']=='error') error($json['msg']);
			$file_array['file'] = $json['url'];
			if ($is_image) {
				$file_array['width'] = $json['width'];
				$file_array['height'] = $json['height'];
			}
		}
		catch (Exception $e) {}
		return $file_array;
	}
	
	public function delete($url) {
		return true;
	}
}
