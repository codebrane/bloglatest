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

// If the edit boxes are empty, these are the defaults
define(MAX_LATEST_LIMIT, '10');
define(MAX_EXCERPT_LENGTH, '140');

// No need to edit anything after this
define(SUBTYPE_BLOG, 4);
define(SUBTYPE_WIRE, 6);
define(POST_TYPE_ALL, "POST_TYPE_ALL");
define(POST_TYPE_BLOG, "POST_TYPE_BLOG");
define(POST_TYPE_WIRE, "POST_TYPE_WIRE");

/**
 * Gets all the latest posts of all the supported subtypes
 * @param username The username to filter on. If this is empty, it means get posts for all users
 * @param tags The tags to filter on. If this is empty, no tag filter will occur
 * @param no_of_posts The limit on how many posts to grab, independent of filtering
 * @param post_type One of the defined POST_TYPE_*
 * @return Returns an array of the form:
 *         USER_FULL_NAME,USER_LOGIN_ID,SMALL_ICON_URL,COUNT => post object
 *         the COUNT value can be ignored
 */
function get_posts($username, $tags, $no_of_posts = MAX_LATEST_LIMIT, $post_type) {
	if ($post_type == POST_TYPE_ALL) {
		$posts = array_merge(get_objects(POST_TYPE_BLOG), get_objects(POST_TYPE_WIRE));
	}
	if ($post_type == POST_TYPE_BLOG) {
		$posts = get_objects(POST_TYPE_BLOG);
	}
	if ($post_type == POST_TYPE_WIRE) {
		$posts = get_objects(POST_TYPE_WIRE);
	}
	
	return filter_posts($posts, $username, $tags);
}

/**
 * Does the work of getting the posts from Elgg
 * @param object_type POST_TYPE_BLOG or POST_TYPE_WIRE
 * @return An array of post objects
 */
function get_objects($object_type) {
	if ($object_type == POST_TYPE_BLOG) {
		$options = array('offset' => 0,
										 'limit' => $no_of_posts,
										 'type' => 'object',
										 'subtype' => 'blog');
	}
	if ($object_type == POST_TYPE_WIRE) {
		$options = array('offset' => 0,
										 'limit' => $no_of_posts,
										 'type' => 'object',
										 'subtype' => 'thewire');
	}
	
	return elgg_get_entities($options);
}

/**
 * Filters posts on either username, tags, or both
 * @param posts The posts to filter
 * @param username The login ID of the user to filter on, or empty if no user filtering required
 * @param tags The tags to filter on, or empty if no tag filtering required
 */
function filter_posts($posts, $username, $tags) {
	$count = 0;
	if ($tags != "") {
		if (stristr($tags, ",") === FALSE) $tags .= ",";
	}
	
	foreach ($posts as $post) {
		$the_blog = new ElggObject($post->guid);
		$user = get_post_owner($the_blog->owner_guid);
		
		$ok_to_add = true;
		
		// Filter on username
		if ($username != "") {
			if ($username != $user->username) {
				$ok_to_add = false;
			}
		}
		
		// Filter on tags. Unless it's a wire post as it has no tags
		if ($post->subtype != SUBTYPE_WIRE) {
			if (($ok_to_add) && ($tags != "")) {
				$ok_to_add = false;
				$tags_parts = explode(",", $tags);
				foreach($tags_parts as $tag) {
					if (!is_array($post->tags)) {
						$blogtags = array($post->tags);
					}
					else {
						$blogtags = $post->tags;
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
		}
		
		if ($ok_to_add) {
			$the_blogs[$user->name.",".$user->username.",".$user->getIcon("small").",".$count] = $post;
		}
		
		$count++;
	}
	
	return $the_blogs;
}

/**
 * Gets the owner of a post
 * @param the guid of the post
 * @return User object representing the post owner
 */
function get_post_owner($blog_guid) {
	return get_entity($blog_guid);
}

/**
 * Generates the text to display at the top of the widget.
 * If the current widget is filtering on a user then the top text
 * will include that user's icon. Otherwise it will just be text
 * and each user's icon will appear next to their post in the list.
 * @param username The full name of the user or empty if the widget is for all users
 * @param userid The login ID of the user or empty if the widget is for all users
 * @param entity_owner The object representing the filtered user
 * @return Displaying text for the top bar of the widget
 */
function get_widget_top_text($username, $userid, $entity_owner) {
	global $CONFIG;
	if ($username != "") {
		$user = get_user_by_username($username);
		
		$topline =  "<div class=\"thewire_icon\">";
		$topline .= elgg_view("profile/icon", array('entity' => $entity_owner, 'size' => 'small'));
		$topline .= "</div>";
		$topline .= "<p><a href=\"{$CONFIG->wwwroot}pg/profile/{$user->username}\">{$user->name}</a></p>";
		
		return $topline;
	}
	
	return elgg_echo('bloglatest:view:allusers:text');
}

/**
 * Extracts an excerpt from the post
 * @param post The post
 * @param excerpt_length The length of the excerpt. Defaults to MAX_EXCERPT_LENGTH if not specified
 * @return Excerpt from the post
 */
function get_excerpt($post, $excerpt_length = MAX_EXCERPT_LENGTH) {
	$excerpt = substr($post, 0, $excerpt_length);
	if ($excerpt_length < MAX_EXCERPT_LENGTH) $excerpt .= " ...";
	return $excerpt;
}
?>