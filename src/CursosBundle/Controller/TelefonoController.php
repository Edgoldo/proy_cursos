<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Telefono;
use CursosBundle\Form\TelefonoType;

class TelefonoController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        $telefonos = $telefono_bd->findAll();

        return $this->render('CursosBundle:Telefono:index.html.twig', [
            "telefonos" => $telefonos
        ]);
    }

    public function registerAction(Request $request){
        $telefono = new Telefono();
        $form = $this->createForm(TelefonoType::class, $telefono);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            // Se genera un objeto para gestionar la base de datos sobre la variable $em
            $em = $this->getDoctrine()->getEntityManager();

            // Encuentra la entidad persona a la que está relacionada el telefono
            $persona_bd = $em->getRepository("CursosBundle:Persona");
            $persona = $persona_bd->find($form->get("persona")->getData());
            // Obtención de los datos del formulario y almacenamiento en el objeto $telefono
            $telefono->setNumero($form->get("numero")->getData());
            $telefono->setPersona($persona);
                    
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($telefono);
            $flush = $em->flush();
            
            if($flush == null)
                $status = "Los datos han sido registrados!";
            else
                $status = "No se realizó el registro. Verifique los datos ingresados";
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            #return $this->redirectToRoute("telefono_consulta");
        }else
            $status = "Los datos del registro no son válidos";

        return $this->render('CursosBundle:Telefono:register.html.twig', [
            "form" => $form->createView()
        ]);
    }
}
