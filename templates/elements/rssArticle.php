<?php
$wrapper = empty($article->wrapper) ? 'item' : $article->wrapper;
unset($article->wrapper);
unset($article->template);
echo '<' . $wrapper . '>';

$mappings = array(
	'description' => 'contents',
	'pubDate' => 'date',
	'guid' => 'date',
);
$keys = array(
	'title', 'description', 'pubDate', 'link',
);
foreach ($keys as $key) {

	$articleKey = isset($mappings[$key]) ? $mappings[$key] : $key;
	if (empty($article->{$articleKey})) {
		continue;
	}
	$value = $article->{$articleKey};

	switch ($key) {
	case 'link':
		$value = $this->ds->config['site']['url'];
		break;
	case 'pubDate':
		$value = date(DATE_RSS, $value);
		break;
	}

	echo sprintf('<%1$s>%2$s</%1$s>', htmlentities($key), htmlentities($value));
}
echo '<guid isPermaLink="false">' . $article->link . '</guid>';
echo '</' . $wrapper . '>';
?>
