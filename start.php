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

function bloglatest_init() {
	global $CONFIG;
	add_widget_type('bloglatest',
								  elgg_echo('bloglatest:start:widget:name'),
								  elgg_echo('bloglatest:start:widget:name'));
}

register_elgg_event_handler('init', 'system', 'bloglatest_init');

?>
