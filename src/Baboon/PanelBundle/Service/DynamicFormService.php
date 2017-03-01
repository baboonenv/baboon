<?php

namespace Baboon\PanelBundle\Service;

use Baboon\PanelBundle\Params\AssetTypes;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Yaml\Yaml;

/**
 * Class DynamicFormService
 * @package Baboon\PanelBundle\Service
 */
class DynamicFormService
{
    /**
     * @var ToolsService
     */
    private $tools;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var FormFactory
     */
    private $formFactory;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var array
     */
    private $normalizedFormData;

    /**
     * DynamicFormService constructor.
     * @param ToolsService $toolsService
     * @param RequestStack $requestStack
     *
     * @param FormFactory $formFactory
     */
    public function __construct(ToolsService $toolsService, RequestStack $requestStack, FormFactory $formFactory)
    {
        $this->tools    = $toolsService;
        $this->request  = $requestStack->getMasterRequest();
        $this->formFactory = $formFactory;
    }

    public function generateForm()
    {
        $data = $this->tools->getSiteData();
        $this->form = $this->injectAssets($data['assets']);

        return $this->form;
    }

    public function injectAssets(array $assets = [])
    {
        $form = $this->formFactory->create(FormType::class, null, [
            'auto_initialize' => false,
        ]);
        foreach ($assets as $assetKey => $asset){
            $formType = AssetTypes::getAssetFormType($asset['type']);
            $options = [];
            if($asset['type'] == AssetTypes::FILE){
                $options['endpoint'] = 'gallery';
            }
            if($asset['type'] == AssetTypes::IMAGE){
                $options = [
                    'endpoint' => 'gallery',
                    'img_width' => $asset['width'],
                    'img_height' => $asset['height'],
                    'crop_options' => [
                        'aspect-ratio' => $asset['width'] / $asset['height'],
                    ]
                ];
            }
            if($asset['type'] == AssetTypes::TREE){
                $formType = $this->injectAssets($asset['assets']);

                $form->add($formType, null, [
                    ''
                ]);

                continue;
            }
            $form->add($assetKey, $formType, $options);
        }

        return $form;
    }
}
