<?php

namespace Alawar\NginxPushStreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('NginxPushStreamBundle:Default:index.html.twig', array('name' => $name));
    }
}
