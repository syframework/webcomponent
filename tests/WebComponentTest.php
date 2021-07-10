<?php

session_start();
$_SESSION['sy_language'] = 'fr';

use PHPUnit\Framework\TestCase;
use Sy\Component\WebComponent;

class WebComponentTest extends TestCase {

	public function testParentWithoutCssLink() {
		$a = new WebComponent();
		$b = new WebComponent();
		$c = new WebComponent();
		$c->addCssLink('c.css');
		$this->assertEquals($c->getCssLinks(), [
			'' => [
				'c.css' => 'c.css'
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($b->getCssLinks(), [
			'' => [
				'c.css' => 'c.css',
			]
		]);
		$this->assertEquals($a->getCssLinks(), [
			'' => [
				'c.css' => 'c.css',
			]
		]);
	}

	public function testCssLink() {
		$a = new WebComponent();
		$a->addCssLink('a.css');
		$a->addCssLink('z.css');
		$this->assertEquals($a->getCssLinks(), [
			'' => [
				'a.css' => 'a.css',
				'z.css' => 'z.css',
			]
		]);

		$b = new WebComponent();
		$b->addCssLink('b.css');
		$this->assertEquals($b->getCssLinks(), [
			'' => [
				'b.css' => 'b.css'
			]
		]);

		$c = new WebComponent();
		$c->addCssLink('c.css');
		$this->assertEquals($c->getCssLinks(), [
			'' => [
				'c.css' => 'c.css'
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getCssLinks(), [
			'' => [
				'a.css' => 'a.css',
				'z.css' => 'z.css',
				'b.css' => 'b.css',
				'c.css' => 'c.css',
			]
		]);
	}

	public function testSameCssLink() {
		$a = new WebComponent();
		$a->addCssLink('a.css');
		$a->addCssLink('z.css');
		$this->assertEquals($a->getCssLinks(), [
			'' => [
				'a.css' => 'a.css',
				'z.css' => 'z.css',
			]
		]);

		$b = new WebComponent();
		$b->addCssLink('a.css');
		$this->assertEquals($b->getCssLinks(), [
			'' => [
				'a.css' => 'a.css'
			]
		]);

		$c = new WebComponent();
		$c->addCssLink('a.css');
		$this->assertEquals($c->getCssLinks(), [
			'' => [
				'a.css' => 'a.css'
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getCssLinks(), [
			'' => [
				'a.css' => 'a.css',
				'z.css' => 'z.css',
			]
		]);
	}

	public function testCssLinkWithMedia() {
		$a = new WebComponent();
		$a->addCssLink('a.css', 'screen');
		$a->addCssLink('z.css', 'print');
		$this->assertEquals($a->getCssLinks(), [
			'screen' => [
				'a.css' => 'a.css',
			],
			'print' => [
				'z.css' => 'z.css',
			]
		]);

		$b = new WebComponent();
		$b->addCssLink('a.css', 'screen');
		$b->addCssLink('b.css', 'screen');
		$b->addCssLink('z.css', 'print');
		$this->assertEquals($b->getCssLinks(), [
			'screen' => [
				'a.css' => 'a.css',
				'b.css' => 'b.css',
			],
			'print' => [
				'z.css' => 'z.css',
			]
		]);

		$c = new WebComponent();
		$c->addCssLink('a.css', 'screen');
		$c->addCssLink('c.css', 'print');
		$c->addCssLink('z.css', 'print');
		$this->assertEquals($c->getCssLinks(), [
			'screen' => [
				'a.css' => 'a.css',
			],
			'print' => [
				'c.css' => 'c.css',
				'z.css' => 'z.css',
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getCssLinks(), [
			'screen' => [
				'a.css' => 'a.css',
				'b.css' => 'b.css',
			],
			'print' => [
				'c.css' => 'c.css',
				'z.css' => 'z.css',
			]
		]);
	}

	public function testCssCode() {
		$a = new WebComponent();
		$a->addCssCode('a');
		$a->addCssCode('b');
		$this->assertEquals($a->getCssCode(), "a\nb");

		$b = new WebComponent();
		$b->addCssCode('b');
		$this->assertEquals($b->getCssCode(), 'b');

		$c = new WebComponent();
		$c->addCssCode('c');
		$this->assertEquals($c->getCssCode(), 'c');

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getCssCode(), "a\nb\nc");
	}

	public function testJsLink() {
		$a = new WebComponent();
		$a->addJsLink('a.js');
		$a->addJsLink('z.js');
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer'],
				'z.jsdefer' => ['url' => 'z.js', 'type' => '', 'load' => 'defer'],
			],
			WebComponent::JS_BOTTOM => []
		]);

		$b = new WebComponent();
		$b->addJsLink('b.js');
		$this->assertEquals($b->getJsLinks(), [
			WebComponent::JS_TOP => [
				'b.jsdefer' => ['url' => 'b.js', 'type' => '', 'load' => 'defer']
			],
			WebComponent::JS_BOTTOM => []
		]);

		$c = new WebComponent();
		$c->addJsLink('c.js');
		$this->assertEquals($c->getJsLinks(), [
			WebComponent::JS_TOP => [
				'c.jsdefer' => ['url' => 'c.js', 'type' => '', 'load' => 'defer']
			],
			WebComponent::JS_BOTTOM => []
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer'],
				'z.jsdefer' => ['url' => 'z.js', 'type' => '', 'load' => 'defer'],
				'b.jsdefer' => ['url' => 'b.js', 'type' => '', 'load' => 'defer'],
				'c.jsdefer' => ['url' => 'c.js', 'type' => '', 'load' => 'defer'],
			],
			WebComponent::JS_BOTTOM => []
		]);
	}

	public function testJsLinkArray() {
		$a = new WebComponent();
		$a->addJsLink(['url' => 'a.js', 'integrity' => '1234']);
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => ['url' => 'a.js', 'integrity' => '1234'], 'type' => '', 'load' => 'defer'],
			],
			WebComponent::JS_BOTTOM => []
		]);
	}

