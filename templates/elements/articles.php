<?php
foreach ($articles as $article) {
	echo $this->renderElement('article.php', array('article' => $article));
}
?>
