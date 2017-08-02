<?php

namespace CursosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Persona;
use CursosBundle\Entity\Usuario;
use CursosBundle\Entity\Telefono;
use CursosBundle\Entity\Curso;

class SistemaController extends Controller
{
    private $session;

    public function __construct(){
        $this->session = new Session();
    }

    public function indexAction()
    {
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

    public function infoAction()
    {
        return $this->render('CursosBundle:Sistema:sistema_info.html.twig');
    }

    public function testAction()
    {
        // Se genera un objeto para gestionar la base de datos sobre la variable $em
        $em = $this->getDoctrine()->getEntityManager();
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $usuario = $usuario_bd->findBy(["apodo"=>"admin"]);
        if ($usuario == null){
            // Creación del administrador del sistema
            // Creación de las diferentes entidades a registrar
            $persona = new Persona();
            $usuario = new Usuario();
            $telefono = new Telefono();
            // Encriptación de la contraseña ingresada por el usuario, usando el servicio creado
            // en app.config.security.yml
            $factory = $this->get("security.encoder_factory");
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword("admin123", $usuario->getSalt());
            // Almacenamiento de datos en el objeto $persona
            $persona->setNombre("Admin Name");
            $persona->setApellido("Admin Last Name");
            $persona->setDireccion("Admin Address");
            // Obtención y almacenamiento de los datos del formulario relacionados con la entidad $usuario
            $usuario->setApodo("admin");
            $usuario->setCorreo("admin@admin.com");
            $usuario->setPassword($password);
            $usuario->setRol("ROLE_ADMIN");
            $usuario->setPersona($persona);
            // Obtención de los datos del formulario y almacenamiento en el objeto $telefono
            $telefono->setNumero(00000000000);
            $telefono->setPersona($persona);
            // Se almacenan los datos de los objetos en la base de datos
            $em->persist($persona);
            $em->persist($usuario);
            $em->persist($telefono);
            $flush = $em->flush();
            
            if($flush != null){
                $status = "Ocurrió un problema al registrar los datos del usuario administrador. Intente nuevamente";
                return;
            }
            // Creación de un usuario
            // Creación de las diferentes entidades a registrar
            $persona = new Persona();
            $usuario = new Usuario();
            $telefono = new Telefono();
            // Encriptación de la contraseña ingresada por el usuario, usando el servicio creado
            // en app.config.security.yml
            $factory = $this->get("security.encoder_factory");
            $encoder = $factory->getEncoder($usuario);
            $password = $encoder->encodePassword("user123", $usuario->getSalt());
            // Almacenamiento de datos en el objeto $persona
            $persona->setNombre("User Name");
            $persona->setApellido("User Last Name");
            $persona->setDireccion("User Address");
            // Obtención y almacenamiento de los datos del formulario relacionados con la entidad $usuario
            $usuario->setApodo("User");
            $usuario->setCorreo("user@user.com");
            $usuario->setPassword($password);
            $usuario->setRol("ROLE_USER");
            $usuario->setPersona($persona);
            // Obtención de los datos del formulario y almacenamiento en el objeto $telefono
            $telefono->setNumero(00000000000);
            $telefono->setPersona($persona);
            // Se almacenan los datos de los objetos en la base de datos
            $em->persist($persona);
            $em->persist($usuario);
            $em->persist($telefono);
            $flush = $em->flush();
            
            if($flush != null){
                $status = "Ocurrió un problema al registrar los datos de un nuevo usuario. Intente nuevamente";
                return;
            }

            // Creación de un curso
            $curso = new Curso();
            // Obtención de los datos del formulario y almacenamiento en el objeto $curso
            $curso->setTitulo("Modelado de Datos");
            $curso->setDescripcion("Explicación y práctica de métodos para desarrollar modelos de sistemas.");
            $curso->setDuracion(8);
            $curso->setParticipantes(20);
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($curso);
            $flush = $em->flush();

            if($flush != null){
                $status = "Ocurrió un problema al registrar los datos del primer curso. Intente nuevamente";
                return;
            }

            // Creación de un curso
            $curso = new Curso();
            // Obtención de los datos del formulario y almacenamiento en el objeto $curso
            $curso->setTitulo("Ingeniería de Software");
            $curso->setDescripcion("Conceptos relacionados con diseño de software, metodologías, requerimientos, patrones de diseño, frameworks, entre otros.");
            $curso->setDuracion(12);
            $curso->setParticipantes(40);
            // Se almacenan los datos del objeto en la base de datos
            $em->persist($curso);
            $flush = $em->flush();

            if($flush != null){
                $status = "Ocurrió un problema al registrar los datos del segundo curso. Intente nuevamente";
                return;
            }

            if($flush == null)
                $status = "Los datos de la prueba han sido generados!";
            else
                $status = "Ocurrió un problema al registrar los datos. Intente nuevamente";
        }
        else
            $status = "Los datos para realizar la prueba del sistema ya han sido cargados.";
        // Almacena el mensaje de la sesión y en caso de que todo esté correcto
        // genera y activa la sesión
        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("login");
    }
}
