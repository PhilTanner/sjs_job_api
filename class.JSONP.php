<?php
	/**
	 * Validate JSONP Callback
	 *
	 * https://github.com/tav/scripts/blob/master/validate_jsonp.py
	 * https://github.com/talis/jsonp-validator/blob/master/src/main/java/com/talis/jsonp/JsonpCallbackValidator.java
	 * http://tav.espians.com/sanitising-jsonp-callback-identifiers-for-security.html
	 * http://news.ycombinator.com/item?id=809291
	 *
	 * ^[a-zA-Z_$][0-9a-zA-Z_$]*(?:\[(?:".+"|\'.+\'|\d+)\])*?$
	 */
	class Jsonp { //https://gist.github.com/ptz0n/1217080
	 
		/**
		 * Is valid callback
		 *
		 * @param string $callback
		 *
		 * @return boolean
		 */
		public static function isValidCallback($callback)
		{
			$reserved = array(
				'break',
				'do',
				'instanceof',
				'typeof',
				'case',
				'else',
				'new',
				'var',
				'catch',
				'finally',
				'return',
				'void',
				'continue', 
				'for',
				'switch',
				'while',
				'debugger',
				'function',
				'this',
				'with', 
				'default',
				'if',
				'throw',
				'delete',
				'in',
				'try',
				'class',
				'enum', 
				'extends',
				'super',
				'const',
				'export',
				'import',
				'implements',
				'let', 
				'private',
				'public',
				'yield',
				'interface',
				'package',
				'protected', 
				'static',
				'null',
				'true',
				'false',
				'eval'
			);
		 
			foreach(explode('.', strtolower($callback)) as $identifier) {
				if(!preg_match('/^[a-zA-Z_$][0-9a-zA-Z_$]*(?:\[(?:".+"|\'.+\'|\d+)\])*?$/', $identifier)) {
				return false;
				}
				if(in_array($identifier, $reserved)) {
				return false;
				}
			}
		 
			return true;
		}
	 
	}
?>
