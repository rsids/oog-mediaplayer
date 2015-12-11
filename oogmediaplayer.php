<?php

/***************************************************************************

Plugin Name:  Oog Media Player
Plugin URI:   http://www.oogtv.nl
Description:  Native HTML player for audio and video
Version:      1.0
Author:       Ids Klijnsma
Author URI:   http://www.idsklijnsma.nl/

 **************************************************************************

Copyright (C) 2015 Ids Klijnsma

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

 **************************************************************************/
if(!function_exists('oog_autoloader')) {

    function oog_autoloader($classname) {
        if(strpos($classname, 'oog\\') === 0) {
            $packages = explode('\\', $classname);
            $pluginname = str_replace('-', '',$packages[1]);

            $path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . 'oog' . $pluginname . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $packages) . '.php';
            if(file_exists($path)) {
                include_once $path;
                return true;
            }
        }
        return false;
    }
	spl_autoload_register('oog_autoloader');
}

define('OOG_MEDIAPLAYER_PLUGIN_FILE', __FILE__);
define('OOG_MEDIAPLAYER_PLUGIN_DIR', __DIR__);

new \oog\mediaplayer\Player();