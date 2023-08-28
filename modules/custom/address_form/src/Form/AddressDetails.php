<?php

namespace Drupal\address_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\Yaml\Yaml;

class AddressDetails extends FormBase {

    public function getFormId() {
        return "address_user_details_form";
    }  
    public function buildForm(array $form, FormStateInterface $form_state) {
    $form['country'] = [
        '#type' => 'textfield',
        '#title' => 'Country',
        '#required' => true,
    ];
    $form['city'] = [
        '#type' => 'textfield',
        '#title' => 'City',
        '#required' => true,
    ];
    $form['postalcode'] = [
        '#type' => 'textfield',
        '#title' => 'Postal code',
    ];
    $form['submit'] = [
        '#type' => 'submit',
        '#value' => 'Submit',
    ];

    return $form;
}

public function submitForm(array &$form, FormStateInterface $form_state) {
    
    require __DIR__ . '/dhlapi.php';
    require __DIR__ . '/getCountries.php';

    $countries = getCountries();
    $values = $form_state->getValues();
    if (array_key_exists($values['country'], $countries)) {
             $countryCode = $countries[$values['country']];
             //echo "Country: ".$country.", Country Code: ".$countryCode;
    }
    $data = getData($countryCode,$values['city'],$values['postalcode']);
    $tempArray = array();

    $decoded_json = json_decode($data, true);
    $locations = $decoded_json['locations'];

    foreach($locations as $location) {
        $place = $location['place'];
        $address = $place['address'];
        $streetAddress = $address['streetAddress'];
        $openingHours = $location['openingHours'];
        $weekendcount = 0;

        $streetAddressNum = preg_replace('/\D+/', '', $streetAddress);  //identify all non-numeric characters in a string, and replace them with an empty string ("") 
        
        foreach($openingHours as $openingHour) {
            $strDayOfWeek = $openingHour['dayOfWeek'];
            $strWeekend1 = "Saturday";
            $strWeekend2 = "Sunday";
             if((strpos($strDayOfWeek, $strWeekend1) !== false) || (strpos($strDayOfWeek, $strWeekend2) !== false)) {
                $worksonweekend = TRUE;
              } 
        }
        
        if ($streetAddressNum % 2 == 0 && $worksonweekend == TRUE) {
            array_push($tempArray, $location);
        }              
    }
  
     $yaml = Yaml::dump($tempArray);
     echo $yaml; 
     exit;
   }
}
