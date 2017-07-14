<?php

namespace AppBundle\Controller;

use AppBundle\Model\PlayersQuery;
use authentication\AuthBundle\authenticationAuthBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PlayerController extends Controller
{
    /**
     * @Route("/players_list", name="playerslist")
     */
    public function showAction(Request $request)
    {
        $cookies = $request->cookies;

        if ($cookies->get('token_id') !== $this->get('session')->get('token_id'))
        {
            return $this->redirecttoRoute('login');
        }

        $players = PlayersQuery::create()->find();

        return $this->render('@App/list.html.twig', [
            'players' => $players

        ]);
    }
}
