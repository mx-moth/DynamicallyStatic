<?php
class Article {
	function __construct($details = null) {
		if (!empty($details)) {
			$this->set($details);
		}
	}

	function set($details, $override = true) {
		foreach ($details as $key => $value) {
			if ($override || empty($this->{$key})) {
				$this->{$key} = $value;
			}
		}
	}
}
