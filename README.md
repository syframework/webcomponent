# sy/webcomponent

A web component is a component with CSS, JS and translation properties.

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

## Translation

You can add several Translator in a WebComponent.
Each Translator will load translation data from a file in a specified directory.
This translation file must be named as the detected language. For example, if the detected language is "fr",
the PHP Translator will try to load "fr.php". And Gettext Translator will try to load "fr.mo".

This feature is provided by the library [sy/translate](https://github.com/syframework/translate)

### Language detection

Language will be detected using these variables in this order:

1. $_SESSION['sy_language']
2. $_COOKIE['sy_language']
3. $_SERVER['HTTP_ACCEPT_LANGUAGE']

### Translation methods

- void **WebComponent::addTranslator**(string *$directory* [, string *$type* = 'php'])
- string **WebComponent::_**(string *$message*)

Exemple:
```php
<?php

use Sy\Component\WebComponent;

class MyComponent extends WebComponent {

	public function __construct() {
		parent::__construct();
		$this->setTemplateFile(__DIR__ . '/template.tpl');

		// Add a translator, it will look for translation file into specified directory
		$this->addTranslator(__DIR__ . '/lang');

		// Use translation method
		$table = new Sy\Component\Html\Table();
		$table->addTr()->addTd($this->_('Hello world'));

		$this->setComponent('TABLE', $table);
	}

}

echo new MyComponent();
```

PHP Translation file:
```php
<?php
return array(
	'Hello world' => 'Bonjour monde',
);
```

Template file:
```html
<h3>{"Hello world"}</h3>

<h3>{"No traduction"}</h3>

{TABLE}
```

Output result:
```html
<h3>Bonjour monde</h3>

<h3>No traduction</h3>

<table>
<tr>
<td>Bonjour monde</td>
</tr>
</table>
```