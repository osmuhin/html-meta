<?php

namespace Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;

final class RemoveStrictTypesFixer extends AbstractFixer
{
	public function getDefinition(): FixerDefinitionInterface
	{
		return new FixerDefinition('Remove declare(strict_types=1)', []);
	}

	public function getName(): string
	{
		return 'App/remove_strict_types';
	}

	public function isCandidate(Tokens $tokens): bool
	{
		return $tokens->isTokenKindFound(T_DECLARE);
	}

	protected function applyFix(\SplFileInfo $file, Tokens $tokens): void
	{
		foreach ($tokens as $index => $token) {
			if (!$token->isGivenKind(T_DECLARE)) {
				continue;
			}

			$endIndex = $tokens->getNextTokenOfKind($index, [';']);
			$content = '';
			for ($i = $index; $i <= $endIndex; $i++) {
				$content .= $tokens[$i]->getContent();
			}

			if (stripos($content, 'strict_types') !== false) {
				$tokens->clearRange($index, $endIndex);
				// Удаляем лишние переводы строк после
				if ($tokens[$endIndex + 1]->isWhitespace()) {
					$tokens->clearAt($endIndex + 1);
				}
			}
		}
	}
}
