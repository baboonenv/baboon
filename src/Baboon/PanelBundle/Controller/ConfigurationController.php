<?php

namespace Baboon\PanelBundle\Controller;

use Baboon\PanelBundle\Entity\ThemeServer;
use Baboon\PanelBundle\Form\ThemeServerType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Yaml\Yaml;

class ConfigurationController extends Controller
{
    /**
     * @Route("/configuration", name="bb_configuration")
     */
    public function configurationAction(Request $request)
    {
        $rootDir = $this->get('kernel')->getRootDir();
        $baboonConfFile = $rootDir.'/../web/_site/_source/.baboon.yml';
        if(!file_exists($baboonConfFile)){
            return $this->redirectToRoute('bb_themes');
        }
        $baboonConf = Yaml::parse(file_get_contents($baboonConfFile));

        return $this->render('BaboonPanelBundle:Configuration:index.html.twig', [
            'baboonConf' => $baboonConf,
        ]);
    }
}
