<?php
/*
Plugin Name: Corner Bracket Lover
Plugin URI: http://sparanoid.com/lab/corner-bracket-lover/
Description: A plugin converts all full-width quotation marks to traditional corner brackets.
Version: 1.0.1
Author: Tunghsiao Liu
Author URI: http://sparanoid.com/
Author Email: info@sparanoid.com
License: GPLv2 or later
*/

// Define replaced contents
function cbl_replace($the_content) {
  $the_content = str_replace("“", "&#12300;", $the_content);
  $the_content = str_replace("”", "&#12301;", $the_content);
  $the_content = str_replace("‘", "&#12302;", $the_content);
  $the_content = str_replace("’", "&#12303;", $the_content);
  return $the_content;
}

// Set replace sections
function cbl_filter($content) {
	return cbl_replace($content);
}

// WordPress hooks
add_filter ('bloginfo','cbl_filter');
add_filter ('the_title','cbl_filter');
add_filter ('the_content','cbl_filter');
add_filter ('the_excerpt','cbl_filter');
add_filter ('category_description','cbl_filter');
// add_filter ('comment_author','cbl_filter');
// add_filter ('comment_excerpt','cbl_filter');
// add_filter ('comment_text','cbl_filter');
add_filter ('list_cats','cbl_filter');
// add_filter ('single_post_title','cbl_filter');
?>
