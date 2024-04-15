<?php

namespace Drupal\migration\Plugin\migrate\process;

use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removes links from body if they match a specific URL.
 *
 * @MigrateProcessPlugin(
 *   id = "custom_link_plugin"
 * )
 */
class CustomLinkPlugin extends ProcessPluginBase implements ContainerFactoryPluginInterface {
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected PathValidatorInterface $pathValidator) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path.validator'),
    );
  }



  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array {
    if (!empty($value['value'])) {
      $dom = new \DOMDocument();
      $dom->loadHTML($value['value']);
      $anchors = $dom->getElementsByTagName('a');
      foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');
        $href = str_replace('http://ehistory.osu.edu', '', $href);
        $is_valid = $this->pathValidator->isValid($href);
        if ($is_valid) {
          $anchor->setAttribute('href', $href);
        } else {
          $anchor->removeAttribute('href');
        }
      }

      $value['value'] = $dom->saveHTML();
    }

    return $value;
  }
}
