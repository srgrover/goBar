<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Usuario;
use AppBundle\Form\Type\RegisterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @Route("/comprobar", name="login_check")
     * @Route("/salir", name="salir")
     */
    public function comprobarAction() {
    }

    /**
     * @Route("/registro", name="nuevo_usuario")
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
        if ($formulario->isSubmitted() && $formulario->isValid()) {
            // Obtener el EntityManager
            $em = $this->getDoctrine()->getManager();
            $query = $em->createQueryBuilder()
                ->select('u')
                ->from('AppBundle:Usuario', 'u')
                ->where('u.email = :email')
                ->setParameter('email', $formulario->get("email")->getData())
                ->getQuery();
            $usuario_isset = $query->getResult();
            if(count($usuario_isset) == 0){
                $usuario->setPassword('qwertyuiop159');
                $usuario->setImagenPerfil(null);
                $usuario->setActivacion(new \DateTime("now"));
                $em->persist($usuario);
                $flush = $em->flush();
                if($flush == null){ //No devuelve ningun error
                    $expire = 30;
                    if($usuario->getToken() && $usuario->getTokenValidity() > new \Datetime()){
                        return $this->redirectToRoute('entrar');
                    }else{
                        // guardar token
                        $token = bin2hex(random_bytes(16));
                        $usuario->setToken($token);
                        $validity = new \DateTime();
                        $validity->add(new \DateInterval('PT'.$expire.'M'));
                        $usuario->setTokenValidity($validity);

                        //Enviar correo
                        $message = \Swift_Message::newInstance()
                            ->setSubject('Completa tu registro en FindBAR!')
                            ->setFrom($this->getParameter('mailer_user'))
                            ->setTo($usuario->getEmail())
                            ->setBody(
                                $this->renderView('security/confirmaEmail.html.twig',[
                                    'usuario' => $usuario,
                                    'token' => $token
                                ]), 'text/html'
                            );
                        $this->get('mailer')->send($message);

                        $this->get('doctrine')->getManager()->flush();                    }
                    $this->addFlash('estado', 'Estás a un paso de completar tu registro. Revisa tu email y confirma tu cuenta');
                    return $this->redirectToRoute("entrar");
                }else{
                    $this->addFlash('error', 'Hubo algún problema con el registro de tu usuario');
                }
            }else{
                $this->addFlash('error', 'Este usuario ya existe. Vuelve a intentarlo con otro email');
            }
        }
        return $this->render('security/registro.html.twig', [
            'formulario' => $formulario->createView()
        ]);
    }

    /**
     * @Route("/confirmar/{usuario}/{token}", name="confirmar_usuario_pass", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @param $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function confirmarAction(Request $request, Usuario $usuario, $token){
        /**
         * @var Usuario|null
         */
        if ($usuario->getToken() == $token && $usuario->getTokenValidity() > new \DateTime()) {
            $form = $this->createForm('AppBundle\Form\Type\NewPassType');
            $form->handleRequest($request);
            $error = '';
            if ($form->isSubmitted() && $form->isValid()) {
                //codificar la nueva contraseña y asignarla al usuario
                $password = $this->get('security.password_encoder')
                    ->encodePassword($usuario, $form->get('newPassword')->get('first')->getData());
                $usuario->setPassword($password);
                $usuario->setToken(null);
                $usuario->setTokenValidity(null);
                $usuario->setEstado(true);
                $this->getDoctrine()->getManager()->flush();
                // indicar que los cambios se han realizado con éxito y volver a la página de inicio
                $this->addFlash('estado', 'Te has registrado correctamente. Inicia sesión en cualquier momento con tu nueva contraseña');
                return $this->redirectToRoute('entrar');
            }
        }else{
            return $this->redirectToRoute('entrar');
        }
        return $this->render(
            'security/restaurarPass.html.twig', [
                'usuario' => $usuario,
                'form' => $form->createView(),
                'error' => $error
            ]
        );
    }

    /**
     * @Route("/restaurar", name="restaurar_usuario", methods={"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function resetAction(Request $request){
        $form = $this->createForm('AppBundle\Form\ResetEmailType');
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $usuario = $this->getDoctrine()->getManager()->getRepository('AppBundle:Usuario')->findOneBy(['email' => $email]);
            if($usuario === null){
                $this->addFlash('error','El email introducido no pertenece a ningún usuario registrado en esta aplicación');
                return $this->redirectToRoute('restaurar_usuario');
            }else{
                return $this->redirectToRoute('restaurar_usuario_pass',['usuario'=> $usuario->getId()]);
            }
        }
        return $this->render(
            ':security:introduceEmail.twig', [
                'form' => $form->createView(),
            ]
        );
    }
    /**
     * @Route("/restaurar/{usuario}", name="restaurar_usuario_pass", methods={"GET", "POST"})
     * @param Request $request
     * @param Usuario $usuario
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function resetPassAction(Request $request, Usuario $usuario){
        $expire = 30;
        if($usuario->getToken() && $usuario->getTokenValidity() > new \Datetime()){
            return $this->redirectToRoute('entrar');
        }else{
            $token = bin2hex(random_bytes(16));
            $usuario->setToken($token);
            $validity = new \DateTime();
            $validity->add(new \DateInterval('PT'.$expire.'M'));
            $usuario->setTokenValidity($validity);
            $message = \Swift_Message::newInstance()
                ->setSubject('Completa tu registro en PickmeUP!')
                ->setFrom($this->getParameter('mailer_user'))
                ->setTo($usuario->getEmail())
                ->setBody(
                    $this->renderView('security/confirmaEmail.html.twig',[
                        'usuario' => $usuario,
                        'token' => $token
                    ])
                );
            $this->get('mailer')->send($message);
            // guardar token
            $this->get('doctrine')->getManager()->flush();                    }
        $this->addFlash('estado', 'Se han enviado instrucciones para la restauración de su contraseña al email indicado');
        return $this->redirectToRoute("entrar");
    }
}
