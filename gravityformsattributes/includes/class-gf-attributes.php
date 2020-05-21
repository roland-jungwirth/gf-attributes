<?php

	if ( ! class_exists( 'GFForms' ) ) {
		die();
	}

	class GF_Attributes_Field extends GF_Field {

		/**
		 * @var string $type The field type.
		 */
		public $type = 'attributes';

		private $attributes_default = array(
			array( 'id' => 'zf_bereich_1', 'label' => 'Aus- / Weiterbildung' ),
			array( 'id' => 'zf_bereich_2', 'label' => 'Berufliche Situation' ),
			array( 'id' => 'zf_bereich_3', 'label' => 'Finanzielle Situation' ),
			array( 'id' => 'zf_bereich_4', 'label' => 'Gesundheit' ),
			array( 'id' => 'zf_bereich_5', 'label' => 'Familie' ),
			array( 'id' => 'zf_bereich_6', 'label' => 'Kinder' ),
			array( 'id' => 'zf_bereich_7', 'label' => 'Freunde' ),
			array( 'id' => 'zf_bereich_8', 'label' => 'Beziehung' ),
			array( 'id' => 'zf_bereich_9', 'label' => 'Wohnsituation' ),
			array( 'id' => 'zf_bereich_11', 'label' => 'Entspannung' ),
			array( 'id' => 'zf_bereich_12', 'label' => 'Lebensfreude' ),
			array( 'id' => 'zf_bereich_13', 'label' => 'Persönliche Entwicklung' ),
		);

		public function set_attributes( $attributes ) {
			$this->attributes_default = $attributes;
		}

		public function get_attributes() {
			return $this->attributes_default;
		}

		public function get_form_editor_field_title() {
			return esc_attr__( 'Attributes', 'attributes' );
		}

		/**
		 * The settings which should be available on the field in the form editor.
		 *
		 * @return array
		 */
		function get_form_editor_field_settings() {
			return array(
				'conditional_logic_field_setting',
				'prepopulate_field_setting',
				'error_message_setting',
				'label_setting',
				'label_placement_setting',
				'admin_label_setting',
				'rules_setting',
				'visibility_setting',
				'description_setting',
				'css_class_setting',
			);
		}

		public function is_conditional_logic_supported() {
			return TRUE;
		}

		public function get_field_input( $form, $value = '', $entry = NULL ) {
			$form_id        = absint( $form['id'] );
			$is_admin       = $this->is_entry_detail() || $this->is_form_editor();
			$is_form_editor = $this->is_form_editor();

			$id            = $this->id;
			$field_id      = $form_id == 0 ? "gf-attributes-$id" : "gf-attributes-$form_id-$id";
			$disabled_text = $is_form_editor ? 'disabled="disabled"' : '';

			$hidden_input     = sprintf( "<input type='hidden' id='%s-hidden' name='input_%d' value='%s' />", $field_id, $this->id, esc_attr( $value ) );
			$points_available = sprintf( "<p class='gf_attributes_result'>Sie können noch <span id='gf-attributes-remainder-%s-%s'>10</span> Punkte vergeben.</p>", $form_id, $this->id );

			if ( is_admin() ) {
				return sprintf( 'Dieses Feld wird im Code mit Inhalten bef&uuml;llt.' );
			} else {
				return sprintf(
					"<div class='ginput_container ginput_container_attributes'>%s<ul class='gf-attributes-options' id='%s'>%s</ul>%s</div>",
					$points_available,
					$field_id,
					$this->get_attribute_choices( $value, $disabled_text, $form_id ),
					$hidden_input
				);
			}
		}

		/**
		 * Returns the markup for the attribute choices.
		 *
		 * @param string|array $value The field value. From default/dynamic population, $_POST, or a resumed incomplete submission.
		 * @param string       $disabled_text The input disabled attribute when in the form editor or entry detail page.
		 * @param int          $form_id The ID of the current form.
		 *
		 * @return string
		 */
		public function get_attribute_choices( $value, $disabled_text, $form_id ) {
			$attributes      = '';
			$is_entry_detail = $this->is_entry_detail();
			$is_form_editor  = $this->is_form_editor();
			$is_admin        = $is_entry_detail || $is_form_editor;

			$attribute_number = 1;

			if ( ! empty( $value ) ) {
				$value = json_decode( $value, true );
			}

			// Loop through field choices.
			foreach ( $this->attributes_default as $attribute ) {
				// Hack to skip numbers ending in 0, so that 5.1 doesn't conflict with 5.10.

				if ( $is_entry_detail || $is_form_editor || $form_id == 0 ) {
					$id = $this->id . '_' . $attribute_number ++;
				} else {
					$id = $form_id . '_' . $this->id . '_' . $attribute_number ++;
				}

				$tabindex        = $this->get_tabindex();
				$attribute_id    = $attribute['id'];
				$input_id        = $this->id . '_' . $attribute_id;
				$attribute_value = '';

				if ( ! empty( $value ) ) {
					foreach ( $value as $val ) {
						if ( $val['id'] == $attribute_id ) {
							$attribute_value = $val['value'];
							break;
						}
					}
				}

				$attribute_markup = "<li class='gf_attributes_{$id}'>
					<input name='attribute_{$input_id}' type='text' value='{$attribute_value}' id='{$input_id}' class='square' {$tabindex} {$disabled_text} />
					<label for='attribute_{$id}' id='label_{$input_id}'>{$attribute['label']}</label>
				</li>";

				/**
				 * Override the default choice markup used when rendering radio button, checkbox and drop down type fields.
				 *
				 * @param string $attribute_markup The string containing the choice markup to be filtered.
				 * @param array  $attribute An associative array containing the choice properties.
				 * @param object $field The field currently being proce ssed.
				 * @param string $value The value to be selected if the field is being populated.
				 *
				 * @since 1.9.6
				 *
				 */
				$attributes .= gf_apply_filters( array( 'gform_field_choice_markup_pre_render', $this->formId, $this->id ), $attribute_markup, $attribute, $this, $value );
			}

			/**
			 * Modify the checkbox items before they are added to the checkbox list.
			 *
			 * @param string $attributes The string containing the choices to be filtered.
			 * @param object $field Ahe field currently being processed.
			 *
			 * @since Unknown
			 *
			 */
			return gf_apply_filters( array( 'gform_field_choices', $this->formId, $this->id ), $attributes, $this );
		}

	}

	GF_Fields::register( new GF_Attributes_Field() );
