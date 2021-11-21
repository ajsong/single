<?php
!defined('ROOT_PATH') && define('ROOT_PATH', dirname(dirname(__FILE__)));
$auth = isset($_GET['auth']) ? trim($_GET['auth']) : '';
if (!strlen($auth) || !file_exists(ROOT_PATH."/temp/auth/{$auth}")) exit(json_encode(array('code'=>0, 'msg'=>'INVALID AUTHORIZATION CODE')));
exit(json_encode(array('code'=>1, 'msg'=>'VERIFIED')));
