<?php
class MarkdownParser extends AbstractParser {

	var $ds = null;

	function __construct(DynamicallyStatic $ds) {
		$this->ds = $ds;
		include_once($ds->config['dirs']['vendors'] . '/markdown/markdown.php');
		include_once($ds->config['dirs']['vendors'] . '/spyc/spyc.php');
	}

	function parse($file) {
		$contents = file($file);
		$yaml = array();

		while (($line = array_shift($contents)) && !preg_match('`^\s*$`', $line)) {
			$yaml[] = $line;
		}

		$yaml = implode('', $yaml);
		$markdown = implode('', $contents);

		$details = Spyc::YAMLLoad($yaml);
		$article = new Article($details);
		$article->contents = Markdown($markdown);

		if (!empty($article->date)) {
			$article->date = strtotime($article->date);
		}

		return $article;
	}

	function handles($file) {
		return preg_match('`\.md$`', $file);
	}
}
