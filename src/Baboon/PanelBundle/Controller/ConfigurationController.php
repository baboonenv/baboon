<?php

namespace Baboon\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $confService = $this->get('baboon.panel.theme_configuration_service');
        $assetKey = $request->get('assetKey');
        $asset = $confService->collectConfigurationData()['assets'][$assetKey];

        return $this->render('@BaboonPanel/Configuration/_configure_asset/_'.$asset['type'].'.html.twig', [
            'asset'     => $asset,
            'assetKey'  => $assetKey,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function saveAssetValueAction(Request $request)
    {
        $confService = $this->get('baboon.panel.theme_configuration_service');

        $assetKey = $request->get('assetKey');
        $assetValue = $request->get('value');

        $confData = $confService->collectConfigurationData();
        $confData['assets'][$assetKey]['value'] = $assetValue;
        $confData['assets'][$assetKey]['isDefault'] = true;

        $confService->saveConfigurationData($confData);

        return new JsonResponse([
            'success' => true,
        ]);
    }
}
