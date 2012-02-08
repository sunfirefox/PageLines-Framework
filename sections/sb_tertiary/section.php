<?php
/*
	Section: Tertiary Sidebar
	Author: PageLines
	Author URI: http://www.pagelines.com
	Description: A 3rd widgetized sidebar for the theme that can be used in standard sidebar templates.
	Class Name: TertiarySidebar
	Workswith: sidebar1, sidebar2, sidebar_wrap
*/

/**
 * Tertiary Sidebar Section
 *
 * @package PageLines Framework
 * @author PageLines
 **/
class TertiarySidebar extends PageLinesSection {

   function section_persistent() { 
		$setup = pagelines_standard_sidebar($this->name, $this->settings['description']);
		pagelines_register_sidebar($setup, 3);
	}

   function section_template() { 
	 	 pagelines_draw_sidebar($this->id, $this->name);
	}
}