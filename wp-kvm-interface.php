<?php
/*
Plugin Name: WP Karte von Morgen Interface
Plugin URI: https://github.com/kartevonmorgen/
Description: Easily transport events from your WordPress event calendar to the Karte von Morgen. 
Version: 0.1
Author: Sjoerd Takken
Author URI: https://www.sjoerdscomputerwelten.de/
License: GPL2

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

$loaderClass = WP_PLUGIN_DIR . '/wp-libraries/inc/lib/plugin/class-wp-pluginloader.php';
if(!file_exists($loaderClass))
{
  echo "Das Plugin 'wp-libraries' muss erst installiert und aktiviert werden";
  exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( $loaderClass);

const JSON_PRETTY_PRINT = true;

class WPKVMInterfacePluginLoader extends WPPluginLoader
{
  public function init()
  {
    $this->add_dependency('wp-events-interface/wp-events-interface.php');

// -- OpenFairDB API --
    $this->add_include("/inc/lib/openfairdb/api/AbstractOpenFairDBApi.php");
    $this->add_include("/inc/lib/openfairdb/api/OpenFairDBEventsApi.php");
    $this->add_include("/inc/lib/openfairdb/api/OpenFairDBEntriesApi.php");
    $this->add_include("/inc/lib/openfairdb/OpenFairDBApiException.php");
    $this->add_include("/inc/lib/openfairdb/OpenFairDBConfiguration.php");


// -- Controllers --
    $this->add_include("/inc/controllers/class-kvm-interface-admincontrol.php" );
    $this->add_include("/inc/controllers/class-kvm-interface-handleevents.php" );
    $this->add_include("/inc/controllers/class-kvm-interface-handleentries.php" );
    $this->add_include("/inc/controllers/class-kvm-interface.php" );
  }

  public function start()
  {
    $eiInterface = EIInterface::get_instance();
    $eiInterface->register_for_kartevonmorgen();

    $this->add_starter(KVMInterface::get_instance());
    $this->add_starter(new KVMInterfaceAdminControl());
  }
}

$loader = new WPKVMInterfacePluginLoader();
$loader->register( __FILE__ , 30);
