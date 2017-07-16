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

    public function indexAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:persona");
        $persona = $persona_bd->find($id);

        return $this->render('CursosBundle:Persona:persona_index.html.twig', [
            "persona" => $persona
        ]);
    }

    public function registerAction(Request $request){
        $persona = new persona();
        $form = $this->createForm(PersonaType::class, $persona);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
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
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("persona_index", ["id" => $persona->getId()]);
        }else
            $status = "Los datos del registro no son válidos";
        
        return $this->render('CursosBundle:Persona:persona_register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function editAction(Request $request, $id){
        // Genera la entidad que hace la gestión de consulta y almacenamiento en la bd
        $em = $this->getDoctrine()->getEntityManager();
        // Realiza la conexión con la tabla persona a través de la entidad Persona y la entidad de gestión
        $persona_bd = $em->getRepository("CursosBundle:Persona");
        // Busca en la tabla persona, el id de la persona a editar
        $persona = $persona_bd->find($id);

        $form = $this->createForm(PersonaType::class, $persona);
        // Se hace la petición y obtención de la información suministrada en el formulario
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
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
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("persona_index", ["id" => $persona->getId()]);
        }else
            $status = "Los datos del registro no son válidos";

        return $this->render('CursosBundle:Persona:persona_edit.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:Persona");
        $persona = $persona_bd->find($id);

        return $this->redirectToRoute("logout");
    }
}
