<?php

// I think this isn't necessary yet..
if ( ! defined( 'AV_ACF_KEY_PREFIX' ) ) {
	define( 'AV_ACF_KEY_PREFIX', 'av_acfk_' );
}
if ( ! defined( 'AV_ACF_FIELD_PREFIX' ) ) {
	define( 'AV_ACF_FIELD_PREFIX', '' );
}

if ( ! function_exists( 'av_acf_field_name' ) ) {
	function av_acf_field_name( $label, $prefix = null ) {
		$slug = av_sanitize_title_with_underscores( $label );
		if ( $prefix ) {
			$slug = $prefix . '_' . $slug;
		}
		return AV_ACF_FIELD_PREFIX . $slug;
	}
}
if ( ! function_exists( 'av_acf_field_group' ) ) {
	function av_acf_field_group( $label, $locations = [], $fields = [], $args = [] ) {
		$prefix = false;
		if ( isset( $args['prefix'] ) ) {
			$prefix = $args['prefix'];
			unset( $args['prefix'] );
		}

		$slug  = av_sanitize_title_with_underscores( $label );
		if( $prefix ) {
			$slug = $prefix . '_' . $slug;
		}
		$key   = AV_ACF_KEY_PREFIX . $slug;
		$title = $label;
		$defaults = [
			'key'                   => $key,
			'title'                 => $title,
			'fields'                => $fields,
			'location'              => $locations,
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
		];
		return array_replace( $defaults, $args );
	}
}
/**
 * @param string $type ( options_page | post_type | category )
 */
if ( ! function_exists( 'av_acf_location' ) ) {
	function av_acf_location( $type, $value ) {
		return [
			'param'    => $type,
			'operator' => '==',
			'value'    => $value,
		];
	}
}
/** Sets field key, label and name */
if ( ! function_exists( 'av_acf_field' ) ) {
	function av_acf_field( $label, $data, $overwrite = [] ) {
		$slug = av_sanitize_title_with_underscores( $label );
		if ( isset( $overwrite['prefix'] ) ) {
			$slug = $overwrite['prefix'] . '_' . $slug;
			unset( $overwrite['prefix'] );
		}
		$key            = AV_ACF_KEY_PREFIX . $slug;
		$name           = AV_ACF_FIELD_PREFIX . $slug;
		$key_label_name = compact( 'key', 'label', 'name' );
		return array_replace( array_merge( $key_label_name, $data ), $overwrite );
	}
}
/** Tab field defaults
 * Returns ACF tab field with default values that can be overwritten.
 * @param string $tab_name The tab label
 * @param mixed $overwrite Values to overwrite the default values
 * @return mixed ACF Tab field data
 */
