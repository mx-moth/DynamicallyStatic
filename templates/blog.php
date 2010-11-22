<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/tr/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf8" />
		<title>Maelstroms Corner<?php if (!empty($article['title']))  echo ' - ' . $article['title']; ?></title>

		<link href="/css/blog.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="/js/common/mootools/mootools.js"></script>
		<script type="text/javascript" src="/js/generic.js"></script>
	</head>
	<body>
		<div class="header">
			<h1>Maelstrom's Corner</h1>

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
