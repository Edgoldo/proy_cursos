<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Curso;
use CursosBundle\Form\CursoType;

class CursoController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction(){
        // Genera la entidad que hace la gestión de consulta y almacenamiento en la bd
        $em = $this->getDoctrine()->getEntityManager();
        // Realiza la conexión con la tabla curso a través de la entidad Curso y la entidad de gestión
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        // Busca todos los registros de la tabla cursos y las almacena en el arreglo cursos
        $cursos = $curso_bd->findAll();

        return $this->render('CursosBundle:Curso:curso_index.html.twig', [
            "cursos" => $cursos
        ]);
    }

    public function registerAction(Request $request){
        // Se crea una nueva entidad Curso
        $curso = new Curso();
        // Se crea el formulario del tipo formulario de cursos
        $form = $this->createForm(CursoType::class, $curso);
        // Se hace la petición y obtención de la información suministrada en el formulario
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            // Se genera un objeto para gestionar la base de datos sobre la variable $em
            $em = $this->getDoctrine()->getEntityManager();
                
            // Obtención de los datos del formulario y almacenamiento en el objeto $curso
            $curso->setTitulo($form->get("titulo")->getData());
            $curso->setDescripcion($form->get("descripcion")->getData());
            $curso->setDuracion($form->get("duracion")->getData());
            $curso->setParticipantes($form->get("participantes")->getData());
                    
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($curso);
            $flush = $em->flush();
            
            if($flush == null)
                $status = "Los datos han sido registrados!";
            else
                $status = "No se realizó el registro. Verifique los datos ingresados";
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("curso_index");
        }else
            $status = "Los datos del registro no son válidos";
        
        return $this->render('CursosBundle:Curso:curso_register.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function editAction(Request $request, $id){
        // Genera la entidad que hace la gestión de consulta y almacenamiento en la bd
        $em = $this->getDoctrine()->getEntityManager();
        // Realiza la conexión con la tabla curso a través de la entidad Curso y la entidad de gestión
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        // Busca en la tabla curso, el id del curso a editar
        $curso = $curso_bd->find($id);
         // Se crea el formulario del tipo formulario de cursos
        $form = $this->createForm(CursoType::class, $curso);
        // Se hace la petición y obtención de la información suministrada en el formulario
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
                
            // Obtención de los datos del formulario y almacenamiento en el objeto $curso
            $curso->setTitulo($form->get("titulo")->getData());
            $curso->setDescripcion($form->get("descripcion")->getData());
            $curso->setDuracion($form->get("duracion")->getData());
            $curso->setParticipantes($form->get("participantes")->getData());
                    
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($curso);
            $flush = $em->flush();
            
            if($flush == null)
                $status = "Los datos han sido registrados!";
            else
                $status = "No se realizó el registro. Verifique los datos ingresados";
            // Almacena el mensaje de la sesión y en caso de que todo esté correcto
            // genera y activa la sesión
            $this->session->getFlashBag()->add("status", $status);
            return $this->redirectToRoute("curso_index");
        }else
            $status = "Los datos del registro no son válidos";

        return $this->render('CursosBundle:Curso:curso_edit.html.twig', [
            "form" => $form->createView()
        ]);
    }

    public function deleteAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        $curso = $curso_bd->find($id);

        if(count($curso->getPersonaCurso()) == 0){
            $em->remove($curso);
            $em->flush();
        }

        return $this->redirectToRoute("curso_index");
    }
}
