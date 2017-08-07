<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
     /**
     * @Route("/entrar", name="entrar")
     */
    public function entrarAction(){
        //Si el usuario está logueado se redirecciona a la página principal
        if(is_object($this->getUser())){
            return $this->redirect('inicio');
        }
        $autenticationUtils = $this->get('security.authentication_utils');  //Utiles para autenticacion
        $error = $autenticationUtils->getLastAuthenticationError();         //Capturamos el error
        $lastUserName = $autenticationUtils->getLastUsername();             //Capturamos el usuario del error
        return $this->render(':security:entrar.html.twig', [
            'last_username' => $lastUserName,
            'error' => $error
        ]);
    }
}
