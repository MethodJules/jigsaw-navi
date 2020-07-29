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
        foreach ($nodes as $node) {
            foreach($node->field_networks as $reference) {
                $sid = $this->getIndex($data['nodes'], $node->id());
                $tid = $this->getIndex($data['nodes'], $reference->target_id);
                array_push($edges, ['sid' => $sid, 'tid' => $tid]);
                //dpm($reference->target_id);
            }
        }

        $index = array_search(1214, $data['nodes']);
        dpm($index);

        $data['edges'] = $edges;



        $graphdata = $data;
        //dpm($data);

        $render_html = ['#markup' => '<div id="person_network"></div>'];
        $render_html['#attached']['library'][] = 'person_network/person_network';
        //$render_html['#attached']['drupalSettings']['baseUrl'] = $base_url;
        $render_html['#attached']['drupalSettings']['kompetenz_word_visual']['graphdata'] = $graphdata;

        return $render_html;

    }

    public function getIndex($data, $nid) {
        $index = array_search($nid, $data['id']);
        //dpm($index);
    }
}