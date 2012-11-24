<?php

namespace Api\CommonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('ApiCommonBundle:Default:index.html.twig', array('name' => $name));
    }
}
