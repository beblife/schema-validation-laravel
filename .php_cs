<?php

$finder = Symfony\Component\Finder\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->notPath('bootstrap/*')
    ->notPath('storage/*')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true)
;

$config = new PhpCsFixer\Config();

return $config->setRules([
    'array_syntax' => ['syntax' => 'short'],
    'ordered_imports' => ['sortAlgorithm' => 'alpha'],
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
    'trailing_comma_in_multiline_array' => true,
    'phpdoc_scalar' => true,
    'unary_operator_spaces' => true,
    'binary_operator_spaces' => true,
    'blank_line_before_statement' => [
        'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
    ],
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_var_without_name' => true,
    'method_argument_space' => [
        'on_multiline' => 'ensure_fully_multiline',
        'keep_multiple_spaces_after_comma' => true,
    ],
    'multiline_whitespace_before_semicolons' => [
        'strategy' => 'new_line_for_chained_calls',
    ],
    'single_quote' => true,
    'single_blank_line_before_namespace' => true,
    'no_whitespace_in_blank_line' => true,
    'no_whitespace_before_comma_in_array' => true,
    'function_typehint_space' => true,
    'return_type_declaration' => true,
]);
