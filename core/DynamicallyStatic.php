<?php
class DynamicallyStatic {

	var $config = null;

	var $parsers = null;
	var $renderers = null;
	var $modules = null;

	var $callbacks = array();

	var $files = null;

	var $templateHandleCache = array();

	function __construct($config) {
		$this->config = $config;
		$this->cleanUp();
		$this->loadClasses();

		echo "Parsing source files\n";
		$this->articles = $this->findArticles();

		echo "Preprocessing articles\n";
		foreach ($this->modules as $name => $module) {
			echo "  Module: " . $name . "\n";
			$module->preprocessArticles();
		}

		echo "Creating pages\n";
		foreach ($this->modules as $name => $module) {
			echo "  Module: " . $name . "\n";
			$module->createPages();
		}

		$cache = $this->config['dirs']['tmp'] . '/articles';
		file_put_contents($cache, serialize($this->renderedFiles));
	}

	/**
	 * Search the source directory for parseable files
	 *
	 * Parameters
	 *   $articlePath - The directory to search files for. This is searched recursively
	 */
	function findArticles($articlePath = '') {
		$dirDetails = new Article();
		$dirDetails->_type = 'directory';
		$dirDetails->_name = basename($articlePath);
		$dirDetails->_path =  $articlePath;
		$dirDetails->files = array();

		$indent = '	' . preg_replace(array('`[^/]`', '`/`'), array('', "\t"), $articlePath);
		$realArticlePath = $this->config['dirs']['articles'] . $articlePath;

		$files = array_diff(scandir($realArticlePath), array('.','..'));
		foreach ($files as $fileName) {
			$filePath = $articlePath . '/' . $fileName;
			$realFilePath = $realArticlePath . '/' . $fileName;

			$details = null;

			echo $indent . $fileName;

			if (is_dir($realFilePath)) {

				echo "/\n";
				$details = $this->findArticles($filePath);

			} else {

				$parser = $this->findParser($this->config['dirs']['articles'] . $filePath);
				if ($parser != null) {
					$details = $parser->parse($this->config['dirs']['articles'] . $filePath);
					$details->_type = 'file';
					$details->_name = $fileName;
					$details->_path = $filePath;
				}
				echo "\n";

			}

			if (!empty($details)) {
				$dirDetails->files[$fileName] = $details;
			}
		}

		return (empty($dirDetails->files) ? null : $dirDetails);
	}

	/**
	 * Gets all the parsed articles from a directory, including its subdirectories
	 *
	 * Parameters:
	 *   $dir - The directory to get the files from, in the format /path/to/dir
	 *
	 * Returns:
	 * A directory or file array, if the directory/file is found and has articles, null otherwise
	 *
	 * TODO: Only parse directories as they are needed?
	 */
	function getArticles($dir = null) {
		$dir = preg_replace('`^/|/$`', '', $dir);
		$bits = (empty($dir) ? array() : explode('/', $dir));
		$articles = $this->articles;

		foreach ($bits as $bit) {
			if ($articles->_type == 'directory' && isset($articles->files[$bit])) {
				$articles = $articles->files[$bit];
			} else {
				$articles = null;
				break;
			}
		}

		return $articles;
	}

	/**
	 * Load and register all the renderer, parser and module classes
	 */
	function loadClasses() {
		$this->parsers = $this->_loadClasses($this->config['dirs']['parsers'], 'Parser');
		$this->renderers = $this->_loadClasses($this->config['dirs']['renderers'], 'Renderer');
		$this->modules = $this->_loadClasses($this->config['dirs']['modules'], 'Module');

		foreach ($this->callbacks as $callback) {
			krsort($callback);
		}
	}

	/**
	 * Load all the classes found in a directory.
	 */
	private function _loadClasses($path, $suffix) {
		$classes = array();

		$files = array_diff(scandir($path), array('.', '..'));
		foreach ($files as $file) if (preg_match('`\.php$`', $file)) {
			$name = basename($file, '.php');
			$className = $name . $suffix;
			$fullFile = $path . '/' . $file;

			include ($fullFile);
			if (!class_exists($className)) {
				return ('File ' . $fullFile . ' does not define class ' . $className);
			}
			$classes[$name] = new $className($this);
		}

		return $classes;
	}

