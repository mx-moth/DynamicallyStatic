<?php

class PhpRenderer extends AbstractRenderer {

	var $dm = null;

	function __construct(DynamicallyStatic $dm) {
		$this->ds = $dm;
	}
	
	function render($templatePath, $args) {
		// Extract all arguments in to the local variable scope
		extract($args, EXTR_SKIP);

		// Modules can be accessed through $Module
		extract($this->ds->modules, EXTR_SKIP);

		// The DynamicallyStatic instance can be accessed through $DS
		$DS = $this->ds;

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
