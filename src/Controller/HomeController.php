<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AdRepository $repository) : Response
    {
        $ads = $repository->findAll();
        return $this->render('home/index.html.twig', [
            'ads'=>$ads
        ]);
    }

    /**
     * @Route("/userad", name="user_ad")
     */
    public function showUserAds(AdRepository $repository) : Response
    {
        $ads = $repository->findAll();
        return $this->render('home/index.html.twig', [
            'ads'=>$ads
        ]);
    }
}
