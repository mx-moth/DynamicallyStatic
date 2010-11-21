<?php
abstract class AbstractRenderer {
	abstract function render($templatePath, $article);
	abstract function handles($templatePath);
}
