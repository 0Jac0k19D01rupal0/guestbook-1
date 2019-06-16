<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/signup", name="user_signup")
     */
    public function signup()
    {
        return $this->render('user/form.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    /**
     * @Route("/signin", name="user_singin")
     */
    public function singin()
    {
        return $this->render('user/signin.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}
