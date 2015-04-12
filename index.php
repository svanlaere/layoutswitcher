<?php
if (!defined('IN_CMS')) {
    exit();
}

Plugin::setInfos(array(
    'id' => 'layoutswitcher',
    'title' => 'Layout switcher',
    'description' => 'Allow visitors to switch between layouts.',
    'version' => '0.1.0',
    'license'               => 'GPLv3',
    'author'                => 'svanlaere',
    'website' => 'http://svanlaere.nl/',
    'update_url' => 'http://svanlaere.nl/plugin-versions.xml',
    'require_wolf_version' => '0.7.0'
));

Observer::observe('page_found', 'ls_output');

function ls_output($page)
{
    if (!isset($_SESSION)) {
        session_start();
    }
    $layout = $page->layout_id;
    $object = Layout::findAll();
    
    if (isset($_POST['layout'])) {
        $layout = $_POST['layout'];
        foreach ($object as $item) {
            $layouts = (array) $item->id;
            if (in_array($layout, $layouts)) {
                $_SESSION['layout'] = $layout;
            }
        }
    } elseif (!isset($_SESSION['layout'])) {
        $_SESSION['layout'] = $layout;
        $layout             = $_SESSION['layout'];
    } else {
        $layout = $_SESSION['layout'];
    }
    $page->layout_id = $layout;
}

function ls_dropdown()
{
    $url = $_SERVER['REQUEST_URI'];
    echo '<form action="' . $url . '" method="post">' . PHP_EOL;
    echo '<p>' . PHP_EOL;
    echo '<label class="layout" for="layout">Layouts</label>' . PHP_EOL;
    echo '<select name="layout" id="layout" onchange="this.form.submit();">' . PHP_EOL;
    $layouts        = Layout::findAll();
    $current_layout = $_SESSION['layout'];
	asort($layouts);
    foreach ($layouts as $layout) {
        $selected = ($current_layout == $layout->id) ? ' selected="selected"' : '';
        if ($layout->content_type == 'text/html') {
            echo '<option value="' . $layout->id . '"' . $selected . '>' . $layout->name . '</option>' . PHP_EOL;
        }
    }
    echo '</select>' . PHP_EOL;
    echo '</p>' . PHP_EOL;
    echo '<noscript>' . PHP_EOL;
    echo '<p><input type="submit" value="Switch Layout" /></p>' . PHP_EOL;
    echo '</noscript>' . PHP_EOL;
    echo '</form>' . PHP_EOL;
}