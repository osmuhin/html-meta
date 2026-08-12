<?php

namespace Osmuhin\HtmlMeta\Distributors;

use InvalidArgumentException;
use Osmuhin\HtmlMeta\Config;
use Osmuhin\HtmlMeta\Container;
use Osmuhin\HtmlMeta\Contracts\Distributor;
use Osmuhin\HtmlMeta\Dto\Meta;
use Osmuhin\HtmlMeta\Element;
use Osmuhin\HtmlMeta\ServiceLocator;

/**
 * Base distributor with sub-distributor tree polling and element attribute helpers.
 */
abstract class AbstractDistributor implements Distributor
{
	public Element $el;

	protected Container $container;

	protected Config $config;

	protected Meta $meta;

	/** @var \Osmuhin\HtmlMeta\Distributors\AbstractDistributor[] */
	private array $subDistributors = [];

	public function __construct(?Container $container = null)
	{
		$this->container = $container ?: ServiceLocator::container();
		$this->meta = $this->container->get(Meta::class);
		$this->config = $this->container->get(Config::class);
	}

	public static function init(?Container $container = null): self
	{
		return new static($container);
	}

	public function useSubDistributors(...$args): self
	{
		$distributors = $args;

		foreach ($distributors as $distributor) {
			$this->setSubDistributor($distributor);
		}

		return $this;
	}

	/**
	 * @throws \InvalidArgumentException
	 */
	public function setSubDistributor(Distributor|string $distributor, ?string $key = null): self
	{
		$distributor = is_string($distributor) ? new $distributor($this->container) : $distributor;

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
