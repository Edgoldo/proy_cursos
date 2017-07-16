<?php

namespace CursosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SistemaController extends Controller
{
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
}
