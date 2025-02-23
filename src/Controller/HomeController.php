<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    public function index(Request $request): Response
    {
        return $this->renderTwig(
            "home/index.html.twig",
            [
                "name" => $request->query->get('name')
            ]
        );
    }

}