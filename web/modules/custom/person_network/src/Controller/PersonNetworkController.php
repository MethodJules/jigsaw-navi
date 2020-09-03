<?php

namespace Drupal\person_network\Controller;

class PersonNetworkController {
    public function content() {

        //Hole alle Nodes vom Typ Person
        $nids = \Drupal::entityQuery('node')->condition('type','person')->execute();
        $nodes =  \Drupal\node\Entity\Node::loadMultiple($nids);

        $data = [
            'nodes' => [],
            'edges' => [],
            'praedikate' => [],
        ];

        foreach ($nodes as $node) {
            array_push($data['nodes'], ['id' => $node->id(), 'type' => 'person', 'name' => $node->title->value, 'link' => 'x']);
            //dpm($node);
            //foreach($node->field_networks as $reference) {
            //    array_push($data['edges'], ['sid' => $node->id(), 'tid' => $reference->target_id]);
                //dpm($reference->target_id);
            //}
        }
        $edges = [];


        $index = array_search(1214, $data['nodes']);

        $data['edges'] = $edges;



        $graphdata = $data;
        //dpm($data);

        $render_html = ['#markup' => '<div id="person_network"></div>'];
        $render_html['#attached']['library'][] = 'person_network/person_network';
        //$render_html['#attached']['drupalSettings']['baseUrl'] = $base_url;
        $render_html['#attached']['drupalSettings']['kompetenz_word_visual']['graphdata'] = $graphdata;

        return $render_html;

    }

    public function network() {
        //Get data from database
        $database = \Drupal::database();
        $query = $database->select('node__field_networks', 'networks');
        //$query->fields('networks',['entity_id', 'field_networks_target_id']);
        $query->addField('networks', 'entity_id', 'originID');
        $query->addField('networks', 'field_networks_target_id', 'destinationID');
        $result = $query->execute();

        //print_r($query->__toString());


        $data = [
            'nodes' => [],
            'edges' => [],
            'praedikate' => [],
        ];
        foreach($result as $record) {

                $edges[] = [
                    'source' => $record->originID,
                    'target' => $record->destinationID,
                ];
                $origin_ids[] = $record->originID;
                $destination_ids[] = $record->destinationID;
        }

        $nodes = array_unique(array_merge($origin_ids, $destination_ids));

        foreach($nodes as $node) {
            $id = $node;
            $title = $this->getTitle($node);
            if(!is_null($title)) {
                $data['nodes'][] = [
                    'nid' => $id,
                    'name' => $title,
                ];
            } else {
                $depricated_nodes[] = $id;
            }
        }

        //dsm($depricated_nodes);
        $filtered_edges = array_diff($edges, $depricated_nodes);




        $data['edges'] = $filtered_edges;

        dsm($data);

        $render_html = ['#markup' => '<div id="person_network"></div>'];
        $render_html['#attached']['library'][] = 'person_network/person_network';
        //$render_html['#attached']['drupalSettings']['baseUrl'] = $base_url;
        $render_html['#attached']['drupalSettings']['kompetenz_word_visual']['graphdata'] = $data;

        return $render_html;

    }

    public function getTitle($nid) {
        $node_storage = \Drupal::entityTypeManager()->getStorage('node');
        $node = $node_storage->load($nid);
        return $node->title->value;
    }

    public function network_search_index($network_nodes, $search_value) {
        for($i=0; $i<count($network_nodes);$i++) {
            //dsm($network_nodes[$i]['name']);
            if($network_nodes[$i]['name'] == $search_value) {
                break;
            }
        };
        return $i;
    }





}