<?php

// [banner]
function title_shortcode($params = array(), $content = null) {
	
	extract(shortcode_atts(array(
		'title_text' => '',
		'title_font' => '',
		'title_tag' => '',
		'title_font_size' => '',
		'title_line_height' => '',
		'title_text_color' => '',
		'title_text_align' => ''
	), $params));
	
	$content = do_shortcode($content);

	$title_styles = "";
	if ($title_font_size != '') 	$title_styles .= 'font-size:' . $title_font_size . ';';
	if ($title_line_height != '') 	$title_styles .= 'line-height:' . $title_line_height . ';';
	if ($title_text_color != '') 	$title_styles .= 'color:' . $title_text_color . ' !important;';
	if ($title_text_align != '') 	$title_styles .= 'text-align:' . $title_text_align . ';';
	
	$title_html = '<'.$title_tag.' style="'.$title_styles.'" class="shortcode_title '.$title_font.'">'.$title_text.'</'.$title_tag.'>';
	
	return $title_html;
}

add_shortcode('title', 'title_shortcode');