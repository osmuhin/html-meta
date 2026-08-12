<?php

namespace Tests\Unit\Traits;

use Osmuhin\HtmlMeta\Contracts\DataMapper;
use Osmuhin\HtmlMeta\Contracts\Distributor;
use ReflectionClass;

trait DataMapperInjector
{
	protected static function injectDataMapper(Distributor $distributor, DataMapper $dataMapper)
	{
		$reflection = new ReflectionClass($distributor);

		$property = $reflection->getProperty('dataMapper');
		$property->setValue($distributor, $dataMapper);
	}
}
