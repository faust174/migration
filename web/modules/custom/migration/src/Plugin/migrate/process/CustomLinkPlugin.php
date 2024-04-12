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
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $callback = function ($matches) {
      $url = $matches[2];
      $modified_url = str_replace('ehistory.osu.edu', '', $url);
      return $matches[1] . $modified_url . $matches[3];
    };

    $pattern = '/(<a[^>]+href=["\'])([^"\']+ehistory\.osu\.edu\/[^"\']*)(["\'][^>]*>)/i';

    return preg_replace_callback($pattern, $callback, $value);
  }

}
