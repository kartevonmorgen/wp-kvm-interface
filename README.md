# kvm-interface
This plugin is used to upload and download events and initiative(entries) to the Karte von Morgen.

The Plugin depends on the ["Events Interface"](https://github.com/kartevonmorgen/events-interface) - Wordpress Plugin which need to be installed and activated first.

The following Settings can be setted under Events Interface - Karte von Morgen
* URL - > URL to the OpenFairDB Database
* Access Token	-> Access Token for saving events and entries as an authorized entity.
* Fixed Tag -> Gives uploaded events and entries a fixed tag so they all can be found by this tag

In PHP the following Interface can be used to load and save entries and events.

Loading Entries:
```php
$kvmInterface = KVMInterface::get_instance();
$wpInitiativen = $kvmInterface->get_entries();
```

Saving Entries:
```php
$kvmInterface = KVMInterface::get_instance();
$kvmInterface->save_entry($wpInitiative);
```

Loading Events:
```php
$kvmInterface = KVMInterface::get_instance();
$eiEvents = $kvmInterface->get_events();
```

Saving Events:
```php
$kvmInterface = KVMInterface::get_instance();
$kvmInterface->event_saved($eiEvent);
```
