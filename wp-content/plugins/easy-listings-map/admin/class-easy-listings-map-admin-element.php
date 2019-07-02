<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-facing html elements.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Admin_Element {

	/**
	 * Plugin admin-face.
	 *
	 * @since   1.0.0
	 * @var     Easy_Listings_Map_Public
	 */
	private $plugin_admin;

	public function __construct( Easy_Listings_Map_Admin $plugin_admin ) {
		$this->plugin_admin = $plugin_admin;
	}

	/**
	 * Callback that called when rendering callback not found for element type.
	 *
	 * @since   1.0.0
	 * @param   $args
	 */
	public function missing( $args ) {
		printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'elm' ), $args['id'] );
	}

	/**
	 * Callback function for rendering marker_upload types in settings page.
	 *
	 * @since   1.0.0
	 * @param   $args
	 */
	public function marker_upload( $args ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
		if ( isset( $elm_settings[ $args['id'] ] ) ) {
			$value = $elm_settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

		if ( true === $args['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
		}
		echo '<input type="text" class="' . esc_attr( $size ) . '-text" id="elm_settings[' . esc_attr( $args['id'] ) . ']" name="elm_settings[' . esc_attr( $args['id'] ) .
			']" value="' . esc_url( $value ) . '"/>' .
			'<span>&nbsp;<input type="button" class="elm_settings_upload_button button-secondary" value="' . __( 'Upload File', 'elm' ) . '"/></span>' .
			( trim( $value ) ? '<img style="height: 30px; max-height: 30px; max-width: 50px; margin-left: 20px; vertical-align: middle;" src="' . esc_url( $value ) . '">' : '' );
	}

	/**
	 * Radio Callback
	 *
	 * Renders radio boxes.
	 *
	 * @since   1.0.0
	 * @param   array $args Arguments passed by the setting
	 * @return  void
	 */
	public function radio( $args ) {
		if ( count( $args['options'] ) ) {
			$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

			if ( true === $args['desc_tip'] ) {
				echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
			}
			foreach ( $args['options'] as $key => $option ) {
				$checked = false;

				if ( isset( $elm_settings[ $args['id'] ] ) && $elm_settings[ $args['id'] ] == $key ) {
					$checked = true;
				} else if ( isset( $args['std'] ) && $args['std'] == $key && ! isset( $elm_settings[ $args['id'] ] ) ) {
					$checked = true;
				}

				echo '<input name="elm_settings[' . $args['id'] . ']"" id="elm_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked( true, $checked, false ) . '/>&nbsp;' .
					'<label for="elm_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
			}
			if ( false === $args['desc_tip'] ) {
				echo '<p class="description">' . $args['desc'] . '</p>';
			}
		}
	}

	/**
	 * Number Callback
	 *
	 * Renders number fields.
	 *
	 * @since   1.0.0
	 * @param   array $args Arguments passed by the setting
	 * @return  void
	 */
	public function number( $args ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

		if ( isset( $elm_settings[ $args['id'] ] ) ) {
			$value = $elm_settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$max  = isset( $args['max'] ) ? $args['max'] : 999999;
		$min  = isset( $args['min'] ) ? $args['min'] : 0;
		$step = isset( $args['step'] ) ? $args['step'] : 1;
		$size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';

		if ( true === $args['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
		}
		echo '<input type="number" step="' . esc_attr( $step ) . '" max="' . esc_attr( $max ) . '" min="' . esc_attr( $min ) . '" class="' . esc_attr( $size ) . '-text" id="elm_settings[' . esc_attr( $args['id'] ) . ']" name="elm_settings[' . esc_attr( $args['id'] ) . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
		if ( false === $args['desc_tip'] ) {
			echo '<label for="elm_settings[' . esc_attr( $args['id'] ) . ']"> '  . $args['desc'] . '</label>';
		}
	}

	/**
	 * Select Callback
	 *
	 * Renders select fields.
	 *
	 * @since 1.0.0
	 * @param  array $args Arguments passed by the setting
	 * @return void
	 */
	public function select( $args ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

		if ( isset( $elm_settings[ $args['id'] ] ) ) {
			$value = $elm_settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		if ( true === $args['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
		}
		echo '<select id="elm_settings[' . esc_attr( $args['id'] ) . ']" name="elm_settings[' . esc_attr( $args['id'] ) . ']"/>';
		if ( count( $args['options'] ) ) {
			foreach ( $args['options'] as $option => $name ) {
				$selected = selected( $option, $value, false );
				echo '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_attr( $name ) . '</option>';
			}
		}
		echo '</select>';
		if ( false === $args['desc_tip'] ) {
			echo '<label for="elm_settings[' . esc_attr( $args['id'] ) . ']"> '  . esc_attr( $args['desc'] ) . '</label>';
		}
	}

	/**
	 * Checkbox Callback
	 *
	 * Renders checkboxes.
	 *
	 * @since 1.0.0
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	public function checkbox( $args ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

		$checked = isset( $elm_settings[ $args['id'] ] ) ? checked( 1, $elm_settings[ $args['id'] ], false ) : '';
		if ( true === $args['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
		}
		echo '<input type="checkbox" id="elm_settings[' . $args['id'] . ']" name="elm_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
		echo '<label for="elm_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
	}

	/**
	 * Multicheck Callback
	 *
	 * Renders multiple checkboxes.
	 *
	 * @since 1.0.0
	 * @param array $args Arguments passed by the setting
	 * @return void
	 */
	public function multicheck( $args ) {
		if ( ! empty( $args['options'] ) ) {
			$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
			if ( true === $args['desc_tip'] ) {
				echo '<img class="help_tip" data-tip="' . esc_attr( $args['desc'] ) . '" src="' . esc_url( $this->plugin_admin->get_images_folder() ) . 'help.png" height="16" width="16" />';
			}
			foreach ( $args['options'] as $key => $option ) {
				$enabled = null;
				if ( isset( $elm_settings[ $args['id'] ][ $key ] ) ) {
					$enabled = $option['id'];
				}

				echo '<input name="elm_settings[' . $args['id'] . '][' . $key . ']"" id="elm_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . esc_attr( $option['id'] ) . '" ' . checked( $option['id'], $enabled, false ) . '/>&nbsp;' .
					'<label for="elm_settings[' . $args['id'] . '][' . $key . ']">' . esc_attr( $option['name'] ) . '</label><br/>';
			}
			if ( false === $args['desc_tip'] ) {
				echo '<p class="description">' . $args['desc'] . '</p>';
			}
		}
	}

	/**
	 * Textarea Callback
	 *
	 * Renders textarea fields.
	 *
	 * @since  1.2.0
	 * @param  array $args Arguments passed by the setting
	 * @return void
	 */
	public function textarea( array $args ) {
		$elm_settings = ELM_IOC::make( 'settings' )->get_settings();

		if ( isset( $elm_settings[ $args['id'] ] ) ) {
			$value = $elm_settings[ $args['id'] ];
		} else {
			$value = isset( $args['std'] ) ? $args['std'] : '';
		}

		$html = '<textarea class="large-text" cols="50" rows="5" id="elm_settings[' . $args['id'] . ']" name="elm_settings[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
		$html .= '<label for="elm_settings[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

		echo $html;
	}

	/**
	 * Header Callback
	 *
	 * Renders the header.
	 *
	 * @since  1.2.0
	 * @param  array $args Arguments passed by the setting
	 * @return void
	 */
	public function header( $args ) {
		echo '<hr/>';
	}

}
