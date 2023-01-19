<?php

/**
 * Register, display and save a text field setting in the admin menu
 *
 * @since 1.0
 * @package Simple Admin Pages
 */

class sapAdminPageSettingNumber_2_6_12 extends sapAdminPageSetting_2_6_12 {

	public $sanitize_callback = 'sanitize_text_field';

	/**
	 * Placeholder string for the input field
	 * @since 2.0
	 */
	public $placeholder = '';

	/**
	 * Display this setting
	 * @since 1.0
	 */
	public function display_setting() {
		?>

		<fieldset <?php $this->print_conditional_data(); ?>>

			<input name="<?php echo $this->get_input_name(); ?>" type="number" id="<?php echo esc_attr( $this->get_input_name() ); ?>" value="<?php echo esc_attr( $this->value ); ?>"<?php echo !empty( $this->placeholder ) ? ' placeholder="' . esc_attr( $this->placeholder ) . '"' : ''; ?> class="regular-text" <?php echo ( $this->disabled ? 'disabled' : ''); ?> />

			<?php $this->display_disabled(); ?>	
			
		</fieldset>
		
		<?php

		$this->display_description();

	}

}
