<?php

namespace Drupal\network_person\Controller;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;

class NetworkController extends ControllerBase {
  private $indexMap = [];
  private $singleClusters = [];   // tracks single clusters to optionally remove them

  public function content() {
    $nodes = $this->getPersons();
    $graphdata['nodes'] = $this->getNetworkNodes($nodes);
    $graphdata['edges'] = $this->getNetworkEdges($nodes);

    $render_html = ['#markup' => '<div id="network_person"></div>'];
    $render_html['#attached']['library'][] = 'network_person/network_person_d3';
    $render_html['#attached']['drupalSettings']['network_person']['graphdata'] = $graphdata;

    return $render_html;
  }


  /**
   * Returns all person nodes.
   *
   * @return array|EntityInterface[]
   */
  private function getPersons() {
    $personNodes = [];

    try {
      $node_storage = $this->entityTypeManager()->getStorage('node');
      $query = $this->entityTypeManager->getStorage('node')->getQuery();
      $query->condition('type', 'person');
      $nids = $query->execute();
      $personNodes = $node_storage->loadMultiple($nids);
    } catch (InvalidPluginDefinitionException $e) {
    } catch (PluginNotFoundException $e) {
    }

    return $personNodes;
  }


  /**
   * Returns a D3.js compliant node array.
   *
   * @param array $nodes
   * @return array
   */
  private function getNetworkNodes(array $nodes) {
    $graphNodes = [];
    $graphIndex = 0;

    foreach ($nodes as $node) {
      $personName = $node->title->value;
      $graphNodes[] = [
        'name' => $personName,
        'link' => $node->toUrl()->toString()
      ];

      // map node ID to graph node array ID
      $personNid = $node->nid->value;
      $this->indexMap[$personNid] = $graphIndex;
      $graphIndex++;
    }

    return $graphNodes;
  }


  /**
   * Returns a D3.js compliant edge array.
   *
   * @param array $persons
   *
   * @return array
   */
  private function getNetworkEdges(array $persons) {
    $graphEdges = [];
    foreach ($persons as $person) {
      $personNid = $person->nid->value;
      $referencedPersons = $person->field_networks;

      foreach ($referencedPersons as $referencedPerson) {
        $target = $this->indexMap[$referencedPerson->target_id];

        if (isset($target)) {
          $graphEdges[] = [
            'source' => $this->indexMap[$personNid],
            'target' => $target
          ];
        }
      }
    }

    return $graphEdges;
  }
}
