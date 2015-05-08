<?php
/*
Plugin Name: Corner Bracket Lover
Plugin URI: http://sparanoid.com/work/corner-bracket-lover/
Description: Corner Bracket Lover converts all curly quotation marks in your posts to traditional corner brackets.
Version: 1.2.3
Author: Tunghsiao Liu
Author URI: http://sparanoid.com/
Author Email: t@sparanoid.com
Text Domain: corner-bracket-lover
Domain Path: /lang/
Network: false
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

  Copyright 2014 Tunghsiao Liu (t@sparanoid.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

register_activation_hook( __FILE__, 'cbl_add_defaults' );
register_uninstall_hook( __FILE__, 'cbl_delete_plugin_options' );
add_action( 'init', 'cbl_i18n_init' );
add_action( 'admin_init', 'cbl_init' );
add_action( 'admin_menu', 'cbl_add_options_page' );
add_action( 'plugins_loaded', 'cbl_conditions' );
add_filter( 'plugin_action_links', 'cbl_plugin_action_links', 10, 2 );

add_action( 'activated_plugin','cbl_save_error' );
function cbl_save_error(){
    update_option( 'sparanoid_plugin_error',  ob_get_contents() );
}

add_action( 'shutdown','sparanoid_show_plugin_error' );
function sparanoid_show_plugin_error(){
    echo get_option('plugin_error');
}

/**
 * Admin Options
 *
 * @since Corner Bracket Lover 1.1.0
 */

// Delete plugin created table when plugin deleted
function cbl_delete_plugin_options() {
	delete_option('cbl_options');
}

// Default option settings
function cbl_add_defaults() {

	$tmp = get_option('cbl_options');

	if( ( (isset($tmp['chk_default_options_db']) && $tmp['chk_default_options_db']=='1')) || (!is_array($tmp)) ) {
		$arr = array(
      "chk_post_content" => "1",
      "chk_excerpt" => "1",
      "chk_post_title" => "1",
      "chk_comments" => "1",
      "radio_strict_filtering" => "strict_on"
    );
		update_option('cbl_options', $arr);
	}
}

// Load the plugin text domain for translation
function cbl_i18n_init() {
	load_plugin_textdomain( 'corner-bracket-lover', false, basename( dirname( __FILE__ ) ) . '/lang' );
}

// Initialized options to white list our options
function cbl_init() {

	// Checks radio buttons have a valid choice (ie. no section is blank)
	// Primarily to check newly added options have correct initial values
	$tmp = get_option('cbl_options');

  // Check strict filtering option has a starting value
	if(!$tmp['radio_strict_filtering']) {
		$tmp["radio_strict_filtering"] = "strict_off";
		update_option('cbl_options', $tmp);
	}

  // Register settings
	register_setting( 'cbl_plugin_options', 'cbl_options' );
}

// Menu page
function cbl_add_options_page() {
	add_options_page(
    __( 'Corner Bracket Lover', 'corner-bracket-lover' ),
    __( 'Corner Bracket Lover', 'corner-bracket-lover' ),
    'manage_options', 'corner-bracket-lover', 'cbl_render_form'
  );
}

// Menu page content
function cbl_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2><?php _e( 'Corner Bracket Lover Options', 'corner-bracket-lover' ); ?></h2>
    <!-- <h3>Corner Bracket Lover Options</h3> -->
		<p><?php _e( 'Corner Bracket Lover converts all curly quotation marks in your posts to traditional corner brackets.', 'corner-bracket-lover' ); ?></p>

		<form method="post" action="options.php">
			<?php settings_fields('cbl_plugin_options'); ?>
			<?php $options = get_option('cbl_options'); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php _e( 'Apply corner bracket filter on', 'corner-bracket-lover' ); ?></th>
					<td>
            <fieldset>
  						<label>
                <input name="cbl_options[chk_post_content]" type="checkbox" value="1" <?php if (isset($options['chk_post_content'])) { checked('1', $options['chk_post_content']); } ?> />
                <?php _e( 'Post Content', 'corner-bracket-lover' ); ?><?php if (class_exists('bbPress')) { echo " (including bbPress content)"; } ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_excerpt]" type="checkbox" value="1" <?php if (isset($options['chk_excerpt'])) { checked('1', $options['chk_excerpt']); } ?> />
                <?php _e( 'Post Excerpt', 'corner-bracket-lover' ); ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_post_title]" type="checkbox" value="1" <?php if (isset($options['chk_post_title'])) { checked('1', $options['chk_post_title']); } ?> />
                <?php _e( 'Post Titles', 'corner-bracket-lover' ); ?><?php if (class_exists('bbPress')) { echo " (including bbPress titles)"; } ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_comments]" type="checkbox" value="1" <?php if (isset($options['chk_comments'])) { checked('1', $options['chk_comments']); } ?> />
                <?php _e( 'Comments and its author names', 'corner-bracket-lover' ); ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_bloginfo]" type="checkbox" value="1" <?php if (isset($options['chk_bloginfo'])) { checked('1', $options['chk_bloginfo']); } ?> />
                <?php _e( 'Blog Info', 'corner-bracket-lover' ); ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_category_description]" type="checkbox" value="1" <?php if (isset($options['chk_category_description'])) { checked('1', $options['chk_category_description']); } ?> />
                <?php _e( 'Category Description', 'corner-bracket-lover' ); ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_tags]" type="checkbox" value="1" <?php if (isset($options['chk_tags'])) { checked('1', $options['chk_tags']); } ?> />
                <?php _e( 'Tags', 'corner-bracket-lover' ); ?>
              </label><br>

  						<label>
                <input name="cbl_options[chk_tag_cloud]" type="checkbox" value="1" <?php if (isset($options['chk_tag_cloud'])) { checked('1', $options['chk_tag_cloud']); } ?> />
                <?php _e( 'Tag Cloud', 'corner-bracket-lover' ); ?>
              </label>
            </fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e( 'Database Options', 'corner-bracket-lover' ); ?></th>
					<td>
            <label>
  						<input name="cbl_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> />
              <?php _e( 'Restore defaults upon plugin deactivation or reactivation.', 'corner-bracket-lover' ); ?>
            </label>
            <p class="description"><?php _e( 'Only check this if you want to reset plugin settings when reactivation', 'corner-bracket-lover' ); ?></p>
					</td>
				</tr>
			</table>
			<p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        <input class="button" type="button" value="<?php _e( 'Follow on Twitter', 'corner-bracket-lover' ); ?>" onClick="window.open('http://twitter.com/tunghsiao')">
        <input class="button" type="button" value="<?php _e( 'Visit My Website', 'corner-bracket-lover' ); ?>" onClick="window.open('http://sparanoid.com/')">
  			<p><?php _e( 'Love this plugin? Please consider', 'corner-bracket-lover' ); ?> <a href="http://sparanoid.com/donate/"><?php _e( 'buying me a cup of coffee', 'corner-bracket-lover' ); ?></a><?php _e( '!', 'corner-bracket-lover' ); ?></p>
			</p>
		</form>
	</div>
	<?php
}

