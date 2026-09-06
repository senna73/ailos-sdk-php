<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests');

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12'                       => true,
        'array_syntax'                 => ['syntax' => 'short'],
        'ordered_imports'              => ['sort_algorithm' => 'alpha'],
        'no_unused_imports'            => true,
        'trailing_comma_in_multiline'  => true,
        'declare_strict_types'         => true,
        'blank_line_after_namespace'   => true,
        'single_quote'                 => true,
    ])
    ->setFinder($finder)
    ->setCacheFile(__DIR__ . '/cache/php-cs-fixer.php')
    ->setRiskyAllowed(true);