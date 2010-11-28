<?php
class BlogModule extends AbstractModule {

	var $config;

	var $articles = null;

	function __construct($dynamicallyStatic) {
		$this->ds = $dynamicallyStatic;
		$this->config = $this->ds->config['blog'];
	}

	/**
	 * Add links to all blog articles
	 */
	function preprocessArticles() {
		// Grab all articles from the blog path
		$page = $this->ds->getArticles($this->config['articles']);
		$blogs = array($page);
		$this->articles = array();

		// Grab all the blogs in to a flat array
		while (!empty($blogs)) {
			$file = array_pop($blogs);
			if ($file->_type == 'directory') {
				foreach ($file->files as $child) {
					array_push($blogs, $child);
				}
			} else {
				// Create the link for the article
				$link = $this->createLink($file);
				$file->link = $link;

				// Add the article to the array of articles
				$this->articles[] = $file;
			}
		}
	}

	/**
	 * Create all blog pages, and the blog index file
	 */
	function createPages() {

		// Sort them in reverse cronological order. <3 anonymous functions.
		usort($this->articles, function ($a, $b) {
			return $b->date - $a->date;
		});

		// Render all articles
		foreach ($this->articles as $article) {
			$article->set(array('template' => $this->config['template']), false);
			$this->ds->renderArticle($article, $article->link);
		}

		// Render the index article
		$this->ds->renderArticle(new Article(array(
			'title' => 'Latest posts',
			'contents' => $this->ds->renderElement($this->config['element'], array('articles' => $this->articles)),
			'template' => $this->config['template'],
		)), $this->config['rootPath']);

		// Render the index article
		$this->ds->renderArticle(new Article(array(
			'title' => $this->ds->config['site']['name'],
			'link' => $this->ds->config['site']['url'] . $this->config['rootPath'],
			'description' => 'Latest blog posts',
			'date' => time(),
			'template' => $this->config['rssTemplate'],
			'contents' => $this->ds->renderElement($this->config['rssElement'], array('articles' => $this->articles)),
		)), $this->config['rssPath']);
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
		$replace = array(
			'{year}' => date('Y', $article->date),
			'{month}' => date('m', $article->date),
			'{day}' => date('d', $article->date),
			'{slug}' => Inflector::slug($article->title),
			'{title}' => $article->title,
		);
		return strtr($this->config['articlePath'], $replace);
	}

}
