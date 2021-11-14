<?php
define('DIRNAME', 'api');
define('APP_PATH', APPLICATION_PATH . '/' . DIRNAME . '/controller');
require_once(ROOT_PATH . '/protected/global.php');

define('SUBMIT_OUTPUT', false); //提交结果使用新页面
define('SHOW_HOST', false); //显示域名
define('RESPONSE_TIPS', '勾选替换响应例子后点击发送按钮即可固化替换'); //响应例子提示
$interface = 'index.html';
$path = ROOT_PATH . '/public/xfile';
$host = https().$_SERVER['HTTP_HOST'];
$responses = array();
$ignore_actions = array( //忽略制作文档的APP、ACT
	//'home' => array('index', 'detail'),
	//'other' => array('*')
);

//生成规则
//class 上面的#//注释为接口分类名称
//public function 上面的#//注释为接口名称，接口名称格式：名称|接口说明|output，设置output即提交使用新页面
//参数标识 $this->request->[get|post|file]('参数字段名', '默认值'); #//*参数名称|注释说明|输入框类型(暂只支持input|textarea|file):宽度*高度，参数名称前的星号为可选，存在即代表必填
//使用 #//GET BEGIN与#//GET END 或者 #//POST BEGIN与#//POST END 包裹参数标识，即可分拆为对应的提交方式

//替换响应例子
$response = isset($_GET['response']) ? trim($_GET['response']) : '';
if (strlen($response)) {
	$json = isset($_POST['json']) ? trim($_POST['json']) : '';
	if (!file_exists("{$path}/{$response}") || !strlen($json)) exit;
	$content = file_get_contents("{$path}/{$response}");
	$content = preg_replace('/<div class="response">([\s\S]+?)<\/div>/', '<div class="response">'.$json.'</div>', $content);
	file_put_contents("{$path}/{$response}", $content);
	exit;
}

$app = isset($_GET['app']) ? trim($_GET['app']) : '';
$act = isset($_GET['act']) ? trim($_GET['act']) : '';
if (!strlen($app) && !strlen($act)) {
	if ($dh = opendir(APP_PATH)) {
		$list = glob("{$path}/*.html");
		foreach ($list as $file) {
			$filename = basename($file);
			$content = file_get_contents($file);
			preg_match('/<div class="response">([\s\S]+?)<\/div>/', $content, $matcher);
			if ($matcher) {
				$responses[$filename] = $matcher[1];
			}
		}
		delete_folder($path);
		$files = array();
		while (($file=readdir($dh))!==false) {
			if ($file!='.' && $file!='..' && !is_dir(APP_PATH.'/'.$file) && strtolower(substr(strrchr($file, '.'), 1))=='php') {
				$files[] = $file;
			}
		}
		closedir($dh);
		$cycle = 0;
		sort($files);
		foreach ($files as $file) {
			$app = (explode('.', $file))[0];
			if ( !isset($ignore_actions[$app]) ) {
				$content = file_get_contents(APP_PATH.'/'.$file);
				$className = '';
				preg_match('/#\/\/(.+)[\n\t]*class\s+/', $content, $matcher);
				if ($matcher) $className = $matcher[1];
				preg_match_all('/#\/\/.+[\n\t]*public function (\w+)\s*\(/', $content, $matcher);
				if ($matcher && count($matcher[1])) {
					foreach ($matcher[1] as $act) {
						if ( !in_array('*', $ignore_actions[$app]) && !in_array($act, $ignore_actions[$app]) ) {
							$actHeader = "public function {$act}(";
							matchMark($className, substr($file, 0, strpos($file, '.')), $act, $content, $actHeader, $cycle);
							$cycle++;
						}
					}
				}
			}
		}
	}
	exit;
}

if (preg_match("/^[a-zA-Z0-9_.-]+$/", $app) && preg_match("/^[a-zA-Z0-9_.-]+$/", $act)) {
	if ( !isset($ignore_actions[$app]) || (!in_array('*', $ignore_actions[$app]) && !in_array($act, $ignore_actions[$app])) ) {
		$file = APP_PATH . "/{$app}.php";
		$actHeader = "public function {$act}(";
		if (file_exists($file)) {
			$content = file_get_contents($file);
			if (stripos($content, $actHeader)!==false) {
				$className = '';
				preg_match('/#\/\/(.+)[\n\t]*class\s+/', $content, $matcher);
				if ($matcher) $className = $matcher[1];
				matchMark($className, $app, $act, $content, $actHeader);
			} else {
				exit('MISSING METHOD');
			}
		} else {
			exit('MISSING FILE');
		}
	}
}

