<?php
namespace Doge\FacebookBundle\Provider;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class OauthProvider extends BaseClass
{

    protected $entityManager;

    public function __construct(UserManagerInterface $userManager, array $properties, $em)
    {
        parent::__construct( $userManager, $properties );
        $this->entityManager = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $username = $response->getUsername();
        $user = $this->userManager->findUserBy(array($this->getProperty($response) => $username));

        //when the user is registrating
        if (null === $user) {
            $service = $response->getResourceOwner()->getName();
            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';
            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($username);
            $user->$setter_token($response->getAccessToken());
            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($username);
            $user->setEmail($username);
            $user->setPassword($username);

            $user->setEnabled(true);
            $this->userManager->updateUser($user);

            $user->setNom($response->getResponse()["first_name"]);
            $user->setPrenom($response->getResponse()["last_name"]);
            $user->setGender($response->getResponse()["gender"]);
            $user->setCountry($response->getResponse()["location"]);
            $user->setAge($this->getAgefromBirthday($response->getResponse()["birthday"]));

        }
        else {
            //if user exists - go with the HWIOAuth way
            $user = parent::loadUserByOAuthUserResponse($response);

            $user->setNom($response->getResponse()["first_name"]);
            $user->setPrenom($response->getResponse()["last_name"]);
            $user->setGender($response->getResponse()["gender"]);
            $user->setCountry($response->getResponse()["location"]);
            $user->setAge($this->getAgefromBirthday($response->getResponse()["birthday"]));

            $serviceName = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

            //update access token
            $user->$setter($response->getAccessToken());
        }
        echo 'THIS IS A TEST';
        print_r($response->getResponse()["location"]);
        $this->entityManager->persist($user);
        $this->entityManager->flush($user);

        return $user;
    }

    // This function returns the age from a birthday date
    public function getAgefromBirthday($date) {
      //explode the date to get month, day and year
      $birthDate = explode("/", $date);
      //get age from date or birthdate
      $age = (date("md", date("U", mktime(0, 0, 0, $birthDate[0], $birthDate[1], $birthDate[2]))) > date("md")
        ? ((date("Y") - $birthDate[2]) - 1)
        : (date("Y") - $birthDate[2]));
      return $age;
    }

}