if ( ! function_exists( 'av_acf_tab_field' ) ) {
	function av_acf_tab_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'tab',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'placement'         => 'left',
			'endpoint'          => 0,
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Text field defaults */
if ( ! function_exists( 'av_acf_text_field' ) ) {
	function av_acf_text_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'text',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'default_value'     => '',
			'placeholder'       => '',
			'prepend'           => '',
			'append'            => '',
			'maxlength'         => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Repeater field defaults */
if ( ! function_exists( 'av_acf_repeater_field' ) ) {
	function av_acf_repeater_field( $label, $children = [], $overwrite = [] ) {
		$button_label                 = substr( $label, -1 ) === 's' ? substr( $label, 0, -1 ) : $label;
		$field_data                   = [
			'type'              => 'repeater',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'collapsed'         => '',
			'min'               => 0,
			'max'               => 0,
			'layout'            => 'table',
			'button_label'      => 'Add ' . $button_label,
			'sub_fields'        => $children,
		];
		$repeater_field               = av_acf_field( $label, $field_data, $overwrite );
		$repeater_field['sub_fields'] = array_map(
			function( $child ) use ( $repeater_field ) {
				$child['key'] = $repeater_field['key'] . '_' . $child['key'];
				return $child;
			},
			$repeater_field['sub_fields']
		);
		return $repeater_field;
	}
}
/** Post Object field defaults */
if ( ! function_exists( 'av_acf_post_field' ) ) {
	function av_acf_post_field( $label, $post_type, $overwrite = [] ) {
		$field_data = [
			'type'              => 'post_object',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'post_type'         => $post_type,
			'taxonomy'          => '',
			'allow_null'        => 0,
			'multiple'          => 0,
			'return_format'     => 'object',
			'ui'                => 1,
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Image field defaults */
if ( ! function_exists( 'av_acf_image_field' ) ) {
	function av_acf_image_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'image',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'return_format'     => 'array',
			'preview_size'      => 'medium',
			'library'           => 'all',
			'min_width'         => '',
			'min_height'        => '',
			'min_size'          => '',
			'max_width'         => '',
			'max_height'        => '',
			'max_size'          => '',
			'mime_types'        => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** True/False field defaults */
if ( ! function_exists( 'av_acf_boolean_field' ) ) {
	function av_acf_boolean_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'true_false',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'message'           => '',
			'default_value'     => 0,
			'ui'                => 1,
			'ui_on_text'        => '',
			'ui_off_text'       => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Select field defaults */
if ( ! function_exists( 'av_acf_select_field' ) ) {
	function av_acf_select_field( $label, $options, $overwrite = [] ) {
		$field_data = [
			'type'              => 'select',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'choices'           => $options,
			'default_value'     => [],
			'allow_null'        => 0,
			'multiple'          => 0,
			'ui'                => 1,
			'ajax'              => 0,
			'return_format'     => 'value',
			'placeholder'       => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Post Relationship field defaults */
if ( ! function_exists( 'av_acf_post_relationship_field' ) ) {
	function av_acf_post_relationship_field( $label, $post_type, $overwrite = [] ) {
		$field_data = [
			'type'              => 'relationship',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'post_type'         => $post_type,
			'taxonomy'          => '',
			'filters'           => [ 'search', 'post_type', 'taxonomy' ],
			'elements'          => [
				0 => 'featured_image',
			],
			'min'               => '',
			'max'               => '',
			'return_format'     => 'object',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Accordion field defaults */
if ( ! function_exists( 'av_acf_accordion_field' ) ) {
	function av_acf_accordion_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'accordion',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'open'              => 0,
			'multi_expand'      => 0,
			'endpoint'          => 0,
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Button Group field defaults */
if ( ! function_exists( 'av_button_group_field' ) ) {
	function av_button_group_field( $label, $choices, $overwrite = [] ) {
		$field_data = [
			'type'              => 'button_group',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'choices'           => $choices,
			'allow_null'        => 0,
			'default_value'     => '',
			'layout'            => 'horizontal',
			'return_format'     => 'value',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Color Picker field defaults */
if ( ! function_exists( 'av_acf_color_field' ) ) {
	function av_acf_color_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'color_picker',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'default_value'     => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
if ( ! function_exists( 'av_acf_gallery_field' ) ) {
	function av_acf_gallery_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'gallery',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'return_format'     => 'array',
			'preview_size'      => 'medium',
			'insert'            => 'append',
			'library'           => 'all',
			'min'               => 0,
			'max'               => 0,
			'min_width'         => 0,
			'min_height'        => 0,
			'min_size'          => 0,
			'max_width'         => 0,
			'max_height'        => 0,
			'max_size'          => 0,
			'mime_types'        => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
if ( ! function_exists( 'av_acf_date_field' ) ) {
	function av_acf_date_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'date_picker',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'display_format'    => 'F j, Y',
			'return_format'     => 'F j, Y',
			'first_day'         => 1,
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
if ( ! function_exists( 'av_acf_textarea_field' ) ) {
	function av_acf_textarea_field( $label, $overwrite = [] ) {
		$field_data = [
			'type'              => 'textarea',
			'instructions'      => '',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => [
				'width' => '',
				'class' => '',
				'id'    => '',
			],
			'default_value'     => '',
			'placeholder'       => '',
			'maxlength'         => '',
			'rows'              => '',
			'new_lines'         => '',
		];
		return av_acf_field( $label, $field_data, $overwrite );
	}
}
/** Use to set the value of the 'wrapper' option */
if ( ! function_exists( 'av_acf_field_wrapper' ) ) {
	function av_acf_field_wrapper( $width = '', $class = '', $id = '' ) {
		return [
			'width' => $width,
			'class' => $class,
			'id'    => $id,
		];
	}
}
/** Use to replace the 'conditional_logic' option with a single condition based on a boolean field */
if ( ! function_exists( 'av_acf_boolean_field_condition' ) ) {
	function av_acf_boolean_field_condition( $key, $display_if ) {
		$operator = $display_if ? '==' : '!=';
		return [
			[
				[
					'field'    => AV_ACF_KEY_PREFIX . $key,
					'operator' => $operator,
					'value'    => '1',
				],
			],
		];
	}
}
/** Use to replace the 'conditional_logic' option with a one or more conditions based on a possible values of a select field */
if ( ! function_exists( 'av_acf_option_field_condition' ) ) {
	function av_acf_option_field_condition( $key, $values ) {
		$values     = is_string( $values ) ? [ $values ] : $values;
		$conditions = array_map(
			function( $value ) use ( $key ) {
				return [
					[
						'field'    => AV_ACF_KEY_PREFIX . $key,
						'operator' => '==',
						'value'    => $value,
					],
				];
			},
			$values
		);
		return $conditions;
	}
}















if ( ! function_exists( 'av_acf_filter_gallery' ) ) {
	function av_acf_filter_gallery( $gallery, $tag = '' ) {
		return array_map(
			function( $image_id ) use ( $tag ) {
				return av_get_image_array_by_id( $image_id, $tag );
			},
			$gallery
		);
	}
}
if ( ! function_exists( 'av_acf_filter_image_array' ) ) {
	function av_acf_filter_image_array( $image_array, $tag = '' ) {
		return [
			'url'     => $image_array['url'],
			'title'   => $image_array['title'] ?? '',
			'caption' => $image_array['caption'] ?? '',
			'alt'     => $image_array['alt'] ?? '',
			'type'    => $tag,
		];
	}
}

if ( ! function_exists( 'av_acf_import_field_data' ) ) {
	function av_acf_import_field_data( $name, $value, $object_id, $repeater_index = false ) {

		try {

			$field = av_acf_get_field_object( $name, $object_id );

			if ( $field ) {
				$field_label = $field['label'];
				switch ( $field['type'] ) {
					case 'gallery':
						$r = av_acf_import_gallery_item( $name, $value, $object_id );
						break;
					case 'repeater':
						$r = av_acf_import_row( $name, $value, $object_id, $repeater_index );
						break;
					case 'select':
						$r = av_acf_import_select_option( $name, $value, $object_id );
						break;
					default:
						$r = av_acf_import_field( $name, $value, $object_id );
						break;
				}
				if ( $r ) {
					av_import_admin_notice( "Imported to ACF field $field_label in the object $object_id the value: " . var_export( $value, true ) );
				}
				return $r;
			} else {
				throw new \Exception( "No field $name found for $object_id" );
			}
		} catch ( \Throwable $th ) {
			av_import_admin_exception( $th . "Failed to import to ACF field $name in the object $object_id with the value: " . var_export( $value, true ) );
		}

	}
}
if ( ! function_exists( 'av_acf_get_field_object' ) ) {
	function av_acf_get_field_object( $name, $object_id ) {
		$field = get_field_object( $name, $object_id );
		if ( ! $field ) {
			update_field( $name, 0, $object_id );
			$field = get_field_object( $name, $object_id );
		}
		if ( ! $field ) {
			throw new \Exception("No field $name for $object_id");		
		}
		return $field;
	}
}
if ( ! function_exists( 'av_acf_import_gallery_item' ) ) {
	function av_acf_import_gallery_item( $name, $value, $object_id ) {
		if ( ! is_array( $value ) ) {
			$value = [ $value ];
		}

		$array = get_field( $name, $object_id, false );

		if ( ! is_array( $array ) ) {
			$array = [];
		}

		$changed = false;

		foreach ( $value as $val ) {

			if ( ! in_array( $val, $array ) ) {
				$changed = true;
				$array[] = $val;
			}
		}

		if ( ! $changed ) {
			return false;
		}

		$result = update_field( $name, $array, $object_id );

		if ( ! $result ) {
			throw new \Exception( "Failed to add row to field $name of object $object_id with value" . var_export( $value, true ) );
		}

		return $result;
	}
}
if ( ! function_exists( 'av_acf_import_select_option' ) ) {
	function av_acf_import_select_option( $name, $value, $object_id ) {
		$field = av_acf_get_field_object( $name, $object_id );
		if ( $field ) {
			$options = $field['choices'] ?? false;
			if ( $options ) {
				$set_option_key = false;
				foreach( $options as $k => $val ) {
					if ( $value === $k || $value === $val ) {
						$set_option_key = $k;
						break;
					}
				}
				if( $set_option_key ) {
					update_field( $name, $set_option_key, $object_id );
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
if ( ! function_exists( 'av_acf_import_row' ) ) {
	function av_acf_import_row( $name, $value, $object_id, $index = false ) {
		if ( $index && isset( $value[ $index ] ) && av_acf_repeater_has_index( $index, $value[ $index ], $name, $object_id ) ) {
			return true;
		}
		$result = add_row( $name, $value, $object_id );
		if ( ! $result ) {
			throw new \Exception( "Failed to add row to field $name of object $object_id with value " . var_export( $value, true ) );
		}
		return $result;
	}
}
if ( ! function_exists( 'av_acf_repeater_has_index' ) ) {
	function av_acf_repeater_has_index( $index_name, $index_value, $name, $object_id ) {
		if ( have_rows( $name, $object_id ) ) {
			while ( have_rows( $name, $object_id ) ) {
				the_row();
				$sub_value = get_sub_field( $index_name );
				if ( $sub_value === $index_value ) {
					return true;
				}
			}
		}
		return false;
	}
}
if ( ! function_exists( 'av_acf_import_field' ) ) {
	function av_acf_import_field( $name, $value, $object_id ) {
		$current_value = get_field( $name, $object_id, false );

		if ( $current_value === $value ) {
			return false;
		}

		$result = update_field( $name, $value, $object_id );
		if ( ! $result ) {
			throw new \Exception( "Failed to update field $name of object $object_id with value " . var_export( $value, true ) );
		}
		return $result;
	}
}
