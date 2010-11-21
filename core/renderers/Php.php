<?php

class PhpRenderer extends AbstractRenderer {

	var $dm = null;

	function __construct(DynamicallyStatic $dm) {
		$this->ds = $dm;
	}
	
	function render($templatePath, $args) {
		extract($args, EXTR_SKIP);

		ob_start();
		include($templatePath);
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

	function handles($templatePath) {
		return preg_match('`\.php$`', $templatePath);
	}

	function renderElement($name, $args = array()) {
		return $this->ds->renderElement($name, $args);
	}

}
