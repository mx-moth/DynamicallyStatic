<?php
foreach ($articles as $article) {
	echo $this->renderElement('rssArticle.php', array('article' => $article));
}
?>
