<?php

/**
 * @file
 * Here we override the default HTML output of drupal.
 *
 * Refer to https://drupal.org/node/457740.
 */

// Auto-rebuild the theme registry during theme development.
if (theme_get_setting('clear_registry')) {
	// Rebuild .info data.
	system_rebuild_theme_data();
	// Rebuild theme registry.
	drupal_theme_rebuild();
}

// Add Zen Tabs styles.
if (theme_get_setting('cvve_tabs')) {
	drupal_add_css(drupal_get_path('theme', 'cvve') . '/css/tabs.css');
}

/**
 * Implements hook_preprocess_html().
 */
function cvve_preprocess_html(&$variables) {
	global $user, $language;

	// Add role name classes (to allow css based show for admin/hidden from user).
	foreach ($user->roles as $role) {
		$variables['classes_array'][] = 'role-' . cvve_id_safe($role);
	}

	// HTML Attributes
	// Use a proper attributes array for the html attributes.
	$variables['html_attributes'] = array();
	$variables['html_attributes']['lang'][] = $language -> language;
	$variables['html_attributes']['dir'][] = $language -> dir;

	// Convert RDF Namespaces into structured data using drupal_attributes.
	$variables['rdf_namespaces'] = array();
	if (function_exists('rdf_get_namespaces')) {
		foreach (rdf_get_namespaces() as $prefix => $uri) {
			$prefixes[] = $prefix . ': ' . $uri;
		}
		$variables['rdf_namespaces']['prefix'] = implode(' ', $prefixes);
	}

	// Flatten the HTML attributes and RDF namespaces arrays.
	$variables['html_attributes'] = drupal_attributes($variables['html_attributes']);
	$variables['rdf_namespaces'] = drupal_attributes($variables['rdf_namespaces']);

	if (!$variables['is_front']) {
		// Add unique classes for each page and website section.
		$path = drupal_get_path_alias($_GET['q']);
		list($section, ) = explode('/', $path, 2);
		$variables['classes_array'][] = 'with-subnav';
		$variables['classes_array'][] = cvve_id_safe('page-' . $path);
		$variables['classes_array'][] = cvve_id_safe('section-' . $section);

		if (arg(0) == 'node') {
			if (arg(1) == 'add') {
				if ($section == 'node') {
					// Remove 'section-node'.
					array_pop($variables['classes_array']);
				}
				// Add 'section-node-add'.
				$variables['classes_array'][] = 'section-node-add';
			} elseif (is_numeric(arg(1)) && (arg(2) == 'edit' || arg(2) == 'delete')) {
				if ($section == 'node') {
					// Remove 'section-node'.
					array_pop($variables['classes_array']);
				}
				// Add 'section-node-edit' or 'section-node-delete'.
				$variables['classes_array'][] = 'section-node-' . arg(2);
			}
		}
	}
	// For normal un-themed edit pages.
	if ((arg(0) == 'node') && (arg(2) == 'edit')) {
		$variables['template_files'][] = 'page';
	}

	// Add IE classes.
	if (theme_get_setting('cvve_ie_enabled')) {
		$cvve_ie_enabled_versions = theme_get_setting('cvve_ie_enabled_versions');
		if (in_array('ie8', $cvve_ie_enabled_versions, TRUE)) {
			drupal_add_css(path_to_theme() . '/css/ie8.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 8', '!IE' => FALSE), 'preprocess' => FALSE, ));
			drupal_add_js(path_to_theme() . '/js/build/selectivizr-min.js');
		}
		if (in_array('ie9', $cvve_ie_enabled_versions, TRUE)) {
			drupal_add_css(path_to_theme() . '/css/ie9.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 9', '!IE' => FALSE), 'preprocess' => FALSE, ));
		}
	}
}

/**
 * Implements hook_preprocess_page().
 */
