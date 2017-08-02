<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use CursosBundle\Entity\Curso;
use CursosBundle\Entity\PersonaCurso;
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

    public function excelReportAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        $cursos = $curso_bd->findAll();
        $num = count($cursos);
        // Solicita el servicio de excel
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $sharedStyle1 = new \PHPExcel_Style();
        $sharedStyle2 = new \PHPExcel_Style();
        $sharedStyle3 = new \PHPExcel_Style();

        $sharedStyle1->applyFromArray(
            ['fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['argb' => '004169E1']
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_THIN],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM]
            ],
            'font' => [
                'size' => 14,
                'color' => ['argb' => 'FFFFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        $sharedStyle2->applyFromArray(
            ['fill' => [
                'type'   => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['argb' => '00edd46f']
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_THIN],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM]
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ]
        ]);
        $sharedStyle3->applyFromArray(
            ['font' => [
                'bold' => true,
                'size' => 16,
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_NONE],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_NONE]
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle1, "A2:D2");
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle2, "A3:D".(string)($num+2));
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle3, "A1:D1");
        $phpExcelObject->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
        $phpExcelObject->getActiveSheet()->mergeCells("A1:D1");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Listado de Cursos Registrados en el Sistema')
            ->setCellValue('A2', 'Título')
            ->setCellValue('B2', 'Descripción')
            ->setCellValue('C2', 'Semanas de Duración')
            ->setCellValue('D2', 'Capacidad de Participantes');
        $i = 2;
        foreach($cursos as $curso){
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.(string)($i+1), $curso->getTitulo())
                ->setCellValue('B'.(string)($i+1), $curso->getDescripcion())
                ->setCellValue('C'.(string)($i+1), $curso->getDuracion())
                ->setCellValue('D'.(string)($i+1), $curso->getParticipantes());
            $numeros = "";
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Hoja 1');
        // Define el indice de página al número 1, para abrir esa página al abrir el archivo
        $phpExcelObject->setActiveSheetIndex(0);

        // Crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // Envia la respuesta del controlador
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // Agrega los headers requeridos
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ReporteCursos.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function pdfReportAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        $cursos = $curso_bd->findAll();
        $num = count($cursos);
        // Solicita el servicio de excel
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $sharedStyle1 = new \PHPExcel_Style();
        $sharedStyle2 = new \PHPExcel_Style();
        $sharedStyle3 = new \PHPExcel_Style();

        $sharedStyle1->applyFromArray(
            ['fill' => [
                'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['argb' => '0048764A']
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_THIN],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM]
            ],
            'font' => [
                'size' => 14,
                'color' => ['argb' => 'FFFFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        $sharedStyle2->applyFromArray(
            ['fill' => [
                'type'   => \PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['argb' => '00cfc7bc']
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_THIN],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_MEDIUM]
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ]
        ]);
        $sharedStyle3->applyFromArray(
            ['font' => [
                'bold' => true,
                'size' => 16,
            ],
            'borders' => [
                'bottom' => ['style' => \PHPExcel_Style_Border::BORDER_NONE],
                'right' => ['style' => \PHPExcel_Style_Border::BORDER_NONE]
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ]
        ]);
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle1, "A2:D2");
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle2, "A3:D".(string)($num+2));
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle3, "A1:D1");
        $phpExcelObject->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getRowDimension(1)->setRowHeight(20);
        $phpExcelObject->getActiveSheet()->mergeCells("A1:D1");

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Listado de Cursos Registrados en el Sistema')
            ->setCellValue('A2', 'Título')
            ->setCellValue('B2', 'Descripción')
            ->setCellValue('C2', 'Semanas de Duración')
            ->setCellValue('D2', 'Capacidad de Participantes');
        $i = 2;
        foreach($cursos as $curso){
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.(string)($i+1), $curso->getTitulo())
                ->setCellValue('B'.(string)($i+1), $curso->getDescripcion())
                ->setCellValue('C'.(string)($i+1), $curso->getDuracion())
                ->setCellValue('D'.(string)($i+1), $curso->getParticipantes());
            $numeros = "";
            $i++;
        }
        // Define el indice de página al número 1, para abrir esa página al abrir el archivo
        $phpExcelObject->setActiveSheetIndex(0);

        $rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;
        $rendererLibrary = 'mPDF';
        $rendererLibraryPath = dirname(__FILE__).'/../../../vendor/mpdf/mpdf';

        if (!\PHPExcel_Settings::setPdfRenderer(
            $rendererName,
            $rendererLibraryPath
        )) {
            die(
                'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                '<br />' .
                'at the top of this script as appropriate for your directory structure'
            );
        }
        // Crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'PDF');
        // Envia la respuesta del controlador
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // Agrega los headers requeridos
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ReporteCursos.pdf'
        );

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=0');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
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

    public function suscribeAction(Request $request, $id){
        $personaCurso = new PersonaCurso();
        // Genera la entidad que hace la gestión de consulta y almacenamiento en la bd
        $em = $this->getDoctrine()->getEntityManager();
        // Realiza la conexión con la tabla curso a través de la entidad Curso y la entidad de gestión
        $curso_bd = $em->getRepository("CursosBundle:Curso");
        // Busca en la tabla curso, el id del curso a editar
        $curso = $curso_bd->find($id);
        $persona = $this->getUser()->getPersona();
        $personaCurso->setPersona($persona);
        $personaCurso->setCurso($curso);
        // Se almacenan los datos del objeto en la base de datos
        $em->persist($personaCurso);
        $flush = $em->flush();
        
        if($flush == null)
            $status = "Bienvenido al curso ".(string)$curso->getTitulo();
        else
            $status = "Ocurrió un error al suscribirse, intente nuevamente";
        // Almacena el mensaje de la sesión y en caso de que todo esté correcto
        // genera y activa la sesión
        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("curso_index");
    }

    public function unsuscribeAction(Request $request, $id){
        // Genera la entidad que hace la gestión de consulta y almacenamiento en la bd
        $em = $this->getDoctrine()->getEntityManager();
        // Realiza la conexión con la tabla curso a través de la entidad Curso y la entidad de gestión
        $personaCurso_bd = $em->getRepository("CursosBundle:PersonaCurso");
        // Busca en la tabla curso, el id del curso
        $curso = $personaCurso_bd->findOneById($id);
        
        $em->remove($curso);
        $flush = $em->flush();
        
        if($flush == null)
            $status = "Lamentamos que abandones el curso ".(string)$curso->getCurso()->getTitulo();
        else
            $status = "Ocurrió un error al eliminar la suscripción, intente nuevamente";
        // Almacena el mensaje de la sesión y en caso de que todo esté correcto
        // genera y activa la sesión
        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("persona_index", ["id" => $curso->getPersona()->getId()]);
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
