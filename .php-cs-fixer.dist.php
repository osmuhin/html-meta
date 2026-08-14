<?php

$finder = PhpCsFixer\Finder::create()
	->in([
		__DIR__ . '/src',
		__DIR__ . '/tests',
		__DIR__ . '/fixer'
	])
	->append([
		__DIR__ . '/.php-cs-fixer.dist.php'
	])
	->name('*.php');

return (new PhpCsFixer\Config())
	->setRiskyAllowed(false)
	->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
	->setIndent("\t")
	->setLineEnding("\n")
	->registerCustomFixers([
		new \Fixer\RemoveStrictTypesFixer(),
		new \Fixer\SortUseGroupsFixer(),
		new \Fixer\NoTrailingCommaInMultilineFixer()
	])
	->setRules([
		'App/remove_strict_types' => true,
		'App/sort_use_groups' => true,
		'App/no_trailing_comma_in_multiline' => true,

		'unary_operator_spaces' => true,
		'array_indentation' => true,
		'array_syntax' => ['syntax' => 'short'],
		'binary_operator_spaces' => ['default' => 'at_least_single_space'],
		'blank_line_after_namespace' => true,
		'blank_line_after_opening_tag' => true,
		'blank_line_between_import_groups' => true,
		'trailing_comma_in_multiline' => false,
		'no_trailing_comma_in_singleline' => true,
		'braces_position' => [
			'allow_single_line_empty_anonymous_classes' => true,
			'allow_single_line_anonymous_functions' => false,
			'anonymous_classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
			'anonymous_functions_opening_brace' => 'same_line',
			'classes_opening_brace' => 'next_line_unless_newline_at_signature_end',
			'control_structures_opening_brace' => 'same_line',
			'functions_opening_brace' => 'next_line_unless_newline_at_signature_end'
		],
		'cast_spaces' => ['space' => 'single'],
		'class_attributes_separation' => [
			'elements' => [
				'case' => 'none',
				'const' => 'one',
				'method' => 'one',
				'property' => 'one',
				'trait_import' => 'none'
			]
		],
		'concat_space' => ['spacing' => 'one'],
		'control_structure_braces' => true,
		'control_structure_continuation_position' => true,
		'declare_parentheses' => true,
		'encoding' => true,
		'full_opening_tag' => true,
		'function_declaration' => ['closure_fn_spacing' => 'one'],
		'indentation_type' => true,
		'line_ending' => true,
		'lowercase_keywords' => true,
		'method_argument_space' => ['on_multiline' => 'ignore'],
		'new_with_parentheses' => ['anonymous_class' => false],
		'no_closing_tag' => true,
		'no_empty_statement' => true,
		'no_extra_blank_lines' => [
			'tokens' => [
				'break',
				'continue',
				'curly_brace_block',
				'extra',
				'parenthesis_brace_block',
				'return',
				'square_brace_block',
				'throw',
				'use_trait'
			]
		],
		'no_leading_import_slash' => true,
		'no_trailing_whitespace' => true,
		'no_unused_imports' => true,
		'no_whitespace_in_blank_line' => true,
		'ordered_imports' => [
			'sort_algorithm' => 'alpha'
		],
		'single_blank_line_at_eof' => true,
		'single_blank_line_before_namespace' => true,
		'single_quote' => true,
		'single_space_around_construct' => true,
		'statement_indentation' => true,
		'ternary_operator_spaces' => true,
		'trim_array_spaces' => true,
		'types_spaces' => true,
		'whitespace_after_comma_in_array' => true
	])
	->setFinder($finder);
