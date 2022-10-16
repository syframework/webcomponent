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
		parent::__construct();

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
            [0] => /web/path/to/style1.css
            [1] => /web/path/to/style2.css
        )

    [screen] => Array
        (
            [0] => /web/path/to/screen_style1.css
            [1] => /web/path/to/screen_style2.css
        )

    [print] => Array
        (
            [0] => /web/path/to/print_style1.css
            [1] => /web/path/to/print_style2.css
        )

)
Array
(
    [0] => /web/path/to/script1.js
    [1] => /web/path/to/script2.js
)
```

### CSS/JS code

Use **addCssCode** and **addJsCode** to specify stylesheet and javascript code.

Example:
```php
<?php

use Sy\Component\WebComponent;

class MyComponent extends WebComponent {

	public function __construct() {
		parent::__construct();

		// Add CSS code
		$this->addCssCode(file_get_contents(__DIR__) . '/style.css');

		// Add JS code
		$this->addJsCode(file_get_contents(__DIR__) . '/script.js');

		// You can also do that but it's not very clean
		$this->addJsCode('
			function javaScriptTest() {
				alert("Test");
			}
		');
	}

}
```

### CSS/JS transmission to parent component

When you add a component B on another component A, all CSS and JS properties of B are transmitted to A.

Exemple:
```php
<?php

use Sy\Component\WebComponent;

// Component A
class A extends WebComponent {

	public function __construct() {
		parent::__construct();

		// Add CSS/JS links
		$this->addCssLink('/web/path/to/A/style.css');
		$this->addJsLink('/web/path/to/A/script.js');

		// Add the component B
		$this->setComponent('SOMEWHERE', new B());
	}

}

// Component B
class B extends WebComponent {

	public function __construct() {
		parent::__construct();

		// Add CSS/JS links
		$this->addCssLink('/web/path/to/B/style.css');
		$this->addJsLink('/web/path/to/B/script.js');
	}

}

$a = new A();

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
            [0] => /web/path/to/A/style.css
            [1] => /web/path/to/B/style.css
        )

)
Array
(
    [0] => /web/path/to/A/script.js
    [1] => /web/path/to/B/script.js
)
```