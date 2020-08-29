<?php

/**
 * Controller Karte von Morgen für Entries
 *
 * @author   Sjoerd Takken
 * @copyright  No Copyright.
 * @license    GNU/GPLv2, see https://www.gnu.org/licenses/gpl-2.0.html
 */
class KVMInterface 
{
  private static $instance = null;

  private $handleEvents;
  private $handleEntries;
  private $config;
  private $client;

  private function __construct() 
  {
    $this->config = OpenFairDBConfiguration::getDefaultConfiguration();
    $this->handle_events = new KVMInterfaceHandleEvents($this);
    $this->handle_entries = new KVMInterfaceHandleEntries($this);
  }

  /** 
   * The object is created from within the class itself
   * only if the class has no instance.
   */
  public static function get_instance()
  {
    if (self::$instance == null)
    {
      self::$instance = new KVMInterface();
    }
    return self::$instance;
  }

  public function initialize()
  {
    $this->get_handle_events()->initialize();
    $this->get_handle_entries()->initialize();

  }

  public function save_entry($wpInitiative)
  {
    return $this->get_handle_entries()->save_entry(
      $wpInitiative);
  }

  public function get_entries()
  {
    return $this->get_handle_entries()->get_entries();
  }

  public function get_entries_by_ids($ids)
  {
    return $this->get_handle_entries()->get_entries_by_ids(
      $ids);
  }

  public function event_saved($eiEvent)
  {
    return $this->get_handle_events()->event_saved($eiEvent);
  }

  public function event_deleted($eiEvent)
  {
    return $this->get_handle_events()->event_deleted($eiEvent);
  }

  public function get_events()
  {
    return $this->get_handle_events()->get_events();
  }

  public function update_config()
  {
    $config = $this->get_config();
    $config->setHost( get_option('kvm_interface_fairdb_url'));
    $config->setAccessToken( get_option('kvm_access_token'));
  }
  
  private function get_handle_entries()
  {
    return $this->handle_entries;
  }

  private function get_handle_events()
  {
    return $this->handle_events;
  }

  public function get_config()
  {
    return $this->config;
  }

  public function get_client()
  {
    if(empty($this->client))
    {
      $this->client = new WordpressHttpClient();
    }
    return $this->client;
  }

}
