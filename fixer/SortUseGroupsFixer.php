<?php

namespace Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceUseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;

final class SortUseGroupsFixer extends AbstractFixer implements WhitespacesAwareFixerInterface
{
	private const TYPE_CLASS = NamespaceUseAnalysis::TYPE_CLASS;

	private const TYPE_CONSTANT = NamespaceUseAnalysis::TYPE_CONSTANT;

	private const TYPE_FUNCTION = NamespaceUseAnalysis::TYPE_FUNCTION;

	/**
	 * @var list<int>
	 */
	private const GROUP_ORDER = [
		self::TYPE_CLASS,
		self::TYPE_CONSTANT,
		self::TYPE_FUNCTION
	];

	public function getDefinition(): FixerDefinitionInterface
	{
		return new FixerDefinition(
			'Sort namespace imports into class, const, and function groups with a blank line between groups.',
			[
				new CodeSample(
					<<<'PHP'
						<?php

						use function Illuminate\Filesystem\join_paths;
						use App\Models\File;
						use const Foo\BAR;
						use Illuminate\Support\Str;
						use function Laravel\Prompts\text;
						use const Foo\AAA;

						PHP
				)
			]
		);
	}

	public function getName(): string
	{
		return 'App/sort_use_groups';
	}

	/**
	 * {@inheritdoc}
	 *
	 * Must run after OrderedImportsFixer, BlankLineBetweenImportGroupsFixer.
	 */
	public function getPriority(): int
	{
		return -50;
	}

	public function isCandidate(Tokens $tokens): bool
	{
		return $tokens->isTokenKindFound(\T_USE);
	}

	protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
	{
		$tokensAnalyzer = new TokensAnalyzer($tokens);
		$namespacesImports = $tokensAnalyzer->getImportUseIndexes(true);
		$declarations = $this->indexDeclarationsByStart(
			(new NamespaceUsesAnalyzer())->getDeclarationsFromTokens($tokens)
		);

		foreach (array_reverse($namespacesImports, true) as $usesPerNamespace) {
			if ([] === $usesPerNamespace) {
				continue;
			}

			foreach (array_reverse($this->splitIntoContiguousGroups($tokens, $usesPerNamespace)) as $groupUses) {
				$this->fixGroup($tokens, $groupUses, $declarations);
			}
		}
	}

	/**
	 * @param list<NamespaceUseAnalysis> $declarations
	 *
	 * @return array<int, NamespaceUseAnalysis>
	 */
	private function indexDeclarationsByStart(array $declarations): array
	{
		$indexed = [];

		foreach ($declarations as $declaration) {
			$indexed[$declaration->getStartIndex()] = $declaration;
		}

		return $indexed;
	}

	/**
	 * @param list<int> $uses
	 *
	 * @return list<list<int>>
	 */
	private function splitIntoContiguousGroups(Tokens $tokens, array $uses): array
	{
		$groups = [];
		$groupOffset = 0;
		$groups[$groupOffset] = [$uses[0]];
		$count = \count($uses);

		for ($index = 0; $index < $count - 1; ++$index) {
			$nextGroupUse = $tokens->getNextTokenOfKind($uses[$index], [';', [\T_CLOSE_TAG]]);

			if ($tokens[$nextGroupUse]->isGivenKind(\T_CLOSE_TAG)) {
				$nextGroupUse = $tokens->getNextTokenOfKind($uses[$index], [[\T_OPEN_TAG]]);
			}

			$nextGroupUse = $tokens->getNextMeaningfulToken($nextGroupUse);

			if ($nextGroupUse !== $uses[$index + 1]) {
				$groups[++$groupOffset] = [];
			}

			$groups[$groupOffset][] = $uses[$index + 1];
		}

		return $groups;
	}

	/**
	 * @param list<int>                             $groupUses
	 * @param array<int, NamespaceUseAnalysis>      $declarations
	 */
	private function fixGroup(Tokens $tokens, array $groupUses, array $declarations): void
	{
		foreach ($groupUses as $useIndex) {
			if ($this->isGroupUse($tokens, $useIndex)) {
				return;
			}

			if (!isset($declarations[$useIndex])) {
				return;
			}
		}

		$imports = [];
		$previousEnd = null;

		foreach ($groupUses as $useIndex) {
			$declaration = $declarations[$useIndex];
			$bounds = $this->findImportBounds(
				$tokens,
				$useIndex,
				null !== $previousEnd ? $previousEnd + 1 : null
			);
			$imports[] = [
				'start' => $bounds['start'],
				'end' => $bounds['end'],
				'type' => $declaration->getType(),
				'fqn' => $declaration->getFullName()
			];
			$previousEnd = $bounds['end'];
		}

		$contentStart = $imports[0]['start'];
		$contentEnd = $imports[0]['end'];

		foreach ($imports as $import) {
			$contentEnd = max($contentEnd, $import['end']);
		}

		$blockRange = $this->findImportBlockRange($tokens, $contentStart, $contentEnd);
		$newTokens = $this->buildSortedTokens(
			$tokens,
			$imports,
			$blockRange['start'],
			$blockRange['end'],
			$groupUses[0]
		);

		if ($this->tokensRangesEqual($tokens, $blockRange['start'], $blockRange['end'], $newTokens)) {
			return;
		}

		$tokens->overrideRange($blockRange['start'], $blockRange['end'], $newTokens);
	}

