<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
     /**
     * @Route("/perfil", name="perfil")
     */
    public function perfilAction(){
        return $this->render('user/perfil.html.twig', [

        ]);
    }
}
