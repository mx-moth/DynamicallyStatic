<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<title><?php echo $DS->config['site']['name']; if (!empty($article->title))  echo ' - ' . $article->title; ?></title>

		<link href="/css/blog.css" type="text/css" rel="stylesheet" />
		<link href="<?php echo $Blog->config['rssPath']; ?>" rel="alternate" title="Latest posts" type="application/atom+xml" />

		<script type="text/javascript" src="/js/common/mootools/mootools.js"></script>
		<script type="text/javascript" src="/js/generic.js"></script>
		<?php
		$header = empty($article->header) ? '' : $article->header;
		if (!empty($article->scripts)) foreach ($article->scripts as $script) {
			$url = '/js/' . $script . (preg_match('`\.js$`', $script) ? null : '.js');
			$header.='<script type="text/javascript" src="' . $url . '"></script>';
		}
		echo $header;
		?>
	</head>
	<body>
		<div class="header">
			<h1><?php echo $DS->config['site']['name']; ?></h1>

			<!-- Note careful use of whitepace here -->
			<div class="navigation"><ul>
				<?php echo $this->renderElement('navigation/top.php'); ?>
			</ul><span class="fill"></span></div>

		</div>
		<div class="content">
			<?php echo $this->renderElement('article.php', array('article' => $article)); ?>
		</div>
		<div class="content subnavigation">
			<?php echo $this->renderElement('navigation/side.php'); ?>
		</div>
	</body>
</html>
