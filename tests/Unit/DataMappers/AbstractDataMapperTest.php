<?php

namespace Tests\Unit\DataMappers;

use Osmuhin\HtmlMeta\Context;
use Osmuhin\HtmlMeta\DataMappers\AbstractDataMapper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Tests\Unit\Traits\SetupContext;

use function PHPUnit\Framework\assertFalse;
use function PHPUnit\Framework\assertSame;
use function PHPUnit\Framework\assertTrue;

final class AbstractDataMapperTest extends TestCase
{
	use SetupContext;

	public static function providerForTestingOfIntConversions(): array
	{
		return [
			[true, '15', 15],
			[true, '15.5', null],
			[true, 'asdasd', null],
			[false, '15', '15'],
			[false, 'asdasd', 'asdasd']
		];
	}

	public static function providerForTestingOfUrlProcessing(): array
	{
		return [
			[true, 'favicon.ico', 'http://example.com/api/favicon.ico'],
			[false, 'favicon.ico', 'favicon.ico']
		];
	}

	private function makeDataMapper(array $onlyMethods = []): AbstractDataMapper
	{
		return $this->getMockBuilder(AbstractDataMapper::class)
			->setConstructorArgs([$this->context])
			->onlyMethods($onlyMethods)
			->getMock();
	}

	public function test_assigning_property_with_object()
	{
		$dataMapper = $this->makeDataMapper();

		$object = new stdClass();
		$object->someProp = null;
		$object->someProp1 = 'someValue';

		$dataMapper->assignPropertyWithObject($object, 'someProp', '123');
		$dataMapper->assignPropertyWithObject($object, 'someProp', '321');

		assertSame('123', $object->someProp);

		$callable = fn (string $value, stdClass $object) => $object->someProp1 = $value . 'foo';

		$dataMapper->assignPropertyWithObject($object, $callable, 'bar');

		assertSame('barfoo', $object->someProp1);
	}

	public function test_assigning_according_to_map(): void
	{
		$dataMapper = $this->makeDataMapper(['assignPropertyWithObject']);

		$map = ['key1' => 'property1'];

		$object = new stdClass();
		$object->property1 = null;

		$dataMapper
			->expects($this->once())
			->method('assignPropertyWithObject')
			->with(
				$this->identicalTo($object),
				$this->identicalTo('property1'),
				$this->identicalTo('foo')
			);

		assertTrue(
			$dataMapper->assignAccordingToTheMap($map, $object, 'key1', 'foo')
		);

		assertFalse(
			$dataMapper->assignAccordingToTheMap($map, $object, 'key2', 'foo')
		);
	}

	#[DataProvider('providerForTestingOfIntConversions')]
	public function test_int_convention(bool $useTypeConversions, string $input, mixed $expected): void
	{
		$this->config->dontUseTypeConversions(!$useTypeConversions);

		$dataMapper = $this->makeDataMapper(['assignPropertyWithObject']);

		$dataMapper
			->expects($this->once())
			->method('assignPropertyWithObject')
			->with(
				$this->isObject(),
				$this->identicalTo('property'),
				$this->identicalTo($expected)
			);

		$dataMapper->int('property')($input, new stdClass());
	}

	#[DataProvider('providerForTestingOfUrlProcessing')]
	public function test_url_processing(bool $useUrlProcessing, string $input, mixed $expected): void
	{
		$this->config->processUrlsWith('http://example.com/api/');
		$this->config->dontProcessUrls(!$useUrlProcessing);

		$dataMapper = $this->makeDataMapper(['assignPropertyWithObject']);

		$dataMapper
			->expects($this->once())
			->method('assignPropertyWithObject')
			->with(
				$this->isObject(),
				$this->identicalTo('property'),
				$this->identicalTo($expected)
			);

		$dataMapper->url('property')($input, new stdClass());
	}

	public function test_force_overwriting(): void
	{
		$dataMapper = new class($this->context) extends AbstractDataMapper
		{
			public function __construct(Context $context)
			{
				parent::__construct($context);
			}
		};

		$object = new stdClass();
		$object->property = 123;

		$dataMapper->forceOverwrite('property')(321, $object);

		assertSame($object->property, 321);
	}

	public function test_mime_type_guessing(): void
	{
		$dataMapper = $this->makeDataMapper();

		$object = new stdClass();
		$object->property = null;
		$object->type = null;

		$dataMapper->guessMimeType('property')('/storage/image.jpg', $object);

		assertSame($object->property, '/storage/image.jpg');
		assertSame($object->type, 'image/jpeg');
	}
}
