<?php

namespace Drupal\migration\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Removes links from body if they match a specific URL.
 *
 * @MigrateProcessPlugin(
 *   id = "custom_link_plugin"
 * )
 */
class CustomLinkPlugin extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property): array
  {
    if (!empty($value['value'])) {
      $dom = new \DOMDocument();
      $dom->loadHTML($value['value']);
      $anchors = $dom->getElementsByTagName('a');
      foreach ($anchors as $anchor) {
        $href = $anchor->getAttribute('href');
        $href = str_replace('http://ehistory.osu.edu', '', $href);
        $is_valid = \Drupal::pathValidator()->isValid($href);
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