function matchMark($className, $app, $act, $content, $actHeader, $cycle=0) {
	preg_match('/#\/\/(.+)[\n\t]*'.str_replace('(', '\(', $actHeader).'/', $content, $matcher);
	$title = $matcher ? $matcher[1] : '';
	$content = substr($content, stripos($content, $actHeader)+strlen($actHeader));
	preg_match('/^([\s\S]+?)(public function |$)/', $content, $matcher);
	preg_match('/^[^{]+\{([\s\S]+)\}/', $matcher[0], $matcher);
	$content = $matcher[1];
	$methods = array();
	if (preg_match('/#\/\/GET BEGIN/', $content) || preg_match('/#\/\/POST BEGIN/', $content)) {
		preg_match('/#\/\/GET BEGIN([\s\S]*)#\/\/GET END/', $content, $matcher);
		if ($matcher) $methods[] = matchMethod($matcher[1]);
		preg_match('/#\/\/POST BEGIN([\s\S]*)#\/\/POST END/', $content, $matcher);
		if ($matcher) $methods[] = matchMethod($matcher[1], $app, $act);
	} else {
		$methods[] = matchMethod($content);
	}
	//if (!count($methods)) exit('NON SET THE SPECIAL MARK');
	create($className, $title, $methods, $app, $act, $cycle);
}

function matchMethod($content, $app='', $act='') {
	$rows = explode(PHP_EOL, $content);
	$method = 'get';
	$fields = array();
	foreach ($rows as $row) {
		if (!strlen($row)) continue;
		if (strpos($row, '#//')!==false &&
			(strpos($row, '$this->request->request')!==false || strpos($row, '$this->request->get')!==false || strpos($row, '$this->request->post')!==false ||
			strpos($row, '$this->request->file')!==false)) {
			$matcher = NULL;
			$is_upload = false;
			if (preg_match('/\$this->request->(get|request)/', $row)) {
				$method = 'get';
				preg_match('/\$this->request->(?:get|request)\s*\(([\s\S]+?)\);\s*#\/\/(.*)$/', $row, $matcher);
			} else if (preg_match('/\$this->request->(post|request)/', $row)) {
				$method = 'post';
				preg_match('/\$this->request->(?:post|request)\s*\(([\s\S]+?)\);\s*#\/\/(.*)$/', $row, $matcher);
			} else if (strpos($row, '$this->request->file')!==false) {
				$method = 'post';
				$is_upload = true;
				preg_match('/\$this->request->file\s*\(([\s\S]+?)\);\s*#\/\/(.*)$/', $row, $matcher);
			}
			if (!$matcher) continue;
			$param = array();
			preg_match('/^[\'"](\w+)[\'"](?:,\s*([\s\S]+))?$/', trim($matcher[1]), $fieldArr);
			if (!$is_upload) {
				$param['field'] = preg_replace('/^[\'"]([\s\S]*)[\'"]$/', '$1', trim($fieldArr[1]));
				if (count($fieldArr)>2 && strlen(trim($fieldArr[2]))) {
					preg_match('/^([\'"]?[\s\S]*?[\'"]?)(?:,\s*(.+))?$/', trim($fieldArr[2]), $fieldArray);
					if (is_numeric(trim($fieldArray[1]))) {
						$param['type'] = 'number';
						$param['inputType'] = 'tel';
					}
					$default = '';
					if (preg_match('/^[\'"][\s\S]*[\'"]$/', trim($fieldArray[1]))) {
						if (preg_match('/^date\(/', trim($fieldArray[1]))) {
							$param['class'] = 'date';
							if (isset($fieldArray[2])) {
								$string = trim(trim(trim($fieldArray[2]), "'"), '"');
								$attr = 'format="'.$string.'"';
								preg_match('/^(\w{4})-(\w{1,2})(?:-(\w{1,2})(?: (\w{1,2})(?::(\w{1,2})(?::(\w{1,2}))?)?)?)?$/', $string, $dates);
								if (count($dates)==2) $attr .= ' just="year"';
								else if (count($dates)==3) $attr .= ' just="month"';
								else if (count($dates)>4) {
									$attr .= ' showTime="true"';
									if (count($dates)==5) $attr .= ' showMinute="false"';
									else if (count($dates)==6) $attr .= ' showSecond="false"';
								}
								$param['attr'] = $attr;
							}
						} else {
							$default = preg_replace('/^[\'"]([\s\S]*)[\'"]$/', '$1', trim($fieldArray[1]));
							if (isset($fieldArray[2])) {
								switch (trim($fieldArray[2])) {
									case 'int':
									case 'integer':
										$param['type'] = 'number';
										$default = intval($default);
										break;
									case 'float':
									case 'double':
									case 'decimal':
										$param['type'] = 'float';
										$default = floatval($default);
										break;
									case 'array()':
									case '[]':
										$param['type'] = 'array';
								}
							}
						}
					} else if (is_numeric(trim($fieldArray[1]))) {
						$default = trim($fieldArray[1]);
					}
					$param['default'] = $default;
				}
			} else {
				$param['field'] = preg_replace('/^[\'"](\w+)[\'"][\s\S]*$/', '$1', trim($fieldArr[2]));
				$param['comment'] = '文件将上传到'.trim($fieldArr[1]).'文件夹';
				$param['control'] = 'file';
			}
			if (!strlen($matcher[2])) {
				if ($param['field']=='offset') $param['title'] = '记录偏移量';
				else if ($param['field']=='pagesize') $param['title'] = '每页数量';
				else $param['title'] = '<span style="color:#f00;">未知参数名称</span>';
			} else {
				$commentArr = explode('|', $matcher[2]);
				$title = $commentArr[0];
				if (substr($title, 0, 1)=='*') {
					$param['required'] = true;
					$title = substr($title, 1);
				}
				$param['title'] = $title;
				if (count($commentArr)>1) {
					if (strlen($commentArr[1])) $param['comment'] = isset($param['comment']) ? $param['comment'].'，'.$commentArr[1] : $commentArr[1];
					if (count($commentArr)>2 && strlen($commentArr[2])) {
						$controls = explode(':', $commentArr[2]);
						$param['control'] = $controls[0];
						if (count($controls)>1) {
							$wh = explode('*', $controls[1]);
							$param['width'] = $wh[0];
							if (count($wh)>1) $param['height'] = $wh[1];
						}
					}
				}
			}
			$fields[] = $param;
		}
	}
	return compact('method', 'fields');
}

