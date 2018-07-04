<?php

namespace Drupal\devportal_api_reference;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\devportal_api_reference\Annotation\Reference;
use Traversable;

class ReferenceTypeManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(Traversable $namespaces, CacheBackendInterface $cacheBackend, ModuleHandlerInterface $module_handler) {
    parent::__construct(
      'Plugin/Reference',
      $namespaces,
      $module_handler,
      ReferenceInterface::class,
      Reference::class
    );

    $this->alterInfo('reference_info');
    $this->setCacheBackend($cacheBackend, 'reference_info_plugins');
  }

  /**
   * {@inheritdoc}
   */
  protected function findDefinitions() {
    $definitions = parent::findDefinitions();
    uasort($definitions, function (array $def0, array $def1): int {
      return ($def0['weight'] ?? 0) <=> ($def1['weight'] ?? 0);
    });

    return $definitions;
  }

  /**
   * @param string $filename
   *
   * @return \Drupal\devportal_api_reference\ReferenceInterface[]
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  public function getInstancesFor(string $filename): array {
    $definitions = $this->getDefinitions();
    if (!($extension = pathinfo($filename, PATHINFO_EXTENSION))) {
      return [];
    }
    $instances = [];
    foreach ($definitions as $name => $definition) {
      $extensions = $definition['extensions'] ?? [];
      if (!in_array($extension, $extensions)) {
        continue;
      }

      /** @var \Drupal\devportal_api_reference\ReferenceInterface $instance */
      $instance = $this->createInstance($name);
      $instances[] = $instance;
    }

    return $instances;
  }

  public function lookupPlugin(string $filename): ?ReferenceInterface {
    $instances = $this->getInstancesFor($filename);

    foreach ($instances as $instance) {
      if ($instance->parse($filename)) {
        return $instance;
      }
    }

    return NULL;
  }

}
