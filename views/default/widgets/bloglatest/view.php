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
	 
	global $CONFIG;
	require_once($CONFIG->pluginspath . "bloglatest/util/utils.php");
?>

<div class="contentWrapper">
	<? echo get_widget_top_text($vars['entity']->username); ?>
</div>

<?php
$posts = get_posts($_SESSION['user'], $vars['entity']->username, $vars['entity']->tags, $vars['entity']->no_of_posts, $vars['entity']->posttype, $vars['entity']->restricttofriends);

foreach ($posts as $username_and_icon => $post) {
	$user_parts = explode(",", $username_and_icon);
	$description = get_excerpt($post->description, $vars['entity']->excerpt_length);
	?>
	
	<?php if ($post->post_type == POST_TYPE_BLOG) { ?>
	<div class="contentWrapper">
	<?php
	echo "<div class=\"thewire_icon\">";
	echo elgg_view("profile/icon", array('entity' => get_post_owner($post->owner_guid), 'size' => 'small'));
	echo "</div>";	
	echo "<a href=\"{$CONFIG->wwwroot}pg/profile/{$user_parts[1]}\">{$user_parts[0]}</a><br />";
	echo "<a href=\"{$CONFIG->wwwroot}/pg/blog/admin/read/{$post->guid}\">{$post->title}</a><br />{$description}";
	?>
	</div>
	<?php } ?>
	
	<?php
		/* The wire view code was taken from mod/thewire/views/default/object/thewire.php
		 * and modified to display the correct objects.
		 */
	?>
	
	<?php if ($post->post_type == POST_TYPE_WIRE) { ?>
		<div class="thewire-singlepage">
			<div class="thewire-post">

			    <!-- the actual shout -->
				<div class="note_body">

			    <div class="thewire_icon">
			    <?php
				        echo elgg_view("profile/icon", array('entity' => get_post_owner($post->owner_guid), 'size' => 'small'));
			    ?>
			    </div>

					<div class="thewire_options">

					<a href="<?php echo $vars['url']; ?>mod/thewire/add.php?wire_username=<?php echo $vars['entity']->getOwnerEntity()->username; ?>" class="reply"><?php echo elgg_echo('thewire:reply'); ?></a>
			    <div class="clearfloat"></div>
			    		<?php

				?>
			    </div>


				<?php
			    echo "<b>{$user_parts[0]}: </b><br />";
					$desc = get_excerpt($post->description, $vars['entity']->excerpt_length);
			    $desc = preg_replace('/\@([A-Za-z0-9\_\.\-]*)/i','@<a href="' . $vars['url'] . 'pg/thewire/$1">$1</a>',$desc);
					echo parse_urls($desc);
				?>


				<div class="clearfloat"></div>
				</div>
				<div class="note_date">

				<?php

						echo elgg_echo("thewire:wired") . " " . sprintf(elgg_echo("thewire:strapline"),
										friendly_time($vars['entity']->time_created)
						);

						echo " via " . elgg_echo($vars['entity']->method) . ".";

				?>
				</div>


			</div>
		</div>
	<?php } ?>
	
	
	<?php
}
?>

