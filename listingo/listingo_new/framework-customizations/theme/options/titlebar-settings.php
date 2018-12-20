<?php

if (!defined('FW')) {
    die('Forbidden');
}
$options = array(
    'titlebars' => array(
        'title' => esc_html__('Title Bar Settings', 'listingo'),
        'type' => 'tab',
        'options' => array(
            'general-box' => array(
                'title' => esc_html__('Title Bar Settings', 'listingo'),
                'type' => 'box',
                'options' => array(
					'titlebar_type' => array(
						'type'         => 'multi-picker',
						'label'        => false,
						'desc'         => false,
						'picker'       => array(
							'gadget' => array(
								'label'   => esc_html__( 'Title bar Type', 'listingo' ),
								'desc'   => esc_html__( 'Select title bar type', 'listingo' ),
								'type'    => 'select',
								'value'    => 'default',
								'choices' => array(
									'default' => esc_html__('Default', 'listingo'),	
									'none' => esc_html__('None, hide it', 'listingo'),	

								)
							)
						),
						'choices'      => array(
							'default'  => array(
								'enable_breadcrumbs' => array(
									'type' => 'switch',
									'value' => 'disable',
									'label' => esc_html__('Breadcrumbs', 'listingo'),
									'desc' => esc_html__('Enable or Disable breadcrumbs. Please note global settings(From Theme Settings) should be enabled', 'listingo'),
									'left-choice' => array(
										'value' => 'enable',
										'label' => esc_html__('Enable', 'listingo'),
									),
									'right-choice' => array(
										'value' => 'disable',
										'label' => esc_html__('Disable', 'listingo'),
									),
								),
								'titlebar_bg_image' => array (
									'type'        => 'upload' ,
									'label'       => esc_html__('Background?' , 'listingo') ,
									'desc'        => esc_html__('Upload background image' , 'listingo') ,
									'images_only' => true ,
								) ,
								'titlebar_bg'     => array (
									'type'  => 'rgba-color-picker' ,
									'value' => 'rgba(54, 59, 77, 0.40)' ,
									'label' => esc_html__('Background color' , 'listingo') ,
									'desc'  => esc_html__('RGBA color will be over image and solid color will override image' , 'listingo') ,
								) ,
							),
						)
					),
                )
            ),
        )
    )
);
