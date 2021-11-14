<?php
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidListcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/android/AndroidCmdcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSBroadcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSFilecast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSGroupcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSUnicast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCustomizedcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSListcast.php');
require_once(dirname(__FILE__) . '/' . 'notification/ios/IOSCmdcast.php');

class notify
{
    protected $appkey = NULL;
    protected $appMasterSecret = NULL;
    protected $android_appkey = NULL;
    protected $android_appMasterSecret = NULL;
    protected $timestamp = NULL;
    protected $validation_token = NULL;

    function __construct($key, $secret, $android_key, $android_secret)
    {
        $this->appkey = $key;
        $this->appMasterSecret = $secret;
        $this->android_appkey = $android_key;
        $this->android_appMasterSecret = $android_secret;
        $this->timestamp = strval(time());
    }

    //发送通知
	//20150723 by ajsong 修改为兼容iOS与Android
	public function send($udid, $text, $extra=array(), $production_mode=true, $os='', $title='', $ticker='') {
    	return $this->sendNotify($udid, $text, $extra, $production_mode, '', $os, $title, $ticker);
	}
	//is_cmd为静默推送设置,值: unicast-单播、broadcast-广播(不需要udid)
	public function sendNotify($udid, $text, $extra=array(), $production_mode=true, $is_cmd='', $os='', $title='', $ticker='') {
		if (!$ticker) $ticker = $text;
		if (!$title) $title = $text;
		if ($os=='') {
			if (strlen($udid)) {
				if (strlen($is_cmd)) {
					$this->sendIOSCmdcast($udid, $extra, $production_mode);
					$this->sendAndroidCmdcast($udid, $extra, $production_mode);
				} else {
					$this->sendIOSUnicast($udid, $text, $extra, $production_mode);
					$this->sendAndroidUnicast($udid, $text, $extra, $production_mode, $title, $ticker);
				}
			} else {
				if (strlen($is_cmd)) {
					$this->sendIOSCmdcast($is_cmd, $extra, $production_mode);
					$this->sendAndroidCmdcast($is_cmd, $extra, $production_mode);
				} else {
					$this->sendIOSBroadcast($text, $extra, $production_mode);
					$this->sendAndroidBroadcast($text, $extra, $production_mode, $title, $ticker);
				}
			}
		} else {
			if (strlen($udid)) {
				if ($os=='ios') {
					if (strlen($is_cmd)) {
						$this->sendIOSCmdcast($udid, $extra, $production_mode);
					} else {
						$this->sendIOSUnicast($udid, $text, $extra, $production_mode);
					}
				} else {
					if (strlen($is_cmd)) {
						$this->sendAndroidCmdcast($udid, $extra, $production_mode);
					} else {
						$this->sendAndroidUnicast($udid, $text, $extra, $production_mode, $title, $ticker);
					}
				}
			} else {
				if ($os=='ios') {
					if (strlen($is_cmd)) {
						$this->sendIOSCmdcast($is_cmd, $extra, $production_mode);
					} else {
						$this->sendIOSBroadcast($text, $extra, $production_mode);
					}
				} else {
					if (strlen($is_cmd)) {
						$this->sendAndroidCmdcast($is_cmd, $extra, $production_mode);
					} else {
						$this->sendAndroidBroadcast($text, $extra, $production_mode, $title, $ticker);
					}
				}
			}
		}
		return true;
	}

    public function sendIOSUnicast($device_tokens, $text, $extra=array(), $production_mode=true)
    {
        $api = new IOSUnicast();
        $api->setAppMasterSecret($this->appMasterSecret);
        $api->setPredefinedKeyValue('appkey', $this->appkey);
        $api->setPredefinedKeyValue('timestamp', $this->timestamp);
        // Set your device tokens here
        $api->setPredefinedKeyValue('device_tokens', $device_tokens);
        $api->setPredefinedKeyValue('alert', $text);
        $api->setPredefinedKeyValue('badge', isset($extra['badge']) ? intval($extra['badge']) : 1);
        $api->setPredefinedKeyValue('sound', isset($extra['sound']) ? strval($extra['sound']) : 'default');
        // Set 'production_mode' to 'true' if your app is under production mode
        //$unicast->setPredefinedKeyValue("production_mode", "true");
	    if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
        $api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
        // Set customized fields
		if (is_array($extra)) {
        	foreach ($extra as $key=>$val) {
		        if ($key=='badge' || $key=='sound' || $key=='production_mode') continue;
				$api->setExtraField($key, $val);
			}
		}
        //print("Sending unicast notification, please wait...\r\n");
        $api->send();
        //print("Sent SUCCESS\r\n");
    }

