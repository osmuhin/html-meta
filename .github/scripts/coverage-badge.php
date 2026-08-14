<?php

/**
 * Build a shields-style coverage SVG from a PHPUnit clover report.
 *
 * Usage: php .github/scripts/coverage-badge.php build/logs/clover.xml .github/coverage.svg
 */

$cloverPath = $argv[1] ?? 'build/logs/clover.xml';
$outputPath = $argv[2] ?? '.github/coverage.svg';

if (!is_file($cloverPath)) {
	fwrite(STDERR, "Clover report not found: {$cloverPath}\n");
	exit(1);
}

$clover = simplexml_load_file($cloverPath);

if ($clover === false) {
	fwrite(STDERR, "Failed to parse clover report: {$cloverPath}\n");
	exit(1);
}

$metrics = $clover->project->metrics ?? null;
$statements = $metrics !== null ? (int) $metrics['statements'] : 0;
$covered = $metrics !== null ? (int) $metrics['coveredstatements'] : 0;
$percent = $statements > 0 ? (int) round(($covered / $statements) * 100) : 0;

$color = match (true) {
	$percent >= 90 => '#4c1',
	$percent >= 80 => '#97ca00',
	$percent >= 70 => '#a4a61d',
	$percent >= 60 => '#dfb317',
	$percent >= 50 => '#fe7d37',
	default => '#e05d44',
};

$left = 'coverage';
$right = $percent . '%';
$leftWidth = 10 + (int) round(strlen($left) * 6.6);
$rightWidth = 10 + (int) round(strlen($right) * 7.2);
$totalWidth = $leftWidth + $rightWidth;
$leftCenter = (int) round($leftWidth * 5);
$rightCenter = (int) round(($leftWidth + $rightWidth / 2) * 10);
$leftTextLength = (int) round(strlen($left) * 66);
$rightTextLength = (int) round(strlen($right) * 70);

$svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="{$totalWidth}" height="20" role="img" aria-label="{$left}: {$right}">
	<title>{$left}: {$right}</title>
	<linearGradient id="s" x2="0" y2="100%">
		<stop offset="0" stop-color="#bbb" stop-opacity=".1"/>
		<stop offset="1" stop-opacity=".1"/>
	</linearGradient>
	<clipPath id="r">
		<rect width="{$totalWidth}" height="20" rx="3" fill="#fff"/>
	</clipPath>
	<g clip-path="url(#r)">
		<rect width="{$leftWidth}" height="20" fill="#555"/>
		<rect x="{$leftWidth}" width="{$rightWidth}" height="20" fill="{$color}"/>
		<rect width="{$totalWidth}" height="20" fill="url(#s)"/>
	</g>
	<g fill="#fff" text-anchor="middle" font-family="Verdana,Geneva,DejaVu Sans,sans-serif" text-rendering="geometricPrecision" font-size="110">
		<text aria-hidden="true" x="{$leftCenter}" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="{$leftTextLength}">{$left}</text>
		<text x="{$leftCenter}" y="140" transform="scale(.1)" fill="#fff" textLength="{$leftTextLength}">{$left}</text>
		<text aria-hidden="true" x="{$rightCenter}" y="150" fill="#010101" fill-opacity=".3" transform="scale(.1)" textLength="{$rightTextLength}">{$right}</text>
		<text x="{$rightCenter}" y="140" transform="scale(.1)" fill="#fff" textLength="{$rightTextLength}">{$right}</text>
	</g>
</svg>

SVG;

if (file_put_contents($outputPath, $svg) === false) {
	fwrite(STDERR, "Failed to write badge: {$outputPath}\n");
	exit(1);
}

echo "Wrote {$outputPath} ({$percent}%)\n";
