<?php

namespace Drupal\Tests\islandora\FunctionalJavascript;

use Behat\Mink\Exception\ExpectationException;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\field\Entity\FieldConfig;
use Drupal\node\Entity\Node;

/**
 * Test integer weight selector.
 *
 * Taken from the weight module with some edits.
 *
 * @group islandora
 */
class IntegerWeightTest extends WebDriverTestBase {

  use FieldUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'node',
    'views',
    'field_ui',
    'integer_weight_test_views',
  ];

  /**
   * Name of theme.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * Name of the field.
   *
   * Used in the test view; change there
   * if changed here.
   *
   * @var string
   */
  protected static $fieldName = 'field_integer_weight';

  /**
   * Type of the field.
   *
   * @var string
   */
  protected static $fieldType = 'integer';

  /**
   * User that can edit content types.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  public static $testViews = [
    'test_integer_weight',
  ];

  /**
   * Array of nodes to test with.
   *
   * @var array
   */
  public $nodes = [];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser(
          [
            'administer content types',
            'administer node fields',
            'administer node display',
          ]
    );

    // Create dummy repo_item type to sort (since we don't have
    // repository_object without islandora_defaults).
    $type = $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create([
        'type' => 'repo_item',
        'name' => 'Repository Item',
      ]);
    $type->save();
    $this->container->get('router.builder')->rebuild();

    $fieldStorage = FieldStorageConfig::create([
      'fieldName' => static::$fieldName,
      'entity_type' => 'node',
      'type' => static::$fieldType,
    ]);
    $fieldStorage->save();
    $field = FieldConfig::create([
      'field_storage' => $fieldStorage,
      'bundle' => 'repo_item',
      'required' => FALSE,
    ]);
    $field->save();

    for ($n = 1; $n <= 3; $n++) {
      $node = $this->drupalCreateNode([
        'type' => 'repo_item',
        'title' => "Item $n",
        static::$fieldName => $n,
      ]);
      $node->save();
      $this->nodes[] = $node;
    }

    ViewsTestData::createTestViews(get_class($this), ['integer_weight_test_views']);
  }

  /**
   * Test integer weight selector.
   */
  public function testIntegerWeightSelector() {
    $this->drupalGet('test-integer-weight');
    $page = $this->getSession()->getPage();

    $weight_select1 = $page->findField("field_integer_weight[0][weight]");
    $weight_select2 = $page->findField("field_integer_weight[1][weight]");
    $weight_select3 = $page->findField("field_integer_weight[2][weight]");

    // Are row weight selects hidden?
    $this->assertFalse($weight_select1->isVisible());
    $this->assertFalse($weight_select2->isVisible());
    $this->assertFalse($weight_select3->isVisible());

    // Check that 'Item 2' is feavier than 'Item 1'.
    $this->assertGreaterThan($weight_select1->getValue(), $weight_select2->getValue());

    // Does 'Item 1' preced 'Item 2'?
    $this->assertOrderInPage(['Item 1', 'Item 2']);

    // No changes yet, so no warning...
    $this->assertSession()->pageTextNotContains('You have unsaved changes.');

    // Drag and drop 'Item 1' over 'Item 2'.
    $dragged = $this->xpath("//tr[@class='draggable'][1]//a[@class='tabledrag-handle']")[0];
    $target = $this->xpath("//tr[@class='draggable'][2]//a[@class='tabledrag-handle']")[0];
    $dragged->dragTo($target);

    // Pause for javascript to do it's thing.
    $this->assertJsCondition('jQuery(".tabledrag-changed-warning").is(":visible")');

    // Look for unsaved changes warning.
    $this->assertSession()->pageTextContains('You have unsaved changes.');

    // 'Item 2' should now preced 'Item 1'.
    $this->assertOrderInPage(['Item 2', 'Item 1']);

    $this->submitForm([], 'Save');

    // Form refresh should reflect the new order still.
    $this->assertOrderInPage(['Item 2', 'Item 1']);

    // Ensure the stored values reflect the new order.
    $item1 = Node::load($this->nodes[0]->id());
    $item2 = Node::load($this->nodes[1]->id());
    $this->assertGreaterThan($item2->field_integer_weight->getString(), $item1->field_integer_weight->getString());
  }

  /**
   * Asserts that several pieces of markup are in a given order in the page.
   *
   * Taken verbatim from the weight module.
   *
   * @param string[] $items
   *   An ordered list of strings.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   *   When any of the given string is not found.
   */
  protected function assertOrderInPage(array $items) {
    $session = $this->getSession();
    $text = $session->getPage()->getHtml();
    $strings = [];
    foreach ($items as $item) {
      if (($pos = strpos($text, $item)) === FALSE) {
        throw new ExpectationException("Cannot find '$item' in the page", $session->getDriver());
      }
      $strings[$pos] = $item;
    }
    ksort($strings);
    $ordered = implode(', ', array_map(function ($item) {
       return "'$item'";
    }, $items));
    $this->assertSame($items, array_values($strings), "Found strings, ordered as: $ordered.");
  }

}
