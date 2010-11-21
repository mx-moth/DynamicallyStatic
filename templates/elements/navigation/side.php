			<h2>Subnavigation</h2>
			<h3>Latest posts</h3>
			<ul>
<?php
$posts = $this->ds->modules['Blog']->articles;

foreach (array_slice($posts, 0, 3) as $article) {
	echo sprintf('<li><a href="%s">%s</a></li>', $article['link'], $article['title']);
}
?>
			</ul>
			<h3>Tags</h3>
			<div class="tags content-item">
<?php

$tag = $this->ds->modules['Tag'];
foreach ($tag->tags as $tagName => $articles) {
	echo sprintf('<a href="%s" style="font-size: %0.2f%%;">%s</a> ', $tag->createLink($tagName), count($articles) / $tag->maxCount * 100, $tagName);
}
?>
			</div>

			<h3>Search</h3>
			<form action='search' class="search">
				<fieldset class="inline">
					<input type="text" name="data[Search][q]" value="" />
					<input type="submit" value="Search" />
				</fieldset>
			</form>
