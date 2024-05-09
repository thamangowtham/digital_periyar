<?php

namespace Drupal\islandora\ContextProvider;

use Drupal\node\NodeInterface;
use Drupal\Core\Plugin\Context\ContextProviderInterface;
use Drupal\Core\Plugin\Context\EntityContext;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Sets the provided media as a context.
 */
class NodeContextProvider implements ContextProviderInterface {

  use StringTranslationTrait;

  /**
   * Node to provide in a context.
   *
   * @var \Drupal\node\NodeInterface
   */
  protected $node;

  /**
   * Constructs a new NodeContextProvider.
   *
   * @var \Drupal\node\NodeInterface $node
   *   The node to provide in a context.
   */
  public function __construct(NodeInterface $node) {
    $this->node = $node;
  }

  /**
   * {@inheritdoc}
   */
  public function getRuntimeContexts(array $unqualified_context_ids) {
    $context = EntityContext::fromEntity($this->node);
    return ['@node.node_route_context:node' => $context];
  }

  /**
   * {@inheritdoc}
   */
  public function getAvailableContexts() {
    $context = EntityContext::fromEntityTypeId('node', $this->t('Node from entity hook'));
    return ['@node.node_route_context:node' => $context];
  }

}
