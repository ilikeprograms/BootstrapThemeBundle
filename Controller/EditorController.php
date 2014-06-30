<?php

namespace ILP\BootstrapThemeBundle\Controller;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditorController extends Controller
{
    /**
     * Saves the Modifications to the Current Theme.
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function saveModificationsAction(Request $request)
    {
        $themeManager = $this->get('ilp_bootstrap_theme.theme_manager');
        $themeManager->saveTheme($request->get('modifications'));
        
        return new Response();
    }
}