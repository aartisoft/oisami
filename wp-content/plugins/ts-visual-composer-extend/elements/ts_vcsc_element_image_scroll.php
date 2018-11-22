<?php
    global $VISUAL_COMPOSER_EXTENSIONS;
	if ((class_exists('WPBakeryShortCode')) && (!class_exists('WPBakeryShortCode_TS_VCSC_Image_Scroll'))) {
		class WPBakeryShortCode_TS_VCSC_Image_Scroll extends WPBakeryShortCode {};
	};
    $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_VisualComposer_Element = array(
		"name"                          	=> __( "TS Image Scroll", "ts_visual_composer_extend" ),
		"base"                          	=> "TS_VCSC_Image_Scroll",
		"icon"                          	=> "ts-composer-element-icon-image-scroll",
		"class"                         	=> "ts_vcsc_main_image_scroll",
		"category"                      	=> __( "VC Extensions", "ts_visual_composer_extend" ),
		"description" 		            	=> __("Place an image with scroll effect", "ts_visual_composer_extend"),
		"admin_enqueue_js"            		=> "",
		"admin_enqueue_css"           		=> "",
		"params"                        	=> array(
			// Image Selections
			array(
				"type"                  	=> "seperator",
				"param_name"            	=> "seperator_1",
				"seperator"					=> "Image Selections",
			),
			array(
				"type"                  	=> "attach_image",
				"holder" 					=> ($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_EditorImagePreview == "true" ? "img" : ""),
				"heading"               	=> __( "Image", "ts_visual_composer_extend" ),
				"param_name"            	=> "image",
				"class"						=> "ts_vcsc_holder_image",
				"value"                 	=> "",
				"admin_label"           	=> ($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_EditorImagePreview == "true" ? false : true),
				"description"           	=> __( "Select the image you want to use.", "ts_visual_composer_extend" )
			),
			array(
				"type"                  	=> "nouislider",
				"heading"               	=> __( "Visible Height", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_height",
				"value"                 	=> "250",
				"min"                   	=> "50",
				"max"                   	=> "1000",
				"step"                 	 	=> "1",
				"unit"                  	=> 'px',
				"description"           	=> __( "Define the visible height of the image.", "ts_visual_composer_extend" ),
			),
			array(
				"type"                  	=> "nouislider",
				"heading"               	=> __( "Speed: Scroll Down", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_speed_down",
				"value"                 	=> "4",
				"min"                   	=> "1",
				"max"                   	=> "25",
				"step"                 	 	=> "1",
				"unit"                  	=> 's',
				"description"           	=> __( "Define the speed in seconds at which the image should scroll down.", "ts_visual_composer_extend" ),
			),
			array(
				"type"                  	=> "nouislider",
				"heading"               	=> __( "Speed: Scroll Up", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_speed_up",
				"value"                 	=> "1",
				"min"                   	=> "1",
				"max"                   	=> "5",
				"step"                 	 	=> "1",
				"unit"                  	=> 's',
				"description"           	=> __( "Define the speed in seconds at which the image should scroll back up.", "ts_visual_composer_extend" ),
			),
			array(
				"type"             	 		=> "switch_button",
				"heading"               	=> __( "Retain Position", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_memory",
				"value"                 	=> "true",
				"admin_label"       		=> true,
				"description"       		=> __( "Use the toggle if you want the image to retain its current scroll position after moving the mouse away from it.", "ts_visual_composer_extend" ),
			),
			array(
				"type"                  	=> "dropdown",
				"heading"               	=> __( "Animation Easing", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_easing",
				"width"                 	=> 150,
				"value" 					=> array(
					__( "Linear", "ts_visual_composer_extend" )						=> "linear",
					__( "Ease", "ts_visual_composer_extend" )						=> "ease",
					__( "Ease In", "ts_visual_composer_extend" )					=> "ease-in",
					__( "Ease Out", "ts_visual_composer_extend" )					=> "ease-out",
					__( "Ease In Out", "ts_visual_composer_extend" )				=> "ease-in-out",
				),
				"dependency"            	=> array( 'element' => "scroll_memory", 'value' => 'false' ),
				"description"           	=> __( "Select the type of CSS3 easing effect that should be used for the scroll.", "ts_visual_composer_extend" ),				
			),			
			array(
				"type"                  	=> "dropdown",
				"heading"               	=> __( "Browser Frame", "ts_visual_composer_extend" ),
				"param_name"            	=> "scroll_browser",
				"width"                 	=> 150,
				"value" 					=> array(
					__( "No Browser Frame", "ts_visual_composer_extend" )			=> "none",
					__( "Simple Browser Frame", "ts_visual_composer_extend" )		=> "simple",
					__( "Extended Browser Frame", "ts_visual_composer_extend" )		=> "full",
				),
				"description"           	=> __( "Select if and what type of imitation browser frame the image should be shown in.", "ts_visual_composer_extend" ),
				"admin_label"       		=> true,
			),
			array(
				"type"             	 		=> "switch_button",
				"heading"               	=> __( "Show Handle", "ts_visual_composer_extend" ),
				"param_name"            	=> "overlay_handle_show",
				"value"                 	=> "true",
				"edit_field_class"			=> "vc_col-sm-6 vc_column",
				"description"       		=> __( "Use the toggle to show or hide a handle indicator button below the image.", "ts_visual_composer_extend" ),
			),
			array(
				"type"                  	=> "colorpicker",
				"heading"              	 	=> __( "Handle Color", "ts_visual_composer_extend" ),
				"param_name"            	=> "overlay_handle_color",
				"value"                 	=> "#0094FF",
				"edit_field_class"			=> "vc_col-sm-6 vc_column",
				"description"           	=> __( "Define the color for the handle indicator button.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "overlay_handle_show", 'value' => 'true' ),
			),
			// Click Events
			array(
				"type"				    	=> "seperator",
				"param_name"		    	=> "seperator_2",
				"seperator"					=> "Click Event",
				"group" 					=> "Click Event",
			),
			array(
				"type"                  	=> "dropdown",
				"heading"               	=> __( "Click Event", "ts_visual_composer_extend" ),
				"param_name"            	=> "hover_event",
				"width"                 	=> 150,
				"value" 					=> array(
					__( "None", "ts_visual_composer_extend" )									=> "none",
					__( "Open Image in Lightbox", "ts_visual_composer_extend" )					=> "image",
					__( "Open Popup in Lightbox", "ts_visual_composer_extend" )					=> "popup",
					__( "Open YouTube Video in Lightbox", "ts_visual_composer_extend" )			=> "youtube",
					__( "Open Vimeo Video in Lightbox", "ts_visual_composer_extend" )			=> "vimeo",
					__( "Open DailyMotion Video in Lightbox", "ts_visual_composer_extend" )		=> "dailymotion",
					__( "Open Page in iFrame", "ts_visual_composer_extend" )					=> "iframe",
					__( "Simple Link to Page", "ts_visual_composer_extend" )					=> "link",
				),
				"description"           	=> __( "Select if the Hover image should trigger any other action.", "ts_visual_composer_extend" ),
				"admin_label"       		=> true,
				"group" 					=> "Click Event",
			),
			// Modal Popup
			array(
				"type"                  	=> "switch_button",
				"heading"			    	=> __( "Show Hover Title", "ts_visual_composer_extend" ),
				"param_name"		    	=> "hover_show_title",
				"value"                 	=> "true",
				"description"		    	=> __( "Switch the toggle if you want to show the title in the modal popup.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => 'popup' ),
				"group" 					=> "Click Event",
			),
			array(
				"type"                  	=> "textfield",
				"heading"               	=> __( "Popup Title", "ts_visual_composer_extend" ),
				"param_name"            	=> "hover_title",
				"value"                 	=> "",
				"description"           	=> __( "Enter an optional title for the modal popup.", "ts_visual_composer_extend" ),
				"dependency"        		=> array( 'element' => "hover_event", 'value' => 'popup' ),
				"group" 					=> "Click Event",
			),				
			array(
				"type"		            	=> "textarea_html",
				"heading"               	=> __( "Popup Content", "ts_visual_composer_extend" ),
				"param_name"            	=> "content",
				"value"                 	=> "",
				"admin_label"           	=> false,
				"description"           	=> __( "Create the content for the modal popup.", "ts_visual_composer_extend" ),
				"dependency"        		=> array( 'element' => "hover_event", 'value' => 'popup' ),
				"group" 					=> "Click Event",
			),
			// YouTube / DailyMotion / Vimeo
			array(
				"type"                  	=> "textfield",
				"heading"               	=> __( "Video URL", "ts_visual_composer_extend" ),
				"param_name"            	=> "hover_video_link",
				"value"                 	=> "",
				"description"           	=> __( "Enter the URL for the video to be shown in a lightbox.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('youtube','dailymotion','vimeo') ),
				"group" 					=> "Click Event",
			),
			array(
				"type"              		=> "switch_button",
				"heading"			    	=> __( "Show Related Videos", "ts_visual_composer_extend" ),
				"param_name"		    	=> "hover_video_related",
				"value"             		=> "false",
				"description"		    	=> __( "Switch the toggle if you want to show related videos once the video has finished playing.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => 'youtube' ),
				"group" 					=> "Click Event",
			),
			array(
				"type"              		=> "switch_button",
				"heading"			    	=> __( "Autoplay Video", "ts_visual_composer_extend" ),
				"param_name"		    	=> "hover_video_auto",
				"value"             		=> "true",
				"description"		    	=> __( "Switch the toggle if you want to auto-play the video once opened in the lightbox.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('youtube','dailymotion','vimeo') ),
				"group" 					=> "Click Event",
			),
			// Link / iFrame
			array(
				"type" 						=> ($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_ParameterLinkPicker['enabled'] == "false" ? "vc_link" : "advancedlinks"),
				"heading" 					=> __("Link + Title", "ts_visual_composer_extend"),
				"param_name" 				=> "hover_link",
				"description" 				=> __("Provide a link to another site/page to be used for the Hover event.", "ts_visual_composer_extend"),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('iframe','link') ),
				"group" 					=> "Click Event",
			),
			// Image Tooltip
			array(
				"type"						=> "seperator",
				"param_name"				=> "seperator_4",
				"seperator"					=> "Tooltip Settings",
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"                  	=> "switch_button",
				"heading"			    	=> __( "Use HTML in Tooltip", "ts_visual_composer_extend" ),
				"param_name"		    	=> "tooltip_html",
				"value"                 	=> "false",
				"description"		    	=> __( "Switch the toggle if you want to allow basic HTML code for the tooltip content.", "ts_visual_composer_extend" ),
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"						=> "textarea",
				"heading"					=> __( "Tooltip Content", "ts_visual_composer_extend" ),
				"param_name"				=> "tooltip_content",
				"value"						=> "",
				"description"		    	=> __( "Enter the tooltip content here (do not use quotation marks or HTML code).", "ts_visual_composer_extend" ),
				"dependency"        		=> array( 'element' => "tooltip_html", 'value' => 'false' ),
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"              		=> ($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_EditorBase64TinyMCE == "true" ? "wysiwyg_base64" : "textarea_raw_html"),
				"heading"           		=> __( "Tooltip Content", "ts_visual_composer_extend" ),
				"param_name"        		=> "tooltip_content_html",
				"minimal"					=> "true",
				"value"             		=> base64_encode(""),
				"description"      	 		=> __( "Enter the tooltip content here; HTML code can be used.", "ts_visual_composer_extend" ),
				"dependency"        		=> array( 'element' => "tooltip_html", 'value' => 'true' ),
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"						=> "dropdown",
				"heading"					=> __( "Tooltip Style", "ts_visual_composer_extend" ),
				"param_name"				=> "tooltip_style",
				"value"             		=> $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_ToolTipster_Layouts,
				"description"				=> __( "Select the tooltip style.", "ts_visual_composer_extend" ),
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"						=> "nouislider",
				"heading"					=> __( "Tooltip X-Offset", "ts_visual_composer_extend" ),
				"param_name"				=> "tooltipster_offsetx",
				"value"						=> "0",
				"min"						=> "-100",
				"max"						=> "100",
				"step"						=> "1",
				"unit"						=> 'px',
				"description"				=> __( "Define an optional X-Offset for the tooltip position.", "ts_visual_composer_extend" ),
				"group" 					=> "Tooltip Settings",
			),
			array(
				"type"						=> "nouislider",
				"heading"					=> __( "Tooltip Y-Offset", "ts_visual_composer_extend" ),
				"param_name"				=> "tooltipster_offsety",
				"value"						=> "0",
				"min"						=> "-100",
				"max"						=> "100",
				"step"						=> "1",
				"unit"						=> 'px',
				"description"				=> __( "Define an optional Y-Offset for the tooltip position.", "ts_visual_composer_extend" ),
				"group" 					=> "Tooltip Settings",
			),
			// Lightbox Settings
			array(
				"type"                  	=> "seperator",
				"param_name"            	=> "seperator_5",
				"seperator"					=> "Lightbox Settings",
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('image', 'popup', 'youtube', 'vimeo', 'dailymotion', 'iframe') ),
				"group" 					=> "Lightbox Settings",
			),
			array(
				"type"             	 		=> "switch_button",
				"heading"			    	=> __( "Create AutoGroup", "ts_visual_composer_extend" ),
				"param_name"		    	=> "lightbox_group",
				"value"				    	=> "true",
				"description"       		=> __( "Switch the toggle if you want the plugin to group this image with all other non-gallery images on the page.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('image', 'popup', 'youtube', 'vimeo', 'dailymotion', 'iframe') ),
				"group" 					=> "Lightbox Settings",
			),
			array(
				"type"                  	=> "textfield",
				"heading"               	=> __( "Group Name", "ts_visual_composer_extend" ),
				"param_name"            	=> "lightbox_group_name",
				"value"                 	=> "",
				"admin_label"           	=> true,
				"description"           	=> __( "Enter a custom group name to manually build group with other non-gallery items.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "lightbox_group", 'value' => 'false' ),
				"group" 					=> "Lightbox Settings",
			),
			array(
				"type"                  	=> "dropdown",
				"heading"               	=> __( "Transition Effect", "ts_visual_composer_extend" ),
				"param_name"            	=> "lightbox_effect",
				"width"                 	=> 150,
				"value"                 	=> $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_Lightbox_Animations,
				"default" 					=> $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_LightboxDefaultAnimation,
				"std" 						=> $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_LightboxDefaultAnimation,
				"admin_label"           	=> true,
				"description"           	=> __( "Select the transition effect to be used for the image in the lightbox.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('image', 'popup', 'youtube', 'vimeo', 'dailymotion', 'iframe') ),
				"group" 					=> "Lightbox Settings",
			),
			array(
				"type"                  	=> "dropdown",
				"heading"               	=> __( "Backlight Effect", "ts_visual_composer_extend" ),
				"param_name"            	=> "lightbox_backlight",
				"width"                 	=> 150,
				"value"                 	=> array(
					__( 'Auto Color', "ts_visual_composer_extend" )       											=> "auto",
					__( 'Custom Color', "ts_visual_composer_extend" )     											=> "custom",
					__( 'Transparent Backlight', "ts_visual_composer_extend" )     	=> "hideit",
				),
				"admin_label"           	=> true,
				"description"           	=> __( "Select the backlight effect for the image.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "hover_event", 'value' => array('image', 'popup', 'youtube', 'vimeo', 'dailymotion', 'iframe') ),
				"group" 					=> "Lightbox Settings",
			),
			array(
				"type"                  	=> "colorpicker",
				"heading"               	=> __( "Custom Backlight Color", "ts_visual_composer_extend" ),
				"param_name"            	=> "lightbox_backlight_color",
				"value"                 	=> "#ffffff",
				"description"           	=> __( "Define the backlight color for the lightbox image.", "ts_visual_composer_extend" ),
				"dependency"            	=> array( 'element' => "lightbox_backlight", 'value' => 'custom' ),
				"group" 					=> "Lightbox Settings",
			),
			// Other Settings
			array(
				"type"                  	=> "seperator",
				"param_name"            	=> "seperator_6",
				"seperator"                 => "Other Settings",
				"group" 					=> "Other Settings",
			),
			array(
				"type"                  	=> "nouislider",
				"heading"               	=> __( "Margin: Top", "ts_visual_composer_extend" ),
				"param_name"            	=> "margin_top",
				"value"                 	=> "0",
				"min"                   	=> "0",
				"max"                   	=> "200",
				"step"                 	 	=> "1",
				"unit"                  	=> 'px',
				"description"           	=> __( "Select the top margin for the element.", "ts_visual_composer_extend" ),
				"group" 					=> "Other Settings",
			),
			array(
				"type"                  	=> "nouislider",
				"heading"               	=> __( "Margin: Bottom", "ts_visual_composer_extend" ),
				"param_name"            	=> "margin_bottom",
				"value"                 	=> "0",
				"min"                   	=> "0",
				"max"                   	=> "200",
				"step"                 	 	=> "1",
				"unit"                  	=> 'px',
				"description"           	=> __( "Select the bottom margin for the element.", "ts_visual_composer_extend" ),
				"group" 					=> "Other Settings",
			),
			array(
				"type"                  	=> "textfield",
				"heading"               	=> __( "Define ID Name", "ts_visual_composer_extend" ),
				"param_name"            	=> "el_id",
				"value"                 	=> "",
				"description"          	 	=> __( "Enter an unique ID for the element.", "ts_visual_composer_extend" ),
				"group" 					=> "Other Settings",
			),
			array(
				"type"                  	=> "tag_editor",
				"heading"           		=> __( "Extra Class Names", "ts_visual_composer_extend" ),
				"param_name"            	=> "el_class",
				"value"                 	=> "",
				"description"      			=> __( "Enter additional class names for the element.", "ts_visual_composer_extend" ),
				"group" 					=> "Other Settings",
			),
		)
	);	
	if ($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_VisualComposer_LeanMap == "true") {
		return $VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_VisualComposer_Element;
	} else {			
		vc_map($VISUAL_COMPOSER_EXTENSIONS->TS_VCSC_VisualComposer_Element);
	};
?>