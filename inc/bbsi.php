<?php

class BBsi {

	protected $option_name = 'bbsi';

	public function __construct() {

		// Admin sub-menu
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'add_page'));
		add_shortcode('bb_social_icons', array($this, 'bb_social_icons_shortcode'));
		add_filter( 'widget_text', 'do_shortcode');
		add_action('init', array($this,'add_styles'));
	}

	function add_styles(){
		if(!is_admin()){ //solo mostramos en front
			wp_enqueue_style( 'bbsi_icons', BBSI_URL_PATH.'assets/icons/style.css', array(), BBSI_VERSION );
			wp_enqueue_style( 'bbsi_style', BBSI_URL_PATH.'style.css', array('bbsi_icons'), BBSI_VERSION );
		}
	}
	function bb_social_icons_shortcode($atts){
		
		$options = get_option($this->option_name);
		$a = shortcode_atts( array(
			'color' => $options['color'],
			'icon_size' => $options['icon_size'],
			'color_hover' => $options['color_hover'],
			'transition' => $options['transition'],
		), $atts );
		$hover_class='bbsi_h'.substr($a['color_hover'],1);
		$color_class='bbsi_c'.substr($a['color'],1);
		$trans_class='bbsi_t'.$a['transition'];
		$size_class='bbsi_s'.$a['icon_size'];
		
		$custom_css = '.bbsi.'.$color_class.' a i{color:'.$a['color'].'}'.
		'.bbsi.'.$hover_class.' a:hover i{color:'.$a['color_hover'].'}'.
		'.bbsi.'.$size_class.' a,.bbsi.'.$size_class.' a i{font-size:'.$a['icon_size'].'px !important}'.
		'.bbsi.'.$trans_class.' a i{transition:color '.$a['transition'].'ms ease}';
		//wp_add_inline_style('bbsi_style', $custom_css);
		echo '<style scoped>'.$custom_css.'</style>'; //hideous, pero no puedo agregarme al head una vez que estoy procesando custom atts, y no puedo hacerlo inline por el hover
		
		$socials = json_decode($options['socials']);
		$output = "<ul class=\"bbsi {$hover_class} {$color_class} {$trans_class} {$size_class}\" >";

		if( is_array($socials) && count($socials) > 0 ){
			foreach ($socials as $social) {
				$output .= '<li><a href="'.$social->link.'" target="'.$options['link_target'].'"><i class="'.$social->icon.'"></i></a></li>';
			}
		}
		$output .= '</ul>';
		return $output;
	}	

	// White list our options using the Settings API
	public function admin_init() {
		register_setting('bbsi_options', $this->option_name, array($this, 'validate'));
	}

	// Add entry in the settings menu
	public function add_page() {
		$hook_suffix = add_options_page('BB Social Icons', 'BB Social Icons', 'manage_options', 'bb-social-icons', array($this, 'options_do_page'));
		add_action( 'load-'. $hook_suffix, array( $this, 'bbsi_settings_page_load' ) );
	}

	function bbsi_settings_page_load() {
		//Enqueue styles and scripts
		wp_enqueue_style( 'bbsi_iconpicker', BBSI_URL_PATH.'assets/css/jquery.fonticonpicker.min.css', array(),  '1.0' );
		wp_enqueue_style( 'bbsi_icons', BBSI_URL_PATH.'assets/icons/style.css', array('bbsi_iconpicker'), BBSI_VERSION );
		wp_add_inline_style('bbsi_icons', '.icons-selector *{font-family:bbsi}');
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue icon-picker js
		wp_enqueue_script( 'icon-picker', BBSI_URL_PATH.'assets/js/jquery.fonticonpicker.min.js', array( 'jquery' ), '1.0' );

		// Enqueue custom option panel JS
		wp_enqueue_script( 'options-custom', BBSI_URL_PATH.'assets/js/script.js', array( 'jquery','wp-color-picker', 'icon-picker' ), '1.0' );
		$option_var = array( 'options_path' => BBSI_URL_PATH.'assets' );
		wp_localize_script( 'options-custom', 'bbsi' , $option_var );
	}

	// Print the menu page itself
	public function options_do_page() {
		$options = get_option($this->option_name);
		?>
		<div class="wrap">
			<h2>BB Social Icons</h2>
			<form method="post" action="options.php">
				<?php settings_fields('bbsi_options'); ?>
				<table class="form-table">
					<tr><td width="60%"><table class="widefat" style="padding:15px;>
					<tr valign="top"><th scope="row">Icon size(in px):</th>
						<td><input type="number" style="width:110px;" name="<?php echo $this->option_name?>[icon_size]" value="<?php echo $options['icon_size']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row">Transition time(in ms):</th>
						<td><input type="number" style="width:110px;" name="<?php echo $this->option_name?>[transition]" value="<?php echo $options['transition']; ?>" /></td>
					</tr>
					<tr valign="top"><th scope="row">Open links in new tab:</th>
						<td>
							<input type="radio" name="<?php echo $this->option_name?>[link_target]" value="_blank" <?php if( $options['link_target'] == '_blank' ) echo 'checked="checked"';?> />Yes &nbsp;
							<input type="radio" name="<?php echo $this->option_name?>[link_target]" value="_self" <?php if( $options['link_target'] != '_blank' ) echo 'checked="checked"';?> /> No</td>
					</tr>
					<tr valign="top"><th scope="row">Icon Color:</th>
						<td><input type="text" name="<?php echo $this->option_name?>[color]" class="bb-color" value="<?php echo $options['color']; ?>" /></td>
					</tr>                    <tr valign="top"><th scope="row">Icon Hover Color:</th>
						<td><input type="text" name="<?php echo $this->option_name?>[color_hover]" class="bb-color" value="<?php echo $options['color_hover']; ?>" /></td>
					</tr>                    <tr><th scope="row" colspan="2">Social Icons:</th></tr>
					<tr><td colspan="2">
						<?php
						$socials = json_decode($options['socials']);
						$output = '';
						$output .='<div class="bb-new-field"><input type="button" class="bb-add-new button button-primary" value="Add New"></div><div class="bb-social-fields">';
						
						if( is_array ( $socials ) && count( $socials ) > 0 ) {
							foreach( $socials as $social ) {
								
								$output .= '<div class="bb-new-fields"><input type="text" name="' . esc_attr( $this->option_name . '[socials][icon][]' ) . '" class="bb-icon-picker"  value="'.$social->icon.'">';
								
								$output .= '<input type="text" name="' . esc_attr( $this->option_name . '[socials][link][]' ) . '" class="social_link" value="'.$social->link.'"><input type="button" class="button" value="Remove" /></div>';

							}
						}
						else {
								$output .= '<div class="bb-new-fields"><input type="text" name="' . esc_attr( $this->option_name . '[socials][icon][]' ) . '" class="bb-icon-picker"  value="">';
								
								$output .= '<input type="text" placeholder="Enter your url here" name="' . esc_attr( $this->option_name . '[socials][link][]' ) . '" class="' . esc_attr( 'social_link' ) . '"  value=""><input type="button" class="button" value="Remove" /></div>';
						}
						$output .= '</div>';                        
						echo $output;
						?>
					</td></tr>
				</table>
			</td>
			<td style="vertical-align:top">
				<table class="widefat">
					<tr>
						<td>
							Put this shortcode on any page/post/widget etc to show the social icons on your site.
						</td>
					</tr>
					<tr>
						<td>
							<code>[bb_social_icons]</code> For this defaults
						</td>
					</tr>
					<tr>
						<td>
							<code>[bb_social_icons icon_size="12" color="#fff" color_hover="#000" transition="300"]</code> This options are available
						</td>
					</tr>
				</table>
			</td>
		</tr></table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	public function validate($input) {

		$valid = array();
		$out = array();
		if ( is_array( $input['socials'] ) ) {
			foreach ( $input['socials']['icon'] as $k => $icon ) {
				if( $icon != '' && $input['socials']['link'][$k] != '' ) {
					$out[$k]['icon'] = $icon;
					$out[$k]['link'] = esc_url( $input['socials']['link'][$k], array('http', 'https', 'ftp', 'ftps', 'mailto', 'skype') );
				}
			}
		}

		$valid['color'] = sanitize_text_field($input['color']);
		$valid['link_target'] = sanitize_text_field($input['link_target']);
		$valid['color_hover'] = sanitize_text_field($input['color_hover']);
		$valid['icon_size'] = sanitize_text_field($input['icon_size']);
		$valid['transition'] = sanitize_text_field($input['transition']);
		$valid['socials'] = json_encode($out);
		
		return $valid;
	}
	

}
