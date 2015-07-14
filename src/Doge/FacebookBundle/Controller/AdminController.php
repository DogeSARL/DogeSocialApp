<?php

namespace Doge\FacebookBundle\Controller;

use Doge\FacebookBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Security;

class AdminController extends Controller{
    public function indexAction(){

        $url = 'https://api.facebook.com/method/fql.query?query=select%20like_count%20from%20link_stat%20where%20url=%27https://www.facebook.com/pages/Kawaii-Pets/801971733218893?fref=ts/%27&format=json';
        $json = json_decode(file_get_contents($url), true);

        $userStatsService = $this->get("doge_facebook.helper.stats.user");
        $countries = [];

        $users = $this->getDoctrine()->getManager()->getRepository("DogeFacebookBundle:User")->findAll();
        $countries = $userStatsService->getCountries($users);
        $user_per_country = $userStatsService->getUserPerCountry($users);
        $nb_user_age_below_25 = $userStatsService->getNbOfUserWhoseAgeIsBelow25($users);
        $nb_user_age_over_25 = $userStatsService->getNbOfUserWhoseAgeIsOver25($users);

        return $this->render('DogeFacebookBundle:Admin:index.html.twig', [  'users' => $users, 'json' => $json, 'countries' => $countries, 'user_per_country' => $user_per_country,
                                                                        'nb_user_age_below_25' => $nb_user_age_below_25, 'nb_user_age_over_25' => $nb_user_age_over_25]);
    }

    public function adminLoginAction(){
        return $this->render('DogeFacebookBundle:Admin:login.html.twig');
    }

    public function adminStatsAction(){
        return $this->render('DogeFacebookBundle:Admin:stats.html.twig');
    }
}
