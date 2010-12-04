<?php
class EmailObfuscatorModule extends AbstractModule {

	/**
	 * The regular expression used to match email addresses. It is fairly complete, but not perfect.
	 * 
	 * See:
	 * <Emails and Regular Expressions at http://www.regular-expressions.info/email.html>
	 */
	var $regex = '`<a ([^>]*)href=[\'"]mailto:([a-z0-9!#$%&\'*+/=?^_\`{|}~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_\`{|}~-]+)*)@((?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?)[\'"]([^>]*)>`';

	function __construct($dynamicallyStatic) {
		$this->ds = $dynamicallyStatic;
		$this->config = $this->ds->config['emailObfuscator'];
		$this->ds->registerInterest($this, 'renderArticle');
	}

	function renderArticle($article, $dest) {

		$matches = null;
		preg_match_all($this->regex, $article->contents, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

		// Continue on with rendering if nothing was found
		if (empty($matches)) {
			return true;
		}

		$content = $article->contents;

		foreach (array_reverse($matches) as $match) {
			$position = $match[0][1];
			$newContent = array();
			$newContent[] = substr($content, 0, $match[0][1]);
			$newContent[] = $this->obfuscate($match);
			$newContent[] = substr($content, $match[0][1] + strlen($match[0][0]));
			$content = implode('', $newContent);
		}

		$article->contents = $content;

		return array($article, $dest);
	}

	function obfuscate($link) {
		return sprintf($this->config['output'], $link[1][0], $link[2][0], $link[3][0], $link[4][0]);
	}


}
?>
