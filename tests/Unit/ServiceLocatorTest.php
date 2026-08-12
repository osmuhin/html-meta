<?php

namespace Tests\Unit;

use Osmuhin\HtmlMeta\Container;
use Osmuhin\HtmlMeta\ServiceLocator;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;
use RuntimeException;
use Tests\Unit\Fixtures\ServiceLocatorTestSubject1;
use Tests\Unit\Fixtures\ServiceLocatorTestSubject2;

use function PHPUnit\Framework\assertEmpty;
use function PHPUnit\Framework\assertNotSame;
use function PHPUnit\Framework\assertSame;

class ServiceLocatorTest extends TestCase
{
	private ReflectionProperty $count;

	private ReflectionProperty $containers;

	#[Before]
	public function setUpCountProperty(): void
	{
		$this->count = new ReflectionProperty(ServiceLocator::class, 'count');
	}

	#[Before]
	public function setUpContainersProperty(): void
	{
		$this->containers = new ReflectionProperty(ServiceLocator::class, 'containers');
	}

	#[After]
	public function destructContainer(): void
	{
		try {
			ServiceLocator::destructContainer();
		} catch (RuntimeException $e) {

		}
	}

	public function test_can_register_new_container_and_destruct_it(): void
	{
		assertSame(0, $this->count->getValue());

		ServiceLocator::register($container = new Container());

		$thisObjectId = spl_object_id($this);

		assertSame(1, $this->count->getValue());
		assertSame(
			[self::class => [$thisObjectId => $container]],
			$this->containers->getValue()
		);

		ServiceLocator::destructContainer();

		assertSame(0, $this->count->getValue());
		assertEmpty($this->containers->getValue());
	}

	public function test_exception_when_there_arent_containers(): void
	{
		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage('Unable to find container');

		ServiceLocator::container();
	}

	public function test_containers_in_multiple_instances()
	{
		$subject1_1 = new ServiceLocatorTestSubject1();
		$subject1_2 = new ServiceLocatorTestSubject1();
		$subject2_1 = new ServiceLocatorTestSubject2();

		assertSame(3, $this->count->getValue());
		assertSame(
			[
				ServiceLocatorTestSubject1::class => [
					spl_object_id($subject1_1) => $subject1_1->container,
					spl_object_id($subject1_2) => $subject1_2->container
				],
				ServiceLocatorTestSubject2::class => [
					spl_object_id($subject2_1) => $subject2_1->container
				]
			],
			$this->containers->getValue()
		);

		assertSame($subject1_1->container, $subject1_1->getContainerViaServiceLocator());
		assertSame($subject1_2->container, $subject1_2->getContainerViaServiceLocator());
		assertSame($subject2_1->container, $subject2_1->getContainerViaServiceLocator());
		assertNotSame($subject1_1->container, $subject1_2->getContainerViaServiceLocator());

		unset($subject1_2);
		unset($subject2_1);

		assertSame(1, $this->count->getValue());
		assertSame(
			[
				ServiceLocatorTestSubject1::class => [
					spl_object_id($subject1_1) => $subject1_1->container
				]
			],
			$this->containers->getValue()
		);
	}

	public function test_exception_no_container_associated(): void
	{
		$subject = new ServiceLocatorTestSubject1();
		$objectId = spl_object_id($subject);
		$class = $subject::class;

		$this->containers->setValue(null, [$subject::class => []]);

		$this->expectException(RuntimeException::class);
		$this->expectExceptionMessage("No container associated with object {$objectId} of class {$class}");
	}
}