/**
 * Plugin Functions
 *
 * @since Corner Bracket Lover 1.1.0
 */

// Define replaced contents
function cbl_replace($the_content) {

  $the_content = str_replace("n’t", "n&rsquo;t", $the_content);
  $the_content = str_replace("’s", "&rsquo;s", $the_content);
  $the_content = str_replace("’m", "&rsquo;m", $the_content);
  $the_content = str_replace("’re", "&rsquo;re", $the_content);
  $the_content = str_replace("’ve", "&rsquo;ve", $the_content);
  $the_content = str_replace("’d", "&rsquo;d", $the_content);
  $the_content = str_replace("’ll", "&rsquo;ll", $the_content);
  $the_content = str_replace("“", "&#12300;", $the_content);
  $the_content = str_replace("”", "&#12301;", $the_content);
  $the_content = str_replace("‘", "&#12302;", $the_content);
  $the_content = str_replace("’", "&#12303;", $the_content);

  return $the_content;
}

function cbl_conditions() {

	$tmp = get_option('cbl_options');

	if (isset($tmp['chk_post_content'])) {
		if($tmp['chk_post_content']=='1'){ add_filter('the_content', 'cbl_filter'); }

		/* bbPress specific filtering (only if bbPress is present). */
		if (class_exists('bbPress')) {
			add_filter('bbp_get_topic_content', 'cbl_filter');
			add_filter('bbp_get_reply_content', 'cbl_filter');
		}
	}
  if (isset($tmp['chk_excerpt'])) {
    if($tmp['chk_excerpt']=='1'){ add_filter('the_excerpt', 'cbl_filter'); }
  }
  if (isset($tmp['chk_post_title'])) {
    if($tmp['chk_post_title']=='1'){ add_filter('the_title', 'cbl_filter'); }
  }
	if (isset($tmp['chk_comments'])) {
		if($tmp['chk_comments']=='1'){ add_filter('comment_text','cbl_filter'); }
	}
  if (isset($tmp['chk_comments'])) {
    if($tmp['chk_comments']=='1'){ add_filter('get_comment_author', 'cbl_filter'); }
  }
  if (isset($tmp['chk_bloginfo'])) {
    if($tmp['chk_bloginfo']=='1'){ add_filter('bloginfo', 'cbl_filter'); }
  }
  if (isset($tmp['chk_category_description'])) {
    if($tmp['chk_category_description']=='1'){ add_filter('category_description', 'cbl_filter'); }
  }
	if (isset($tmp['chk_tags'])) {
		if($tmp['chk_tags']=='1'){ add_filter('term_links-post_tag', 'cbl_filter' ); }
	}
	if (isset($tmp['chk_cloud'])) {
		if($tmp['chk_cloud']=='1'){ add_filter('wp_tag_cloud', 'cbl_filter'); }
	}
}

// Set replace sections
function cbl_filter($content) {
	return cbl_replace($content);
}

// Add a 'Settings' link on Plugins page
function cbl_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="'.get_admin_url().'options-general.php?page=corner-bracket-lover">'.__('Settings').'</a>';
		// Make sure the 'Settings' link at first
		array_unshift( $links, $posk_links );
	}

	return $links;
}
