<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Entity\GitConfiguration;
use Baboon\PanelBundle\Form\GitConfigurationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GitDeployController extends Controller
{
    public function GitisConfiguredAction()
    {
        $em = $this->getDoctrine()->getManager();
        $gitConfiguration = $em->getRepository(GitConfiguration::class)->findOneBy([]);
        if($gitConfiguration){
            return new JsonResponse([
                'isConfigured' => true
            ]);
        }

        return new JsonResponse([
            'isConfigured' => false
        ]);
    }

    public function configureGitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $GitConfiguration = $em->getRepository(GitConfiguration::class)->findOneBy([]);
        if(!$GitConfiguration){
            $GitConfiguration = new GitConfiguration();
        }
        $form = $this->createForm(GitConfigurationType::class, $GitConfiguration, [
            'action' => $this->generateUrl('bb_panel_deploy_configure_git')
        ]);
        $form->handleRequest($request);

        if($request->getMethod() == 'POST' && $form->isValid()){
            $em->persist($GitConfiguration);
            $em->flush();
            $this->addFlash('success', 'successful.update');
        }

        return $this->render('@BaboonPanel/Deploy/_git_configuration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function GitConnectionTestAction(Request $request)
    {
        return new Response('fill this test logic');
    }

    public function deployToGitAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $GitConfiguration = $em->getRepository(GitConfiguration::class)->findOneBy([]);
        $form = $this->getGitPasswordForm($GitConfiguration);
        $form->handleRequest($request);

        return $this->render('@BaboonPanel/Deploy/_git_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function postDeployToGitAction(Request $request)
    {
        $GitDeployService = $this->get('baboon.panel.git_deploy');
        $em = $this->getDoctrine()->getManager();

        $GitConfiguration = $em->getRepository(GitConfiguration::class)->findOneBy([]);
        $form = $this->getGitPasswordForm($GitConfiguration);
        $form->handleRequest($request);
        $GitDeployService->deployToGit();

        return new JsonResponse([
            'success' => true,
        ]);
    }

    private function getGitPasswordForm(GitConfiguration $GitConfiguration)
    {
        $form = $this->createForm(GitConfigurationType::class, $GitConfiguration);
        $form->remove('username');
        $form->remove('hostname');
        $form->remove('path');

        return $form;
    }
}
