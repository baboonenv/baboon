<?php

namespace Baboon\PanelBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="bb_dashboard")
     */
    public function indexAction(Request $request)
    {
        return $this->render('BaboonPanelBundle:Dashboard:index.html.twig');
    }
}
