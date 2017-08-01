<?php

namespace CursosBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
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

    public function reportAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:Persona");
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        $personaCurso_bd = $em->getRepository("CursosBundle:PersonaCurso");
        $personas = $persona_bd->findAll();
        $num = count($personas);
        // Solicita el servicio de excel
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $sharedStyle1 = new \PHPExcel_Style();
        $sharedStyle2 = new \PHPExcel_Style();

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
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:G1");
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle2, "A2:G".(string)($num+1));
        $phpExcelObject->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Nombre')
            ->setCellValue('B1', 'Apellido')
            ->setCellValue('C1', 'Dirección')
            ->setCellValue('D1', 'Apodo')
            ->setCellValue('E1', 'Correo')
            ->setCellValue('F1', 'Teléfono')
            ->setCellValue('G1', 'Cursos Inscritos');
        $i = 1;
        foreach($personas as $persona){
            $usuario = $usuario_bd->findOneByPersona($persona);
            $telefonos = $telefono_bd->findBy(["persona"=>$persona]);
            $cursos = $personaCurso_bd->findBy(["persona"=>$persona]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.(string)($i+1), $persona->getNombre())
                ->setCellValue('B'.(string)($i+1), $persona->getApellido())
                ->setCellValue('C'.(string)($i+1), $persona->getDireccion())
                ->setCellValue('D'.(string)($i+1), $usuario->getApodo())
                ->setCellValue('E'.(string)($i+1), $usuario->getCorreo());
            $numeros = "";
            foreach($telefonos as $telefono)
                $numeros = $numeros.$telefono->getNumero().", ";
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.(string)($i+1), $numeros);
                $titulos = "";
                foreach($cursos as $curso)
                    $titulos = $titulos.$curso->getCurso()->getTitulo().". ";
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.(string)($i+1), $titulos);
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
            'ReportePersonas.xlsx'
        );

        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function pdfAction(){
        $em = $this->getDoctrine()->getEntityManager();
        $persona_bd = $em->getRepository("CursosBundle:Persona");
        $usuario_bd = $em->getRepository("CursosBundle:Usuario");
        $telefono_bd = $em->getRepository("CursosBundle:Telefono");
        $personaCurso_bd = $em->getRepository("CursosBundle:PersonaCurso");
        $personas = $persona_bd->findAll();
        $num = count($personas);
        // Solicita el servicio de excel
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();

        $sharedStyle1 = new \PHPExcel_Style();
        $sharedStyle2 = new \PHPExcel_Style();

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
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle1, "A1:G1");
        $phpExcelObject->getActiveSheet()->setSharedStyle($sharedStyle2, "A2:G".(string)($num+1));
        $phpExcelObject->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("C")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("D")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("E")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("F")->setAutoSize(true);
        $phpExcelObject->getActiveSheet()->getColumnDimension("G")->setAutoSize(true);

        $phpExcelObject->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Nombre')
            ->setCellValue('B1', 'Apellido')
            ->setCellValue('C1', 'Dirección')
            ->setCellValue('D1', 'Apodo')
            ->setCellValue('E1', 'Correo')
            ->setCellValue('F1', 'Teléfono')
            ->setCellValue('G1', 'Cursos Inscritos');
        $i = 1;
        foreach($personas as $persona){
            $usuario = $usuario_bd->findOneByPersona($persona);
            $telefonos = $telefono_bd->findBy(["persona"=>$persona]);
            $cursos = $personaCurso_bd->findBy(["persona"=>$persona]);
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('A'.(string)($i+1), $persona->getNombre())
                ->setCellValue('B'.(string)($i+1), $persona->getApellido())
                ->setCellValue('C'.(string)($i+1), $persona->getDireccion())
                ->setCellValue('D'.(string)($i+1), $usuario->getApodo())
                ->setCellValue('E'.(string)($i+1), $usuario->getCorreo());
            $numeros = "";
            foreach($telefonos as $telefono)
                $numeros = $numeros.$telefono->getNumero().", ";
            $phpExcelObject->setActiveSheetIndex(0)
                ->setCellValue('F'.(string)($i+1), $numeros);
                $titulos = "";
                foreach($cursos as $curso)
                    $titulos = $titulos.$curso->getCurso()->getTitulo().". ";
                $phpExcelObject->setActiveSheetIndex(0)
                    ->setCellValue('G'.(string)($i+1), $titulos);
            $i++;
        }
        $phpExcelObject->getActiveSheet()->setTitle('Hoja 1');
        // Define el indice de página al número 1, para abrir esa página al abrir el archivo
        $phpExcelObject->setActiveSheetIndex(0);

        //  Change these values to select the Rendering library that you wish to use
        //      and its directory location on your server
        //$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
        $rendererName = \PHPExcel_Settings::PDF_RENDERER_MPDF;
        //$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
        //$rendererLibrary = 'tcPDF5.9';
        $rendererLibrary = 'mPDF';
        //$rendererLibrary = 'domPDF0.6.0beta3';
        // /../../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/PDF/
        $rendererLibraryPath = dirname(__FILE__).'/../../../vendor/phpoffice/phpexcel/Classes/PHPExcel/Writer/PDF/mPDF.php';

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
        echo "** PASA **";
        // Redirect output to a client’s web browser (PDF)
        // header('Content-Type: application/pdf');
        // header('Content-Disposition: attachment;filename="01simple.pdf"');
        // header('Cache-Control: max-age=0');
        // $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
        // $objWriter->save('php://output');
        // exit;

        $phpExcelObject = \PHPExcel_IOFactory::createWriter($phpExcelObject, 'PDF');
        $phpExcelObject->writeAllSheets();
        $phpExcelObject->save('php://output');
        exit(0);

        // Crea el writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'PDF');
        // Envia la respuesta del controlador
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        // Agrega los headers requeridos
        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'ReportePersonas.pdf'
        );

        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=0');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
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
        $usuario = $usuario_bd->findOneByPersona($persona);
        $telefonos = $telefono_bd->findBy(["persona"=>$persona]);
        $personaCursos = $personaCurso_bd->findBy(["persona"=>$persona]);

        if($usuario)
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
        $this->session->getFlashBag()->add("status", $status);
        return $this->redirectToRoute("logout");
    }
}
