<?php defined('_JEXEC') or die;

/**
 * File       feedcontrol.php
 * Created    6/12/13 11:18 AM
 * Author     Matt Thomas | matt@betweenbrain.com | http://betweenbrain.com
 * Support    https://github.com/betweenbrain/
 * Copyright  Copyright (C) 2013 betweenbrain llc. All Rights Reserved.
 * License    GNU GPL v3 or later
 */

// Import library dependencies
jimport('joomla.plugin.plugin');

class plgSystemFeedcontrol extends JPlugin {

	function onAfterRoute() {

		if ($this->checkContext() == TRUE) {

			$app = JFactory::getApplication();

			if (JRequest::getVar('format') == "feed") {
				$app->redirect(juri::current());
			}
		}
	}

	function onAfterRender() {

		if ($this->checkContext() === TRUE) {

			$buffer = JResponse::getBody();
			preg_match_all('/<link[^>]*>/', $buffer, $matches);

			foreach ($matches[0] as $match) {
				if (strpos($match, 'format=feed')) {
					$buffer = str_replace($match, '', $buffer);
				}
			}

			// Clean messy output
			$buffer = preg_replace('/<\/title>\s{2,}<link/', '</title>' . "\n" . '  <link', $buffer);

			JResponse::setBody($buffer);
		}
	}

	function checkContext() {

		$app = JFactory::getApplication();

		if (!$app->isAdmin()) {
			$item       = $app->getMenu()->getActive()->id;
			$exclusions = $this->params->get('exclusions');

			if (!is_array($exclusions)) {
				$exclusions = explode(' ', $exclusions);
			}

			if (!in_array($item, $exclusions)) {
				return TRUE;
			}
		}

		return FALSE;
	}
}