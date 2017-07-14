<?php

namespace authentication\AuthBundle\Controller;

use authentication\AuthBundle\Model\Users;
use authentication\AuthBundle\Model\UsersQuery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

class LoginController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {

        if ($request->getMethod() == 'POST') {

            $user_request = $request->request->get('u_name');

            $password_request = md5($request->request->get('p_word'));

            $checkuser = UsersQuery::create()->findByUsername($user_request);

          if($checkuser->count()==1) {


              if (md5($password_request . $checkuser[0]->getSalt()) == $checkuser[0]->getPassword()) {

                  $session = $request->getSession();

                  $response = new Response();

                  $token = uniqid(mt_rand(), true);
                  $session->set('token_id', $token);

                  $response->headers->setCookie(new Cookie('token_id', $token));
                  $response->send();

                  return $this->redirecttoRoute('playerslist');

              }
              else
                  return $this->render('authenticationAuthBundle::login.html.twig',[
                      'error' => "Incorrect password"
                  ]);

          }
          else
              return $this->render('authenticationAuthBundle::login.html.twig',[
                  'error' => 'Bad credentials'
              ]);


        }

        return $this->render('authenticationAuthBundle::login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(Request $request){

        $session = $request->getSession();

        $session->remove('token_id');

        //$session->clear();
        return $this->redirectToRoute('login');
    }
    /**
     * @Route("/adduser")
     */
    public function addUser(){

        $user= new Users();

        $user->setUsername('max');

        $user->setEmail('max.ololo@mail.com');

        $salt = uniqid(mt_rand(), true);

        $hash = md5(12345678);

        $saltedHash = md5($hash . $salt);

        $user->setPassword($saltedHash);
        $user->setSalt($salt);
        $user->save();

        return $this->redirecttoRoute('login');
    }


}