function cvve_preprocess_page(&$variables, $hook) {
	if (isset($variables['node_title'])) {
		$variables['title'] = $variables['node_title'];
	}
	// Adding classes whether #navigation is here or not.
	if (!empty($variables['main_menu']) or !empty($variables['sub_menu'])) {
		$variables['classes_array'][] = 'with-navigation';
	}
	if (!empty($variables['secondary_menu'])) {
		$variables['classes_array'][] = 'with-subnav';
	}

	// Add first/last classes to node listings about to be rendered.
	if (isset($variables['page']['content']['system_main']['nodes'])) {
		// All nids about to be loaded (without the #sorted attribute).
		$nids = element_children($variables['page']['content']['system_main']['nodes']);
		// Only add first/last classes if there is more than 1 node being rendered.
		if (count($nids) > 1) {
			$first_nid = reset($nids);
			$last_nid = end($nids);
			$first_node = $variables['page']['content']['system_main']['nodes'][$first_nid]['#node'];
			$first_node -> classes_array = array('first');
			$last_node = $variables['page']['content']['system_main']['nodes'][$last_nid]['#node'];
			$last_node -> classes_array = array('last');
		}
	}

	// Get children
	$parent = menu_link_get_preferred($_GET['q']);
	$parameters = array('active_trail' => array($parent['plid']), 'only_active_trail' => FALSE, 'min_depth' => $parent['depth'] + 1, 'max_depth' => $parent['depth'] + 1, 'conditions' => array('plid' => $parent['mlid']), );

	$variables['children'] = menu_build_tree($parent['menu_name'], $parameters);

	// Allow page override template suggestions based on node content type.
	if (isset($variables['node'] -> type) && isset($variables['node'] -> nid)) {
		$variables['theme_hook_suggestions'][] = 'page__' . $variables['node'] -> type;
		$variables['theme_hook_suggestions'][] = "page__node__" . $variables['node'] -> nid;
	}

	// If front-page get content types
	if (drupal_is_front_page()) {
		$query = new EntityFieldQuery;
		$types = array('article', 'cvve_wanted');
		foreach ($types as $type) {
			$query -> entityCondition('entity_type', 'node') -> entityCondition('bundle', $type) -> propertyCondition('status', 1) -> propertyOrderBy('created', 'DESC') -> range(0, 3);
			$temp = $query -> execute();
			if (isset($temp['node'])) {
				$result[$type]['nids'] = array_keys($temp['node']);
				$result[$type]['nodes'] = node_load_multiple($result[$type]['nids']);
			}
		}
		$variables['cvve_nodes'] = $result;

		$eventParameters = array();
		$eventParameters['return'] = array('id', 'event_start_date', 'title', 'summary', 'registration_start_date', 'registration_end_date', 'registration_link_text', 'is_online_registration', );
		$eventParameters['is_active'] = 1;
		$eventOptions['limit'] = 3;
		$eventOptions['offset'] = 0;
		$eventOptions['sort'] = 'event_start_date DESC';
		$eventOptions['cache'] = '30 min';
		$call = cmrf_views_sendCall('Event', 'get', $eventParameters, $eventOptions);
		$result = $call -> getReply();
		$events = array();
		foreach ($result['values'] as $value) {
			$object = json_decode(json_encode($value));
			$event = array();
			$event['date_time'] = new DateTime($object -> event_start_date);
			$event['title'] = $object -> title;
			$event['summary'] = $object -> summary;
			$event['registration_link'] = '';
			$event['classes'] = '';
			$event['link'] = '';

			$registerEnabled = true;
			$now = new DateTime();
			if ($now > $event['date_time']) {
				$event['classes'] .= ' past';
			}

			if (!$object->is_online_registration) {
				$registerEnabled = false;
			}
			if ($registerEnabled && $object->registration_start_date) {
				$registrationStartDate = new DateTime($object->registration_start_date);
				if ($now < $registrationStartDate) {
					$registerEnabled = false;
				}
			}
			if ($registerEnabled && $object->registration_end_date) {
				$registrationEndDate = new DateTime($object->registration_end_date);
				if ($now > $registrationEndDate) {
					$registerEnabled = false;
				}
			}
			if ($registerEnabled) {
				$event['link'] = 'https://crm.cvvede.nl/civicrm/event/info?reset=1&id=' . $object -> id;
				$event['registration_link'] = '<a style="display: inline; padding: 0px;" href="https://crm.cvvede.nl/civicrm/event/register?reset=1&id=' . $object -> id . '">' . $object -> registration_link_text . '</a>';
			}
			$events[] = $event;
		}
		$variables['events'] = $events;
	}
}

