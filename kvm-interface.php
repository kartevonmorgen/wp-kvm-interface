<?php
/*
Plugin Name: Karte von Morgen Interface
Plugin URI: https://github.com/kartevonmorgen/
Description: Easily transport events from your WordPress event calendar to ESS. 
Version: 0.1
Author: Sjoerd Takken
Author URI: https://www.sjoerdscomputerwelten.de/
Text Domain: kvm-interface
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

const JSON_PRETTY_PRINT = true;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( !is_plugin_active( 'events-interface/events-interface.php' ) ) 
{
	// Plugin is not active
  // TODO: See https://waclawjacek.com/check-wordpress-plugin-dependencies/
  echo 'The plugin Events Interface must be activated';
  die();
}

if ( ! function_exists( 'kvm_load_textdomain' ) ) {
    /**
     * Load in any language files that we have setup
     */
    function kvm_load_textdomain() {
        load_plugin_textdomain( 'kvm-interface', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
    }
    add_action( 'plugins_loaded', 'kvm_load_textdomain' );
}


// -- OpenFairDB API --
require_once( dirname( __FILE__ )."/inc/lib/openfairdb/api/AbstractOpenFairDBApi.php");
require_once( dirname( __FILE__ )."/inc/lib/openfairdb/api/OpenFairDBEventsApi.php");
require_once( dirname( __FILE__ )."/inc/lib/openfairdb/api/OpenFairDBEntriesApi.php");
require_once( dirname( __FILE__ )."/inc/lib/openfairdb/OpenFairDBApiException.php");
require_once( dirname( __FILE__ )."/inc/lib/openfairdb/OpenFairDBConfiguration.php");


// -- Controllers --
require_once( dirname( __FILE__ )."/inc/controllers/class-kvm-interface-admincontrol.php" );
require_once( dirname( __FILE__ )."/inc/controllers/class-kvm-interface-handleevents.php" );
require_once( dirname( __FILE__ )."/inc/controllers/class-kvm-interface-handleentries.php" );
require_once( dirname( __FILE__ )."/inc/controllers/class-kvm-interface.php" );

function kvm_start()
{
  $kvmInterface = KVMInterfaceAdminControl::get_instance();
  $kvmInterface->start();
}

add_action( 'init', 'kvm_start' );

//register_activation_hook( __FILE__, array( $feedhandler, 'set_activation'));
//register_deactivation_hook( __FILE__, array( $feedhandler, 'set_deactivation'));
//register_uninstall_hook( __FILE__, array( $feedhandler, 'set_uninstall' ));

$kvmInterface = KVMInterface::get_instance();
$kvmInterface->initialize();
