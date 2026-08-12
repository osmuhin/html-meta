<?php

namespace Tests\Unit\Distributors;

use Osmuhin\HtmlMeta\DataMappers\TwitterDataMapper;
use Osmuhin\HtmlMeta\Distributors\TwitterDistributor;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Traits\DataMapperInjector;
use Tests\Unit\Traits\ElementCreator;
use Tests\Unit\Traits\SetupContext;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertSame;

final class TwitterDistributorTest extends TestCase
{
	use ElementCreator, SetupContext, DataMapperInjector;

	private TwitterDistributor $distributor;

	protected function setUp(): void
	{
		$this->distributor = new TwitterDistributor($this->context);
	}

	public function test_can_handle_method(): void
	{
		$this->distributor->el = self::makeElement('meta');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeNamedMetaElement('    ', '    ');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeMetaWithProperty('    ', '    ');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeMetaWithProperty('twitterProperty', 'someContent');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeMetaWithProperty('twitter:name', '');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeMetaWithProperty('twitter:name', '    ');
		self::assertFalse($this->distributor->canHandle());

		$this->distributor->el = self::makeMetaWithProperty('twitter:name', 'someContent');
		self::assertTrue($this->distributor->canHandle());

		$this->distributor->el = self::makeNamedMetaElement('twitter:name', 'someContent');
		self::assertTrue($this->distributor->canHandle());
	}

	public function test_handle_method_uses_data_mapper(): void
	{
		$dataMapper = self::createMock(TwitterDataMapper::class);

		$dataMapper->expects($this->once())
			->method('assign')
			->with($this->identicalTo('twitter:card'), $this->identicalTo('summary_large_image'))
			->willReturn(true);

		self::injectDataMapper($this->distributor, $dataMapper, TwitterDataMapper::class);

		$this->distributor->el = self::makeMetaWithProperty('twitter:card', 'summary_large_image');

		$this->distributor->canHandle();
		$this->distributor->handle();

		assertEmpty($this->meta->twitter->other);
	}

	public function test_handle_method_write_other_property_of_dto(): void
	{
		$dataMapper = self::createMock(TwitterDataMapper::class);

		$dataMapper->expects($this->once())
			->method('assign')
			->with($this->identicalTo('twitter:app:id:iphone', '123456789'))
			->willReturn(false);

		self::injectDataMapper($this->distributor, $dataMapper, TwitterDataMapper::class);

		$this->distributor->el = self::makeMetaWithProperty('twitter:app:id:iphone', '123456789');

		$this->distributor->canHandle();
		$this->distributor->handle();

		assertSame(['twitter:app:id:iphone' => '123456789'], $this->meta->twitter->other);
	}
}
