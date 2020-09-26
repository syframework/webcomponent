<?php
namespace Sy\Component;

use Sy\Component;
use Sy\Translate\TranslatorProvider;

class WebComponent extends Component {

	const JS_TOP    = 0;
	const JS_BOTTOM = 1;

	private $cssLinks;
	private $jsLinks;

	private $cssCode;
	private $jsCode;

	private $translators;

	public function __construct() {
		parent::__construct();
		$this->cssLinks = array();
		$this->jsLinks  = array(self::JS_TOP => array(), self::JS_BOTTOM => array());
		$this->cssCode  = array();
		$this->jsCode   = array(self::JS_TOP => array(), self::JS_BOTTOM => array());
		$this->translators = array();
	}

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
		foreach ($this->cssLinks as $media => $links) {
			if (!isset($componentCssLinks[$media])) continue;
			$this->cssLinks[$media] = array_merge($this->cssLinks[$media], $componentCssLinks[$media]);
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
	 * @return string
	 */
	public function getCssCodeArray() {
		return $this->cssCode;
	}

	/**
	 * Return the js code array
	 *
	 * @return string
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
	 * @return string
	 */
	public function getJsCode($position = self::JS_BOTTOM) {
		return implode("\n", $this->jsCode[$position]);
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
	 * @param int $position \Sy\Component\WebComponent::JS_TOP or \Sy\Component\WebComponent::JS_BOTTOM
	 */
	public function addJsCode($code, $position = self::JS_BOTTOM) {
		if (is_file($code)) $code = file_get_contents($code);
		$code = trim($code);
		if ($position === self::JS_BOTTOM)
			$this->jsCode[self::JS_BOTTOM][sha1($code)] = $code;
		else
			$this->jsCode[self::JS_TOP][sha1($code)] = $code;
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
	 * @param int $position \Sy\Component\WebComponent::JS_TOP or \Sy\Component\WebComponent::JS_BOTTOM
	 */
	public function addJsLink($url, $position = self::JS_BOTTOM) {
		if (is_string($url)) {
			$url = trim($url);
		}
		if (is_array($url) and isset($url['url'])) {
			$url = trim($url['url']);
		}
		if (empty($url)) return;
		$key = $url;
		if ($position === self::JS_BOTTOM)
			$this->jsLinks[self::JS_BOTTOM][$key] = $url;
		else
			$this->jsLinks[self::JS_TOP][$key] = $url;
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
	 */
	public function addTranslator($directory, $type = 'php') {
		$this->translators[] = TranslatorProvider::createTranslator($directory, $type);
	}

	/**
	 * Translate message
	 *
	 * @param string $message
	 * @return string
	 */
	public function _($message) {
		foreach ($this->translators as $translator) {
			$res = $translator->translate($message);
			if (!empty($res)) break;
		}
		return !empty($res) ? $res : $message;
	}

	public function __toString() {
		foreach ($this->translators as $translator) {
			$this->setVars($translator->getTranslationData());
		}
		return parent::__toString();
	}

}