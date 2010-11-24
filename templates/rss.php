<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>

<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom"><channel><?php

$mappings = array( 'pubDate' => 'date',);
$keys = array( 'title', 'pubDate', 'link', 'description');

foreach ($keys as $_ => $key) {

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
echo '<atom:link rel="self" href="' . $DS->config['site']['url'] . $Blog->config['rssPath'] . '" type="application/rss+xml" />';
echo $article->contents;
?></channel></rss>
