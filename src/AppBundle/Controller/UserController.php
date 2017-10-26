<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
     /**
     * @Route("/perfil", name="perfil")
     */
    public function perfilAction(){
        return $this->render('user/perfil.html.twig', [

        ]);
    }

    /**
     * @Route("/perfil/editar", name="editar_perfil")
     * @param Request $peticion
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registroAction(Request $peticion)
    {
        $usuario = $this->getUser();

        $usuario_image = $usuario->getImagenPerfil();
        // Crear el formulario a partir de la clase
        $formulario = $this->createForm(UserType::class, $usuario);
        // Procesar el formulario si se ha enviado con un POST
        $formulario->handleRequest($peticion);
        // Si se ha enviado y el contenido es válido, guardar los cambios
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // Obtener el EntityManager
            $em = $this->getDoctrine()->getManager();

                //Fichero subido
                $imagenPerfil = $formulario["imagenPerfil"]->getData();
                if(!empty($imagenPerfil) && $imagenPerfil != null){
                    $ext = $imagenPerfil->guessExtension();
                    if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png' || $ext == 'gif'){
                        $nombre_imagen = $usuario->getId().time().'.'.$ext;
                        $imagenPerfil->move("uploads/users/img/profile", $nombre_imagen);
                        $usuario->setImagenPerfil($nombre_imagen);
                    }
                }else{
                    $usuario->setImagenPerfil($usuario_image);
                }

                $em->persist($usuario);
                $flush = $em->flush();
                if($flush == null){ //No devuelve ningun error
                    $this->addFlash('estado', 'Los cambios se han guardado correctamente');
                    return $this->redirectToRoute("perfil");
                }else{
                    $this->addFlash('error', 'Hubo algún problema al editar tu perfil');
                }

        }
        return $this->render('user/edit.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }
}
