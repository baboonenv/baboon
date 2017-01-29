<?php

namespace Baboon\PanelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DeployController extends Controller
{
    public function syncAction()
    {
        $deployService = $this->get('baboon.panel.theme_deploy');
        $deployService->syncSiteTheme();

        return new Response('successful');
    }
}
