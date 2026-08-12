<?php

namespace Tests\Unit\Traits;

use Osmuhin\HtmlMeta\Config;
use Osmuhin\HtmlMeta\Context;
use Osmuhin\HtmlMeta\Dto\Meta;
use PHPUnit\Framework\Attributes\Before;

trait SetupContext
{
	protected Meta $meta;

	protected Config $config;

	protected Context $context;

	#[Before]
	protected function setUpContext(): void
	{
		$this->meta = new Meta();
		$this->config = new Config();
		$this->context = new Context($this->meta, $this->config);

		parent::setUp();
	}
}
