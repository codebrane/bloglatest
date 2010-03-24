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
<?
echo elgg_echo('bloglatest:view:edit:username:text').":";
echo elgg_view('input/text', array('internalname' => 'params[username]', 
                                   'value' => $vars['entity']->username));
?>
</p>

<p>
<?
echo elgg_echo('bloglatest:view:edit:tags:text').":";
echo elgg_view('input/text', array('internalname' => 'params[tags]', 
                                   'value' => $vars['entity']->tags));
?>
</p>

<p>
<?
echo elgg_echo('bloglatest:view:edit:noofposts:text').":";
if (($vars['entity']->no_of_posts == nil) || ($vars['entity']->no_of_posts == "")) $vars['entity']->no_of_posts = MAX_LATEST_LIMIT;
echo elgg_view('input/text', array('internalname' => 'params[no_of_posts]', 
                                   'value' => $vars['entity']->no_of_posts));

?>
</p>

<p>
<?
echo elgg_echo('bloglatest:view:edit:excerptlength:text').":";
if (($vars['entity']->excerpt_length == nil) || ($vars['entity']->excerpt_length == "")) $vars['entity']->excerpt_length = MAX_EXCERPT_LENGTH;
echo elgg_view('input/text', array('internalname' => 'params[excerpt_length]', 
                                   'value' => $vars['entity']->excerpt_length));
?>
</p>