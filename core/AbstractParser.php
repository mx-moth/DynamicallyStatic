<?php
abstract class AbstractParser {
	abstract function parse($file);
	abstract function handles($file);
}
