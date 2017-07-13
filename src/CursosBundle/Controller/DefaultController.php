<?php

namespace CursosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('CursosBundle:Default:index.html.twig');
    }
}
