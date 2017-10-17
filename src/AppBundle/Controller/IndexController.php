<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class IndexController extends Controller
{
     /**
     * @Route("/inicio", name="inicio")
     */
    public function inicioAction(){
        return $this->render(':default:inicio.html.twig', [

        ]);
    }
}
