<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Entity\FTPConfiguration;
use Baboon\PanelBundle\Form\FTPConfigurationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    public function syncAction()
    {
        $deployService = $this->get('baboon.panel.theme_deploy');
        $deployService->syncSiteTheme();

        return new Response('successful');
    }

    public function FTPisConfiguredAction()
    {
        $em = $this->getDoctrine()->getManager();
        $ftpConfiguration = $em->getRepository(FTPConfiguration::class)->findOneBy([]);
        if($ftpConfiguration){
            return new JsonResponse([
                'isConfigured' => true
            ]);
        }

        return new JsonResponse([
            'isConfigured' => false
        ]);
    }

    public function configureFTPAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $FTPConfiguration = $em->getRepository(FTPConfiguration::class)->findOneBy([]);
        if(!$FTPConfiguration){
            $FTPConfiguration = new FTPConfiguration();
        }
        $form = $this->createForm(FTPConfigurationType::class, $FTPConfiguration, [
            'action' => $this->generateUrl('bb_panel_deploy_configure_ftp')
        ]);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){
            $em->persist($FTPConfiguration);
            $em->flush();
            $this->addFlash('success', 'successful.update');
        }

        return $this->render('@BaboonPanel/Deploy/_ftp_configuration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function FTPConnectionTestAction(Request $request)
    {
        $FTPConfiguration = new FTPConfiguration();
        $form = $this->createForm(FTPConfigurationType::class, $FTPConfiguration, [
            'action' => $this->generateUrl('bb_panel_deploy_configure_ftp')
        ]);
        $form->handleRequest($request);
        $errors = [];
        $successes = [];

        //test connection is valid
        try{
            $FTPConnect = ftp_connect($FTPConfiguration->getHostname());
            $successes[] = 'host.works.correctly';
        }catch (\Exception $exception){
            $errors[] = 'host.connection.refused';
        }

        //test login is valid
        try{
            if(empty($errors)){
                ftp_login($FTPConnect, $FTPConfiguration->getUsername(), $FTPConfiguration->getPassword());
                $successes[] = 'ftp.login.successful';
            }
        }catch (\Exception $exception){
            $errors[] = 'ftp.login.failed';
        }

        //test dir exists
        try{
            if(empty($errors)){
                ftp_chdir($FTPConnect, $FTPConfiguration->getPath());
                $successes[] = 'ftp.dir.exists';
            }
        }catch (\Exception $exception){
            $errors[] = 'i.could.not.find.path';
        }

        //test put granted
        try{
            if(empty($errors)){
                $fileName = 'ftp_test_file.html';
                $FTPPath = $FTPConfiguration->getPath();
                $FTPPath = (substr($FTPPath,-1)!='/') ? $FTPPath.='/' : $FTPPath;
                $putPath = $FTPPath.$fileName;
                $testFile = $this->get('kernel')->getRootDir().'/../web/'.$fileName;
                $fopen = fopen($testFile, 'r');
                ftp_fput($FTPConnect, $putPath, $fopen, FTP_ASCII);
                $successes[] = 'ftp.put.successful';
            }
        }catch (\Exception $exception){
            $errors[] = 'i.could.not.put.file';
        }

        //test delete granted
        try{
            if(empty($errors)){
                ftp_delete($FTPConnect, $putPath);
                $successes[] = 'ftp.delete.file.successful';
            }
        }catch (\Exception $exception){
            $errors[] = 'i.could.not.delete.file';
        }

        if(!empty($successes)){
            $this->addFlash('success', implode('<br>', $successes));
        }
        if(!empty($errors)){
            $this->addFlash('error', implode('<br>', $errors));
        }

        return $this->render('@BaboonPanel/Deploy/_ftp_configuration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function deployToFTPAction()
    {
        return new Response('deployed to ftp');
    }
}
