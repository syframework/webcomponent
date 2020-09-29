<?php

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
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js',
				'z.js' => 'z.js',
			]
		]);

		$b = new WebComponent();
		$b->addJsLink('b.js');
		$this->assertEquals($b->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'b.js' => 'b.js'
			]
		]);

		$c = new WebComponent();
		$c->addJsLink('c.js');
		$this->assertEquals($c->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'c.js' => 'c.js'
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js',
				'z.js' => 'z.js',
				'b.js' => 'b.js',
				'c.js' => 'c.js',
			]
		]);
	}

	public function testSameJsLink() {
		$a = new WebComponent();
		$a->addJsLink('a.js');
		$a->addJsLink('z.js');
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js',
				'z.js' => 'z.js',
			]
		]);

		$b = new WebComponent();
		$b->addJsLink('a.js');
		$this->assertEquals($b->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js'
			]
		]);

		$c = new WebComponent();
		$c->addJsLink('a.js');
		$this->assertEquals($c->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js'
			]
		]);

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsLinks(), [
			WebComponent::JS_TOP => [],
			WebComponent::JS_BOTTOM => [
				'a.js' => 'a.js',
				'z.js' => 'z.js',
			]
		]);
	}

	public function testJsCode() {
		$a = new WebComponent();
		$a->addJsCode('a');
		$a->addJsCode('b');
		$this->assertEquals($a->getJsCode(), "a\nb");

		$b = new WebComponent();
		$b->addJsCode('b');
		$this->assertEquals($b->getJsCode(), 'b');

		$c = new WebComponent();
		$c->addJsCode('c');
		$this->assertEquals($c->getJsCode(), 'c');

		$b->setComponent('SLOT', $c);
		$a->setComponent('SLOT', $b);
		$this->assertEquals($a->getJsCode(), "a\nb\nc");
	}

}