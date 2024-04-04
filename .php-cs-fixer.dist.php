<?php

/**
 * Penobit.
 *
 * @author Penobit <info@penobit.com>
 * @copyright Copyright © 2021, Penobit all rights received
 * @license MIT
 *
 * @link https://Penobit.com
 * @link https://github.com/Penobit
 *
 * @version 1.0.0
 */
$headerComment = <<<'EOF'
Penobit

@author R8 <R8@Penobit.com>
@author Penobit <info@penobit.com>
@copyright Copyright © 2021, Penobit all rights received
@license MIT

@link https://Penobit.com
@link https://github.com/penobit

@version 1.0.0
EOF;

$finder = PhpCsFixer\Finder::create()
    ->exclude(['tests/Fixtures', 'vendor', 'node_modules', 'storage'])
    ->in(__DIR__)
    ->append([
        __DIR__.'/dev-tools/doc.php',
        // __DIR__.'/php-cs-fixer', disabled, as we want to be able to run bootstrap file even on lower PHP version, to show nice message
    ])
;

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PHP71Migration:risky' => false,
        '@PHPUnit75Migration:risky' => false,
        '@PhpCsFixer:risky' => false,
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['expectedDeprecation'],
        ],
        // 'header_comment' => ['header' => $headerComment, 'comment_type' => 'PHPDoc', 'location' => 'after_open'],
        'array_syntax' => ['syntax' => 'short'],
        'no_superfluous_elseif' => true,
        'no_superfluous_phpdoc_tags' => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'function_declaration' => [
            'closure_function_spacing' => 'none',
        ],
        'yoda_style' => [
            'equal' => true,
            'identical' => true,
            'less_and_greater' => true,
            'always_move_variable' => true,
        ],
        // @deprecated 3.16.0
        'braces' => [
            'allow_single_line_closure' => false,
            'position_after_functions_and_oop_constructs' => 'same',
        ],

        'curly_braces_position' => [
            'control_structures_opening_brace' => 'same_line',
            'functions_opening_brace' => 'same_line',
            'anonymous_functions_opening_brace' => 'same_line',
            'classes_opening_brace' => 'same_line',
            'anonymous_classes_opening_brace' => 'same_line',
            // 'allow_single_line_empty_anonymous_classes' => true,
            // 'allow_single_line_anonymous_functions' => true,
        ],

        'strict_param' => false,
        'native_function_invocation' => false,
        'native_constant_invocation' => false,
        'no_unused_imports' => true,
        'declare_strict_types' => false,
        'single_quote' => true,
        'phpdoc_align' => ['align' => 'left' /* [vetical, left] */],
        'phpdoc_no_alias_tag' => ['replacements' => ['see' => 'link']],
        'single_blank_line_at_eof' => false,
        'void_return' => false,
        'strict_comparison' => false,
    ])
    ->setFinder($finder)
;

// special handling of fabbot.io service if it's using too old PHP CS Fixer version
if (false !== getenv('FABBOT_IO')) {
    try {
        PhpCsFixer\FixerFactory::create()
            ->registerBuiltInFixers()
            ->registerCustomFixers($config->getCustomFixers())
            ->useRuleSet(new PhpCsFixer\RuleSet($config->getRules()))
        ;
    } catch (PhpCsFixer\ConfigurationException\InvalidConfigurationException $e) {
        $config->setRules([]);
    } catch (UnexpectedValueException $e) {
        $config->setRules([]);
    } catch (InvalidArgumentException $e) {
        $config->setRules([]);
    }
}

return $config;
