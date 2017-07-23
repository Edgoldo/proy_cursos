<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use CursosBundle\Form\PersonaType;
use CursosBundle\Form\UsuarioType;
use CursosBundle\Form\TelefonoType;
use CursosBundle\Entity\Persona;
use CursosBundle\Entity\Usuario;
use CursosBundle\Entity\Telefono;
use CursosBundle\Entity\Curso;
use CursosBundle\Entity\PersonaCurso;

class PersonaController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:Persona");
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        $personaCurso_bd = $em->getRepository("CursosBundle:PersonaCurso");
        $persona = $persona_bd->find($id);
        $usuario = $usuario_bd->findOneByPersona($persona);
        $telefono = $telefono_bd->findOneByPersona($persona);
        $personaCurso = $personaCurso_bd->findBy(["persona"=>$persona]);

        return $this->render('CursosBundle:Persona:persona_index.html.twig', [
            "persona" => $persona,
            "usuario" => $usuario,
            "telefono" => $telefono,
            "cursos" => $personaCurso
        ]);
    }

    public function registerAction(Request $request){
        $persona = new Persona();
        $form = $this->createForm(PersonaType::class, $persona);
        $form->add('usuario', UsuarioType::class, ["mapped"=>false]);
        $form->add('telefono', TelefonoType::class, ["mapped"=>false]);
        $form->add('personaCurso', EntityType::class, ["label"=>"Cursos Disponibles", "class"=>"CursosBundle:Curso", "mapped"=>false, "expanded"=>true, "multiple"=>true, "attr"=>["class"=>"radio"]]);

        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            // Creación de las diferentes entidades a registrar
            $usuario = new Usuario();
            $telefono = new Telefono();
            
            // Se genera un objeto para gestionar la base de datos sobre la variable $em
            $em = $this->getDoctrine()->getEntityManager();
            // Encriptación de la contraseña ingresada por el usuario, usando el servicio creado
            // en app.config.security.yml
            $factory = $this->get("security.encoder_factory");
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($form->get("usuario")->get("password")->getData(), $usuario->getSalt());
            // Obtención de los datos del formulario y almacenamiento en el objeto $persona
            $persona->setNombre($form->get("nombre")->getData());
            $persona->setApellido($form->get("apellido")->getData());
            $persona->setDireccion($form->get("direccion")->getData());
            // Obtención y almacenamiento de los datos del formulario relacionados con la entidad $usuario
            $usuario->setApodo($form->get("usuario")->get("apodo")->getData());
            $usuario->setCorreo($form->get("usuario")->get("correo")->getData());
            $usuario->setPassword($password);
            $usuario->setRol("ROLE_USER");
            $usuario->setPersona($persona);
            // Obtención de los datos del formulario y almacenamiento en el objeto $telefono
            $telefono->setNumero($form->get("telefono")->get("numero")->getData());
            $telefono->setPersona($persona);
            // Se almacenan los datos de los objetos en la base de datos
            $em->persist($persona);
            $em->persist($usuario);
            $em->persist($telefono);
            $flush = $em->flush();
            
            if($flush == null){
                $persona_bd = $em->getRepository("CursosBundle:Persona");
                // Obtención del arreglo de cursos seleccionados en el formulario
                $cursos = $form->get("personaCurso")->getData();
                // Almacenamiento de la persona y los cursos en la tabla de relación entre Persona y Curso
                $flush = $persona_bd->savePersonaCurso($persona, $cursos);
                if($flush == null)
                    $status = "Los datos han sido registrados!";
                else
                    $status = "Ocurrió un error al registrar un curso";
            }
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
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        // Busca en la tabla persona, el id de la persona a editar
        $persona = $persona_bd->find($id);
        $usuario = $usuario_bd->findOneByPersona($persona);
        $telefono = $telefono_bd->findOneByPersona($persona);

        $form = $this->createForm(PersonaType::class, $persona);
        $form->add('usuario', UsuarioType::class, ["mapped"=>false, "data"=>$usuario]);
        $form->add('telefono', TelefonoType::class, ["mapped"=>false, "data"=>$telefono]);
        // Se hace la petición y obtención de la información suministrada en el formulario
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            // Se genera un objeto para gestionar la base de datos sobre la variable $em
            $em = $this->getDoctrine()->getEntityManager();
            // Encriptación de la contraseña ingresada por el usuario, usando el servicio creado
            // en app.config.security.yml
            $factory = $this->get("security.encoder_factory");
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword($form->get("usuario")->get("password")->getData(), $usuario->getSalt());
                
            // Obtención de los datos del formulario y almacenamiento en el objeto $persona
            $persona->setNombre($form->get("nombre")->getData());
            $persona->setApellido($form->get("apellido")->getData());
            $persona->setDireccion($form->get("direccion")->getData());
            // Obtención y almacenamiento de los datos del formulario relacionados con la entidad $usuario
            $usuario->setApodo($form->get("usuario")->get("apodo")->getData());
            $usuario->setCorreo($form->get("usuario")->get("correo")->getData());
            $usuario->setPassword($password);
            $usuario->setRol("ROLE_USER");
            $usuario->setPersona($persona);
            // Obtención de los datos del formulario y almacenamiento en el objeto $telefono
            $telefono->setNumero($form->get("telefono")->get("numero")->getData());
            $telefono->setPersona($persona);
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($persona);
            $em->persist($usuario);
            $em->persist($telefono);
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($persona);
            $em->persist($usuario);
            $em->persist($telefono);
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
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        $personaCurso_bd = $em->getRepository("CursosBundle:PersonaCurso");
        $persona = $persona_bd->find($id);
        $usuarios = $usuario_bd->findBy(["persona"=>$persona]);
        $telefonos = $telefono_bd->findBy(["persona"=>$persona]);
        $personaCursos = $personaCurso_bd->findBy(["persona"=>$persona]);

        foreach($usuarios as $usuario)
            $em->remove($usuario);

        foreach($telefonos as $telefono)
            $em->remove($telefono);

        foreach($personaCursos as $personaCurso)
            $em->remove($personaCurso);

        $em->remove($persona);
        $flush = $em->flush();

        if($flush == null)
                $status = "Los datos han sido eliminados con éxito!";
            else
                $status = "Falló la eliminación del perfil, intente nuevamente";

        return $this->redirectToRoute("logout");
    }
}
