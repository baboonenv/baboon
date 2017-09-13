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
        $mimeTypes = $this->generateUpToDateMimeArray();
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
        $explodeDir = explode('.', $resultDir);
        $explodeDir = explode('?', end($explodeDir))[0];
        $mimeType = isset($mimeTypes[$explodeDir])?$mimeTypes[$explodeDir]: 'text/plain';
        $content = file_get_contents($resultDir);
        $response = new Response($content);
        $response->headers->set('Content-Type', $mimeType);

        return $response;
    }

    private function generateUpToDateMimeArray()
    {
        $path = $this->get('kernel')->getRootDir().'/config/mime.types';
        $s=array();
        foreach(@explode("\n",@file_get_contents($path))as $x){
            if(isset($x[0])&&$x[0]!=='#'&&preg_match_all('#([^\s]+)#',$x,$out)&&isset($out[1])&&($c=count($out[1]))>1){
                for($i=1;$i<$c;$i++){
                    $s[$out[1][$i]]= $out[1][0];
                }
            }
        }

        return $s;
    }
}
