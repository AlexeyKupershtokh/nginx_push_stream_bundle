<?php

namespace Alawar\NginxPushStreamBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function getSubUrlsAction($tokens)
    {
        $tokensArray = explode(',', $tokens);
        $subUrls = $this->get('nginx_push_stream.default_connection')->getSubUrls($tokensArray);
        return $this->render('NginxPushStreamBundle:Default:index.html.twig', array('subUrls' => $subUrls));
        //return new Response(json_encode($subUrls), 200, array('Content-type' => 'text/json'));
    }

    public function pubAction(Request $request)
    {
        return new Response(json_encode('ok'));
    }
}
