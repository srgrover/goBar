<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\RegisterType;
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

    /**
     * @Route("/registro", name="registro_usuario")
     * @param Request $peticion
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registroAction(Request $peticion)
    {
        $usuario = new Usuario();
        // Crear el formulario a partir de la clase
        $formulario = $this->createForm(RegisterType::class, $usuario);
        // Procesar el formulario si se ha enviado con un POST
        $formulario->handleRequest($peticion);
        // Si se ha enviado y el contenido es válido, guardar los cambios
//        if ($formulario->isSubmitted() && $formulario->isValid()) {
//            // Obtener el EntityManager
//            $em = $this->getDoctrine()->getManager();
//            $helper =  $password = $this->container->get('security.password_encoder');
//            $usuario->setPassword($helper->encodePassword($usuario, $usuario->getPassword()));
//            // Asegurarse de que se tiene en cuenta el nuevo tipo de vehículo
//            $em->persist($usuario);
//            // Guardar los cambios
//            $em->flush();
//            // Redirigir al usuario a la lista
//            return new RedirectResponse(
//                $this->generateUrl('usuarios_listar')
//            );
//        }
        return $this->render('security/registro.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }
}
