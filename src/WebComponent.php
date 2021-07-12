<?php
namespace Sy\Component;

use Sy\Component;
use Sy\Translate\TranslatorProvider;

class WebComponent extends Component {

	const JS_TOP    = 0;
	const JS_BOTTOM = 1;

	private $cssLinks = array();
	private $jsLinks  = array(self::JS_TOP => array(), self::JS_BOTTOM => array());

	private $cssCode  = array();
	private $jsCode   = array(self::JS_TOP => array(), self::JS_BOTTOM => array());

	private $translators = array();

	/**
	 * Add a component
	 *
	 * @param string $where
	 * @param Component $component
	 * @param boolean $append
	 */
	public function setComponent($where, Component $component, $append = false) {
		parent::setComponent($where, $component, $append);
		if (!$component instanceof WebComponent) return;
		$this->mergeCss($component);
		$this->mergeJs($component);
	}

	/**
	 * Merge css code and links from a WebComponent
	 *
	 * @param WebComponent $component
	 */
	public function mergeCss(WebComponent $component) {
		$componentCssLinks = $component->getCssLinks();
		foreach ($componentCssLinks as $media => $links) {
			if (isset($this->cssLinks[$media])) {
				$this->cssLinks[$media] = array_merge($this->cssLinks[$media], $links);
			} else {
				$this->cssLinks[$media] = $links;
			}
		}
		$this->cssCode = array_merge($this->cssCode, $component->getCssCodeArray());
	}

	/**
	 * Merge js code and links from a WebComponent
	 *
	 * @param WebComponent $component
	 */
	public function mergeJs(WebComponent $component) {
		$jsLinks = $component->getJsLinks();
		$jsCode  = $component->getJsCodeArray();
		$this->jsLinks[self::JS_TOP]    = array_merge($this->jsLinks[self::JS_TOP]   , $jsLinks[self::JS_TOP]);
		$this->jsLinks[self::JS_BOTTOM] = array_merge($this->jsLinks[self::JS_BOTTOM], $jsLinks[self::JS_BOTTOM]);
		$this->jsCode[self::JS_TOP]     = array_merge($this->jsCode[self::JS_TOP]    , $jsCode[self::JS_TOP]);
		$this->jsCode[self::JS_BOTTOM]  = array_merge($this->jsCode[self::JS_BOTTOM] , $jsCode[self::JS_BOTTOM]);
	}

	/**
	 * Return the css code array
	 *
	 * @return array
	 */
	public function getCssCodeArray() {
		return $this->cssCode;
	}

	/**
	 * Return the js code array
	 *
	 * @return array
	 */
	public function getJsCodeArray() {
		return $this->jsCode;
	}

	/**
	 * Return the css code
	 *
	 * @return string
	 */
	public function getCssCode() {
		return implode("\n", $this->cssCode);
	}

	/**
	 * Return the js code
	 *
	 * @return array
	 */
	public function getJsCode($position = self::JS_BOTTOM) {
		$res = array();
		foreach ($this->jsCode[$position] as $js) {
			if (isset($res[$js['type']][$js['load']])) {
				$res[$js['type']][$js['load']] .= "\n" . $js['code'];
			} else {
				$res[$js['type']][$js['load']] = $js['code'];
			}
		}
		return $res;
	}

	/**
	 * Add the css code
	 *
	 * @param string $code css code or or a css filename
	 */
	public function addCssCode($code) {
		if (is_file($code)) $code = file_get_contents($code);
		$code = trim($code);
		$this->cssCode[sha1($code)] = $code;
	}

	/**
	 * Add the js code
	 *
	 * @param string $code js code or a js filename
	 * @param array $options 'position' = JS_TOP|JS_BOTTOM, 'type' = 'text/javascript'|'module', 'load' = 'async'|''
	 */
	public function addJsCode($code, $options = []) {
		if (is_file($code)) $code = file_get_contents($code);
		$code = trim($code);

		// Position
		$position = isset($options['position']) ? $options['position'] : self::JS_BOTTOM;
		if ($position !== self::JS_TOP) $position = self::JS_BOTTOM;

		// Type
		$type = isset($options['type']) ? $options['type'] : 'module';

		// Loading strategy
		$load = isset($options['load']) ? $options['load'] : '';
		if ($load !== 'async') $load = '';

		$this->jsCode[$position][sha1($code . $type . $load)] = [
			'code' => $code,
			'type' => $type,
			'load' => $load
		];
	}

	/**
	 * Add a css link
	 *
	 * @param mixed $url Url
	 * @param string $media
	 */
	public function addCssLink($url, $media = '') {
		if (is_string($url)) {
			$url = trim($url);
		}
		if (is_array($url) and isset($url['url'])) {
			$url = trim($url['url']);
		}
		if (empty($url)) return;
		$key = $url;
		$this->cssLinks[$media][$key] = $url;
	}

	/**
	 * Add a js link
	 *
	 * @param mixed $url Url
	 * @param array $options 'position' = JS_TOP|JS_BOTTOM, 'type' = 'module'|'', 'load' = 'async'|'defer'
	 */
	public function addJsLink($url, $options = []) {
		if (is_string($url)) {
			$key = trim($url);
		}
		if (is_array($url) and isset($url['url'])) {
			$key = trim($url['url']);
		}
		if (empty($key)) return;

		// Position
		$position = isset($options['position']) ? $options['position'] : self::JS_TOP;
		if ($position !== self::JS_BOTTOM) $position = self::JS_TOP;

		// Type
		$type = isset($options['type']) ? $options['type'] : '';

		// Loading strategy
		$load = isset($options['load']) ? $options['load'] : 'defer';
		if ($load !== 'async') $load = 'defer';

		$this->jsLinks[$position][$key . $type . $load] = [
			'url'  => $url,
			'type' => $type,
			'load' => $load
		];
	}

	/**
	 * Get the css links
	 *
	 * @return array
	 */
	public function getCssLinks() {
		return $this->cssLinks;
	}

	/**
	 * Get the js links
	 *
	 * @return array
	 */
	public function getJsLinks() {
		return $this->jsLinks;
	}

	/**
	 * Add a Translator
	 *
	 * @param string $directory Translator directory
	 * @param string $type Translator type
	 * @param string $lang Translation language. Use auto detection by default
	 */
	public function addTranslator($directory, $type = 'php', $lang = '') {
		$this->translators[] = TranslatorProvider::createTranslator($directory, $type, $lang);
	}

	/**
	 * Translate message
	 *
	 * @param mixed $values The first argument can be a sprintf format string and others arguments will be used as sprintf values
	 * @return string
	 */
	public function _(...$values) {
		// Can also accept a single array as argument
		if (count($values) === 1 and is_array($values[0])) $values = $values[0];

		$message = array_shift($values);

		foreach ($this->translators as $translator) {
			$res = $translator->translate($message);
			if (!empty($res)) break;
		}

		array_walk($values, function(&$value) {
			foreach ($this->translators as $translator) {
				$a = $translator->translate($value);
				if (!empty($a)) {
					$value = $a;
					break;
				}
			}
		});

		if (empty($res)) $res = $message;

		return empty($values) ? $res : sprintf($res, ...$values);
	}

	public function __toString() {
		foreach ($this->translators as $translator) {
			$this->setVars($translator->getTranslationData());
		}
		return parent::__toString();
	}

}