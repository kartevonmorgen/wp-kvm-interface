<?php

/**
 * Controller Karte von Morgen Interface AdminControl
 * Settings page of the Karte von Morgen Interface.
 * It uses the UISettingsPage which use the 
 * Wordpress Settings API
 *
 * @author   Sjoerd Takken
 * @copyright  No Copyright.
 * @license    GNU/GPLv2, see https://www.gnu.org/licenses/gpl-2.0.html
 */
class KVMInterfaceAdminControl 
{
  private static $instance = null;

  private function __construct() 
  {
  }

  /** 
   * The object is created from within the class itself
   * only if the class has no instance.
   */
  public static function get_instance()
  {
    if (self::$instance == null)
    {
      self::$instance = new KVMInterfaceAdminControl();
    }
    return self::$instance;
  }

  public function start() 
  {
    $page = new UISettingsPage('kvm-interface', 
                               'Karte von Morgen');
    $page->set_submenu_page(true, 'events-interface-options-menu');
    $section = $page->add_section('kvm_section_one', 'Settings');
    $section->set_description(
      'The Karte von Morgen Interface allows us to automatically '.
      'upload events to the Karte von Morgen. <br/> '.
      'This Plugin uses the Event Interface which is triggered '.
      'wenn an event is saved in the underlying active event calendar'
      );

    $field = $section->add_textfield('kvm_interface_fairdb_url', 
                                     'URL');
    $field->set_description('URL to the OpenFairDB Database');
    $field = $section->add_textfield('kvm_access_token', 
                                     'Access Token');
    $field->set_description('Access Token for commiting events');

    $field = $section->add_textfield('kvm_fixed_tag', 
                                     'Fixed tag');
    $field->set_description('Gives uploaded events and entries a fixed tag so they all can be found by this tag');
    
    $field = new class('kvm_event_defaultresult', 
                       'Loaded Events from KVM') 
                       extends UISettingsTextAreaField
    {
      public function get_value()
      {
    
        $instance = KVMInterface::get_instance(); 
        $events = $instance->get_events();
    
        $result = '';

        foreach ( $events as $event ) 
        {
          $result .= $event->to_text();
          $result .= PHP_EOL;
          $result .= '-----------------';
          $result .= PHP_EOL;
        }
        return $result;
      }
    };

    $field->set_register(false);
    $section->add_field($field);
    
    $field = new class('kvm_entry_defaultresult', 
                       'Loaded Entries from KVM') 
                       extends UISettingsTextAreaField
    {
      public function get_value()
      {
    
        $instance = KVMInterface::get_instance(); 
        $entries = $instance->get_entries();
    
        $result = '';

        foreach ( $entries as $entry ) 
        {
          $result .= $entry->to_text();
          $result .= PHP_EOL;
          $result .= '-----------------';
          $result .= PHP_EOL;
        }
        return $result;
      }
    };

    $field->set_register(false);
    $section->add_field($field);

    $page->register();
  }
}