	private function isGroupUse(Tokens $tokens, int $useIndex): bool
	{
		$endIndex = $tokens->getNextTokenOfKind($useIndex, [';', [\T_CLOSE_TAG]]);

		for ($index = $useIndex; $index <= $endIndex; ++$index) {
			if ($tokens[$index]->isGivenKind(CT::T_GROUP_IMPORT_BRACE_OPEN)) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return array{start: int, end: int}
	 */
	private function findImportBounds(Tokens $tokens, int $useIndex, ?int $lowerBound): array
	{
		$start = $useIndex;
		$bound = $lowerBound ?? 0;
		$index = $useIndex - 1;

		while ($index >= $bound) {
			$token = $tokens[$index];

			if ($token->isComment()) {
				$start = $index;
				--$index;

				continue;
			}

			if ($token->isWhitespace()) {
				$content = $token->getContent();

				if (!$this->containsLineBreak($content)) {
					$start = $index;
					--$index;

					continue;
				}

				if ($this->hasBlankLine($content)) {
					break;
				}

				if ($index > $bound && $tokens[$index - 1]->isComment()) {
					$start = $index - 1;
					$index -= 2;

					continue;
				}

				break;
			}

			break;
		}

		$semicolonIndex = $tokens->getNextTokenOfKind($useIndex, [';', [\T_CLOSE_TAG]]);
		$end = $semicolonIndex;

		for ($index = $semicolonIndex + 1, $limit = \count($tokens); $index < $limit; ++$index) {
			$token = $tokens[$index];

			if ($token->isComment()) {
				$end = $index;

				continue;
			}

			if ($token->isWhitespace() && !$this->containsLineBreak($token->getContent())) {
				$end = $index;

				continue;
			}

			break;
		}

		return ['start' => $start, 'end' => $end];
	}

	/**
	 * @return array{start: int, end: int}
	 */
	private function findImportBlockRange(Tokens $tokens, int $contentStart, int $contentEnd): array
	{
		$blockStart = $contentStart;

		for ($index = $contentStart - 1; $index >= 0; --$index) {
			$token = $tokens[$index];

			if (!$token->isWhitespace()) {
				break;
			}

			if ($this->hasBlankLine($token->getContent())) {
				break;
			}

			$blockStart = $index;
		}

		$blockEnd = $contentEnd;
		$limit = \count($tokens);

		for ($index = $contentEnd + 1; $index < $limit; ++$index) {
			$token = $tokens[$index];

			if (!$token->isWhitespace()) {
				break;
			}

			if ($this->hasBlankLine($token->getContent())) {
				break;
			}

			$blockEnd = $index;
		}

		return ['start' => $blockStart, 'end' => $blockEnd];
	}

	/**
	 * @param list<array{start: int, end: int, type: int, fqn: string}> $imports
	 */
	private function buildSortedTokens(
		Tokens $tokens,
		array $imports,
		int $blockStart,
		int $blockEnd,
		int $firstUseIndex
	): Tokens {
		$grouped = [
			self::TYPE_CLASS => [],
			self::TYPE_CONSTANT => [],
			self::TYPE_FUNCTION => []
		];

		foreach ($imports as $import) {
			$grouped[$import['type']][] = $import;
		}

		foreach ($grouped as $type => $groupImports) {
			usort(
				$groupImports,
				static fn (array $first, array $second): int => strcasecmp($first['fqn'], $second['fqn'])
			);
			$grouped[$type] = $groupImports;
		}

		$contentStart = $imports[0]['start'];
		$contentEnd = $imports[0]['end'];

		foreach ($imports as $import) {
			$contentEnd = max($contentEnd, $import['end']);
		}

		$lineEnding = $this->whitespacesConfig->getLineEnding();
		$indent = WhitespacesAnalyzer::detectIndent($tokens, $firstUseIndex);
		$tokenItems = [];

		for ($index = $blockStart; $index < $contentStart; ++$index) {
			$tokenItems[] = clone $tokens[$index];
		}

		$isFirstGroup = true;

		foreach (self::GROUP_ORDER as $type) {
			if ([] === $grouped[$type]) {
				continue;
			}

			if (!$isFirstGroup) {
				$tokenItems[] = new Token([\T_WHITESPACE, $lineEnding . $lineEnding . $indent]);
			}

			$isFirstGroup = false;
			$isFirstImport = true;

			foreach ($grouped[$type] as $import) {
				if (!$isFirstImport) {
					$tokenItems[] = new Token([\T_WHITESPACE, $lineEnding . $indent]);
				}

				$isFirstImport = false;

				for ($index = $import['start']; $index <= $import['end']; ++$index) {
					$tokenItems[] = clone $tokens[$index];
				}
			}
		}

		for ($index = $contentEnd + 1; $index <= $blockEnd; ++$index) {
			$tokenItems[] = clone $tokens[$index];
		}

		return Tokens::fromArray($tokenItems);
	}

	private function tokensRangesEqual(Tokens $tokens, int $start, int $end, Tokens $replacement): bool
	{
		$tokenItems = [];

		for ($index = $start; $index <= $end; ++$index) {
			$tokenItems[] = clone $tokens[$index];
		}

		$original = Tokens::fromArray($tokenItems);

		return $original->generateCode() === $replacement->generateCode();
	}

	private function containsLineBreak(string $content): bool
	{
		return str_contains($content, "\n") || str_contains($content, "\r");
	}

	private function hasBlankLine(string $content): bool
	{
		return str_contains($content, "\n\n")
			|| str_contains($content, "\r\n\r\n")
			|| str_contains($content, "\r\r");
	}
}