$prevClassName = '';
function create($className, $title, $methods, $app, $act, $cycle=0) {
	global $interface, $path, $host, $responses, $prevClassName;
	$filename = "{$app}.{$act}.html";
	makedir($path);
	$titles = explode('|', $title);
	$description = '';
	$output = SUBMIT_OUTPUT;
	$title = $titles[0];
	if (count($titles)>1) $description = $titles[1];
	if (count($titles)>2 && $titles[2]=='output') $output = true;
	$html = '<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=320,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no">
<link rel="stylesheet" href="/css/base.css" />
<link rel="stylesheet" href="/css/datepicker.css" />
<script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>
<script type="text/javascript" src="/js/coo.js"></script>
<script type="text/javascript" src="/js/jsonview/jquery.jsonview.min.js"></script>
<link rel="stylesheet" href="/js/jsonview/jquery.jsonview.min.css" />
<title>'.$title.'</title>
<style>
body{margin:0 40px; font-size:12px; color:#333; text-align:left; -webkit-font-smoothing:antialiased;}
body::-webkit-scrollbar{width:6px; height:6px;}
body::-webkit-scrollbar-track{display:none;}
body::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.4); border-radius:3px;}
.hidden{display:none !important;}
h2{padding:20px 0; margin-bottom:20px; font-size:20px;}
h2 span{display:block; font-weight:normal; font-size:12px; color:#666;}
.form .url{text-align:left; font-size:0; padding:0 10px 0 15px; background:#000; color:#fff; height:40px; line-height:40px; overflow:auto; overflow-y:hidden; white-space:nowrap; border-radius:5px; -webkit-overflow-scrolling:touch;}
.form .url div{display:inline-block; vertical-align:middle; font-size:12px; height:100%; white-space:nowrap; font-family:"Source Code Pro";}
.form .url input{display:inline-block; vertical-align:middle; width:240px; height:100%; border-radius:0; padding:0; border:none; background-color:transparent; font-size:12px; color:#fff; font-family:"Source Code Pro";}
.form .url input:-internal-autofill-previewed, .form .url input:-internal-autofill-selected{-webkit-text-fill-color:#fff !important; -webkit-transition:background-color 5000s ease-in-out 0s !important; transition:background-color 5000s ease-in-out 0s !important;}
.form .url input::-webkit-input-placeholder{color:#fff;}
.form form .method{font-weight:700; text-align:center; margin:50px 0 5px 0; width:60px; height:26px; line-height:26px; border-radius:5px; color:#fff; font-size:14px; background:green; font-family:"Source Code Pro";}
.form form .method.post{background:#398bfc;}
.form .title{color:#999; font-size:12px; text-align:center; width:80px; height:26px; line-height:26px; background:#e0e0e0; margin:0; -webkit-transform:scale(0.83); transform:scale(0.83); -webkit-transform-origin:left bottom; transform-origin:left bottom;}
.form .scroll{width:100%; height:auto; margin-bottom:5px; overflow:auto; overflow-y:hidden; -webkit-overflow-scrolling:touch;}
.form .scroll::-webkit-scrollbar{width:6px; height:6px;}
.form .scroll::-webkit-scrollbar-track{display:none;}
.form .scroll::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.4); border-radius:3px;}
.form .scroll:after{content:""; display:block; clear:both;}
.form table{width:100%; background-color:transparent; border-collapse:collapse; border-spacing:0;}
.form table th, .form table td{text-align:left; white-space:nowrap; padding:10px; box-sizing:border-box; border:#e0e0e0 1px solid;}
.form table th{background-color:#f5f5f5;}
.form table td{vertical-align:top; font-family:"Source Code Pro";}
.form table td:nth-child(1){width:240px;}
.form table td:nth-child(2){width:80px;}
.form table td:nth-child(4){width:300px;}
.form table td .replace{float:left; height:30px; line-height:30px;}
.form table td .replace label{margin-right:5px;}
.form table td .submit{display:block; float:right; font-weight:700; text-decoration:none; text-align:center; background:#ff9902; color:#fff; width:80px; height:30px; line-height:30px; border-radius:5px;}
.form table td i{display:block; color:#999; white-space:nowrap;}
.form table td i.light{color:#ccc;}
.form input, .form textarea, .container textarea{font-size:12px; vertical-align:top; border:1px solid #ccc; width:100%; height:30px; min-width:80px; padding:0 5px; box-sizing:border-box; outline:none; -webkit-transition:all 0.3s ease-out; transition:all 0.3s ease-out;}
.form input, .form textarea{width:calc(100% - 60px); -webkit-border-top-left-radius:3px; border-top-left-radius:3px; -webkit-border-bottom-left-radius:3px; border-bottom-left-radius:3px;}
.form input.error, .form textarea.error{border:1px solid #f00;}
.form textarea, .container textarea{height:200px; padding:5px; box-sizing:border-box;}
.form textarea{height:100px; -webkit-border-bottom-right-radius:3px; border-bottom-right-radius:3px;}
.form input + label, .form textarea + label{display:inline-block; vertical-align:top; width:60px; height:30px; line-height:30px; text-align:center; color:#fff; background:#ccc; white-space:nowrap; -webkit-transition:all 0.3s ease-out; transition:all 0.3s ease-out; -webkit-border-top-right-radius:3px; border-top-right-radius:3px; -webkit-border-bottom-right-radius:3px; border-bottom-right-radius:3px;}
.form input:hover, .form input:focus, .form textarea:hover, .form textarea:focus, .container textarea:hover, .container textarea:focus{border-color:#398bfc;}
.form input:focus + label, .form textarea:focus + label{background:#398bfc;}
.form .response{border:#e0e0e0 1px solid; box-sizing:border-box; padding:20px;}
.form .response .jsonview{max-height:260px; overflow:auto; -webkit-overflow-scrolling:touch;}
.form .response .jsonview::-webkit-scrollbar{width:6px; height:6px;}
.form .response .jsonview::-webkit-scrollbar-track{display:none;}
.form .response .jsonview::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.4); border-radius:3px;}
.container{text-align:left; height:auto; box-sizing:border-box; white-space:nowrap; margin-top:50px; padding-bottom:20px; position:relative;}
.container .format{display:block; font-weight:700; margin:0 0 10px 0; text-decoration:none; text-align:center; background:#ffc700; color:#fff; width:80px; height:30px; line-height:30px; border-radius:3px;}
.container textarea{height:500px; border-radius:3px;}
.jsonview, .jsonview li, .jsonview div, .jsonview a, .jsonview span{font-family:"Source Code Pro";}
/*.jsonview .q{width:auto; color:#000;}*/
.jsonview .prop{font-weight:normal;}
.jsonview a{color:#00b; text-decoration:underline;}
.jsonview .bool, .jsonview .num, .jsonview .null{color:#1a01cc;}
@media (max-width: 768px){
body{margin:0 10px;}
.form .url{padding:0 10px;}
.form table{width:auto; float:left;}
.form table td{width:auto !important;}
}
</style>
</head>

<body>
<div class="form">
	<h2 class="ge-bottom ge-light">'.$title.(strlen($description)?'<span>'.$description.'</span>':'').'</h2>
	<div class="url"><div>'.(SHOW_HOST?$host:'').'/api/'.$app.'/'.$act.'?sign=</div><input type="text" id="regular_sign" placeholder="{签名值sign}" /></div>'.PHP_EOL;
	foreach ($methods as $m) {
		$method = strtolower($m['method']);
		$fields = $m['fields'];
		$html .= '	<form class="checkform" method="'.$method.'" target="_blank" enctype="multipart/form-data">
	<div class="method '.$method.'">'.strtoupper($m['method']).'</div>
	<div class="title">请求参数</div>
	<div class="scroll">
		<table>
			<thead><tr><th>字段</th><th>必填</th><th>描述</th><th>默认值</th></tr></thead>
			<tbody>'.PHP_EOL;
			if (is_array($fields)) {
				foreach ($fields as $param) {
					$required = (isset($param['required']) && $param['required']) ? true : false;
					$html .= '				<tr>
					<td>'.$param['field'].'</td>
					<td>'.($required?'<i>true</i>':'<i class="light">false</i>').'</td>
					<td>'.$param['title'].(isset($param['comment'])?'<i>'.trim($param['comment']).'</i>':'').'</td>
					<td>';
					if (isset($param['control']) && trim($param['control'])=='textarea') {
						$html .= '<textarea name="'.$param['field'].'" id="'.$param['field'].'"';
						$html .= ' class="'.(isset($param['class'])?$param['class']:'').($required?' coo-'.((isset($param['type'])&&($param['type']=='number'||$param['type']=='float'))?$param['type']:'need'):'').'"';
						if (isset($param['attr'])) $html .= ' '.$param['attr'];
						if (isset($param['width'])) $html .= ' style="width:'.$param['width'].'px;'.(isset($param['height'])?'height:'.$param['height'].'px;':'').'"';
						$html .= '>'.(isset($param['default'])?trim($param['default']):'').'</textarea>';
					} else if (isset($param['control']) && trim($param['control'])=='file') {
						$output = true;
						$html .= '<input type="file" name="'.$param['field'].'" id="'.$param['field'].'"';
						$html .= ' class="'.(isset($param['class'])?$param['class']:'').($required?' coo-'.((isset($param['type'])&&($param['type']=='number'||$param['type']=='float'))?$param['type']:'need'):'').'"';
						if (isset($param['attr'])) $html .= ' '.$param['attr'];
						$html .= ' />';
					} else {
						$html .= '<input type="'.(isset($param['inputType'])?$param['inputType']:'text').'" name="'.$param['field'].'" id="'.$param['field'].'" value="'.(isset($param['default'])?trim($param['default']):'').'"';
						$html .= ' class="'.(isset($param['class'])?$param['class']:'').($required?' coo-'.((isset($param['type'])&&($param['type']=='number'||$param['type']=='float'))?$param['type']:'need'):'').'"';
						if (isset($param['attr'])) $html .= ' '.$param['attr'];
						if (isset($param['width'])) $html .= ' style="width:'.$param['width'].'px;'.(isset($param['height'])?'height:'.$param['height'].'px;':'').'"';
						$html .= ' />';
					}
					$html .= '<label for="'.$param['field'].'">'.(isset($param['type'])?$param['type']:'string').'</label></td>
				</tr>'.PHP_EOL;
				}
				$html .= '				<tr>
					<td colspan="4">
						<a href="javascript:void(0)" class="submit" method="'.$method.'">发送</a>
						<div class="replace"><label class="checkbox"><input type="checkbox" id="'.$method.'_replace" /><b><b></b></b></label><label for="'.$method.'_replace">替换响应例子</label></div>
					</td>
				</tr>'.PHP_EOL;
			}
			$html .= '			</tbody>
		</table>
	</div>
	<div class="title '.$method.'_title">响应例子</div>
	<div class="response">'.(isset($responses[$filename])?$responses[$filename]:RESPONSE_TIPS).'</div>
	</form>'.PHP_EOL;
		}
	$html .= '</div>
<div class="container">
	<a href="javascript:void(0)" class="format hidden">元数据</a>
	<div class="jsonview-container"></div>
	<textarea class="hidden"></textarea>
</div>
</body>
</html>
<script>
function listenAlt(){
	$(window).on("keydown", function(e){
		if(e.altKey){
			$("ol", top.document.body).addClass("number");
		}
	}).on("keyup", function(e){
		if(!e.altKey){
			$("ol", top.document.body).removeClass("number");
		}
	});
}
$(function(){
	if(window.top !== window.self)listenAlt();
	if($.localStorage("regular_sign"))$("#regular_sign").val($.localStorage("regular_sign"));
	$(".date").datepicker({readonly:false});
	let response = $(".response");
	if(response.html() !== "'.RESPONSE_TIPS.'"){
		response.JSONView(response.html());
	}else{
		$(".replace").removeClass("hidden");
	}
	$(".form .submit").click(function(){
		let regular_sign = $("#regular_sign").val();
		if(regular_sign.length)$.localStorage("regular_sign", regular_sign, 7);
		let submit = $(this), form = submit.parents("form").eq(0);'.PHP_EOL;
		if ($output) {
			$html .= '		form.attr("action", "/api/'.$app.'/'.$act.'?sign="+regular_sign);
		form.submit();'.PHP_EOL;
		} else {
			$html .= '		let isPass = true;
		form.find(".coo-need, .coo-number, .coo-float").removeClass("error").each(function(){
			let _this = $(this);
			if( !_this.val().length ||
				(_this.is(".coo-number") && !/^(-?[1-9]\d*|0)$/.test(_this.val())) ||
				(_this.is(".coo-float") && !/^(-?[1-9][\d\.]*|0)$/.test(_this.val())) ){
				_this.addClass("error");
				isPass = false;
			}
		});
		if(!isPass){
			$.overloadError("请输入必填参数");
			return;
		}
		$.overload();
		if(submit.attr("method") === "get"){
			$.getJSON("/api/'.$app.'/'.$act.'?sign="+regular_sign, form.param(), function(json){
				if($("#get_replace")[0].checked){
					$.post("/gm/xfile?response='.$filename.'", {json:$.jsonString(json)});
					$(".get_title").next().JSONView(json);
					$(".get_title").next().find("a:not(.prop)").attr("target", "_blank");
				}else{
					$(".container .format").removeClass("hidden");
					$(".container textarea").val($.jsonString(json));
					$(".jsonview-container").JSONView(json);
					$(".jsonview-container a:not(.prop)").attr("target", "_blank");
					$.scrollto("a.format");
				}
			});
		}else{
			$.postJSON("/api/'.$app.'/'.$act.'?sign="+regular_sign, form.param(), function(json){
				if($("#post_replace")[0].checked){
					$.post("/gm/xfile?response='.$filename.'", {json:$.jsonString(json)});
					$(".post_title").next().JSONView(json);
					$(".post_title").next().find("a:not(.prop)").attr("target", "_blank");
				}else{
					$(".container .format").removeClass("hidden");
					$(".container textarea").val($.jsonString(json));
					$(".jsonview-container").JSONView(json);
					$(".jsonview-container a:not(.prop)").attr("target", "_blank");
					$.scrollto("a.format");
				}
			});
		}'.PHP_EOL;
		}
	$html .= '	});
	$(".container .format").click(function(){
		if($(".container textarea.hidden").length){
			$(this).html("格式化");
			$(".jsonview-container").addClass("hidden");
			$(".container textarea").removeClass("hidden");
		}else{
			$(this).html("元数据");
			$(".jsonview-container").removeClass("hidden").JSONView($(".container textarea").val());
			$(".jsonview-container a:not(.prop)").attr("target", "_blank");
			$(".container textarea").addClass("hidden");
		}
	});
});
</script>';
	file_put_contents("{$path}/{$filename}", $html);
	if (file_exists("{$path}/{$interface}")) {
		$html = file_get_contents("{$path}/{$interface}");
		if (strpos($html, $filename)===false) {
			if (strpos($html, '<h4>'.$className.'</h4>')===false) {
				$html = str_replace('</ol>', '	<h4>'.$className.'</h4>'.PHP_EOL.'	<li><a href="'.$filename.'" target="iframe"><strong>#'.($cycle+1).'</strong>'.$title.'</a></li>'.PHP_EOL.'</ol>', $html);
			} else {
				preg_match('/<h4>'.str_replace('/', '\/', $className).'<\/h4>([\s\S]+?)(<h4>|<\/ol>)/', $html, $matcher);
				if ($matcher) {
					$content = preg_replace('/[\n\t]*$/', '', $matcher[1]);
					$html = str_replace($content, $content.PHP_EOL.'	<li><a href="'.$filename.'" target="iframe"><strong>#'.($cycle+1).'</strong>'.$title.'</a></li>', $html);
				}
			}
			file_put_contents("{$path}/{$interface}", $html);
		}
	} else {
		$html = '<!doctype html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=320,minimum-scale=1.0,maximum-scale=1.0,initial-scale=1.0,user-scalable=no">
<title>API</title>
<script type="text/javascript" src="/js/jquery-3.4.1.min.js"></script>
<style>
html, body{height:100%;}
body{font-size:14px; -webkit-font-smoothing:antialiased; margin:0; padding:0 0 0 220px; overflow:hidden;}
.hidden{display:none !important;}
pre{font-family:"Source Code Pro";}
h2{padding:20px 0; margin:0 0 20px 0; font-size:20px; position:relative;}
h2:after{content:""; display:block; position:absolute; left:0; bottom:0; right:0; height:1px; background:#e7e7e7;}
table{width:100%; background-color:transparent; border-collapse:collapse; border-spacing:0;}
table th, table td{text-align:left; white-space:nowrap; padding:10px; box-sizing:border-box; border:#e0e0e0 1px solid;}
table th{background-color:#f5f5f5;}
table td{vertical-align:top; font-family:"Source Code Pro";}
ol, ol li{list-style:none; padding:0; margin:0;}
ol{padding:0 10px 10px 10px; width:220px; height:100%; box-sizing:border-box; background:#f6f5f4; float:left; margin-left:-220px; overflow:auto; overflow-x:hidden; -webkit-transition:all 0.3s ease-out; transition:all 0.3s ease-out;}
ol::-webkit-scrollbar{width:6px; height:6px;}
ol::-webkit-scrollbar-track{display:none;}
ol::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.4); border-radius:3px;}
ol.show{-webkit-transform:translateX(0); transform:translateX(0);}
ol span{position:relative; margin:10px 0; display:block; height:30px; background:#fff; border:1px solid #e7e7e7;}
ol span a{display:none; text-decoration:none; position:absolute; z-index:1; top:7px; right:7px; width:16px; height:16px; border-radius:100%; background:#d9d9d9 url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M557.312%20513.248l265.28-263.904c12.544-12.48%2012.608-32.704%200.128-45.248-12.512-12.576-32.704-12.608-45.248-0.128l-265.344%20263.936-263.04-263.84C236.64%20191.584%20216.384%20191.52%20203.84%20204%20191.328%20216.48%20191.296%20236.736%20203.776%20249.28l262.976%20263.776L201.6%20776.8c-12.544%2012.48-12.608%2032.704-0.128%2045.248%206.24%206.272%2014.464%209.44%2022.688%209.44%208.16%200%2016.32-3.104%2022.56-9.312l265.216-263.808%20265.44%20266.24c6.24%206.272%2014.432%209.408%2022.656%209.408%208.192%200%2016.352-3.136%2022.592-9.344%2012.512-12.48%2012.544-32.704%200.064-45.248L557.312%20513.248z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") no-repeat center center; background-size:10px 10px;}
ol span input{width:100%; height:100%; border:none; background-color:transparent; padding-left:13px; box-sizing:border-box; outline:none;}
ol span input:valid + a{display:block;}
ol h3, ol h4{margin:10px 0; font-size:12px; height:30px; line-height:30px; cursor:default; background:#fff; padding-left:13px; font-weight:700; border:1px solid #e7e7e7;}
ol h3 a{display:block; text-decoration:none; color:#398bfc;}
ol li a{position:relative; display:block; text-decoration:none; border-left:4px solid #e7e7e7; padding:8px 35px 8px 10px; font-size:12px; color:#398bfc;}
ol li a.this{background:#e7e7e7;}
ol li a strong{position:absolute; z-index:1; top:8px; right:10px; font-weight:normal; color:#ccc; opacity:0; -webkit-transform:translateX(-20px); transform:translateX(-20px); -webkit-transition:all 0.3s ease-out; transition:all 0.3s ease-out;}
ol.number li a strong{opacity:1; -webkit-transform:translateX(0); transform:translateX(0);}
.showmenu{position:absolute; z-index:1; top:50px; left:0; display:none; width:24px; height:24px; overflow:hidden; background:rgba(0,0,0,0.7) url("data:image/svg+xml;charset=utf-8,%3Csvg%20width%3D%2264%22%20height%3D%2264%22%20viewBox%3D%220%200%2064%2064%22%20version%3D%221.1%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20xmlns%3Axlink%3D%22http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink%22%3E%3Cg%20class%3D%22transform-group%22%3E%3Cg%20transform%3D%22scale(0.0625%2C%200.0625)%22%3E%3Cpath%20d%3D%22M916.238694%20290.722314%20138.030703%20290.722314c-11.30344%200-20.466124-9.162684-20.466124-20.466124s9.162684-20.466124%2020.466124-20.466124L916.238694%20249.790066c11.30344%200%2020.466124%209.162684%2020.466124%2020.466124S927.542134%20290.722314%20916.238694%20290.722314zM936.702772%20495.52477c0%2011.307533-9.15859%2020.466124-20.466124%2020.466124l-778.203898%200c-11.307533%200-20.466124-9.15859-20.466124-20.466124%200-11.2973%209.15859-20.466124%2020.466124-20.466124l778.203898%200C927.544181%20475.058646%20936.702772%20484.22747%20936.702772%20495.52477zM916.238694%20741.263567%20138.030703%20741.263567c-11.30344%200-20.466124-9.162684-20.466124-20.466124s9.162684-20.466124%2020.466124-20.466124L916.238694%20700.331319c11.30344%200%2020.466124%209.162684%2020.466124%2020.466124S927.542134%20741.263567%20916.238694%20741.263567z%22%20fill%3D%22%23ffffff%22%3E%3C%2Fpath%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E") no-repeat center center; background-size:14px 14px; -webkit-border-top-right-radius:12px; border-top-right-radius:12px; -webkit-border-bottom-right-radius:12px; border-bottom-right-radius:12px;}
iframe{width:100%; height:100%;}
@media (max-width: 768px){
body{padding:0;}
ol{position:absolute; z-index:2; left:0; top:0; width:100%; margin-left:0; -webkit-transform:translateX(-100%); transform:translateX(-100%);}
.showmenu{display:block;}
}
</style>
</head>

<body>
<div class="hidden">
	<h2>API返回值说明</h2>
	1. 除了登录注册接口不用带签名，其他接口都需要带上签名来保持登录状态；<br />
	2. 在需要判断是否登录的模块里，如果已登录则返回操作后的结果；如没有登录则返回错误代码为-100的json数据，请在前端执行重新登录的操作；<br />
	3. 若返回错误代码为-9的json数据，即代表该账号已在其他设备登录，请在前端执行重新登录的操作；<br />
	4. 接口返回的json数据结构：
	<pre>
{
    "data": null,
    "msg_type": -100,
    "msg": "请登录",
    "error": 1
}
	</pre>
	<h2>状态码表</h2>
	<table>
		<thead><tr><th>msg_type</th><th>描述</th></tr></thead>
		<tbody>
			<tr><td>0</td><td>成功</td></tr>
			<tr><td>-100</td><td>请登录</td></tr>
			<tr><td>-9</td><td>该账号已在其他设备登录</td></tr>
			<tr><td>-99</td><td>该商品已下架</td></tr>
		</tbody>
	</table>
</div>
<ol class="hidden">
	<span><input type="text" required="required" placeholder="Filter..." /><a href="javascript:void(0);"></a></span>
	<h3><a href="'.$interface.'" target="iframe">API返回值说明</a></h3>
	<h4>'.$className.'</h4>
	<li><a href="'.$filename.'" target="iframe"><strong>#'.($cycle+1).'</strong>'.$title.'</a></li>
</ol>
<div class="showmenu"></div>
<iframe name="iframe" class="hidden" src="index.html" frameborder="0"></iframe>
</body>
</html>
<script>
function listenAlt(){
	$(window).on("keydown", function(e){
		if(e.altKey){
			$("ol").addClass("number");
		}
	}).on("keyup", function(e){
		if(!e.altKey){
			$("ol").removeClass("number");
		}
	});
}
$(function(){
	if(window.top !== window.self){
		$("ol, iframe").addClass("hidden");
		$("div").removeClass("hidden");
		$("body").css("padding", "0 40px");
		return;
	}else{
		$("ol, iframe").removeClass("hidden");
		let hash = window.location.hash;
		if(hash.length){
			$("iframe").attr("src", $("a[href=\'"+hash.replace(/^#([\w\.]+)$/, "$1")+".html\']").addClass("this").attr("href"));
		}
		listenAlt();
	}
	$(".showmenu").on("click", function(){
		$("ol").addClass("show");
	});
	$("input").on("input", function(){
		let val = $.trim($(this).val());
		if(!val.length){
			$("ol h4, ol li").show();
		}else{
			$("ol h4, ol li").hide();
			$("ol li a").each(function(){
				if($(this).text().indexOf(val)>-1 || $(this).attr("href").indexOf(val)>-1){
					$(this).parent().show().prevUntil("h4").last().prev().show();
				}
			});
		}
	});
	$("a").on("click", function(){
		let _this = $(this);
		if(_this.parent().is("span")){
			_this.prev().val("");
			return;
		}
		_this.addClass("this").parent().siblings().find("a").removeClass("this");
		$("ol").removeClass("show");
		if(_this.attr("href") !== "'.$interface.'"){
			let api = _this.attr("href").replace(/^([\w\.]+)\.html$/, "$1");
			window.history.replaceState(null, "", window.location.href.replace(/#.+$/, "")+"#"+api);
		}
		return true;
	});
});
</script>';
		file_put_contents("{$path}/{$interface}", $html);
		chmod("{$path}/{$interface}", 0777);
	}
	if (!$cycle) echo '<title>API文档已生成</title>
<style>
body{font-size:14px; -webkit-font-smoothing:antialiased; font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;}
body::-webkit-scrollbar{width:10px; height:10px;}
body::-webkit-scrollbar-track{display:none;}
body::-webkit-scrollbar-thumb{background:rgba(0,0,0,0.5); border-radius:5px;}
h4{margin:10px 0 0 0;}
ol{padding:0}
ol li{margin-left:30px;}
a{font-size:14px; color:#398bfc; text-decoration:underline;}
</style>
<h2>API文档已生成</h2>
<a href="/xfile/'.$interface.'" target="_blank">打开API列表</a>
<ol>'.PHP_EOL;
if ($prevClassName!=$className) echo '<h4>'.$className.'</h4>'.PHP_EOL;
echo '<li><a href="/xfile/'.$filename.'" target="_blank">'.$title.'</a></li>'.PHP_EOL;
$GLOBALS['prevClassName'] = $className;
}
