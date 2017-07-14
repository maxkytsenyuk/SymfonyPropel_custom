<?php

namespace AppBundle\Controller;

use AppBundle\Model\PlayersQuery;
use authentication\AuthBundle\authenticationAuthBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class PlayerController extends Controller
{
    /**
     * @Route("/players_list", name="playerslist")
     */
    public function showAction()
    {
        //$user = $this->container->get('security.token_storage')->getToken()->getUser();
       // var_dump($user);
        $players = PlayersQuery::create()->find();


        return $this->render('@App/list.html.twig', [
            'players' => $players

        ]);
    }
}
