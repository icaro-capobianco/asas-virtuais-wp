<?php

// I think this isn't necessary yet..
if ( ! defined( 'AV_ACF_KEY_PREFIX' ) ) {
	define( 'AV_ACF_KEY_PREFIX', 'av_acfk_' );
}
if ( ! defined( 'AV_ACF_FIELD_PREFIX' ) ) {
	define( 'AV_ACF_FIELD_PREFIX', '' );
}

// Functions to retrieve ACF data
	if ( ! function_exists( 'av_acf_get_field' ) ) {
		function av_acf_get_field( $name, $id, $formatted = true, $fallback = null ) {
			return asas_virtuais()->acf_manager()->get_field( $name, $id, $formatted, $fallback );
		}
	}
// Functions to create field groups
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
				'ID'                    => $key,
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
// Functions to create field assoc arrays
	if ( ! function_exists( 'av_acf_field_name' ) ) {
		function av_acf_field_name( $label, $prefix = null ) {
			$slug = av_sanitize_title_with_underscores( $label );
			if ( $prefix ) {
				$slug = $prefix . '_' . $slug;
			}
			return AV_ACF_FIELD_PREFIX . $slug;
		}
	}
	/** Sets field key, label and name */
	if ( ! function_exists( 'av_acf_field' ) ) {
		function av_acf_field( $label, $data, $overwrite = [] ) {
			$other_defaults = [
				'wrapper' => [
					'width' => '',
					'class' => '',
					'id'    => '',
				],
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
			];
			$slug = av_sanitize_title_with_underscores( $label );
			if ( isset( $overwrite['prefix'] ) ) {
				$slug = $overwrite['prefix'] . '_' . $slug;
				unset( $overwrite['prefix'] );
			}
			$key            = AV_ACF_KEY_PREFIX . $slug;
			$name           = AV_ACF_FIELD_PREFIX . $slug;
			// $_name          = $name;
			$key_label_name = compact( 'key', 'label', 'name' );
			return array_replace( $other_defaults, array_merge( $key_label_name, $data ), $overwrite );
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
				'default_value'     => '',
				'placeholder'       => '',
				'prepend'           => '',
				'append'            => '',
				'maxlength'         => '',
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_number_field' ) ) {
		function av_acf_number_field( $label, $overwrite = [] ) {
			$field_data = [
				'type' => 'number',
				'default_value' => 0,
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
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
				'open'              => 0,
				'multi_expand'      => 0,
				'endpoint'          => 0,
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	/** Button Group field defaults */
	if ( ! function_exists( 'av_button_group_field' ) ) {
		function av_acf_button_group_field( $label, $choices, $overwrite = [] ) {
			$field_data = [
				'type'              => 'button_group',
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
				'default_value'     => '',
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_gallery_field' ) ) {
		function av_acf_gallery_field( $label, $overwrite = [] ) {
			$field_data = [
				'type'              => 'gallery',
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
				'default_value'     => '',
				'placeholder'       => '',
				'maxlength'         => '',
				'rows'              => '',
				'new_lines'         => '',
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_oembed_field' ) ) {
		function av_acf_oembed_field( $label, $overwrite = [] ) {
			$field_data = [
				'type' => 'oembed',
				'height' => '',
				'width' => '',
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_wysiwyg_field' ) ) {
		function av_acf_wysiwyg_field( $label, $overwrite = [] ) {
			$field_data = [
				'type' => 'wysiwyg',
				'default_value' => '',
				'placeholder' => '',
				'maxlength' => '',
				'rows' => '',
				'new_lines' => ''
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_page_link_field' ) ) {
		function av_acf_page_link_field( $label, $overwrite = [] ) {
			$field_data = [
				'type' => 'page_link',
				'post_type' => '',
				'taxonomy' => '',
				'allow_null' => 0,
				'allow_archives' => 1,
				'multiple' => 0,
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_location_field' ) ) {
		function av_acf_location_field( $label, $overwrite = [] ) {
			$field_data = [
				'type' => 'google_map',
				'center_lat' => '',
				'center_lng' => '',
				'zoom' => '',
				'height' => '',
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_taxonomy_field' ) ) {
		function av_acf_taxonomy_field( $label, $taxonomy, $overwrite = [] ) {
			$field_data = [
				'type' => 'taxonomy',
				'taxonomy' => $taxonomy,
				'field_type' => 'multi_select',
				'allow_null' => 0,
				'add_term' => 0,
				'save_terms' => 0,
				'load_terms' => 0,
				'return_format' => 'id',
				'multiple' => 0,
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}
	if ( ! function_exists( 'av_acf_group_field' ) ) {
		function av_acf_group_field( $label, $sub_fields, $overwrite = [] ) {
			$field_data = [
				'type' => 'group',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'layout' => 'block',
				'sub_fields' => $sub_fields
			];
			return av_acf_field( $label, $field_data, $overwrite );
		}
	}

// Utility functions
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
	/**
	 * @param string $type ( options_page | post_type | category )
	 */
	if ( ! function_exists( 'av_acf_location' ) ) {
		function av_acf_location( $param, $value, $operator = '==' ) {
			return compact( 'param', 'value', 'operator' );
		}
	}


// Functions to import ACF data
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
						case 'image':
							$r = av_acf_import_image( $name, $value, $object_id );
							break;
						default:
							$r = av_acf_import_field( $name, $value, $object_id );
							break;
					}
					if ( $r ) {
						av_import_admin_notice( "Imported to ACF field $field_label in the object $object_id the value: " . var_export( $value, true ) );
					} else {
						av_import_admin_error( "Failed to import ACF field $field_label in the object $object_id with the value: " . var_export( $value, true ) );
					}
					return $r;
				} else {
					throw new \Exception( "No field $name found for $object_id" );
				}
			} catch ( \Throwable $th ) {
				av_import_admin_exception( $th, "Failed to import to ACF field $name in the object $object_id with the value: " . var_export( $value, true ) );
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

			$array = av_acf_get_field( $name, $object_id, false );

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
			if( $index ) {
				if ( ! is_array( $index ) ) {
					$index = [ $index ];
				}
				if ( av_acf_repeater_has_indexes( $index, $value, $name, $object_id ) ) {
					return true;
				}
			}
			$result = add_row( $name, $value, $object_id );
			if ( ! $result ) {
				throw new \Exception( "Failed to add row to field $name of object $object_id with value " . var_export( $value, true ) );
			}
			return $result;
		}
	}
	if ( ! function_exists( 'av_acf_repeater_has_indexes' ) ) {
		function av_acf_repeater_has_indexes( $indexes, $lookfor, $name, $object_id ) {
			if ( have_rows( $name, $object_id ) ) {
				while ( have_rows( $name, $object_id ) ) {
					the_row();
					$has_all_indexes = true;
					foreach( $indexes as $index ) {
						$sub_value = get_sub_field( $index );
						$lookfor_value = $lookfor[$index] ?? false;
						if ( $lookfor_value ) {
							if ( $sub_value ) {
								if (  "$lookfor_value" !== "$sub_value" ) {
									$has_all_indexes = false;
								}
							} else {
								$has_all_indexes = false;
							}
						}
					}
					if ( $has_all_indexes ) {
						return $has_all_indexes;
					}
				}
			}
			return false;
		}
	}
	if ( ! function_exists( 'av_acf_import_image' ) ) {
		function av_acf_import_image( $name, $value, $object_id ) {
			if ( is_numeric( $value ) ) {
				return av_acf_import_field( $name, $value, $object_id );
			} elseif ( is_string( $value ) ) {
				$attachment_id = av_insert_attachment_from_url( $value );
				return av_acf_import_field( $name, $attachment_id, $object_id );
			}
		}
	}
	if ( ! function_exists( 'av_acf_import_field' ) ) {
		function av_acf_import_field( $name, $value, $object_id ) {
			$current_value = av_acf_get_field( $name, $object_id, false );

			if ( $current_value === $value ) {
				return true;
			}

			$result = update_field( $name, $value, $object_id );
			if ( ! $result ) {
				throw new \Exception( "Failed to update field $name of object $object_id with value " . var_export( $value, true ) );
			}
			return $result;
		}
	}
