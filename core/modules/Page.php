<?php
class PageModule extends AbstractModule {

	var $config;

	var $pages = null;

	function __construct($dynamicallyStatic) {
		$this->ds = $dynamicallyStatic;
		$this->config = $this->ds->config['pages'];
	}

	/**
	 * Add links to all blog articles
	 */
	function preprocessArticles() {
		// Grab all articles from the blog path
		$pages = array($this->ds->getArticles($this->config['articles']));
		$this->pages = array();

		// Grab all the blogs in to a flat array
		while (!empty($pages)) {
			$file = &array_pop($pages);
			if ($file['$type'] == 'directory') {
				foreach ($file['files'] as $child) {
					array_push($pages, $child);
				}
			} else {
				// Create the link for the article
				$link = $this->createLink($file);
				$file['link'] = $link;

				// Add the article to the array of articles
				$this->pages[] = $file;
			}
		}
	}

	/**
	 * Create all blog pages, and the blog index file
	 */
	function createPages() {

		// Render all articles
		foreach ($this->pages as &$page) {
			$this->ds->renderArticle($page, $page['link']);
		}

	}

	/**
	 * Create a link to a blog post. The path template is taken from 
	 * <config.blog.articlePath>, with the following substitutions applied:
	 *   {year} - The year the article was published
	 *   {month} - The month the article was published
	 *   {day} - The day the article was published
	 *   {title} - The title of the article
	 *   {slug} - The title of the article formatted in to a URL safe slug
	 *
	 * Parameters:
	 *   $article - The article to create the link for
	 *
	 * Returns:
	 * The link to the article as a string
	 */
	function createLink($article) {
		if ($article['$path'] == $this->config['home']) {
			return '/';
		}
		$path = dirname(substr($article['$path'], strlen($this->config['articles'])));
		$title = empty($article['title']) ? null : $article['title'];
		$replace = array(
			'{slug}' => Inflector::slug($title),
			'{title}' => $title,
			'{path}' => $path,
		);
		return strtr($this->config['articlePath'], $replace);
	}

}