	public function testSameJsLink() {
		$a = new WebComponent();
		$a->addJsLink('a.js');
		$a->addJsLink('z.js');
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer'],
				'z.jsdefer' => ['url' => 'z.js', 'type' => '', 'load' => 'defer'],
			],
			WebComponent::JS_BOTTOM => []
		]);

		$b = new WebComponent();
		$b->addJsLink('a.js');
		$this->assertEquals($b->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer']
			],
			WebComponent::JS_BOTTOM => []
		]);

		$c = new WebComponent();
		$c->addJsLink('a.js');
		$this->assertEquals($c->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer']
			],
			WebComponent::JS_BOTTOM => []
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [
				'a.jsdefer' => ['url' => 'a.js', 'type' => '', 'load' => 'defer'],
				'z.jsdefer' => ['url' => 'z.js', 'type' => '', 'load' => 'defer'],
			],
			WebComponent::JS_BOTTOM => []
		]);
	}

	public function testJsCode() {
		$a = new WebComponent();
		$a->addJsCode('a');
		$a->addJsCode('b');
		$this->assertEquals($a->getJsCode(), [
			'module' => [
				'' => "a\nb",
			]
		]);

		$b = new WebComponent();
		$b->addJsCode('b');
		$this->assertEquals($b->getJsCode(), [
			'module' => [
				'' => "b",
			]
		]);

		$c = new WebComponent();
		$c->addJsCode('c');
		$this->assertEquals($c->getJsCode(), [
			'module' => [
				'' => "c",
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsCode(), [
			'module' => [
				'' => "a\nb\nc",
			]
		]);
	}

	public function testJsCodeOptions() {
		$a = new WebComponent();
		$a->addJsCode('a', array('load' => 'async'));
		$a->addJsCode('b', array('type' => 'text/javascript'));
		$this->assertEquals($a->getJsCode(), [
			'text/javascript' => [
				'' => 'b'
			],
			'module' => [
				'async' => "a",
			]
		]);
	}

	public function testTranslate() {
		$a = new WebComponent();
		$a->addTranslator(__DIR__);
		$this->assertEquals($a->_('Hello world'), 'Bonjour monde');
		$this->assertEquals($a->_('This is %s', 'an apple'), 'Ceci est une pomme');
		$this->assertEquals($a->_('This is %s'), 'Ceci est %s');
		$this->assertEquals($a->_('Number of %d max', 10), 'Nombre de 10 max');
		$this->assertEquals(sprintf($a->_('Number of %d max'), 10), 'Nombre de 10 max');
	}

}