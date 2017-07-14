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

              var_dump(md5($password_request . $checkuser[0]->getSalt()), $checkuser[0]->getPassword());
              if (md5($password_request . $checkuser[0]->getSalt()) == $checkuser[0]->getPassword()) {
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

        /*if (TRUE === $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('playerslist');
        }*/

        //$user= new Users();
        /*$token = new UsernamePasswordToken($user->getUsername(),$user->getPassword(),"main",$user->getRoles());
        $this->get('security.token_storage')->setToken($token);
        $this->get('session')->set('_security_secured_area', serialize($token));*/


        return $this->render('authenticationAuthBundle::login.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction(){

        return $this->redirectToRoute('login');
    }
    /**
     * @Route("/adduser")
     */
    public function addUser(){

        $user= new Users();
        $user->setUsername('max');
        $user->setEmail('max.ololo@mail.com');
        //$encoder = $this->container->get('security.password_encoder');
        $salt = uniqid(mt_rand(), true);
        $hash = md5(12345678);
        $saltedHash = md5($hash . $salt);
        //$password = $encoder->encodePassword($user,'ololo123');
        $user->setPassword($saltedHash);
        $user->setSalt($salt);
        $user->save();

        return $this->redirecttoRoute('login');
    }


}
