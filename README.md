# sy/webcomponent

A web component is a component with CSS and JS properties.

**Sy\Component\WebComponent** class derives from [Sy\Component](https://github.com/syframework/component)

## Installation

Install the latest version with

```bash
$ composer require sy/webcomponent
```

## CSS/JS management

### CSS/JS link

Use **addCssLink** and **addJsLink** to specify stylesheet and javascript source path.

Example:
```php
<?php

use Sy\Component\WebComponent;

class MyComponent extends WebComponent {

	public function __construct() {
		// Add CSS links
		$this->addCssLink('/web/path/to/style1.css');
		$this->addCssLink('/web/path/to/style2.css');

		// Add CSS links with media parameter
		$this->addCssLink('/web/path/to/screen_style1.css', 'screen');
		$this->addCssLink('/web/path/to/screen_style2.css', 'screen');
		$this->addCssLink('/web/path/to/print_style1.css' , 'print');
		$this->addCssLink('/web/path/to/print_style2.css' , 'print');

		// Add JS links
		$this->addJsLink('/web/path/to/script1.js');
		$this->addJsLink('/web/path/to/script2.js');
	}

}

$myComponent = new MyComponent();
$css = $myComponent->getCssLinks();
$js  = $myComponent->getJsLinks();

print_r($css);
print_r($js);
```

Output:
```
Array
(
    [] => Array
        (
            [/web/path/to/style1.css] => /web/path/to/style1.css
            [/web/path/to/style2.css] => /web/path/to/style2.css
        )

    [screen] => Array
        (
            [/web/path/to/screen_style1.css] => /web/path/to/screen_style1.css
            [/web/path/to/screen_style2.css] => /web/path/to/screen_style2.css
        )

    [print] => Array
        (
            [/web/path/to/print_style1.css] => /web/path/to/print_style1.css
            [/web/path/to/print_style2.css] => /web/path/to/print_style2.css
        )

)
Array
(
    [0] => Array
        (
            [/web/path/to/script1.jsdefer] => Array
                (
                    [url] => /web/path/to/script1.js
                    [type] =>
                    [load] => defer
                )

            [/web/path/to/script2.jsdefer] => Array
                (
                    [url] => /web/path/to/script2.js
                    [type] =>
                    [load] => defer
                )

        )

    [1] => Array
        (
        )

)
```

CSS links are indexed by media, then by URL. JS links are indexed by position
(`WebComponent::JS_TOP` = `0`, `WebComponent::JS_BOTTOM` = `1`). Keys are used to
deduplicate assets, so adding the same URL twice has no effect.

### CSS/JS code

Use **addCssCode** and **addJsCode** to specify stylesheet and javascript code.

Example:
```php
<?php

use Sy\Component\WebComponent;

class MyComponent extends WebComponent {

	public function __construct() {
		// Add CSS code from a file
		$this->addCssCode(__DIR__ . '/style.css');

		// Add JS code from a file
		$this->addJsCode(__DIR__ . '/script.js');

		// Inline code is also possible, but a dedicated file is usually cleaner
		$this->addJsCode('
			function javaScriptTest() {
				alert("Test");
			}
		');
	}

}
```

### CSS/JS transmission to parent component

When you add a component B on another component A, all CSS and JS properties of B are
transmitted to A. The merge happens **when B is rendered**: the assets of A are only
complete after the first render of the tree.

Exemple:
```php
<?php

use Sy\Component\WebComponent;

// Component A
class A extends WebComponent {

	public function __construct() {
		// Add CSS/JS links
		$this->addCssLink('/web/path/to/A/style.css');
		$this->addJsLink('/web/path/to/A/script.js');

		// Add the component B
		$this->setVar('SOMEWHERE', new B());
	}

}

// Component B
class B extends WebComponent {

	public function __construct() {
		// Add CSS/JS links
		$this->addCssLink('/web/path/to/B/style.css');
		$this->addJsLink('/web/path/to/B/script.js');
	}

}

$a = new A();

// The merge of B's assets into A happens during the first render
$a->render();

$css = $a->getCssLinks();
$js  = $a->getJsLinks();

print_r($css);
print_r($js);
```
Output:
```
Array
(
    [] => Array
        (
            [/web/path/to/A/style.css] => /web/path/to/A/style.css
            [/web/path/to/B/style.css] => /web/path/to/B/style.css
        )

)
Array
(
    [0] => Array
        (
            [/web/path/to/A/script.jsdefer] => Array
                (
                    [url] => /web/path/to/A/script.js
                    [type] =>
                    [load] => defer
                )

            [/web/path/to/B/script.jsdefer] => Array
                (
                    [url] => /web/path/to/B/script.js
                    [type] =>
                    [load] => defer
                )

        )

    [1] => Array
        (
        )

)
```

Calling `getCssLinks()` or `getJsLinks()` before the render only returns the assets
registered on `A` itself. The merge is also recursive: the assets of B are propagated up
through every `WebComponent` ancestor of the chain.

## Component without markup

A web component does not have to produce HTML: it can contribute only CSS and/or JS.
Because **every** component stored in a variable or a block is rendered even when the
template does not use the corresponding slot, such a component can be attached to a
dummy slot that does not exist in the template:

```php
<?php

use Sy\Component\WebComponent;

// Contributes JS only, no markup
class MyJsOnlyComponent extends WebComponent {

	public function __construct() {
		$this->addJsCode(__DIR__ . '/my-script.js', ['position' => self::JS_BOTTOM]);
	}

}

class MyPage extends WebComponent {

	public function __construct() {
		$this->setTemplateFile(__DIR__ . '/page.tpl');

		$this->mount(function () {
			// DUMMY is not used in page.tpl: the component is attached only
			// to transmit its JS code to the page.
			$this->setVar('DUMMY', new MyJsOnlyComponent());
		});
	}

}

echo new MyPage();
```

The slot name (`DUMMY` here) is arbitrary and must not appear in the template. During the
render of `MyPage`, the component is rendered, its markup is discarded, and its assets
are merged into `MyPage`.

Things to keep in mind:

- The immediate parent must be a `WebComponent`. The merge then propagates recursively
  through the `WebComponent` ancestors and stops at the first component that is not a
  `WebComponent`.
- The assets are merged at render time: `getCssLinks()` / `getJsLinks()` are only
  complete after the first render.
- Attach the component before the parent collects its assets. `Sy\Component\Html\Page`
  collects them in its `mounted` hook, so attaching a component during `mounted` is too
  late.
- If the component is never attached to the tree, call `mergeCss()` / `mergeJs()`
  manually on the target component.