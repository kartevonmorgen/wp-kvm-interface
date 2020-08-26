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

    if(empty($id))
    {
      $id = $api->eventsPost($eiEvent);
      $id = str_replace('"', '', $id);
      update_post_meta($eiEvent->get_post_id(),
                       $meta_id, 
                       $id);
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
        if($e->getCode() == 404)
        {
          echo 'OpenFairDB eventsPut, id not found' . $id;
        }
        else
        {
          throw $e;
        }
      }
    }
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
        if($e->getCode() == 404)
        {
          echo 'OpenFairDB eventsDelete, id not found' . $id;
        }
        else
        {
          throw $e;
        }
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
}
