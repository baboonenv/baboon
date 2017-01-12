<?php

namespace Baboon\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends Controller
{
    /**
     * @param Request $request
     * @return Response
     */
    public function configurationAction(Request $request)
    {
        $configurationService = $this->get('baboon.panel.theme_configuration_service');

        return $this->render('BaboonPanelBundle:Configuration:index.html.twig', [
            'data' => $configurationService->collectConfigurationData(),
        ]);
    }
}
