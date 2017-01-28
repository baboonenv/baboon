<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Form\UploadImageType;
use Baboon\PanelBundle\Params\AssetTypes;
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

        $assetParams = $this->collectAssetParams($asset);
        return $this->render('@BaboonPanel/Configuration/_configure_asset/_'.$asset['type'].'.html.twig', [
            'asset'         => $asset,
            'assetKey'      => $assetKey,
            'assetParams'   => $assetParams,
        ]);
    }

    private function collectAssetParams($asset)
    {
        $assetParams = [];
        if($asset['type'] == AssetTypes::IMAGE){
            $imageOptions = [
                'endpoint' => 'gallery',
                'img_width' => $asset['width'],
                'img_height' => $asset['height'],
                'crop_options' => [
                    'aspect-ratio' => $asset['width'] / $asset['height'],
                ]
            ];
            $assetParams['form'] = $this
                ->createForm(UploadImageType::class, null, [
                    'image_options' => $imageOptions
                    ]
                )
                ->createView();
        }

        return $assetParams;
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

        if($confData['assets'][$assetKey]['type'] = AssetTypes::IMAGE){
            $assetValue = '/_site/_uploads/croped/'.$assetValue;
        }
        $confData['assets'][$assetKey]['value'] = $assetValue;
        $confData['assets'][$assetKey]['isDefaultValue'] = false;

        $confService->saveConfigurationData($confData);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @param string $assetKey
     * @return Response
     */
    public function getAssetWrapAction(Request $request, $assetKey)
    {
        $confService = $this->get('baboon.panel.theme_configuration_service');
        $confData = $confService->collectConfigurationData();

        return $this->render('@BaboonPanel/Configuration/_widgets/_asset_wrap.html.twig', [
            'asset' => $confData['assets'][$assetKey],
            'assetKey' => $assetKey,
        ]);
    }
}
