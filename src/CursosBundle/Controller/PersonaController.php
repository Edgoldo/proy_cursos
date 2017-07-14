<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Persona;
use CursosBundle\Form\PersonaType;
use CursosBundle\Entity\Usuario;
use CursosBundle\Form\UsuarioType;
use CursosBundle\Entity\Telefono;
use CursosBundle\Form\TelefonoType;

class PersonaController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:persona");
        $personas = $persona_bd->findAll();

        return $this->render('CursosBundle:Persona:index.html.twig', [
            "personas" => $personas
        ]);
    }

    public function registerAction(Request $request){
        $persona = new persona();
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);

        if($form->isSubmitted()){
            if($form->isValid()){
                // Se genera un objeto para gestionar la base de datos sobre la variable $em
                $em = $this->getDoctrine()->getEntityManager();
                
                // Obtención de los datos del formulario y almacenamiento en el objeto $persona
                $persona->setNombre($form->get("nombre")->getData());
                $persona->setApellido($form->get("apellido")->getData());
                $persona->setDireccion($form->get("direccion")->getData());
                    
                // Se almacenan los datos del objeto en la base de datos
                $em->persist($persona);
                $flush = $em->flush();
            
                if($flush == null)
                    $status = "Los datos han sido registrados!";
                else
                    $status = "No se realizó el registro. Verifique los datos ingresados";
            }else
                $status = "Los datos del registro no son válidos";
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            #return $this->redirectToRoute("persona_consulta");
        }
        return $this->render('CursosBundle:Persona:register.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
