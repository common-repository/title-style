<?php
/*
Plugin Name: Title Style
Plugin URI: 
Description: Adds emphasis to certain words in post titles.
Author: Kari Pätilä
Version: 0.1.1
Author URI: http://twitter.com/karipatila
*/

function titlestyle_install()
{
	update_option('titlestyle_html_tag', 'em');
	update_option('titlestyle_classname', '');
	update_option('titlestyle_word_type', 1);
	update_option('titlestyle_wordlist', '');	
}

function add_word_boundaries(&$word)
{
	$word = trim($word);
	$word = '\\b'.$word.'\\b';
}

function title_style($string)
{
	#let's not style the admin area
	if(!is_admin())
	{
		$tag = get_option('titlestyle_html_tag');
		$tag_class = get_option('titlestyle_classname');
		$open_tag = '<'.$tag.'>';
		$close_tag = '</'.$tag.'>';
		$wordlist = get_option('titlestyle_wordlist');
		if(!empty($tag_class))
		{
			$open_tag = '<'.$tag . ' class="'.$tag_class.'">';
			$close_tag = '</'.$tag.'>';
		}
		if(get_option('titlestyle_word_type') == 1 && !empty($wordlist))
		{
			$wordlist = explode(',',$wordlist);
			array_walk($wordlist, 'add_word_boundaries');
			$wordlist = implode('|',$wordlist);
			$string = preg_replace("/$wordlist/", "$open_tag\\0$close_tag", $string);
		}
		if(get_option('titlestyle_word_type') == 0)
		{
			# we don't want to wrap html entities like &mdash;, so let's convert them to UTF-8 instead
			$string=html_entity_decode($string,ENT_COMPAT,'UTF-8');
			$string = preg_replace("/(\\b[^(\WA-Z0-9_)]+\\b)/u", "$open_tag\\0$close_tag", $string);
		}
	}
	return $string;		
}

function on_save_changes() {
	if ( !current_user_can('manage_options') )
		wp_die( __('Cheatin&#8217; uh?') );
	check_admin_referer('titlestyle-settings');
	
	$tag = $_POST['tag'];
	$classname = $_POST['classname'];
	$word_type = $_POST['word_type'][0];
	$wordlist = $_POST['wordlist'];

	update_option('titlestyle_html_tag', $tag);
	update_option('titlestyle_classname', $classname);
	update_option('titlestyle_word_type', $word_type);
	update_option('titlestyle_wordlist', $wordlist);
	wp_redirect($_POST['_wp_http_referer']);		
}


function titlestyle_settings_page() {
?>
<div class="wrap">
	<?php screen_icon('options-general'); ?>
<h2>Title Style Settings</h2>
<div class="clear"></div>
<form action="admin-post.php" method="post">
	<?php wp_nonce_field('titlestyle-settings'); ?>
	<input type="hidden" name="action" value="save_titlestyle_settings" />
	<h3>Which words in the titles should we change and how?</h3>
	<p>Title Style can automatically wrap certain words in HTML tags and attach a class name to them.</p>
	<table class="form-table">
		<tr>
			<th scope="row">Put the words inside this element:</th>
			<td>
				<fieldset>
					<select name="tag" id="tag">
						<option value="em"<?php echo get_option('titlestyle_html_tag') === 'em' ? ' selected="selected"':''; ?>>em</option>
						<option value="span"<?php echo get_option('titlestyle_html_tag') === 'span' ? ' selected="selected"':''; ?>>span</option>
						<option value="div"<?php echo get_option('titlestyle_html_tag') === 'div' ? ' selected="selected"':''; ?>>div</option>
					</select>
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row">Add this class name to the element:</th>			
			<td>
				<fieldset>
					<input type="text" name="classname" value="<?php echo get_option('titlestyle_classname'); ?>" />
				</fieldset>
			</td>
		</tr>
		<tr>
			<th scope="row">What kinds of words should we emphasize?</th>
			<td>
				<fieldset>
					<legend class="screen-reader-text"><span>What kinds of words should we emphasize?</span></legend>
					<input type="radio" name="word_type[]" id="lowercase" value="0"<?php echo get_option('titlestyle_word_type') == 0 ? ' checked="checked"':''; ?> /> <label for="lowercase">Emphasize <strong>every word</strong> that's in lowercase</label><br />
					<input type="radio" name="word_type[]" id="list" value="1"<?php echo get_option('titlestyle_word_type') == 1 ? ' checked="checked"':''; ?> /> <label for="list">Emphasize <strong>these words</strong>:</label> <input type="text" name="wordlist" value="<?php echo get_option('titlestyle_wordlist'); ?>" /><br />
					<span class="description">Example: <code>the, of, it, at</code> (words are case sensitive)</span>
				</fieldset>
			</td>
		</tr>
	</table>
	<p class="submit">
		<input name="save" type="submit" class="button-primary" value="Save Changes" />    
	</p>
</form>
<?php } # titlestyle_settings_page

function title_style_settings() {
	if ( function_exists('add_submenu_page') )
		add_submenu_page('plugins.php', __('Title Style'), __('Title Style'), 'manage_options', 'titlestyle', 'titlestyle_settings_page');
}
register_activation_hook( __FILE__, 'titlestyle_install');
add_action('admin_post_save_titlestyle_settings', 'on_save_changes');
add_action('admin_menu', 'title_style_settings');
add_action('the_title', 'title_style');
?>