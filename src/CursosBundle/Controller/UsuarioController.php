<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Usuario;
use CursosBundle\Form\UsuarioType;

class UsuarioController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function registerAction(Request $request){
        $usuario = new Usuario();
        $form = $this->createForm(UsuarioType::class, $usuario);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                // Se genera un objeto para gestionar la base de datos sobre la variable $em
                $em = $this->getDoctrine()->getEntityManager();
                $usuario_bd = $em->getRepository("CursosBundle:Usuario");
                $usuario_exist = $usuario_bd->findOneBy(["apodo"=>$form->get("apodo")->getData()]);
                $correo_exist = $usuario_bd->findOneBy(["correo"=>$form->get("correo")->getData()]);

                if(count($usuario_exist) == 0 and count($correo_exist) == 0){
                    // Encriptación de la contraseña ingresada por el usuario, usando el servicio creado
                    // en app.config.security.yml
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($usuario);
                    $password = $encoder->encodePassword($form->get("password")->getData(), $usuario->getSalt());
                    // Encuentra la entidad persona a la que está relacionada el usuario
                    $persona_bd = $em->getRepository("CursosBundle:Persona");
                    $persona = $persona_bd->find($form->get("persona")->getData());
                    // Obtención de los datos del formulario y almacenamiento en el objeto $usuario
                    $usuario->setApodo($form->get("apodo")->getData());
                    $usuario->setCorreo($form->get("correo")->getData());
                    $usuario->setPassword($password);
                    $usuario->setRol("ROLE_USER");
                    $usuario->setPersona($persona);
                    
                    // Se almacenan los datos del objeto en la base de datos
                    $em->persist($usuario);
                    $flush = $em->flush();
            
                    if($flush == null)
                        $status = "Los datos han sido registrados!";
                    else
                        $status = "No se realizó el registro. Verifique los datos ingresados";
                }else if(count($usuario_exist) != 0)
                    $status = "El apodo ya se encuentra registrado. Por favor introduzca otro apodo";
                else if(count($correo_exist) != 0)
                    $status = "El correo ya se encuentra registrado. Por favor verifique su correo";
            }else
                $status = "Los datos del registro no son válidos";
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            #return $this->redirectToRoute("usuario_consulta");
        }
        return $this->render('CursosBundle:Usuario:register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function loginAction(Request $request)
    {
        $authenticationsUtils = $this->get("security.authentication_utils");
        $error = $authenticationsUtils->getLastAuthenticationError();
        $lastUsername = $authenticationsUtils->getLastUsername();

        return $this->render('CursosBundle:Usuario:login.html.twig', [
            "error" => $error,
            "last_username" => $lastUsername
        ]);
    }
}
