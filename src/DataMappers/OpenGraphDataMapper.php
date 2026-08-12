<?php

namespace Osmuhin\HtmlMeta\DataMappers;

use Osmuhin\HtmlMeta\Dto\OpenGraph\Audio;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Image;
use Osmuhin\HtmlMeta\Dto\OpenGraph\Video;

/**
 * Maps Open Graph properties onto OpenGraph DTOs (including image/video/audio objects).
 *
 * @codeCoverageIgnore
 */
class OpenGraphDataMapper extends AbstractDataMapper
{
	protected function getMap(): array
	{
		return [
			'og:title' => 'title',
			'og:type' => 'type',
			'og:url' => $this->url('url'),
			'og:description' => 'description',
			'og:determiner' => 'determiner',
			'og:site_name' => 'siteName',
			'og:locale' => 'locale'
		];
	}

	protected function getImageMap(): array
	{
		return [
			'og:image' => $this->guessMimeType($this->url('url')),
			'og:image:url' => $this->guessMimeType($this->url('url')),
			'og:image:secure_url' => $this->guessMimeType($this->url('secureUrl')),
			'og:image:type' => $this->forceOverwrite('type'),
			'og:image:width' => $this->int('width'),
			'og:image:height' => $this->int('height'),
			'og:image:alt' => 'alt'
		];
	}

	protected function getVideoMap(): array
	{
		return [
			'og:video' => $this->guessMimeType($this->url('url')),
			'og:video:url' => $this->guessMimeType($this->url('url')),
			'og:video:secure_url' => $this->guessMimeType($this->url('secureUrl')),
			'og:video:type' => $this->forceOverwrite('type'),
			'og:video:width' => $this->int('width'),
			'og:video:height' => $this->int('height')
		];
	}

	protected function getAudioMap(): array
	{
		return [
			'og:audio' => $this->guessMimeType($this->url('url')),
			'og:audio:url' => $this->guessMimeType($this->url('url')),
			'og:audio:secure_url' => $this->guessMimeType($this->url('secureUrl')),
			'og:audio:type' => $this->forceOverwrite('type'),
		];
	}

	public function assign(string $key, string $content): bool
	{
		return $this->assignAccordingToTheMap(
			$this->getMap(),
			$this->meta->openGraph,
			$key,
			$content
		);
	}

	public function assignImage(string $key, string $content, bool $asNew): bool
	{
		if ($asNew) {
			$image = new Image();
		} else {
			if (!$this->meta->openGraph->images) {
				return false;
			}

			$image = array_pop($this->meta->openGraph->images);
		}

		$assignmentResult = $this->assignAccordingToTheMap($this->getImageMap(), $image, $key, $content);

		$this->meta->openGraph->images[] = $image;

		return $assignmentResult;
	}

	public function assignVideo(string $key, string $content, bool $asNew): bool
	{
		if ($asNew) {
			$video = new Video();
		} else {
			if (!$this->meta->openGraph->videos) {
				return false;
			}

			$video = array_pop($this->meta->openGraph->videos);
		}

		$assignmentResult = $this->assignAccordingToTheMap($this->getVideoMap(), $video, $key, $content);

		$this->meta->openGraph->videos[] = $video;

		return $assignmentResult;
	}

	public function assignAudio(string $key, string $content, bool $asNew): bool
	{
		if ($asNew) {
			$audio = new Audio();
		} else {
			if (!$this->meta->openGraph->audio) {
				return false;
			}

			$audio = array_pop($this->meta->openGraph->audio);
		}

		$assignmentResult = $this->assignAccordingToTheMap($this->getAudioMap(), $audio, $key, $content);

		$this->meta->openGraph->audio[] = $audio;

		return $assignmentResult;
	}
}
