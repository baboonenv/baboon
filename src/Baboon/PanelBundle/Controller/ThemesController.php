<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Entity\ThemeServer;
use Baboon\PanelBundle\Form\ThemeServerType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ThemesController extends Controller
{
    /**
     * @Route("/themes", name="bb_themes")
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

    /**
     * @Route("/themes/server/{id}/categories", name="bb_themes_server_categories")
     */
    public function serverCategoriesAction(Request $request, ThemeServer $server)
    {
        $categoriesUrl = json_decode(file_get_contents($server->getUrl().'/configuration'))->categoriesUrl;
        $categories = (json_decode(file_get_contents($categoriesUrl)))->categories;

        return $this->render('BaboonPanelBundle:Themes:categories.html.twig', [
            'categories' => $categories,
            'server' => $server,
        ]);
    }

    /**
     * @Route("/themes/category/themes", name="bb_category_themes")
     */
    public function categoryThemesCategoriesAction(Request $request)
    {
        $url = $request->request->get('url');
        $themes = json_decode(file_get_contents($url));

        return $this->render('BaboonPanelBundle:Themes:themes.html.twig', [
            'themes' => $themes,
        ]);
    }

    /**
     * @Route("/themes/category/themes/enable", name="bb_category_themes_enable")
     */
    public function enableThemeCategoriesAction(Request $request)
    {
        $enableThemeService = $this->get('baboon.panel.enable_theme_service');
        $zipUri = $request->request->get('zip');
        $enable = $enableThemeService->downloadAndEnableTheme($zipUri);

        if(!$enable){
            return new Response('Some error occured');
        }

        return new Response('successful');
    }

    /**
     * @Route("/sync/theme", name="bb_theme_sync")
     */
    public function syncThemeAction(Request $request)
    {
        $m = new \Mustache_Engine();
        $rootDir = $this->get('kernel')->getRootDir();
        $indexFile = $rootDir.'/../web/themes/383/baboon-default-theme-master/index.html';
        $fileContent = file_get_contents($indexFile);
        $renderedTemplate = $m->render($fileContent, [
            'data' => [
                'enter_text' => 'Hello Bitch',
                'enter_paragraph' => 'Paragraph bitch. i am behram.',
            ]
        ]);
        file_put_contents($indexFile, $renderedTemplate);

        return new Response('successful');
    }

    /**
     * @Route("/themes/add-server", name="bb_themes_add_server")
     */
    public function addServerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $themeServer = new ThemeServer();
        $form = $this->createForm(ThemeServerType::class, $themeServer, [
            'action' => $this->generateUrl('bb_themes_add_server'),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if($form->isValid()){

            $jsonConfiguration = file_get_contents($themeServer->getUrl().'/configuration');
            $configuration = json_decode($jsonConfiguration);
            $themeServer->setName($configuration->name);

            $em->persist($themeServer);
            $em->flush();

            $this->addFlash('success', 'Added Server SuccessFully');
        }

        return $this->redirectToRoute('bb_themes');
    }
}
