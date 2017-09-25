<?php

namespace Baboon\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DefaultController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function indexAction()
    {
        return $this->redirectToRoute('bb_panel_dashboard');
    }
}
