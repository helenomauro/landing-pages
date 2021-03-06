<?php
/**
 *	FreshThemes Shortcodes Field Class
 * 	---------------------------------------------------------------------------
 *	@author 	: Rifki A.G
 *	@version	: 1.3
 *	@copyright	: Copyright (c) 2013, FreshThemes
 *		          http://www.freshthemes.net
 *		          http://www.rifki.net
 *	--------------------------------------------------------------------------- */

/* 	Include wp-load
 * 	----------------------------------------------------- */
	$path_to_file = explode( 'wp-content', __FILE__ );
	$path_to_wp = $path_to_file[0];

	require_once( $path_to_wp . '/wp-load.php' );

/* 	The Class
 * 	----------------------------------------------------- */
class InboundShortcodesFields {

/* 	Variables
 * 	----------------------------------------------------- */
	var	$popup,
		$options,
		$shortcode,
		$child_options,
		$child_shortcode,
		$popup_title,
		$no_preview,
		$has_child,
		$output,
		$errors;

/* 	Constuctor
 * 	----------------------------------------------------- */
	function __construct( $popup ) {
		$this->popup = $popup;
		$this->show();
	}

/* 	Show Fields
 * 	----------------------------------------------------- */
	function show() {
		global $shortcodes_config;
		
		$fields = $shortcodes_config;

		if( isset( $fields[$this->popup]['child'] ) )
			$this->has_child = true;

		if( isset( $fields ) && is_array( $fields ) ) {

			$this->options = $fields[$this->popup]['options'];
			$this->shortcode = $fields[$this->popup]['shortcode'];
			$this->popup_title = $fields[$this->popup]['popup_title'];

			$this->append_output('<div id="_fresh_shortcodes_output" class="hidden">'.$this->shortcode.'</div>');
			$this->append_output('<div id="_fresh_shortcodes_popup" class="hidden">'.$this->popup.'</div>');

			if( isset( $fields[$this->popup]['no_preview'] ) && $fields[$this->popup]['no_preview'] ) {
				$this->append_output( "\n" . '<div id="_fresh_shortcodes_preview" class="hidden">false</div>' );
				$this->no_preview = true;		
			}

			foreach( $this->options as $key => $option ) {
				$key = 'inbound_shortcode_' . $key;
				$name = ( isset($option['name'])) ? $option['name'] : '';
				$desc = ( isset($option['desc'])) ? $option['desc'] : '';
				$std = ( isset($option['std']) ) ? $option['std'] : '';
				$placeholder = (isset($option['placeholder'])) ? $option['placeholder'] : '';

				$row_start  = '<tbody>';
				$row_start .= '<tr class="form-row">';
				$row_start .= '<td class="label">' . $name . '</td>';
				$row_start .= '<td class="field">';
				
				if( $option['type'] != 'checkbox' ) {
					$row_end = '<span class="inbound-shortcodes-form-desc">' . $desc . '</span>';
				}
				else {
					$row_end = '';
				}
				$row_end   .= '</td>';
				$row_end   .= '</tr>';					
				$row_end   .= '</tbody>';

				switch( $option['type'] ) {

					case 'text':
						$output  = $row_start;
						$output .= '<input type="text" class="inbound-shortcodes-input '.$key.'" name="'. $key .'" id="'. $key .'" value="'. $std .'" size="40" placeholder="'.$placeholder.'" />';
						$output .= $row_end;
						$this->append_output($output);	
						break;

					case 'textarea' :
						$output  = $row_start;
						$output .= '<textarea class="inbound-shortcodes-input inbound-shortcodes-textarea" name="'. $key .'" id="'. $key .'" rows="5" cols="50">'. $std .'</textarea>';
						$output .= $row_end;
						$this->append_output($output);			
						break;

					case 'select' :		
						$output  = $row_start;
						$output .= '<select name="'. $key .'" id="'.$key.'" class="inbound-shortcodes-input select inbound-shortcodes-select">';
						foreach( $option['options'] as $val => $opt ) {
							$selected = ($std == $val) ? ' selected="selected"' : '';
							$output .= '<option'. $selected .' value="'. $val .'">'. $opt .'</option>';
						}
						$output .= '</select>';
						$output .= $row_end;
						$this->append_output($output);
						break;
						
					case 'checkbox' :
						$output  = $row_start;
						$output .= '<label for="'.$key.'">';
						$output .= '<input type="checkbox" class="inbound-shortcodes-input inbound-shortcodes-checkbox" name="'.$key.'" id="'.$key.'"'. checked( $std, 1, false) .' />';
						$output .= $desc .'</label>';
						$output .= $row_end;
						$this->append_output($output);	
						break;
				}
			}

			if( isset( $fields[$this->popup]['child'] ) ) {
				
				$this->child_options = $fields[$this->popup]['child']['options'];
				$this->child_shortcode = $fields[$this->popup]['child']['shortcode'];
				
				$parent_row_start  = '<tbody>';
				$parent_row_start .= '<tr class="form-row has-child">';
				$parent_row_start .= '<td><a href="#" id="form-child-add" class="button button-secondary">'.$fields[$this->popup]['child']['clone'].'</a>';
				$parent_row_start .= '<div class="child-clone-rows">';
				$parent_row_start .= '<div id="_fresh_shortcodes_child_output" class="hidden">'.$this->child_shortcode.'</div>';
				$parent_row_start .= '<div id="field_instructions">Drag and drop fields to reorder.</div>';
				$parent_row_start .= '<div class="child-clone-row"><span class="form-field-row-number">1</span><a  class="child-clone-row-remove child-options-toggles">Remove</a><a  href="#" class="child-clone-row-shrink child-options-toggles ">Minimize</a><a  href="#" class="child-clone-row-exact child-options-toggles ">Clone</a>';
				$parent_row_start .= '<ul class="child-clone-row-form">';
			
				$this->append_output( $parent_row_start );
				$count = 0;
				foreach( $this->child_options as $key => $option ) {
					
					$hide_class = ($count > 0) ? 'minimize-class' : '';
					$original_key = $key;
					$key = 'inbound_shortcode_' . $key;
					$name = ( isset($option['name'])) ? $option['name'] : '';
					$desc = ( isset($option['desc'])) ? $option['desc'] : '';
					$std = ( isset($option['std']) ) ? $option['std'] : '';
					$type = ( isset($option['type']) ) ? $option['type'] : '';
					$tab_class = (isset($option['class'])) ? " inbound-tab-class-".$option['class'] : '';
					$placeholder = (isset($option['placeholder'])) ? $option['placeholder'] : '';
					$field_class = (isset($option['class'])) ? ' ' . $option['class'] : '';
					$dynamic_hide = (isset($option['reveal_on'])) ? ' inbound-hidden-row' : '';
					$reveal_on = (isset($option['reveal_on'])) ? ' reveal-' . $option['reveal_on'] : '';

					$child_row_start  = '<li class="child-clone-row-form-row '.$hide_class . $dynamic_hide . $reveal_on. $tab_class.'">';
					$child_row_start .= '<div class="child-clone-row-label row-class-'.$type.'">';
					$child_row_start .= '<label>' . $option['name'] . '</label>';
					$child_row_start .= '</div>';
					$child_row_start .= '<div class="child-clone-row-field row-class-'.$type.' row-child-class-'.$type.'">';
				
					if( $option['type'] != 'checkbox' ) {
						$child_row_end	  = '<span class="child-clone-row-desc">'.$desc.'</span>';
					}
					else {
						$child_row_end	  = '';
					}
					$child_row_end   .= '</div>';
					$child_row_end   .= '</li>';
					
					switch( $option['type'] ) {
						
						case 'helper-block' :		
							$child_output  = $child_row_start;
							
							$child_output .= $child_row_end;
							$this->append_output($child_output);	
							break;

						case 'text' :		
							$child_output  = $child_row_start;
							$child_output .= '<input type="text" data-conditional-hide="'.$reveal_on.'" class="inbound-shortcodes-child-input" name="'. $key .'" id="'. $key .'" placeholder="'.$placeholder.'" value="'. $std .'" />';
							$child_output .= $child_row_end;
							$this->append_output($child_output);	
							break;
							
						case 'textarea' :
							$child_output  = $child_row_start; 
							$child_output .= '<textarea class="inbound-shortcodes-child-input inbound-shortcodes-textarea" name="'. $key .'" id="'. $key .'">'. $std .'</textarea>';
							$child_output .= $child_row_end;
							$this->append_output($child_output);	
							break;
							
						case 'select' :		
							$child_output  = $child_row_start;
							$child_output .= '<select data-field-name="'.$original_key.'" name="'. $key .'" id="'. $key .'" class="inbound-shortcodes-child-input select inbound-shortcodes-select">';
							foreach( $option['options'] as $value => $option ) {
								$selected = ( $std == $value ) ? ' selected="selected"' : '';
								$child_output .= '<option'. $selected .' value="'. $value .'">'. $option .'</option>';
							}
							$child_output .= '</select>';
							$child_output .= $child_row_end;
							$this->append_output($child_output);	
							break;
							
						case 'checkbox' :
							$child_output  = $child_row_start;
							$child_output .= '<label for="'.$key.'">';
							$child_output .= '<input type="checkbox" class="inbound-shortcodes-child-input inbound-shortcodes-checkbox" name="'. $key .'" id="'. $key .'" '. checked( $std, 1, false) .' />';
							$child_output .= $desc.'</label>';
							$child_output .= $child_row_end;
							$this->append_output($child_output);
							break;
					}
					$count++;
				}
				
				$parent_row_end    = '</ul>';
				$parent_row_end   .= '</div>';
				$parent_row_end   .= '</div>';
				$parent_row_end   .= '</td>';
				$parent_row_end   .= '</tr>';			
				$parent_row_end   .= '</tbody>';
			
				$this->append_output( $parent_row_end );
			}
		}
	}

	function append_output( $output ) {
		$this->output = $this->output . $output;		
	}

	function reset_output( $output ) {
		$this->output = '';
	}

	function append_error( $error ) {
		$this->errors = $this->errors . $error;
	}

}