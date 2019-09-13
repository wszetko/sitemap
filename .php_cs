<?php

$header = <<<'EOF'
This file is part of Wszetko Sitemap.

(c) Paweł Kłopotek-Główczewski <pawelkg@pawelkg.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.
EOF;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/example')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        '@PHP73Migration' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'header_comment' => [
            'commentType' => 'PHPDoc',
            'header' => $header
        ],
        'is_null' => true,
        'mb_str_functions' => true,
        'modernize_types_casting' => true,
        'no_short_echo_tag' => false,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'alpha'],
        'phpdoc_no_empty_return' => false,
        'phpdoc_no_package' => false,
        'php_unit_test_class_requires_covers' => false,
        'psr4' => true,
        'single_line_comment_style' => ['comment_types' => ['hash']],
    ])
    ->setFinder($finder);
