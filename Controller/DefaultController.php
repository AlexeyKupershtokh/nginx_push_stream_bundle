<?php

namespace Alawar\NginxPushStreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function getSubUrlsAction($tokens)
    {
        return new Response(json_encode($tokens));
    }
}
