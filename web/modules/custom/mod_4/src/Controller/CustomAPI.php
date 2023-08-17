<?php

namespace Drupal\mod_4\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * This controller is used to fetch the data from API.
 */
class CustomAPI extends ControllerBase {
  /**
   * This is store the data of all nodes of the required type.
   *
   * @var array
   */
  protected array $nodeData;

  /**
   * This is store the user entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $user;

  /**
   * Constructs the CustomAPI object with the required depenency.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_manager
   *   This is the EntityTypeManagerInterface.
   */
  public function __construct(EntityTypeManagerInterface $entity_manager) {
    $this->nodeData = $entity_manager->getStorage('node')->loadByProperties([
      'type' => 'blog',
    ]);
    $this->user = $entity_manager->getStorage('user');
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * This is to build the response for the api call.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Returns the JsonResponse.
   */
  public function buildResult(Request $request) {
    $author_name = $request->get('author');
    $tag_name = $request->get('tag');

    $node_data_array = [];
    $node_data_array['title'] = 'Blog Node Data';

    foreach ($this->nodeData as $node) {
      $tags = [];
      foreach ($node->get('field__blog_tags')->referencedEntities() as $tag) {
        $tags[] = $tag->label();
      }
      $author = $this->user->load($node->getOwnerId());
      $node_data_array['data'][] = [
        'id' => $node->get('uuid')->value,
        'label' => $node->label(),
        'body' => $node->get('body')->value,
        'published_date' => $node->get('field_published_date')->value,
        'tags' => $tags,
        'author' => $author->getDisplayName(),
      ];
    }

    if ($author_name && $tag_name) {
      $primary_result = $this->authorBased($node_data_array, $author_name);
      if ($primary_result) {
        $result = $this->tagBased($primary_result, $tag_name);
      }
    }

    elseif ($tag_name) {
      $result = $this->tagBased($node_data_array, $tag_name);
    }

    elseif ($author_name) {
      $result = $this->authorBased($node_data_array, $author_name);
    }

    else {
      $result = $node_data_array;
    }

    $response = new JsonResponse($result, 200);
    $response->setEncodingOptions(JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return $response;
  }

  /**
   * This method is to filter the data based on tags.
   *
   * @param array $node_data_array
   *   The input data array.
   * @param string $tag_name
   *   The tag name.
   *
   * @return array
   *   Returns the result array.
   */
  public function tagBased(array $node_data_array, string $tag_name) {
    foreach ($node_data_array['data'] as $node) {
      foreach ($node['tags'] as $tag) {
        if ($tag == $tag_name) {
          $result[] = $node;
        }
      }
    }
    return $result;
  }

  /**
   * This method is to filter the data based on author name.
   *
   * @param array $node_data_array
   *   The input data array.
   * @param string $author_name
   *   The author name.
   *
   * @return array
   *   Returns the result array.
   */
  public function authorBased(array $node_data_array, string $author_name) {
    foreach ($node_data_array['data'] as $node) {
      if ($node['author'] == $author_name) {
        $result[] = $node;
      }
    }
    return $result;
  }

}
