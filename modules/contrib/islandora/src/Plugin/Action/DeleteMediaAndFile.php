<?php

namespace Drupal\islandora\Plugin\Action;

use Drupal\Core\Action\Plugin\Action\DeleteAction;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;

/**
 * Deletes a media and its source file.
 *
 * @Action(
 *   id = "delete_media_and_file",
 *   label = @Translation("Delete media and file"),
 *   type = "media",
 *   confirm_form_route_name = "islandora.confirm_delete_media_and_file"
 * )
 */
class DeleteMediaAndFile extends DeleteAction {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, PrivateTempStoreFactory $temp_store_factory, AccountInterface $current_user) {
    $this->currentUser = $current_user;
    $this->tempStore = $temp_store_factory->get('media_and_file_delete_confirm');
    $this->entityTypeManager = $entity_type_manager;
    $this->configuration = $configuration;
    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
  }

  /**
   * {@inheritdoc}
   */
  public function executeMultiple(array $entities) {

    $selection = [];
    foreach ($entities as $entity) {
      $langcode = $entity->language()->getId();
      $selection[$entity->id()][$langcode] = $langcode;
    }
    $this->tempStore->set("{$this->currentUser->id()}:media", $selection);
  }

}
