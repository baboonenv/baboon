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

    /**
     * @param Request $request
     * @return Response
     */
    public function configureAction(Request $request)
    {
        $configurationService = $this->get('baboon.panel.theme_configuration_service');
        $assetKey = $request->get('assetKey');
        $asset = $configurationService->collectConfigurationData()['assets'][$assetKey];

        return $this->render('@BaboonPanel/Configuration/_configure_asset/_'.$asset['type'].'.html.twig', [
            'asset' => $asset,
        ]);
    }
}
