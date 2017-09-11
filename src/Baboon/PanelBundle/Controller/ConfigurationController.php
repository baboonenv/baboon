<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Form\UploadFileType;
use Baboon\PanelBundle\Form\UploadImageType;
use Baboon\PanelBundle\Params\AssetTypes;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;

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
        $accessor = PropertyAccess::createPropertyAccessor();
        $confService = $this->get('baboon.panel.theme_configuration_service');
        $assetPath = $request->get('assetPath');
        $asset = $accessor->getValue($confService->collectConfigurationData(), $assetPath);

        $assetParams = $this->collectAssetParams($asset);
        return $this->render('@BaboonPanel/Configuration/_configure_asset/_'.$asset['type'].'.html.twig', [
            'asset'         => $asset,
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
                    'image_options' => $imageOptions,
                    ]
                )
                ->createView();
        }elseif ($asset['type'] == AssetTypes::FILE){
            $fileOptions = [
                'endpoint' => 'gallery',
            ];
            $assetParams['form'] = $this
                ->createForm(UploadFileType::class, null, [
                        'file_options' => $fileOptions,
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
        $accessor = PropertyAccess::createPropertyAccessor();
        $confService = $this->get('baboon.panel.theme_configuration_service');

        $assetPath = $request->get('assetPath');
        $assetValue = $request->get('value');

        $confData = $confService->collectConfigurationData();
        $asset = $accessor->getValue($confData, $assetPath);

        if($asset['type'] == AssetTypes::IMAGE){
            $assetValue = '/_site/_uploads/croped/'.$assetValue;
        }elseif($asset['type'] == AssetTypes::FILE){
            $assetValue = '/_site/_uploads/'.$assetValue;
        }
        $asset['value'] = $assetValue;
        $asset['isDefaultValue'] = false;
        $accessor->setValue($confData, $assetPath, $asset);

        $confService->saveConfigurationData($confData);

        return new JsonResponse([
            'success' => true,
        ]);
    }

    /**
     * @param Request $request
     * @param string $assetPath
     * @return Response
     */
    public function getAssetWrapAction(Request $request, string $assetPath)
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $confService = $this->get('baboon.panel.theme_configuration_service');
        $confData = $confService->collectConfigurationData();
        $asset = $accessor->getValue($confData, $assetPath);

        return $this->render('@BaboonPanel/Configuration/_widgets/_asset_wrap.html.twig', [
            'asset' => $asset,
        ]);
    }
}