/**
 * Implements hook_preprocess_node().
 */
function cvve_preprocess_node(&$variables) {
	// Add a striping class.
	$variables['classes_array'][] = 'node-' . $variables['zebra'];

	// Add $unpublished variable.
	$variables['unpublished'] = (!$variables['status']) ? TRUE : FALSE;

	// Merge first/last class (from cvve_preprocess_page) into classes array of
	// current node object.
	$node = $variables['node'];
	if (!empty($node -> classes_array)) {
		$variables['classes_array'] = array_merge($variables['classes_array'], $node -> classes_array);
	}
}

/**
 * Implements hook_preprocess_block().
 */
function cvve_preprocess_block(&$variables) {
	// Add a zebra striping class.
	$variables['classes_array'][] = 'block-' . $variables['block_zebra'];

	// Add first/last block classes.
	// If block id (count) is 1, it's first in region.
	if ($variables['block_id'] == '1') {
		$variables['classes_array'][] = 'first';
	}
	// Count amount of blocks about to be rendered in the same region.
	$block_count = count(block_list($variables['elements']['#block'] -> region));
	if ($variables['block_id'] == $block_count) {
		$variables['classes_array'][] = 'last';
	}

	// Add simple classes.
	$variables['classes_array'][] = 'block';
}

/**
 * Implements theme_breadcrumb().
 */
function cvve_breadcrumb($variables) {
	$breadcrumb = $variables['breadcrumb'];
	// Determine if we are to display the breadcrumb.
	$show_breadcrumb = theme_get_setting('cvve_breadcrumb');

	if ($show_breadcrumb == 'yes' || $show_breadcrumb == 'admin' && arg(0) == 'admin') {

		// Optionally get rid of the homepage link.
		$show_breadcrumb_home = theme_get_setting('cvve_breadcrumb_home');
		if (!$show_breadcrumb_home) {
			array_shift($breadcrumb);
		}

		// Return the breadcrumb with separators.
		if (!empty($breadcrumb)) {
			$breadcrumb_separator = theme_get_setting('cvve_breadcrumb_separator');
			$trailing_separator = $title = '';
			if (theme_get_setting('cvve_breadcrumb_title')) {
				$item = menu_get_item();
				if (!empty($item['tab_parent'])) {
					// If we are on a non-default tab, use the tab's title.
					$title = check_plain($item['title']);
				} else {
					$title = drupal_get_title();
				}
				if ($title) {
					$trailing_separator = $breadcrumb_separator;
				}
			} elseif (theme_get_setting('cvve_breadcrumb_trailing')) {
				$trailing_separator = $breadcrumb_separator;
			}

			// Provide a navigational heading to give context for breadcrumb links to
			// screen-reader users. Make the heading invisible with
			// .element-invisible.
			$heading = '<h2 class="element-invisible">' . t('You are here') . '</h2>';

			return $heading . '<div class="breadcrumb">' . implode($breadcrumb_separator, $breadcrumb) . $trailing_separator . $title . '</div>';
		}
	}

	// Otherwise, return an empty string.
	return '';
}

/**
 * Converts a string to a suitable html ID attribute.
 *
 * Http://www.w3.org/TR/html4/struct/global.html#h-7.5.2 specifies what makes a
 * valid ID attribute in HTML. This function:
 *
 * - Ensure an ID starts with an alpha character by optionally adding an 'n'.
 * - Replaces any character except A-Z, numbers, and underscores with dashes.
 * - Converts entire string to lowercase.
 *
 * @param string $string
 *   The string.
 *
 * @return string
 *   The converted string.
 */
function cvve_id_safe($string) {
	// Replace with dashes anything that isn't A-Z, numbers, dashes, or
	// underscores.
	$string = strtolower(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $string));
	// If the first character is not a-z, add 'n' in front.
	// Don't use ctype_alpha since its locale aware.
	if (!ctype_lower($string{0})) {
		$string = 'id' . $string;
	}
	return $string;
}

/**
 * Implements theme_menu_link().
 */
