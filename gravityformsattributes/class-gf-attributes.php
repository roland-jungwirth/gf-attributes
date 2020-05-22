<?php

//------------------------------------------

// don't load directly
	if ( ! defined( 'ABSPATH' ) ) {
		die();
	}

	GFForms::include_addon_framework();

	class GFAttributes extends GFAddOn {

		protected $_version = GF_ATTRIBUTES_VERSION;
		protected $_min_gravityforms_version = '2.0';
		protected $_slug = 'gravityformsattributes';
		protected $_path = 'gravityformsattributes/attributes.php';
		protected $_full_path = __FILE__;
		protected $_url = 'http://www.gravityforms.com';
		protected $_title = 'Gravity Forms Attributes Add-On';
		protected $_short_title = 'Attributes';
		protected $_enable_rg_autoupgrade = TRUE;

		/**
		 * @var object $_instance If available, contains an instance of this class.
		 */
		private static $_instance = NULL;

		/**
		 * Returns an instance of this class, and stores it in the $_instance property.
		 *
		 * @return object $_instance An instance of this class.
		 */
		public static function get_instance() {
			if ( self::$_instance == NULL ) {
				self::$_instance = new GFAttributes();
			}

			return self::$_instance;
		}

		private function __clone() {
		} /* do nothing */

		/**
		 * Include the field early so it is available when entry exports are being performed.
		 */
		public function pre_init() {
			parent::pre_init();

			if ( $this->is_gravityforms_supported() && class_exists( 'GF_Field' ) ) {
				require_once( 'includes/class-gf-attributes.php' );

				add_filter( 'gform_export_field_value', array( $this, 'export_field_value' ), 10, 4 );
			}
		}

		/**
		 * Handles hooks and loading of language files.
		 */
		public function init() {
			// add a special class to likert fields so we can identify them later
			add_action( 'gform_field_css_class', array( $this, 'add_custom_class' ), 10, 3 );

			parent::init();
		}

		public function init_admin() {
			parent::init_admin();
		}


		// # SCRIPTS & STYLES -----------------------------------------------------------------------------------------------

		/**
		 * Return the scripts which should be enqueued.
		 *
		 * @return array
		 */
		public function scripts() {
			$scripts = array(
				array(
					'handle'   => 'gf_attributes_form_editor_js',
					'src'      => $this->get_base_url() . '/js/gf_attributes_form_editor.js',
					'version'  => $this->_version,
					'deps'     => array( 'jquery' ),
					'enqueue'  => array(
						array( 'admin_page' => array( 'form_editor' ) ),
					),
				),
				array(
					'handle'  => 'gf_attributes_js',
					'src'     => $this->get_base_url() . '/js/gf_attributes.js',
					'version' => $this->_version,
					'enqueue' => array(
						array( 'field_types' => array( 'attributes' ) ),
					),
				),
			);

			return array_merge( parent::scripts(), $scripts );
		}

		/**
		 * Include my_styles.css when the form contains a 'simple' type field.
		 *
		 * @return array
		 */
		public function styles() {
			$styles = array(
				array(
					'handle'  => 'gf_attributes_form_editor_css',
					'src'     => $this->get_base_url() . '/css/gf_attributes_form_editor.css',
					'version' => $this->_version,
					'enqueue' => array(
						array( 'admin_page' => array( 'form_editor' ) ),
					),
				),
				array(
					'handle'  => 'gf_attributes_css',
					'src'     => $this->get_base_url() . '/css/gf_attributes.css',
					'version' => $this->_version,
					'enqueue' => array(
						array( 'field_types' => array( 'attributes' ) ),
					),
				),
			);

			return array_merge( parent::styles(), $styles );
		}

		/**
		 * Add the gsurvey-field class to the Survey field.
		 *
		 * @param string $classes The CSS classes to be filtered, separated by empty spaces.
		 * @param GF_Field $field The field currently being processed.
		 * @param array $form The form currently being processed.
		 *
		 * @return string
		 */
		public function add_custom_class( $classes, $field, $form ) {
			$classes .= ' gf-attributes-field ';

			return $classes;
		}

		/**
		 * Format the Survey field values on the entry detail page so they use the choice text instead of values.
		 *
		 * @param string|array $value The field value.
		 * @param GF_Field $field The field currently being processed.
		 * @param array $entry The entry object currently being processed.
		 * @param array $form The form object currently being processed.
		 *
		 * @return string|array
		 */
		public function entry_field_value( $value, $field, $entry, $form ) {

			return ! rgblank( $value ) ? $this->maybe_format_field_values( $value, $field ) : $value;
		}
	}
