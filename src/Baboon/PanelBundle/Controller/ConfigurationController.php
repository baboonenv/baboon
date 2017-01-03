<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Entity\ThemeServer;
use Baboon\PanelBundle\Form\ThemeServerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ConfigurationController extends Controller
{
    /**
     * @Route("/configuration", name="bb_configuration")
     */
    public function themesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $servers = $em->getRepository(ThemeServer::class)->findAll();
        $addServerForm = $this->createForm(ThemeServerType::class, new ThemeServer(), [
            'action' => $this->generateUrl('bb_themes_add_server'),
            'method' => 'POST',
        ]);

        return $this->render('BaboonPanelBundle:Themes:index.html.twig', [
            'servers' => $servers,
            'add_server_form' => $addServerForm->createView(),
        ]);
    }
}
