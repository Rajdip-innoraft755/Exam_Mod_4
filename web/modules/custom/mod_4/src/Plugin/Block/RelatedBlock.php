<?php

namespace Drupal\mod_4\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides The Related Blogs.
 *
 * @Block(
 *   id = "mod_4_realted_block",
 *   admin_label = @Translation("Related Block"),
 *   category = @Translation("Mod 4")
 * )
 */
class RelatedBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Stores the object of CurrentRouteMatch.
   *
   * @var \Drupal\Core\Routing\CurrentRouteMatch
   */
  protected CurrentRouteMatch $route;

  /**
   * This is store the node entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $nodeStorage;

  /**
   * Constructs a RelatedBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_defination
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   This is the EntityTypeManagerInterface.
   * @param \Drupal\Core\Routing\CurrentRouteMatch $route
   *   Stores the object of CurrentRouteMatch.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_defination, EntityTypeManagerInterface $entity_manager, CurrentRouteMatch $route) {
    parent::__construct($configuration, $plugin_id, $plugin_defination);
    $this->nodeStorage = $entity_manager->getStorage('node');
    $this->route = $route;
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function build() {
    $content = $this->nodeStorage->loadByProperties([
      'uid' => $this->route->getParameter('node')->getOwnerId(),
    ]);
    $num = 0;
    foreach ($content as $item) {
      if ($num < 3 && $item->id() != $this->route->getParameter('node')->id()) {
        $build['related'][] = [
          '#markup' => '<a href=/node/' . $item->id() . '>' . $item->label() . '<br>',
        ];
        $num++;
      }
    }
    return $build;
  }

}
