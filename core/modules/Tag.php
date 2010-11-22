<?php
class TagModule extends AbstractModule {

	var $tags = array();
	var $maxCount = 0;

	function __construct($dynamicallyStatic) {
		$this->ds = $dynamicallyStatic;
		$this->config = $this->ds->config['tag'];
	}

	/**
	 * Add links to all blog articles
	 */
	function preprocessArticles() {
		// Grab all articles from the blog path
		$page = $this->ds->getArticles();
		$articles = array($page);

		// Get the tag counts
		while (!empty($articles)) {
			$file = array_pop($articles);
			if ($file->_type == 'directory') {
				foreach ($file->files as $child) {
					array_push($articles, $child);
				}
			} else {
				if (!empty($file->tags)) foreach ($file->tags as $tag) {
					if (empty($this->tags[$tag])) {
						$this->tags[$tag] = array();
					}
					$this->tags[$tag][] = $file;
				}
			}
		}

		foreach ($this->tags as $articles) {
			$this->maxCount = max(count($articles), $this->maxCount);
		}
	}

	/**
	 * Create a tag summary page, and a page for each tag with all the articles taged with it
	 */
	function createPages() {

		foreach ($this->tags as $tag => &$articles) {
			// Sort them in reverse cronological order. <3 anonymous functions.
			usort($articles, function ($a, $b) {
				if (isset($b->date) && isset($a->date)) {
					return $b->date - $a->date;
				} else if (isset($b->date)) {
					return 1;
				} else if (isset($a->date)) {
					return -1;
				} else {
					return;
				}
			});
			$this->ds->renderArticle(new Article(array(
				'title' => 'Articles tagged with ' . $tag,
				'contents' => $this->ds->renderElement($this->config['element'], array('articles' => $articles)),
				'author' => null,
				'date' => null,
				'template' => $this->config['template'],
			)), $this->createLink($tag));
		}

	}

	function createLink($tag) {
		$replace = array(
			'{slug}' => Inflector::slug($tag),
			'{tag}' => $tag,
		);
		return strtr($this->config['tagPath'], $replace);
	}

}
