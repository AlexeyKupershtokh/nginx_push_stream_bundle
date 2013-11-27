<?php

namespace Alawar\NginxPushStreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function getSubUrlsAction($tokens)
    {
        return new Response(json_encode($tokens));
    }

    public function pubAction(Request $request)
    {
        $this->get('nginx_push_stream.default_connection');
        return new Response(json_encode('ok'));
    }
}
