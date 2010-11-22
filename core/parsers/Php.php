<?php
class PhpParser extends AbstractParser {

	function __construct($ds) {
		$this->ds = $ds;
	}

	/**
	 * Parse a php based article. Articles in the php format should conform to
	 * the following spec:
	 * * All metadata should be placed in an array named $details, with keys
	 *   such as author, date, title, etc.
	 * * The contents of the article should be echo'd, print'd, or otherwise
	 *   output. The output will be collected in a buffer and used as the
	 *   article content.
	 *
	 * Parameters:
	 *   $file - The file to parse
	 *
	 * Returns:
	 * An article array, with all the usual suspects.
	 *
	 * See: <AbstractParser::parse>
	 */
	function parse($file) {

		// Modules can be accessed through $Module
		extract($this->ds->modules, EXTR_SKIP);

		$details = null;
		ob_start();
		include ($file);
		$details['contents'] = ob_get_contents();
		ob_end_clean();

		if (!empty($details['date'])) {
			$details['date'] = strtotime($details['date']);
		}

		return $details;
	}

	/**
	 * Indicates if this file should be parsed by the PhpParser.
	 * Returns:
	 * True if $file ends in a .php extension, otherwise false.
	 *
	 * See: <AbstractParser::handles>
	 */
	function handles($file) {
		return preg_match('`\.php$`', $file);
	}
}
