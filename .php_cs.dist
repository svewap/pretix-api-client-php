<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/{src,tests}')
;

$header =<<<'HEADER'
This file is part of itk-dev/pretix-api-client-php.

(c) 2020 ITK Development

This source file is subject to the MIT license.
HEADER;

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_indentation' => true,
        'header_comment' => ['header' => $header],
    ])
    ->setFinder($finder)
;
