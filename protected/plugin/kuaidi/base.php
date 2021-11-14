<?php
abstract class kuaidi_base extends plugin_base {
	abstract function company();
	abstract function get($spellName, $mailNo);
}