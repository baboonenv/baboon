<?php

namespace Baboon\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends Controller
{
    /**
     * @param Request $request
     * @param null $path
     *
     * @return Response
     */
    public function indexAction(Request $request, $path = null)
    {
        $path = $this->get('request_stack')->getMasterRequest()->getPathInfo();
        $pathInfo = pathinfo($path);
        $appDir = $this->get('kernel')->getRootDir();
        $resultDir = $appDir.'/../web/_site/_render/'.$path;
        if(!isset($pathInfo['extension'])){
            $resultDir = $resultDir.'/index.html';
        }
        $resultDir = preg_replace('#/+#','/',$resultDir);
        if(!file_exists($resultDir)){
            throw new NotFoundHttpException('File can not be found');
        }
        $content = file_get_contents($resultDir);

        return new Response($content);
    }
}
