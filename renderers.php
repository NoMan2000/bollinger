<?php

class theme_bollinger_core_renderer extends core_renderer {
    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link block_contents} object.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    function block($bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }

        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }
        $attrs = $classes = '';
        foreach($bc->attributes as $name => $value) {
            if('class' != $name) {
                $attrs .= html_writer::attribute($name, $value);
            } else {
                $classes = $value . ' ';
            }
        }
        $controlshtml = $this->block_controls($bc->controls);
        $title = $hide_title = '';
        if ($bc->title) {
            $title = $bc->title;
            $hide_title = '<h2 style="display:none;">' . $bc->title . '</h2>';
        }
        $actionhtml = html_writer::tag('div', '', array('class'=>'block_action', 'style'=>'float:right;z-index:1000;'));
        $output .= <<<EOL
	<div class="moodle-block clearfix contextual-links-region {$classes}" {$attrs}>
EOL;
        $output .= $this->block_header($bc);

        $output .=  <<<EOL
	<div class="moodle-blockcontent content">
EOL;
        if (!$title && !$controlshtml) {
            $output .= html_writer::tag('div', '', array('class'=>'block_action notitle'));
        }
        $output .= $bc->content;

        if ($bc->footer) {
            $output .= html_writer::tag('div', $bc->footer, array('class' => 'footer'));
        }
        $output .= <<<EOL
	</div>
EOL;
        $output .= <<<EOL
	</div>
EOL;

        if ($bc->annotation) {
            $output .= html_writer::tag('div', $bc->annotation, array('class' => 'blockannotation'));
        }
        $output .= $skipdest;

        $this->init_block_hider_js($bc);

        return $output;
    }
    /**
     * Produces a header for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_header(block_contents $bc) {

        $title = '';
        if ($bc->title) {
            $title = html_writer::tag('h2', $bc->title, array('class'=>'t'));
        }

        $controlshtml = $this->block_controls($bc->controls);

        $actionhtml = html_writer::tag('div', '', array('class'=>'block_action', 'style'=>'float:right;z-index:1000;'));

		
        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', html_writer::tag('div', $actionhtml. $title . $controlshtml, array('class' => 'title')), array('class' => 'moodle-blockheader title'));
        }
        return $output;
    }	

    /**
     * Returns the custom menu if one has been set
     *
     * A custom menu can be configured by browsing to
     *    Settings: Administration > Appearance > Themes > Theme settings
     * and then configuring the custommenu config setting as described.
     *
     * @return string
     */
    public function custom_menu() {
        global $CFG;
        if (empty($CFG->custommenuitems)) {
            return '';
        }
        $custommenu = new custom_menu($CFG->custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }
    
    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @staticvar int $menucount
     * @param custom_menu $menu
     * @return string
     */
    protected function render_custom_menu(custom_menu $menu) {
        static $menucount = 0;
        // If the menu has no children return an empty string
        if (!$menu->has_children()) {
            return '';
        }
        // Increment the menu count. This is used for ID's that get worked with
        // in JavaScript as is essential
        $menucount++;
        // Initialise this custom menu
        $this->page->requires->js_init_call('M.core_custom_menu.init', array('custom_menu_'.$menucount));
        // Build the root nodes as required by YUI
        ///$content = html_writer::start_tag('div', array('id'=>'custom_menu_'.$menucount, 'class'=>'yui3-menu yui3-menu-horizontal javascript-disabled'));
        //$content .= html_writer::start_tag('div', array('class'=>'yui3-menu-content'));
        $content .= html_writer::start_tag('ul', array('class'=>'moodle-hmenu') );
        // Render each child
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item);
        }
        // Close the open tags
        $content .= html_writer::end_tag('ul');
        //$content .= html_writer::end_tag('div');
        //$content .= html_writer::end_tag('div');
        // Return the custom menu
        return $content;
    }

    /**
     * Renders a custom menu node as part of a submenu
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @see render_custom_menu()
     *
     * @staticvar int $submenucount
     * @param custom_menu_item $menunode
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode) {
        // Required to ensure we get unique trackable id's
        static $submenucount = 0;
        if ($menunode->has_children()) {
            // If the child has menus render it as a sub menu
            $submenucount++;
            $content = html_writer::start_tag('li');
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('class'=>'yui3-menu-label', 'title'=>$menunode->get_title()));
            $content .= html_writer::start_tag('ul');
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode);
            }
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::end_tag('li');
        } else {
            // The node doesn't have children so produce a final menuitem
            $content = html_writer::start_tag('li', array('class'=>'yui3-menuitem'));
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('class'=>'yui3-menuitem-content', 'title'=>$menunode->get_title()));
            $content .= html_writer::end_tag('li');
        }
        // Return the sub menu
        return $content;
    }

    /**
     * Outputs a heading
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading. Defaulting to 2
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function heading($text, $level = 2, $classes = 'main', $id = null) {
        $level = (integer) $level;
        $id = empty($id) ? '' : ' id="' . $id . '"';
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }
        $html = <<<EOD
        
                                <div class="moodle-postmetadataheader">
                                        <h{$level} class="moodle-postheader {$classes}" {$id}><span class="moodle-postheadericon">{$text}</span></h{$level}>
                                                            
                                    </div>
                                
                                
                
EOD;
        return $html;
    }
}