<?php

abstract class AbstractModule {

	/**
	 * Implementations of <AbstractModule> can use this function to add module
	 * specific information, define artile links, or other such tasts
	 */
	function preprocessArticles() { }

	/**
	 * Implementations of <AbstractModule> should use this function to create
	 * any pages they are responsible for, by calling <DynamicallyStatic::renderArticle>.
	 * This is called once per plug in just after everything has been loaded and
	 * pre-processed.
	 */
	function createPages() { }

	/**
	 * This callback can be called for each plugin before an article is rendered from
	 * <DynamicallyStatic::renderArticle>. The return value determines if the article
	 * will actually be rendered. Plugins can make use of this callback to modify articles
	 * before they are rendered, or to stop the rendering of articles for what ever
	 * reason. Possible examples are a paginator, which splits long articles up in to
	 * pages by rendering the article in multiple parts.
	 *
	 * Modules wishing to use this call back should register their interest using
	 * <DynamicallyStatic::registerInterest>, using the callback 'renderArticle'.
	 *
	 * Parameters:
	 *   $article - The article that is to be rendered
	 *   $output - The output path for the article
	 *
	 * Returns:
	 * If this callback returns true, the article is rendered as is. If the callback
	 * returns an array, the first and second elements are used as the article and
	 * output directory respectively. If the callback returns false, the article is
	 * not rendered
	 */
	function renderArticle($article, $output) { return true; }

}
