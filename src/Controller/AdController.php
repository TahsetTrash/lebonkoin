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
        $ad = new Ad();
        if ($user->getUsername() != "anon") {
            $form = $this->createForm(AdType::class,$ad);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $ad->setOwnerId($user->getId());
                $manager->persist($ad);
                $manager->flush();
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('ad/createAd.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/modifyAd/{id}",name="modify_ad")
     */
    public function modifyAd(Request $request,$id) : Response {
        $user = $this->getUser();
        $ad = $this->manager->find(Ad::class, $id);
        if ($user && $user->getId() == $ad->getOwnerId()) {

            $form = $this->createForm(AdType::class, $ad);
            if (!$ad) {
                throw $this->createNotFoundException(
                    'No ad found for id ' . $id
                );
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                return $this->redirectToRoute('home');
            }
            return $this->render('ad/modifyAd.html.twig', [
                'ad' => $ad, 'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('home');
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
