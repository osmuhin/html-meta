<?php

namespace Osmuhin\HtmlMeta\Dto\OpenGraph;

use Osmuhin\HtmlMeta\Contracts\Dto;

/** Open Graph profile object (`profile:*` properties). */
class Profile implements Dto
{
	public ?string $firstName = null;

	public ?string $lastName = null;

	public ?string $username = null;

	public ?string $gender = null;

	/** @var array<string, string> */
	public array $other = [];

	public function toArray(): array
	{
		return [
			'firstName' => $this->firstName,
			'lastName' => $this->lastName,
			'username' => $this->username,
			'gender' => $this->gender,
			'other' => $this->other,
		];
	}
}
