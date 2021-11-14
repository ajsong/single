<?php
abstract class push_base extends plugin_base {
	abstract function send($udid, $title, $text='', $ticker='', $extra=array(), $os='');
}