<?php

namespace App\Controller;

use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Ad;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{

    private $repository;
    private $manager;
    public function __construct(AdRepository $repository,EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/createAd", name="create_ad")
     */
    public function createAd(Request $request,EntityManagerInterface $manager) {
        $user = $this->getUser();
        //On test si il y a un utilisateur de connecter
        $ad = new Ad(); //On créé notre annonce
        if ($user->getUsername() != "anon") {

        //On récupere le formulaire
        $form = $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);
        //Si le formulaire est valide on push l'annonce.
        if ($form->isSubmitted() && $form->isValid()) {
                $ad->setOwnerId($user->getId());
                $manager->persist($ad);
                $manager->flush();
                return $this->redirectToRoute('home');
            }
        }
        //Si il n'y a pas d'utilisateur on revois sur la homepage.
        return $this->render('home/createAd.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifyAd/{id}",name="modify_ad")
     */
    public function modifyAd(Request $request,$id) : Response {
        $user = $this->getUser();
        //On test si il y a un utilisateur de connecter
        $ad = $this->manager->find(Ad::class,$id); //On créé notre annonce
        $form = $this->createForm(AdType::class,$ad);
        if ($user && $user->getId() == $ad->getOwnerId()) {
            if (!$ad) {
                throw $this->createNotFoundException(
                    'No ad found for id '.$id
                );
            }
            //On récupere le formulaire
            $form->handleRequest($request);
            //Si le formulaire est valide on push l'annonce.
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                return $this->redirectToRoute('home');
            }
        }


        return $this->render('home/createAd.html.twig', [
            'ad' => $ad ,  'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/userAds",name="user_ads")
     */
    public function getUserAds() {
        $user = $this->getUser();
        if ($user->getUsername() != "anon") {
            $ads = $this->repository->findAdsByOwnerId($user->getId());
            return $this->render('home/personalSpace.html.twig', [
                'ads' => $ads
            ]);
        }
        return $this->render('home/createAd.html.twig', [
        ]);
    }
    /**
     * @Route("/ad/{id}",name="ad")
     */
    public function index($id) : Response
    {
        $ad = $this->repository->find($id);
        return $this->render('ad/index.html.twig', [
            'ad'=>$ad
        ]);
    }
}
