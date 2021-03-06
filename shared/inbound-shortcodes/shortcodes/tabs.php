<?php
/**
*   Tabs Shortcode
*   ---------------------------------------------------------------------------
*   @author 	: Rifki A.G
*   @copyright	: Copyright (c) 2013, FreshThemes
*                 http://www.freshthemes.net
*                 http://www.rifki.net
*   --------------------------------------------------------------------------- */

/* 	Shortcode generator config
 * 	----------------------------------------------------- */
	$shortcodes_config['tabs'] = array(
		'no_preview' => true,
		'options' => array(
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Enter the heading text', INBOUND_LABEL),
				'type' => 'text',
				'std' => '' 
			)
		),
		'child' => array(
			'options' => array(
				'title' => array(
					'name' => __('Tab Title',  INBOUND_LABEL),
					'desc' => __('Enter the tab title.',  INBOUND_LABEL),
					'type' => 'text',
					'std' => ''
				),
				'icon' => array(
					'name' => __('Icon', INBOUND_LABEL),
					'desc' => __('Select an icon.', INBOUND_LABEL),
					'type' => 'select',
					'options' => $fontawesome,
					'std' => ''
				),
				'content' => array(
					'name' => __('Tab Content',  INBOUND_LABEL),
					'desc' => __('Put the content here.',  INBOUND_LABEL),
					'type' => 'textarea',
					'std' => ''
				)
			),
			'shortcode' => '[tab title="{{title}}" icon="{{icon}}"]{{content}}[/tab]',
			'clone' => __('Add More Tab',  INBOUND_LABEL )
		),
		'shortcode' => '[tabs]{{child}}[/tabs]',
		'popup_title' => __('Insert Tabs Shortcode',  INBOUND_LABEL)
	);

/* 	Page builder module config
 * 	----------------------------------------------------- */
	$freshbuilder_modules['tabs'] = array(
		'name' => __('Tabs', INBOUND_LABEL),
		'size' => 'one_half',
		'options' => array(
			'heading' => array(
				'name' => __('Heading', INBOUND_LABEL),
				'desc' => __('Enter the heading text', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			)
		),
		'child' => array(
			'title' => array(
				'name' => __('Title', INBOUND_LABEL),
				'desc' => __('Enter the tab title', INBOUND_LABEL),
				'type' => 'text',
				'std' => '',
				'class' => '',
				'is_content' => 0
			),
			'icon' => array(
				'name' => __('Icon', INBOUND_LABEL),
				'desc' => __('Select an icon.', INBOUND_LABEL),
				'type' => 'select',
				'options' => $fontawesome,
				'std' => 'none',
				'class' => '',
				'is_content' => 0
			),
			'content' => array(
				'name' => __('Content', INBOUND_LABEL),
				'desc' => __('Enter the tab content', INBOUND_LABEL),
				'type' => 'textarea',
				'class' => '',
				'is_content' => 1
			)
		),
		'child_code' => 'tab'
	);

/* 	Add shortcode
 * 	----------------------------------------------------- */
	add_shortcode('tabs', 'fresh_shortcode_tabs');
	
	function fresh_shortcode_tabs( $atts, $content = null ) {
		extract(shortcode_atts(array(
			'heading' => ''
		), $atts));

		$out = '';
	
		if (!preg_match_all("/(.?)\[(tab)\b(.*?)(?:(\/))?\](?:(.+?)\[\/tab\])?(.?)/s", $content, $matches)) {
			return do_shortcode($content);
		} 
		else {
			
			for($i = 0; $i < count($matches[0]); $i++) {
				$matches[3][$i] = shortcode_parse_atts($matches[3][$i]);
			}

			if( $heading != '' ) $out .= '<div class="heading"><h3>'.$heading.'</h3><div class="sep"></div></div>';
			$out .= '<div class="tabs-content">';
				$out .= '<ul class="tabs-nav clearfix">';
				for($i = 0; $i < count($matches[0]); $i++) {
					$icon = ($matches[3][$i]['icon'] != '') ? '<i class="tab-icon icon-'.$matches[3][$i]['icon'].'"></i>' : '';


					$out .= '<li><a id="tab_'.$i.'_nav" title="'.$matches[3][$i]['title'].'" href="#tab_'.$i.'">'.$icon.'<span>'.$matches[3][$i]['title'].'<span></a></li>';
				}
				$out .= '</ul>';
			
				$out .= '<div class="tabs">';
				for($i = 0; $i < count($matches[0]); $i++) {
					$out .= '<div id="tab_'.$i.'">' . do_shortcode(trim($matches[5][$i])) .'</div>';
				}
				$out .= '</div>';
			$out .= '</div>';
			
			return $out;
		}
	}