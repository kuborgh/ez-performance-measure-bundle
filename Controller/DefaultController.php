<?php

namespace Kuborgh\Bundle\MeasureBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('KuborghMeasureBundle:Default:index.html.twig', array('name' => $name));
    }
}
