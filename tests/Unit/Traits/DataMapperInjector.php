<?php

namespace Tests\Unit\Traits;

use Osmuhin\HtmlMeta\Contracts\DataMapper;
use Osmuhin\HtmlMeta\Contracts\Distributor;
use Osmuhin\HtmlMeta\Distributors\AbstractDistributor;
use ReflectionClass;

trait DataMapperInjector
{
	protected static function injectDataMapper(Distributor $distributor, DataMapper $dataMapper, string $class): void
	{
		$property = (new ReflectionClass(AbstractDistributor::class))->getProperty('dataMappers');
		$dataMappers = $property->getValue($distributor);
		$dataMappers[$class] = $dataMapper;
		$property->setValue($distributor, $dataMappers);
	}
}