function cvve_menu_link(array $variables) {
	$element = $variables['element'];
	$sub_menu = '';

	if ($element['#below']) {
		$sub_menu = drupal_render($element['#below']);
	}
	$output = l($element['#title'], $element['#href'], $element['#localized_options']);
	// Adding a class depending on the TITLE of the link (not constant)
	$element['#attributes']['class'][] = cvve_id_safe($element['#title']);
	// Adding a class depending on the ID of the link (constant)
	if (isset($element['#original_link']['mlid']) && !empty($element['#original_link']['mlid'])) {
		$element['#attributes']['class'][] = 'mid-' . $element['#original_link']['mlid'];
	}
	return '<li' . drupal_attributes($element['#attributes']) . '>' . $output . $sub_menu . "</li>\n";
}

function cvve_links($variables) {
	$links = $variables['links'];
	$attributes = $variables['attributes'];
	$heading = $variables['heading'];
	global $language_url;
	$output = '';

	if (count($links) > 0) {
		// Treat the heading first if it is present to prepend it to the
		// list of links.
		if (!empty($heading)) {
			if (is_string($heading)) {
				// Prepare the array that will be used when the passed heading
				// is a string.
				$heading = array('text' => $heading,
				// Set the default level of the heading.
				'level' => 'h2', );
			}
			$output .= '<' . $heading['level'];
			if (!empty($heading['class'])) {
				$output .= drupal_attributes(array('class' => $heading['class']));
			}
			$output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
		}

		$output .= '<ul' . drupal_attributes($attributes) . '>';

		$num_links = count($links);
		$i = 1;

		foreach ($links as $key => $link) {
			$class = array($key);

			// Add first, last and active classes to the list of links to help out
			// themers.
			if ($i == 1) {
				$class[] = 'first';
			}
			if ($i == $num_links) {
				$class[] = 'last';
			}
			if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page())) && (empty($link['language']) || $link['language'] -> language == $language_url -> language)) {
				$class[] = 'current';
			}
			$output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

			if (isset($link['href'])) {
				// Pass in $link as $options, they share the same keys.
				$output .= l($link['title'], $link['href'], $link);
			} elseif (!empty($link['title'])) {
				// Some links are actually not links, but we wrap these in <span> for
				// adding title and class attributes.
				if (empty($link['html'])) {
					$link['title'] = check_plain($link['title']);
				}
				$span_attributes = '';
				if (isset($link['attributes'])) {
					$span_attributes = drupal_attributes($link['attributes']);
				}
				$output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
			}

			$i++;
			$output .= "</li>\n";
		}

		$output .= '</ul>';
	}

	return $output;
}

/**
 * Implements hook_preprocess_menu_local_task().
 */
function cvve_preprocess_menu_local_task(&$variables) {
	$link = &$variables['element']['#link'];

	// If the link does not contain HTML already, check_plain() it now.
	// After we set 'html'=TRUE the link will not be sanitized by l().
	if (empty($link['localized_options']['html'])) {
		$link['title'] = check_plain($link['title']);
	}
	$link['localized_options']['html'] = TRUE;
	$link['title'] = '<span class="tab ' . drupal_html_class('task-' . $link['title']) . '">' . $link['title'] . '</span>';
}

/**
 * Implements theme_menu_local_tasks().
 *
 * Duplicate of theme_menu_local_tasks() but adds clearfix to tabs.
 */
function cvve_menu_local_tasks(&$variables) {
	$output = '';

	if (!empty($variables['primary'])) {
		$variables['primary']['#prefix'] = '<h2 class="element-invisible">' . t('Primary tabs') . '</h2>';
		$variables['primary']['#prefix'] .= '<ul class="tabs primary clearfix">';
		$variables['primary']['#suffix'] = '</ul>';
		$output .= drupal_render($variables['primary']);
	}
	if (!empty($variables['secondary'])) {
		$variables['secondary']['#prefix'] = '<h2 class="element-invisible">' . t('Secondary tabs') . '</h2>';
		$variables['secondary']['#prefix'] .= '<ul class="tabs secondary clearfix">';
		$variables['secondary']['#suffix'] = '</ul>';
		$output .= drupal_render($variables['secondary']);
	}

	return $output;
}
