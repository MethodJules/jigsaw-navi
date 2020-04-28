<?php
/**
 * Created by PhpStorm.
 * User: julien
 * Date: 01.02.19
 * Time: 13:17
 */

namespace Drupal\women_data\Controller;


use Drupal\Core\Controller\ControllerBase;

class WomenDataController extends ControllerBase
{

    public function fetchData() {

        //$data = file_get_contents('http://localhost/women/api/v1/node/2937.json');
        //$person_facts = json_decode($data, TRUE);


        //dsm($person_facts);
        //$title = $person_facts['title'];
        //$bibliography = $person_facts['field_biography']['und'][0]['value'];
        //dsm($title);
        //dsm($bibliography);


        $person = $this->fetchDataOfPerson();
        $chronic = $this->fetchDataOfChronic();
        $activity = $this->fetchDataOfActivity();
        dsm($activity);
        return [
            '#markup' => '<p>Funkt</p>',
        ];
    }


    public function fetchDataOfPerson() {
        $url = 'http://localhost/women/api/v1/node/2937.json';
        $data = file_get_contents($url);
        $person_facts = json_decode($data, TRUE);

        //dsm($person_facts);

        if ($person_facts['title']) {
            $person['title'] = $person_facts['title'];
        }

        if ($person_facts['field_nachname']) {
            $person['nachname'] = $person_facts['field_nachname']['und'][0]['value'];
        }

        if ($person_facts['field_biography']) {
            $person['biography'] = $person_facts['field_biography']['und'][0]['value'];
        }

        if ($person_facts['field_networks']) {
            $person['networks'] = $person_facts['field_networks']['und'][0]['value'];
        }

        if ($person_facts['field_mother']) {
            $person['mother'] = $person_facts['field_mother']['und'][0]['value'];
        }

        if ($person_facts['field_father']) {
            $person['father'] = $person_facts['field_father']['und'][0]['value'];
        }

        if ($person_facts['field_siblings']) {
            $person['siblings'] = $person_facts['field_siblings']['und'][0]['value'];
        }
        if ($person_facts['field_marriage_children']) {
            $person['marriage_children'] = $person_facts['field_marriage_children']['und'][0]['value'];
        }

        if ($person_facts['field_age_at_migration']) {
            $person['ag_at_migration'] = $person_facts['field_age_at_migration']['und'][0]['value'];
        }

        if ($person_facts['field_year_of_migration']) {
            $person['year_of_migration'] = $person_facts['field_year_of_migration']['und'][0]['value'];
        }

        if ($person_facts['field_archival_materials']) {
            $person['archival_materials'] = $person_facts['field_archival_materials']['und'][0]['value'];
        }

        if ($person_facts['field_other_sources']) {
            $person['other_sources'] = $person_facts['field_other_sources']['und'][0]['value'];
        }

        if ($person_facts['field_chronics']) {
            $person['chronics'] = $person_facts['field_chronics']['und'];
        }

        if ($person_facts['field_domiciles']) {
            $person['domiciles'] = $person_facts['field_domiciles']['und'];
        }








        return $person;
    }

    public function fetchDataOfChronic() {
        $url = 'http://localhost/women/api/v1/node/2968.json';
        $data = file_get_contents($url);
        $chronic_facts = json_decode($data, TRUE);

       // dsm($chronic_facts);

        if(!empty($chronic_facts['title'])) {
            $chronic['title'] = $chronic_facts['title'];
        }

        if(!empty($chronic_facts['field_time'])) {
            $chronic['time'] = $chronic_facts['field_time']['und'][0];
        }

        if(!empty($chronic_facts['field_activities'])) {
            $chronic['activities'] = $chronic_facts['field_activities']['und'];
        }

        if(!empty($chronic_facts['field_sw_education'])) {
            $chronic['sw_education'] = $chronic_facts['field_sw_education']['und'];
        }

        return $chronic;
    }

    public function fetchDataOfActivity() {
        $url = 'http://localhost/women/api/v1/node/2975.json';
        $data = file_get_contents($url);
        $activity_facts = json_decode($data, TRUE);

        //dsm($person_facts);

        if (!empty($activity_facts['title'])) {
            $activity['title'] = $activity_facts['title'];
        }

        if (!empty($activity_facts['field_description'])) {
            $activity['description'] = $activity_facts['field_description'];
        }

        if (!empty($activity_facts['field_institution_association'])) {
            $activity['institution_association'] = $activity_facts['field_institution_association']['und'];
        }

        if (!empty($activity_facts['field_place'])) {
            $activity['place'] = $activity_facts['field_place']['und'];
        }

        if (!empty($activity_facts['field_professional_activities'])) {
            $activity['professional_activities'] = $activity_facts['field_professional_activities']['und'];
        }

        if (!empty($activity_facts['field_civic_engagements'])) {
            $activity['civic_engagements'] = $activity_facts['field_civic_engagements']['und'];
        }

        if (!empty($activity_facts['field_other_activities'])) {
            $activity['other_activities'] = $activity_facts['field_other_activities']['und'];
        }

        return $activity;




    }
}