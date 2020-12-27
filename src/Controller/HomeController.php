<?php

namespace App\Controller;

use App\Entity\PostSearch;
use App\Form\PostSearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PostRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PostRepository $repository, Request $request): Response
    {
        $search = new PostSearch();
        $form = $this->createForm(PostSearchType::class, $search);
        $form->handleRequest($request);
        if ($search->getField() == '' && $search->getCategory()) {
            $posts = $repository->findAll();
        } else {
            $posts = $repository->findPostsByField($search->getField(), $search->getCategory());
        }
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
            'form' => $form->createView()
        ]);
    }
}
