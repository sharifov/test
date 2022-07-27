<?php

$rules = [
    /* Rule set @PSR12 */
    '@PSR2' => true,
    'blank_line_after_opening_tag' => true,
    'braces' => ['allow_single_line_anonymous_class_with_empty_body' => true],
    'class_definition' => ['inline_constructor_arguments' => false, 'space_before_parenthesis' => true],
    'compact_nullable_typehint' => true,
    'declare_equal_normalize' => true,
    'lowercase_cast' => true,
    'lowercase_static_reference' => true,
    'new_with_braces' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_leading_import_slash' => true,
    'no_whitespace_in_blank_line' => true,
    'ordered_class_elements' => ['order' => ['use_trait']],
    'ordered_imports' => ['imports_order' => ['class', 'function', 'const'], 'sort_algorithm' => 'none'],
    'return_type_declaration' => true,
    'short_scalar_cast' => true,
    'single_blank_line_before_namespace' => true,
    'single_trait_insert_per_statement' => true,
    'ternary_operator_spaces' => true,
    'visibility_required' => true


    /*'align_multiline_comment' => ['comment_type' => 'all_multiline'],
    'array_indentation' => true,
    'array_syntax' => ['syntax' => 'short'],
    'binary_operator_spaces' => true,
    'blank_line_after_namespace' => true,
    'blank_line_after_opening_tag' => true,
    'blank_line_before_statement' => ['statements' => ['return']],
    'braces' => ['allow_single_line_closure' => true],
    'cast_spaces' => true,
    'class_attributes_separation' => ['elements' => ['method']],
    'class_definition' => ['single_line' => true],
    'compact_nullable_typehint' => true,
    'concat_space' => ['spacing' => 'one'],
    'constant_case' => true,
    'declare_equal_normalize' => true,
    'elseif' => true,
    'encoding' => true,
    'full_opening_tag' => true,
    'function_declaration' => true,
    'function_typehint_space' => true,
    'indentation_type' => true,
    'line_ending' => true,
    'linebreak_after_opening_tag' => true,
    'lowercase_cast' => true,
    'lowercase_keywords' => true,
    'lowercase_static_reference' => true,
    'magic_constant_casing' => true,
    'magic_method_casing' => true,
    'method_argument_space' => ['on_multiline' => 'ensure_fully_multiline'],
    'native_function_casing' => true,
    'native_function_type_declaration_casing' => true,
    'new_with_braces' => true,
    'no_blank_lines_after_class_opening' => true,
    'no_blank_lines_after_phpdoc' => true,
    'no_break_comment' => true,
    'no_closing_tag' => true,
    'no_empty_comment' => true,
    'no_empty_phpdoc' => true,
    'no_empty_statement' => true,
    'no_leading_import_slash' => true,
    'no_leading_namespace_whitespace' => true,
    'no_multiline_whitespace_around_double_arrow' => true,
    'no_singleline_whitespace_before_semicolons' => true,
    'no_spaces_after_function_name' => true,
    'no_spaces_around_offset' => true,
    'no_spaces_inside_parenthesis' => true,
    //'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'allow_unused_params' => true],
    'no_trailing_comma_in_singleline_array' => true,
    'no_trailing_whitespace' => true,
    'no_trailing_whitespace_in_comment' => true,
    // 'no_unneeded_control_parentheses' => true,
    //'no_unneeded_curly_braces' => true,
    'no_whitespace_before_comma_in_array' => true,
    'no_whitespace_in_blank_line' => true,
    //'normalize_index_brace' => true,
    //'not_operator_with_successor_space',
    'object_operator_without_whitespace' => true,
    'phpdoc_align' => [
    'tags' => [
            'method',
            'param',
            'property',
            'return',
            'throws',
            'type',
            'var',
        ],
    ],
    'phpdoc_indent' => true,
    'phpdoc_separation' => true,
    'phpdoc_single_line_var_spacing' => true,
    'phpdoc_trim' => true,
    'phpdoc_trim_consecutive_blank_line_separation' => true,
    'phpdoc_types' => true,
    'phpdoc_types_order' => ['null_adjustment' => 'always_last', 'sort_algorithm' => 'none'],
    'phpdoc_var_without_name' => true,
    'return_type_declaration' => true,
    'semicolon_after_instruction' => true,
    //'short_scalar_cast',
    'single_blank_line_at_eof' => true,
    'single_blank_line_before_namespace' => true,
    'single_class_element_per_statement' => true,
    'single_import_per_statement' => true,
    'single_line_after_imports' => true,
    //'single_line_throw',
    'single_trait_insert_per_statement' => true,
    'space_after_semicolon' => ['remove_in_empty_for_expressions' => true],
    'switch_case_semicolon_to_colon' => true,
    'switch_case_space' => true,
    'ternary_operator_spaces' => true,
    'trailing_comma_in_multiline_array' => true,
    'trim_array_spaces' => true,
    'unary_operator_spaces' => true,
    'visibility_required' => true,
    'whitespace_after_comma_in_array' => true,*/
];

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->notPath('#^common/tests#')
    ->notPath('#^console/runtime#')
    ->notPath('#^docker#')
    ->notPath('#^frontend/runtime#')
    ->notPath('#^frontend/tests#')
    ->notPath('#^frontend/web#')
    ->notPath('#^vagrant#')
    ->notPath('#^vendor#')
    ->notPath('#^webapi/runtime#')
    ->notPath('#^webapi/tests#')
    ->notPath('#^webapi/web#')
    ->notPath('#^devops#')
    ->notPath('#^node_modules#')
;

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder)
;