    //发送android消息
    public function sendAndroidUnicast($device_tokens, $text, $extra=array(), $production_mode=true, $title='', $ticker='')
    {
        try {
        	if (!strlen($ticker)) $ticker = $text;
            $api = new AndroidUnicast();
            $api->setAppMasterSecret($this->android_appMasterSecret);
            $api->setPredefinedKeyValue('appkey', $this->android_appkey);
            $api->setPredefinedKeyValue('timestamp', $this->timestamp);
            // Set your device tokens here
            $api->setPredefinedKeyValue('device_tokens', $device_tokens);
	        $api->setPredefinedKeyValue('text', $text); //通知文字描述
            $api->setPredefinedKeyValue('title', $title); //通知标题
            $api->setPredefinedKeyValue('ticker', $ticker); //通知栏提示文字
            //$unicast->setPredefinedKeyValue("after_open", "go_activity");
            //$unicast->setPredefinedKeyValue("activity", "com.xiaofeibao.xiaofeibao.activity.ComplainDetailActivity");
            $api->setPredefinedKeyValue('after_open', 'go_app');
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
	        if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
            $api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
            // Set extra fields
			if (is_array($extra)) {
				foreach ($extra as $key=>$val) {
					if ($key=='badge' || $key=='sound' || $key=='production_mode') continue;
					$api->setExtraField($key, $val);
				}
			}
            //print("Sending unicast notification, please wait...\r\n");
            $api->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
	
	public function sendIOSCmdcast($device_tokens, $extra=array(), $production_mode=true)
	{
		$type = 'unicast';
		if ($device_tokens=='broadcast') {
			$type = 'broadcast';
			$device_tokens = NULL;
		}
		$api = new IOSCmdcast($type);
		$api->setAppMasterSecret($this->appMasterSecret);
		$api->setPredefinedKeyValue('appkey', $this->appkey);
		$api->setPredefinedKeyValue('timestamp', $this->timestamp);
		// Set your device tokens here
		$api->setPredefinedKeyValue('device_tokens', $device_tokens);
		$api->setPredefinedKeyValue('alert', '');
		$api->setPredefinedKeyValue('badge', isset($extra['badge']) ? intval($extra['badge']) : 1);
		$api->setPredefinedKeyValue('sound', isset($extra['sound']) ? strval($extra['sound']) : 'default');
		// Set 'production_mode' to 'true' if your app is under production mode
		//$unicast->setPredefinedKeyValue("production_mode", "true");
		if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
		$api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
		// Set customized fields
		if (is_array($extra)) {
			foreach ($extra as $key=>$val) {
				if ($key=='badge' || $key=='sound' || $key=='production_mode') continue;
				$api->setExtraField($key, $val);
			}
		}
		//print("Sending unicast notification, please wait...\r\n");
		$api->send();
		//print("Sent SUCCESS\r\n");
	}
	
	//发送android消息
	public function sendAndroidCmdcast($device_tokens, $extra=array(), $production_mode=true)
	{
		try {
			$type = 'unicast';
			if ($device_tokens=='broadcast') {
				$type = 'broadcast';
				$device_tokens = NULL;
			}
			$api = new AndroidCmdcast($type);
			$api->setAppMasterSecret($this->android_appMasterSecret);
			$api->setPredefinedKeyValue('appkey', $this->android_appkey);
			$api->setPredefinedKeyValue('timestamp', $this->timestamp);
			// Set your device tokens here
			$api->setPredefinedKeyValue('device_tokens', $device_tokens);
			$api->setPredefinedKeyValue('text', '');
			$api->setPredefinedKeyValue('title', '');
			$api->setPredefinedKeyValue('ticker', '');
			//$unicast->setPredefinedKeyValue("after_open", "go_activity");
			//$unicast->setPredefinedKeyValue("activity", "com.xiaofeibao.xiaofeibao.activity.ComplainDetailActivity");
			$api->setPredefinedKeyValue('after_open', 'go_app');
			// Set 'production_mode' to 'false' if it's a test device.
			// For how to register a test device, please see the developer doc.
			if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
			$api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
			// Set extra fields
			if (is_array($extra)) {
				foreach ($extra as $key=>$val) {
					if ($key=='badge' || $key=='sound' || $key=='production_mode') continue;
					$api->setExtraField($key, $val);
				}
			}
			//print("Sending unicast notification, please wait...\r\n");
			$api->send();
			//print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			print("Caught exception: " . $e->getMessage());
		}
	}

    /**
     * @param $ticker 描述
     * @param $title 标题
     * @param $text 内容
     * @param $activity 安卓跳转的activity
     * @param $article_id 文章id
     */
    public function sendbroad($text, $extra=array(), $production_mode=true, $title='', $ticker='')
    {
	    $this->sendIOSBroadcast($text, $extra, $production_mode);
        $this->sendAndroidBroadcast($text, $extra, $production_mode, $title, $ticker);
    }

    //发送ios广播
    function sendIOSBroadcast($text, $extra=array(), $production_mode=true)
    {
        try {
            $api = new IOSBroadcast();
            $api->setAppMasterSecret($this->appMasterSecret);
            $api->setPredefinedKeyValue('appkey', $this->appkey);
            $api->setPredefinedKeyValue('timestamp', $this->timestamp);

            $api->setPredefinedKeyValue('alert', $text);
            $api->setPredefinedKeyValue('badge', 1);
            $api->setPredefinedKeyValue('sound', 'default');
            // Set 'production_mode' to 'true' if your app is under production mode
	        if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
            $api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
			if (is_array($extra)) {
				foreach ($extra as $key=>$val) {
					$api->setExtraField($key, $val);
				}
			}
            // print("Sending broadcast notification, please wait...\r\n");
            $api->send();
            // print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            // print("Caught exception: " . $e->getMessage());
        }
    }
	
	//发送android广播
	function sendAndroidBroadcast($text, $extra=array(), $production_mode=true, $title='', $ticker='')
	{
		try {
			$api = new AndroidBroadcast();
			$api->setAppMasterSecret($this->android_appMasterSecret);
			$api->setPredefinedKeyValue('appkey', $this->android_appkey);
			$api->setPredefinedKeyValue('timestamp', $this->timestamp);
			$api->setPredefinedKeyValue('text', $text);
			$api->setPredefinedKeyValue('title', $title);
			$api->setPredefinedKeyValue('ticker', $ticker);
			$api->setPredefinedKeyValue('after_open', 'go_app');
			if (isset($extra['production_mode'])) $production_mode = $extra['production_mode'];
			$api->setPredefinedKeyValue('production_mode', $production_mode?'true':'false');
			if (is_array($extra)) {
				foreach ($extra as $key=>$val) {
					$api->setExtraField($key, $val);
				}
			}
			// print("Sending broadcast notification, please wait...\r\n");
			$api->send();
			// print("Sent SUCCESS\r\n");
		} catch (Exception $e) {
			// print("Caught exception: " . $e->getMessage());
		}
	}

    function sendAndroidFilecast()
    {
        try {
            $filecast = new AndroidFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $filecast->setPredefinedKeyValue("ticker", "Android filecast ticker");
            $filecast->setPredefinedKeyValue("title", "Android filecast title");
            $filecast->setPredefinedKeyValue("text", "Android filecast text");
            $filecast->setPredefinedKeyValue("after_open", "go_app");  //go to app
            //print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa" . "\n" . "bb");
            //print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidGroupcast()
    {
        try {
            /*
              *  Construct the filter condition:
              *  "where":
              *	{
              *		"and":
              *		[
                *			{"tag":"test"},
                *			{"tag":"Test"}
              *		]
              *	}
              */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => "test"
                        ),
                        array(
                            "tag" => "Test"
                        )
                    )
                )
            );

            $groupcast = new AndroidGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("ticker", "Android groupcast ticker");
            $groupcast->setPredefinedKeyValue("title", "Android groupcast title");
            $groupcast->setPredefinedKeyValue("text", "Android groupcast text");
            $groupcast->setPredefinedKeyValue("after_open", "go_app");
            // Set 'production_mode' to 'false' if it's a test device.
            // For how to register a test device, please see the developer doc.
            $groupcast->setPredefinedKeyValue("production_mode", "true");
            //print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendAndroidCustomizedcast()
    {
        try {
            $customizedcast = new AndroidCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", "xx");
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", "xx");
            $customizedcast->setPredefinedKeyValue("ticker", "Android customizedcast ticker");
            $customizedcast->setPredefinedKeyValue("title", "Android customizedcast title");
            $customizedcast->setPredefinedKeyValue("text", "Android customizedcast text");
            $customizedcast->setPredefinedKeyValue("after_open", "go_app");
            //print("Sending customizedcast notification, please wait...\r\n");
            $customizedcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }


    function sendIOSFilecast()
    {
        try {
            $filecast = new IOSFilecast();
            $filecast->setAppMasterSecret($this->appMasterSecret);
            $filecast->setPredefinedKeyValue("appkey", $this->appkey);
            $filecast->setPredefinedKeyValue("timestamp", $this->timestamp);

            $filecast->setPredefinedKeyValue("alert", "IOS 文件播测试");
            $filecast->setPredefinedKeyValue("badge", 0);
            $filecast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $filecast->setPredefinedKeyValue("production_mode", "false");
            //print("Uploading file contents, please wait...\r\n");
            // Upload your device tokens, and use '\n' to split them if there are multiple tokens
            $filecast->uploadContents("aa" . "\n" . "bb");
            //print("Sending filecast notification, please wait...\r\n");
            $filecast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSGroupcast()
    {
        try {
            /*
              *  Construct the filter condition:
              *  "where":
              *	{
              *		"and":
              *		[
                *			{"tag":"iostest"}
              *		]
              *	}
              */
            $filter = array(
                "where" => array(
                    "and" => array(
                        array(
                            "tag" => "iostest"
                        )
                    )
                )
            );

            $groupcast = new IOSGroupcast();
            $groupcast->setAppMasterSecret($this->appMasterSecret);
            $groupcast->setPredefinedKeyValue("appkey", $this->appkey);
            $groupcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            // Set the filter condition
            $groupcast->setPredefinedKeyValue("filter", $filter);
            $groupcast->setPredefinedKeyValue("alert", "IOS 组播测试");
            $groupcast->setPredefinedKeyValue("badge", 0);
            $groupcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $groupcast->setPredefinedKeyValue("production_mode", "false");
            //print("Sending groupcast notification, please wait...\r\n");
            $groupcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSCustomizedcast()
    {
        try {
            $customizedcast = new IOSCustomizedcast();
            $customizedcast->setAppMasterSecret($this->appMasterSecret);
            $customizedcast->setPredefinedKeyValue("appkey", $this->appkey);
            $customizedcast->setPredefinedKeyValue("timestamp", $this->timestamp);

            // Set your alias here, and use comma to split them if there are multiple alias.
            // And if you have many alias, you can also upload a file containing these alias, then
            // use file_id to send customized notification.
            $customizedcast->setPredefinedKeyValue("alias", "xx");
            // Set your alias_type here
            $customizedcast->setPredefinedKeyValue("alias_type", "xx");
            $customizedcast->setPredefinedKeyValue("alert", "IOS 个性化测试");
            $customizedcast->setPredefinedKeyValue("badge", 0);
            $customizedcast->setPredefinedKeyValue("sound", "chime");
            // Set 'production_mode' to 'true' if your app is under production mode
            $customizedcast->setPredefinedKeyValue("production_mode", "false");
            //print("Sending customizedcast notification, please wait...\r\n");
            $customizedcast->send();
            //print("Sent SUCCESS\r\n");
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    public function sendListcast($ticker, $title, $text, $activity, $extra, $android_device_tokens, $ios_device_tokens, $push_type, $board_id){
        $this->sendAndroidListcast($ticker, $title, $text, $activity, $extra, $android_device_tokens, $push_type, $board_id) ;
        $this->sendIOSListcast($title, $text, $extra, $ios_device_tokens, $push_type, $board_id);
    }
    function sendAndroidListcast($ticker, $title, $text, $activity, $extra, $device_tokens, $push_type, $board_id)
    {
        try {
            $listcast = new AndroidListcast();
            $listcast->setAppMasterSecret($this->android_appMasterSecret);
            $listcast->setPredefinedKeyValue("appkey", $this->android_appkey);
            $listcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $listcast->setPredefinedKeyValue("ticker", $ticker);
            $listcast->setPredefinedKeyValue("title", $title);
            $listcast->setPredefinedKeyValue("text", $text);
            $listcast->setPredefinedKeyValue("display_type", "notification");
            $listcast->setPredefinedKeyValue("description", $title);
            $listcast->setPredefinedKeyValue("device_tokens", $device_tokens);//用逗号分隔
            $listcast->setPredefinedKeyValue("production_mode", "true");
            $listcast->setPredefinedKeyValue("after_open", "go_activity");
            $listcast->setPredefinedKeyValue("activity", $activity);
            if ($push_type == 1) {
                $listcast->setExtraField("url", $extra);
            } else if($push_type == 3)
            {
                $listcast->setExtraField("topic_id", $extra);
                $listcast->setExtraField("board_id",$board_id) ;
            }else{
                $listcast->setExtraField("id", $extra);
            }
            $listcast->setExtraField("push_type",$push_type);
            $listcast->send();
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }

    function sendIOSListcast($title, $text, $extra, $device_tokens, $push_type, $board_id)
    {
        try {
            $listcast = new IOSListcast();
            $listcast->setAppMasterSecret($this->appMasterSecret);
            $listcast->setPredefinedKeyValue("appkey", $this->appkey);
            $listcast->setPredefinedKeyValue("timestamp", $this->timestamp);
            $listcast->setPredefinedKeyValue("alert", $text);
            $listcast->setPredefinedKeyValue("badge", 0);
            $listcast->setPredefinedKeyValue("sound", "chime");
            $listcast->setPredefinedKeyValue("device_tokens", $device_tokens);//用逗号分隔
            $listcast->setPredefinedKeyValue("production_mode", "true");
            $listcast->setPredefinedKeyValue("description", $title);
            if($push_type ==1) {
                $listcast->setCustomizedField("url", $extra);
            }else {
                $listcast->setCustomizedField("id", $extra);
            }
            $listcast->setCustomizedField("push_type",$push_type);
            $listcast->send();
        } catch (Exception $e) {
            print("Caught exception: " . $e->getMessage());
        }
    }
}

