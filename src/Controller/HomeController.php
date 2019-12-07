<?php

namespace App\Controller;

use App\Entity\AdSearch;
use App\Form\AdSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\AdRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AdRepository $repository,Request $request) : Response
    {
        $search = new AdSearch();
        $form = $this->createForm(AdSearchType::class,$search);
        $form->handleRequest($request);


        if ($search->getField() == '') {
            $ads = $repository->findAll();
        } else {
            $ads = $repository->findAdsByField($search->getField());
        }
        return $this->render('home/index.html.twig', [
            'ads'=>$ads,
            'form' => $form->createView()
        ]);
    }

}
