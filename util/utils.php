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

define(MAX_LATEST_LIMIT, '10');
define(MAX_EXCERPT_LENGTH, '140');

function get_blog_posts($username, $tags, $no_of_posts = MAX_LATEST_LIMIT) {
	$options = array('offset' => 0,
									 'limit' => $no_of_posts,
									 'type' => 'object',
									 'subtype' => 'blog');
	
	$the_blogs = array();
	
	$blogs = elgg_get_entities($options);
	
	$count = 0;
	if ($tags != "") {
		if (stristr($tags, ",") === FALSE) $tags .= ",";
	}
	
	foreach ($blogs as $blog) {
		$the_blog = new ElggObject($blog->guid);
		$user = get_blog_owner($the_blog->owner_guid);
		
		$ok_to_add = true;
		
		// Filter on username
		if ($username != "") {
			if ($username != $user->username) {
				$ok_to_add = false;
			}
		}
		
		// Filter on tags
		if (($ok_to_add) && ($tags != "")) {
			$ok_to_add = false;
			$tags_parts = explode(",", $tags);
			foreach($tags_parts as $tag) {
				if (!is_array($blog->tags)) {
					$blogtags = array($blog->tags);
				}
				else {
					$blogtags = $blog->tags;
				}
				
				foreach($blogtags as $blogtag) {
					if (($tag != "") && ($blogtag != "")) {
						if ($tag == $blogtag) {
							$ok_to_add = true;
						}
					}
				}
			}
		}
		
		if ($ok_to_add) {
			$the_blogs[$user->name.",".$user->username.",".$user->getIcon("small").",".$count] = $blog;
		}
		
		$count++;
	}
	
	return $the_blogs;
}

function get_blog_owner($blog_guid) {
	return get_entity($blog_guid);
}

function get_widget_top_text($username) {
	if ($username != "") {
		$user = get_user_by_username($username);
		
		$topline = "<a href=\"{$CONFIG->wwwroot}pg/profile/{$user->username}\">";
		$topline .= "<img src=\"{$user->getIcon("small")}\" title=\"{$user->name}\" alt=\"{$user->name}\"/>";
		$topline .= "&nbsp;&nbsp;{$user->name}</a><br />";
		
		return $topline;
	}
	
	return elgg_echo('bloglatest:view:allusers:text');
}

function get_excerpt($post) {
	return substr($post, 0, MAX_EXCERPT_LENGTH);
}
?>