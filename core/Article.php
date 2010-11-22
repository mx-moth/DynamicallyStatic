<?php
class Article {
	function __construct($details = null) {
		if (!empty($details)) foreach ($details as $key => $value) {
			$this->{$key} = $value;
		}
	}
}
