<?php

namespace Doge\FacebookBundle\Helper\Stats;

class User{

	public function getCountries( $users ){
    # Set the users countries
    $countries = [];
    foreach ($users as $user) {
      if (in_array($user->getCountry(), $countries)) {
      }
      else {
        array_push($countries, $user->getCountry());
      }
    }
    return $countries;
	}

  public function getUserPerCountry( $users ) {
    $user_per_country = [];
    $countries = $this->getCountries($users);
    foreach($countries as $country) {
      if (array_key_exists($country, $countries)) {
        $user_per_country[$country]++;
      }
      else {
        $user_per_country[$country] = 1;
      }
    }
    return $user_per_country;
  }

}