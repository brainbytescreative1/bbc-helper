<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_66c34c9d05967',
	'title' => 'Schema: Fields',
	'fields' => array(
		array(
			'key' => 'field_66c34c9e27184',
			'label' => 'Schema Format',
			'name' => 'schema_format',
			'aria-label' => '',
			'type' => 'button_group',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'JSON Upload' => 'JSON Upload',
				'Paste Code' => 'Paste Code',
			),
			'default_value' => '',
			'return_format' => 'value',
			'allow_null' => 1,
			'layout' => 'horizontal',
		),
		array(
			'key' => 'field_66c3b4ac04fa2',
			'label' => 'Upload Schema JSON',
			'name' => 'upload_schema_json',
			'aria-label' => '',
			'type' => 'file',
			'instructions' => 'Upload JSON file WITHOUT opening and closing <script> tags.',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_66c34c9e27184',
						'operator' => '==',
						'value' => 'JSON Upload',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'uploader' => '',
			'return_format' => 'array',
			'min_size' => '',
			'max_size' => '',
			'mime_types' => 'json',
			'library' => 'all',
		),
		array(
			'key' => 'field_66c3b4e704fa3',
			'label' => 'Paste Schema Code',
			'name' => 'paste_schema_code',
			'aria-label' => '',
			'type' => 'acfe_code_editor',
			'instructions' => 'Paste JSON code WITHOUT opening and closing <script> tags.',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_66c34c9e27184',
						'operator' => '==',
						'value' => 'Paste Code',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'mode' => 'application/x-json',
			'lines' => 1,
			'indent_unit' => 4,
			'maxlength' => '',
			'rows' => 4,
			'max_rows' => '',
			'return_format' => array(
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'all',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'side',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => false,
	'description' => '',
	'show_in_rest' => 0,
	'acfe_display_title' => '',
	'acfe_autosync' => array(
		0 => 'php',
	),
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
	'modified' => 1756329019,
));

endif;