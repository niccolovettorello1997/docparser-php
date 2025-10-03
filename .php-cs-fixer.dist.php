<?php
// see https://github.com/FriendsOfPHP/PHP-CS-Fixer

$finder = PhpCsFixer\Finder::create()
    ->in(dirs: [__DIR__.'/src', __DIR__.'/tests', __DIR__.'/public'])
    ->exclude(dirs: ['vendor'])
;

$config = new PhpCsFixer\Config();

return $config
    ->setParallelConfig(config: PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(isRiskyAllowed: true)
    ->setRules(rules: [
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'ordered_imports' => true,
        'declare_strict_types' => true,
        'single_blank_line_at_eof' => true,
        'phpdoc_align' => ['align' => 'vertical'],
        'phpdoc_separation' => true,
        'phpdoc_summary' => true,
        'phpdoc_trim' => true,
        'phpdoc_order' => true,
        'yoda_style' => false,
    ])
    ->setFinder(finder: $finder)
;
