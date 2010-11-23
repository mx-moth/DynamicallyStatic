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
		$page = $this->ds->getArticles($this->config['articles']);
		$pages = array($page);
		$this->pages = array();

		// Grab all the blogs in to a flat array
		while (!empty($pages)) {
			$file = array_pop($pages);
			if ($file->_type == 'directory') {
				foreach ($file->files as $child) {
					array_push($pages, $child);
				}
			} else {
				// Create the link for the article
				$link = $this->createLink($file);
				$file->link = $link;

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
		foreach ($this->pages as $page) {
			$this->ds->renderArticle($page, $page->link);
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

		$path = dirname(substr($article->_path, strlen($this->config['articles'])));
		// If the path is the current directory (.), translate it to root (/)
		if ($path == '.') {
			$path = '/';
		}
		if (!preg_match('`^/`', $path)) {
			$path = '/' . $path;
		}

		// Strip the extension off the name
		$name = preg_replace('`^([^.]+)\..*$`', '\1', $article->_name);

		// If the name matches the index pattern, empty $name. This allows for 
		// directory index pages
		if (preg_match($this->config['index'], $name)) {
			$name = '';
		}

		$title = empty($article->title) ? null : $article->title;

		$replace = array(
			'{path}' => $path,
			'{name}' => $name,

			'{slug}' => Inflector::slug($title),
			'{title}' => $title,
		);
		// Make the replacements, and remove any double //s left over
		return preg_replace('`//+`', '/', strtr($this->config['articlePath'], $replace));
	}

}