	/**
	 * Registers a modules interest in a certain callback. Modules which have registered
	 * their interest in a callback will be notified when appropriate
	 *
	 * Parameters:
	 *   $module - The module that is registering interest.
	 *   $callback - The callback to register for.
	 *   $priority - The priority of the callback. Higher priorities get notified first.
	 *     Modules with the same priority will be notified in an undefined order. If a 
	 *     callback needs to run before other callbacks, then they must set a higher
	 *     priority instead of relying upon run-order, as this may vary from system
	 *     to system.
	 */
	function registerInterest($module, $callback, $priority = 10) {
		if (empty($this->callback[$callback])) {
			$this->callback[$callback] = array();
		}
		if (empty($this->callback[$callback][$priority])) {
			$this->callback[$callback][$priority] = array();
		}
		$this->callbacks[$callback][$priority][] = $module;
	}

	/**
	 * Render source files to the destination directory. Sub directories will be
	 * recursively rendered
	 *
	 * Parameters:
	 *   $dest - The output directory for these files
	 *   $files - An array of files and directories to render
	 */
	function renderArticle(Article $article, $dest) {

		if (!empty($this->callbacks['renderArticle'])) {
			foreach ($this->callbacks['renderArticle'] as $modules) {
				foreach ($modules as $module) {

					// Run  the callback
					$return = $module->renderArticle($article, $dest);

					// Check the return values
					if ($return == false) {
						return;
					} else if (is_array($return)) {
						list($article, $dest) = $return;
					}
				}
			}
		}

		// Merge in the defaults
		$article->set($this->config['default']['article'], false);

		// Append the index (eg. index.html) if the output is a folder
		if (preg_match('`/$`', $dest)) {
			$dest = $dest . $this->config['default']['index'];
		}

		$realDest = $this->config['dirs']['output'] . $dest;
		if (!file_exists(dirname($realDest))) {
			mkdir(dirname($realDest), 0755, true);
		}

		$renderer = $this->findRenderer($article->template);
		if (!empty($renderer)) {
			file_put_contents($realDest, $renderer->render($this->config['dirs']['templates'] . '/' . $article->template, array('article' => $article)));
			$this->renderedFiles[] = $dest;
		} else {
			return 'No renderer found for ' . $article->template;
		}
	}

	/**
	 * Render an element for a page. Elements are small, reusable sections of a page,
	 * such as navigation, headers, or sections of articles. They can also be used by
	 * modules to generate content for later use in a template.
	 *
	 * Elements are rendered by Renderers, in exactly the same way as templates. A
	 * suitable renderer is found using <findRenderer>, and <AbstractRenderer::render>
	 * is called with the element and supplied arguments
	 *
	 * Parameters:
	 *   $name - The name of the element. This is taken to be the filename of the,
	 *     which should be found in eg. /templates/elements/.
	 *   $args - Variables to use in the element. This should be an array with key =>
	 *     value pairs denoting the variable name and its contents.
	 *
	 * Returns:
	 * The rendered element as a string.
	 */
	function renderElement($name, $args = array()) {
		$path = $this->config['dirs']['elements'] . '/' . $name;
		$renderer = $this->findRenderer($path);
		if ($renderer) {
			return $renderer->render($path, $args);
		} else {
			return 'No renderer for element ' . $name;
		}
	}

	/**
	 * Find the parser associated with an article source
	 *
	 * Parameters:
	 *   $article - The path to the article to examine
	 *
	 * Returns:
	 * An instance of <AbstractParser> or null, if none were found.
	 */
	function findParser($articlePath) {
		foreach ($this->parsers as $name => $parser) {
			if ($parser->handles($articlePath)) {
				return $parser;
			}
		}
		return null;
	}

	/**
	 * Find the renderer associated with a template
	 *
	 * Parameters:
	 *   $templatePath - The name of the template to examine
	 *
	 * Returns:
	 * An instance of <AbstractRenderer> or null, if none were found.
	 */
	function findRenderer($templatePath) {
		if (!empty($this->templateHandleCache[$templatePath])) {
			return $this->templateHandleCache[$templatePath];
		}

		foreach ($this->renderers as $name => $renderer) {
			if ($renderer->handles($templatePath)) {
				$this->templateHandleCache[$templatePath] = $renderer;
				return $renderer;
			}
		}
		return null;
	}
	/**
	 * Clean up files and folders created from last time
	 */
	function cleanUp() {
		$cache = $this->config['dirs']['tmp'] . '/articles';
		if (file_exists($cache)) {
			$outputDir = $this->config['dirs']['output'];
			echo "Removing old files\n";
			$old = unserialize(file_get_contents($cache));
			foreach($old as $file) {
				@unlink($outputDir . $file);

				$dir = dirname($file);
				while ($dir != '' && is_dir($outputDir . $dir) && array_diff(scandir($outputDir . $dir), array('.','..')) == array()) {

					if (!rmdir($outputDir . $dir)) {
						break;
					}
					$dir = dirname($dir);
				}
			}
		}
		unlink ($cache);
	}
}
