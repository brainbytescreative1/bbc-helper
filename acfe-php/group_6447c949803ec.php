<?php 

if( function_exists('acf_add_local_field_group') ):

acf_add_local_field_group(array(
	'key' => 'group_6447c949803ec',
	'title' => 'Schema: Global Schema',
	'fields' => array(
		array(
			'key' => 'field_6447c94a81db8',
			'label' => 'Schema',
			'name' => 'schema_repeater',
			'aria-label' => '',
			'type' => 'repeater',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'acfe_repeater_stylised_button' => 1,
			'layout' => 'block',
			'pagination' => 0,
			'min' => 0,
			'max' => 0,
			'collapsed' => '',
			'button_label' => 'Add Schema',
			'rows_per_page' => 20,
			'sub_fields' => array(
				array(
					'key' => 'field_6447c96e81db9',
					'label' => 'Schema Module',
					'name' => 'schema_module',
					'aria-label' => '',
					'type' => 'clone',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'clone' => array(
						0 => 'group_66c34c9d05967',
					),
					'display' => 'seamless',
					'layout' => 'block',
					'prefix_label' => 0,
					'prefix_name' => 0,
					'acfe_seamless_style' => 0,
					'acfe_clone_modal' => 0,
					'acfe_clone_modal_close' => 0,
					'acfe_clone_modal_button' => '',
					'acfe_clone_modal_size' => 'large',
					'parent_repeater' => 'field_6447c94a81db8',
				),
			),
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'schema',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'left',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
	'show_in_rest' => 0,
	'acfe_display_title' => '',
	'acfe_autosync' => array(
		0 => 'php',
	),
	'acfe_form' => 0,
	'acfe_meta' => '',
	'acfe_note' => '',
	'modified' => 1758207662,
));

endif;