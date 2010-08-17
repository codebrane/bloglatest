<?php

/**
 * Latest blog entries
 * 
 * @package BlogLatest
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
 * @author Alistair Young <alistair@codebrane.com>
 * @copyright codeBrane 2010
 * @link http://codebrane.com/blog/
 */

require_once($CONFIG->pluginspath . "bloglatest/util/utils.php");
?>
<p>
<?php
/*
 * Post type. Blog, Wire, etc...
 */
echo elgg_echo('bloglatest:view:edit:posttype:text').":";
?>
<select name="params[posttype]">
	<option value="<?php echo POST_TYPE_ALL; ?>" <?php if($vars['entity']->posttype == POST_TYPE_ALL) echo "SELECTED"; ?>><?php echo elgg_echo('bloglatest:view:edit:posttype:all:text') ?></option>
	<option value="<?php echo POST_TYPE_BLOG; ?>" <?php if($vars['entity']->posttype == POST_TYPE_BLOG) echo "SELECTED"; ?>><?php echo elgg_echo('bloglatest:view:edit:posttype:blog:text') ?></option>
	<option value="<?php echo POST_TYPE_WIRE; ?>" <?php if($vars['entity']->posttype == POST_TYPE_WIRE) echo "SELECTED"; ?>><?php echo elgg_echo('bloglatest:view:edit:posttype:wire:text') ?></option>
</select>
</p>

<p>
	<?php
	/*
	 * Restrict to friends
	 * http://trac.elgg.org/ticket/397
	 */
	echo elgg_echo('bloglatest:view:edit:restricttofriends').":"
	?>
	<input type="hidden" name="params[restricttofriends]" value="0" />
	<input type="checkbox" name="params[restricttofriends]"  value="<?php echo RESTRICT_TO_FRIENDS; ?>" <?php if ($vars['entity']->restricttofriends) echo "checked=\"checked\""; ?> />
</p>

<p>
<?php
/*
 * Filter on username
 */
echo elgg_echo('bloglatest:view:edit:username:text').":";
echo elgg_view('input/text', array('internalname' => 'params[username]', 
                                   'value' => $vars['entity']->username));
?>
</p>

<p>
<?php
/*
 * Filter on tags
 */
echo elgg_echo('bloglatest:view:edit:tags:text').":";
echo elgg_view('input/text', array('internalname' => 'params[tags]', 
                                   'value' => $vars['entity']->tags));
?>
</p>

<p>
<?php
/*
 * Number of posts to show
 */
echo elgg_echo('bloglatest:view:edit:noofposts:text').":";
if (($vars['entity']->no_of_posts == nil) || ($vars['entity']->no_of_posts == "")) $vars['entity']->no_of_posts = MAX_LATEST_LIMIT;
echo elgg_view('input/text', array('internalname' => 'params[no_of_posts]', 
                                   'value' => $vars['entity']->no_of_posts));

?>
</p>

<p>
<?php
/*
 * Length of the excerpt of each post to display
 */
echo elgg_echo('bloglatest:view:edit:excerptlength:text').":";
if (($vars['entity']->excerpt_length == nil) || ($vars['entity']->excerpt_length == "")) $vars['entity']->excerpt_length = MAX_EXCERPT_LENGTH;
echo elgg_view('input/text', array('internalname' => 'params[excerpt_length]', 
                                   'value' => $vars['entity']->excerpt_length));
?>
</p>