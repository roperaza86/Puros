<?php

// [banner]

vc_map(array(
   
   "name"			=> "Title",
   "category"		=> 'Content',
   "description"	=> "Place Title",
   "base"			=> "title",
   "class"			=> "",
   "icon"			=> "title",

   
   "params" 	=> array(
      
		array(
			"type"			=> "textfield",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Title",
			"param_name"	=> "title_text",
			"value"			=> "Title",
		),

		array(
			"type"			=> "dropdown",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Tag",
			"param_name"	=> "title_tag",
			"value"			=> array(
				"H1"		=> "h1",
				"H2"		=> "h2",
				"H3"		=> "h3",
				"H4"		=> "h4",
				"H5"		=> "h5",
				"H6"		=> "h6",
			),
			"std"			=> "h3",
		),

		array(
			"type"			=> "dropdown",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Font Family",
			"param_name"	=> "title_font",
			"value"			=> array(
				"Main Font"			=> "main_font",
				"Secondary Font"	=> "secondary_font"
			),
			"std"			=> "",
		),

		array(
			"type"			=> "textfield",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Font Size (px, em)",
			"param_name"	=> "title_font_size",
			"value"			=> "",
		),

		array(
			"type"			=> "textfield",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Line Height (px, em)",
			"param_name"	=> "title_line_height",
			"value"			=> "",
		),

		array(
			"type"			=> "colorpicker",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Text Color",
			"param_name"	=> "title_text_color",
			"value"			=> "",
		),

		array(
			"type"			=> "dropdown",
			"holder"		=> "div",
			"class" 		=> "hide_in_vc_editor",
			"admin_label" 	=> true,
			"heading"		=> "Text Align",
			"param_name"	=> "title_text_align",
			"value"			=> array(
				"Left"		=> "left",
				"Center"	=> "center",
				"Right"		=> "right",
			),
			"std"			=> "",
		),

   )
   
));