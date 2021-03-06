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
define(POST_TYPE_ALL, "POST_TYPE_ALL");
define(POST_TYPE_BLOG, "POST_TYPE_BLOG");
define(POST_TYPE_WIRE, "POST_TYPE_WIRE");

define(RESTRICT_TO_FRIENDS, "RESTRICT_TO_FRIENDS");

/**
 * Gets all the latest posts of all the supported subtypes
 * @param current_user The current user object who is using the plugin
 * @param username The username to filter on. If this is empty, it means get posts for all users
 * @param tags The tags to filter on. If this is empty, no tag filter will occur
 * @param no_of_posts The limit on how many posts to grab, independent of filtering
 * @param post_type One of the defined POST_TYPE_*
 * @param restrict_to_friends If this is set to RESTRICT_TO_FRIENDS only friends wire/posts will be shown
 * @return Returns an array of the form:
 *         USER_FULL_NAME,USER_LOGIN_ID,SMALL_ICON_URL,COUNT => post object
 *         the COUNT value can be ignored
 */
function get_posts($current_user, $username, $tags, $no_of_posts = MAX_LATEST_LIMIT, $post_type = POST_TYPE_ALL, $restrict_to_friends) {
	if ($post_type == "") {
		$post_type = POST_TYPE_ALL;
	}
	
	if ($post_type == POST_TYPE_ALL) {
		$blogs = get_objects(POST_TYPE_BLOG, $no_of_posts);
		$wires = get_objects(POST_TYPE_WIRE, $no_of_posts);

		$noBlogs = false;
		if ((count($blogs) == 1) && ($blogs[0]->description == "")) $noBlogs = true;
		
		$noWires = false;
		if ((count($wires) == 1) && ($wires[0]->description == "")) $noWires = true;
		
		if ($noWires || $noBlogs) {
			if ($noWires) $posts = $blogs;
			else if ($noBlogs) $posts = $wires;
		}
		else {
			$posts = array_merge($blogs, $wires);
		}
	}
	if ($post_type == POST_TYPE_BLOG) {
		$posts = get_objects(POST_TYPE_BLOG, $no_of_posts);
	}
	if ($post_type == POST_TYPE_WIRE) {
		$posts = get_objects(POST_TYPE_WIRE, $no_of_posts);
	}
	
	return filter_posts($current_user, $posts, $username, $tags, $restrict_to_friends);
}

/**
 * Does the work of getting the posts from Elgg
 * @param object_type POST_TYPE_BLOG or POST_TYPE_WIRE
 * @return An array of post objects
 */
function get_objects($object_type, $no_of_posts) {
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
 * Filters posts on either username, tags, friends or all three
 * @param current_user The current user object who is using the plugin
 * @param posts The posts to filter
 * @param username The login ID of the user to filter on, or empty if no user filtering required
 * @param tags The tags to filter on, or empty if no tag filtering required
 * @param restrict_to_friends If this is set to RESTRICT_TO_FRIENDS only friends wire/posts will be shown
 */
function filter_posts($current_user, $posts, $username, $tags, $restrict_to_friends) {
	$count = 0;
	if ($tags != "") {
		if (stristr($tags, ",") === FALSE) $tags .= ",";
	}
	
	$subtype_blog = get_subtype(POST_TYPE_BLOG);
	$subtype_wire = get_subtype(POST_TYPE_WIRE);
	
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
		if ($post->subtype == $subtype_blog) {
			$the_blog->post_type = POST_TYPE_BLOG;
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
		else if ($post->subtype == $subtype_wire) {
			$the_blog->post_type = POST_TYPE_WIRE;
		}
		
		// Check if only friends
		if ($restrict_to_friends == RESTRICT_TO_FRIENDS) {
			$ok_to_add = is_friend($current_user, $user);
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
 * @return Displaying text for the top bar of the widget
 */
function get_widget_top_text($username) {
	global $CONFIG;
	if ($username != "") {
		$user = get_user_by_username($username);
		
		$topline =  "<div class=\"thewire_icon\">";
		$topline .= elgg_view("profile/icon", array('entity' => $user, 'size' => 'small'));
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

/**
 * Gets the numeric subtype from the database for a particular entity
 * @param post_type POST_TYPE_BLOG or POST_TYPE_WIRE
 * @return the numeric subtype of the entity as defined in the database
 */
function get_subtype($post_type) {
	global $CONFIG;
	
	if ($post_type == POST_TYPE_BLOG) {
		$result = get_data_row("SELECT * from {$CONFIG->dbprefix}entity_subtypes where subtype='blog'");
	}
	
	if ($post_type == POST_TYPE_WIRE) {
		$result = get_data_row("SELECT * from {$CONFIG->dbprefix}entity_subtypes where subtype='thewire'");
	}
	
	return $result->id;
}

/**
 * Determines whether other_user is a friend of current_user
 * @param current_user The current user object who is using the plugin
 * @param other_user The user who might be a friend of current_user
 * @return true if other_user is a friend of current_user otherwise false
 */
function is_friend($current_user, $other_user) {
	$friends = $current_user->getFriends("", 100, $offset = 0);
	
	if (is_array($friends) && sizeof($friends) > 0) {
		foreach($friends as $friend) {
			if ($friend->guid == $other_user->guid) return true;
		}
	}
	
	return false;
}
?>