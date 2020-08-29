<?php

/**
 * Update and Read Events from the Karte von Morgen 
 *
 * @author   Sjoerd Takken
 * @copyright  No Copyright.
 * @license    GNU/GPLv2, see https://www.gnu.org/licenses/gpl-2.0.html
 */
class KVMInterfaceHandleEvents 
{
  public CONST KVM_EVENT_ID = 'kvm_event_id';
  public CONST KVM_UPLOAD = 'initiative_kvm_upload';

  private $eventsApi;
  private $parent;

  public function __construct($parent) 
  {
    $this->parent = $parent;
  }

  public function get_parent()
  {
    return $this->parent;
  }

  public function initialize()
  {
    // After all Plugins are loaded we start to deal with events.
    add_filter( 'wp_loaded', array( $this, 'start' ));
  }

  public function start() 
  {
    $eiInterface = EIInterface::get_instance();
    $eiInterface->add_event_saved_listener( 
      new class() implements EIEventSavedListenerIF
      {
        
        public function event_saved($eiEvent)
        {
          $instance = KVMInterface::get_instance();
          $instance->event_saved($eiEvent);
        }
      });

    $eiInterface->add_event_deleted_listener( 
      new class() implements EIEventDeletedListenerIF
      {
        
        public function event_deleted($eiEvent)
        {
          $instance = KVMInterface::get_instance();
          $instance->event_deleted($eiEvent);
        }
      });

    $this->eventsApi = new OpenFairDBEventsApi(
      $this->get_parent()->get_client(),
      $this->get_parent()->get_config());

  }

  public function event_saved($eiEvent)
  {
    // Prevent Upload to KVM if in the User Settings
    // it is not allowed
    $upload = get_user_meta($eiEvent->get_owner_user_id(),
                            self::KVM_UPLOAD, 
                            true);
    if(!$upload)
    {
      return;
    }

    $meta_id = self::KVM_EVENT_ID;
    // By recurring Events it can happen that
    // we have multiple Events in KVM and one
    // post in the event calendar.
    // So we have an instance_id where we can 
    // get the right instance for one event.
    if(!empty($eiEvent->get_event_instance_id()))
    {
      $meta_id = self::KVM_EVENT_ID . '_' . 
        $eiEvent->get_event_instance_id();
    }
    $id = get_post_meta($eiEvent->get_post_id(), 
                        $meta_id, 
                        true);

    $this->get_parent()->update_config();
    $api = $this->getEventsApi();

    $wpLocation = $eiEvent->get_location();
    if(empty($wpLocation->get_lat()) ||
       empty($wpLocation->get_lon()))
    {
      $this->handleOFDBException(
        'Hochladen zu der Karte von Morgen '.
        'geht nicht, die Adresse (' . 
        $wpLocation->get_name() . ')' . 
        ' ist nicht richtig, '.
        'keine Koordinaten gefunden für die Adresse.',
        $eiEvent,
        $id,
        null);
      return;
    }


    if(empty($id))
    {
      try
      {
        $id = $api->eventsPost($eiEvent);
        $id = str_replace('"', '', $id);
        update_post_meta($eiEvent->get_post_id(),
                         $meta_id, 
                         $id);
      }
      catch(OpenFairDBApiException $e)
      {
        $this->handleOFDBException(
          'eventsPut failed',
          $eiEvent,
          '',
          $e);
        return;
      }
    }
    else
    {
      $id = str_replace('"', '', $id);
      try
      {
        $api->eventsPut($eiEvent, $id);
      }
      catch(OpenFairDBApiException $e)
      {
        $this->handleOFDBException(
          'eventsPut failed',
          $eiEvent,
          $id,
          $e);
        return;
      }
    }
    $this->handleOFDBException(
      'Status Okey',
      $eiEvent,
      $id,
      null);
    return;
  }

  public function event_deleted($eiEvent)
  {
    if(empty($eiEvent))
    {
      return;
    }

    // Prevent Upload to KVM if in the User Settings
    // it is not allowed
    $upload = get_user_meta($eiEvent->get_owner_user_id(),
                            self::KVM_UPLOAD, 
                            true);
    if(!$upload)
    {
      return;
    }

    $meta_id = self::KVM_EVENT_ID;
    
    // By recurring Events it can happen that
    // we have multiple Events in KVM and one
    // post in the event calendar.
    // So we have an instance_id where we can 
    // get the right instance for one event.
    if(!empty($eiEvent->get_event_instance_id()))
    {
      $meta_id = self::KVM_EVENT_ID . '_' . 
        $eiEvent->get_event_instance_id();
    }

    $id = get_post_meta($eiEvent->get_post_id(), 
                        $meta_id, 
                        true);

    $this->get_parent()->update_config();
    $api = $this->getEventsApi();

    if(!empty($id))
    {
      $id = str_replace('"', '', $id);

      try
      {
        $api->eventsDelete($id);
      }
      catch(OpenFairDBApiException $e)
      {
        $this->handleOFDBException(
          'eventsDelete failed',
          $eiEvent,
          $id,
          $e);
      }
      delete_post_meta($eiEvent->get_post_id(),
                       $meta_id);
    }
  }

  public function get_events()
  {
    $this->get_parent()->update_config();
    $api = $this->getEventsApi();
    return $api->eventsGet(null, 10); 
  }

  public function getEventsApi()
  {
    return $this->eventsApi;
  }

  public function handleOFDBException($msg, 
                                      $eiEvent,
                                      $kvm_id,
                                      $e)
  {
    if(empty($eiEvent->get_owner_user_id()))
    {
      return;
    }

    $msgTA = '[' . date_create()->format('Y-m-d H:i:s') . ']';
    $msgTA .= ' Veranstaltung hochladen';
    $msgTA .= PHP_EOL;
    $msgTA .= 'Titel: ' . 
              $eiEvent->get_title() . 
              '(postid=' . $eiEvent->get_post_id() .  
              ', eventid=' . $eiEvent->get_event_id() . ')'; 
    $msgTA .= PHP_EOL;
    $msgTA .= 'KVM Id: ' . $kvm_id;
    $msgTA .= PHP_EOL;
    $msgTA .= 'Bericht: ' . $msg;
    $msgTA .= PHP_EOL;
    if( ! empty($e ))
    {
      $msgTA .= PHP_EOL;
      $msgTA .= 'Exception: ';
      $msgTA .= PHP_EOL;
      $msgTA .= $e->getTextareaMessage();
    }

    update_user_meta(
      $eiEvent->get_owner_user_id(),
      'initiative_kvm_errorlog',
      $msgTA);
  }
}
