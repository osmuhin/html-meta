<?php

namespace Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * Remove trailing commas in multiline lists (arrays, calls, parameters, attributes).
 */
class NoTrailingCommaInMultilineFixer extends AbstractFixer
{
	public function getDefinition(): FixerDefinitionInterface
	{
		return new FixerDefinition(
			'Multiline lists must not have a trailing comma after the last item.',
			[
				new CodeSample(
					"<?php\n#[Table(\n\tname: 'users',\n)]\nclass User {}\n"
				),
				new CodeSample(
					"<?php\n\$a = [\n\t1,\n\t2,\n];\n"
				)
			]
		);
	}

	public function getName(): string
	{
		return 'App/no_trailing_comma_in_multiline';
	}

	public function isCandidate(Tokens $tokens): bool
	{
		return $tokens->isTokenKindFound(',')
			&& $tokens->isAnyTokenKindsFound([
				')',
				CT::T_ARRAY_BRACKET_CLOSE,
				CT::T_DESTRUCTURING_BRACKET_CLOSE,
				CT::T_GROUP_IMPORT_BRACE_CLOSE
			]);
	}

	/**
	 * Run after spacing fixers so comma removal sees final layout.
	 */
	public function getPriority(): int
	{
		return -5;
	}

	protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
	{
		for ($index = $tokens->count() - 1; $index >= 0; --$index) {
			if (
				!$tokens[$index]->equals(')')
				&& !$tokens[$index]->isGivenKind([
					CT::T_ARRAY_BRACKET_CLOSE,
					CT::T_DESTRUCTURING_BRACKET_CLOSE,
					CT::T_GROUP_IMPORT_BRACE_CLOSE
				])
			) {
				continue;
			}

			$commaIndex = $tokens->getPrevMeaningfulToken($index);

			if ($commaIndex === null || !$tokens[$commaIndex]->equals(',')) {
				continue;
			}

			$block = Tokens::detectBlockType($tokens[$index]);

			if ($block === null) {
				continue;
			}

			$blockOpenIndex = $tokens->findBlockStart($block['type'], $index);

			if (!$tokens->isPartialCodeMultiline($blockOpenIndex, $index)) {
				continue;
			}

			do {
				$tokens->clearAt($commaIndex);
				$commaIndex = $tokens->getPrevMeaningfulToken($commaIndex);
			} while ($commaIndex !== null && $tokens[$commaIndex]->equals(','));
		}
	}
}
