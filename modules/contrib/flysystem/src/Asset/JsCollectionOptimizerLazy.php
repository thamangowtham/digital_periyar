<?php

namespace Drupal\flysystem\Asset;

use Drupal\Core\Asset\JsCollectionOptimizerLazy as DrupalJsCollectionOptimizerLazy;
use Drupal\Core\File\Exception\FileException;

/**
 * Optimizes JavaScript assets.
 */
class JsCollectionOptimizerLazy extends DrupalJsCollectionOptimizerLazy {

  use SchemeExtensionTrait;

  /**
   * {@inheritdoc}
   */
  public function deleteAll() {
    $this->state->delete('system.js_cache_files');
    try {
      $this->fileSystem->deleteRecursive($this->getSchemeForExtension('js') . '://js');
    } catch (FileException $fileException) {
      \Drupal::logger('flysystem')->error($fileException->getMessage());
    }
  }

}
