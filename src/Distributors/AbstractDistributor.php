<?php

namespace Osmuhin\HtmlMeta\Distributors;

use InvalidArgumentException;
use Osmuhin\HtmlMeta\Config;
use Osmuhin\HtmlMeta\Context;
use Osmuhin\HtmlMeta\Contracts\DataMapper;
use Osmuhin\HtmlMeta\Contracts\Distributor;
use Osmuhin\HtmlMeta\Dto\Meta;
use Osmuhin\HtmlMeta\Element;

/**
 * Base distributor with sub-distributor tree polling and element attribute helpers.
 */
abstract class AbstractDistributor implements Distributor
{
	public Element $el;

	protected Context $context;

	protected Config $config;

	protected Meta $meta;

	/** @var \Osmuhin\HtmlMeta\Distributors\AbstractDistributor[] */
	private array $subDistributors = [];

	/** @var array<class-string<DataMapper>, DataMapper> */
	private array $dataMappers = [];

	public function __construct(Context $context)
	{
		$this->context = $context;
		$this->meta = $context->meta;
		$this->config = $context->config;
	}

	public static function init(Context $context): self
	{
		return new static($context);
	}

	public function useSubDistributors(...$args): self
	{
		foreach ($args as $distributor) {
			$this->setSubDistributor($distributor);
		}

		return $this;
	}

	/**
	 * @template T of DataMapper
	 *
	 * @param class-string<T> $class
	 *
	 * @return T
	 */
	protected function dataMapper(string $class): DataMapper
	{
		return $this->dataMappers[$class] ??= new $class($this->context);
	}

	/**
	 * @throws InvalidArgumentException
	 */
	public function setSubDistributor(Distributor|string $distributor, ?string $key = null): self
	{
		$distributor = is_string($distributor) ? new $distributor($this->context) : $distributor;

		if (!($distributor instanceof Distributor)) {
			$class = $distributor::class;
			throw new InvalidArgumentException("{$class} must implements \Osmuhin\HtmlMeta\Contracts\Distributor interface");
		}

		if ($key) {
			$this->subDistributors[$key] = $distributor;
		} else {
			$key = $distributor::class;

			!isset($this->subDistributors[$key]) && $this->subDistributors[$key] = $distributor;
		}

		return $this;
	}

	public function getSubDistributor(string $key): Distributor|null
	{
		return @$this->subDistributors[$key];
	}

	protected function pollSubDistributors(): bool
	{
		foreach ($this->subDistributors as $subDistributor) {
			$subDistributor->el = $this->el;

			if ($subDistributor->canHandle()) {
				$subDistributor->pollSubDistributors() || $subDistributor->handle();

				return true;
			}
		}

		return false;
	}

	protected function elAttr(string $attribute, bool $trim = true, bool $lowercase = true): ?string
	{
		if (!$value = @$this->el->attributes[$attribute]) {
			return null;
		}

		$trim && $value = trim($value);
		$lowercase && $value = mb_strtolower($value, 'UTF-8');

		return $value;
	}
}
