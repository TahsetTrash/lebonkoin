<?php

namespace App\Controller;

use App\Form\PostType;
use App\Entity\Post;
use App\Entity\Download;
use App\Repository\PostRepository;
use App\Repository\DownloadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    private $repository;
    private $manager;

    public function __construct(PostRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
    }

    /**
     * @Route("/createPost", name="create_post")
     */
    public function createPost(Request $request, EntityManagerInterface $manager)
    {
        $user = $this->getUser();
        $post = new Post();
        if ($user->getUsername() != "anon") {
            $form = $this->createForm(PostType::class, $post);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
             
                $post->setOwnerId($user->getId());
                $post->setDownloads(0);
                $manager->persist($post);
                $manager->flush();
                return $this->redirectToRoute('home');
            }
        }
        return $this->render('post/createPost.html.twig', [
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/modifyPost/{id}",name="modify_post")
     */
    public function modifyPost(Request $request, $id): Response
    {
        $user = $this->getUser();
        $post = $this->manager->find(Post::class, $id);
        if ($user && $user->getId() == $post->getOwnerId()) {

            $form = $this->createForm(PostType::class, $post);
            if (!$post) {
                throw $this->createNotFoundException(
                    'No post found for id ' . $id
                );
            }
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->manager->flush();
                return $this->redirectToRoute('home');
            }
            return $this->render('post/modifyPost.html.twig', [
                'post' => $post, 'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/deletePost/{id}",name="delete_post")
     */
    public function deletePost(Request $request, $id): Response
    {
        $user = $this->getUser();
        $post = $this->manager->find(Post::class, $id);
        if ($user && $user->getId() == $post->getOwnerId()) {


            if (!$post) {
                throw $this->createNotFoundException(
                    'No post found for id ' . $id
                );
            }
            $this->manager->remove($post);
            $this->manager->flush();
            return $this->redirectToRoute('home');


        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/userPosts",name="user_posts")
     */
    public function getUserPosts()
    {
        $user = $this->getUser();
        if ($user->getUsername() != "anon") {
            $posts = $this->repository->findPostsByOwnerId($user->getId());
            return $this->render('home/personalSpace.html.twig', [
                'posts' => $posts
            ]);
        }
        return $this->render('home/createPost.html.twig', [
        ]);
    }


    /**
     * @Route("/downloadPost/{id}",name="download_post")
     */
    public function addDownload(DownloadRepository $rep, $id) {
        $user = $this->getUser();
        $post = $this->repository->findPostById($id);

        if ($user->getUsername() != "anon") {
            //$posts = $this->repository->findPostsByOwnerId($user->getId());

            $postDownloadedByUser = $rep->findDownloadByUserAndPost($user->getId(),$post->getId());

            if ($postDownloadedByUser == null) {

            
                $download = new Download();
                $download->setUserId($user->getId());
                $download->setPostId($post->getId());

                $this->manager->persist($download);

                $this->manager->flush();
               
            }

            $downloads = $rep->findDownloadByPost($post->getId()); 
            $post->setDownloads(count($downloads));
            $this->manager->persist($post);
            $this->manager->flush();
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/jsonPost/{id}",name="json_post")
     */
    public function jsonPost($id): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new DateTimeNormalizer(), new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        $post = $this->repository->find($id);
        $data = $this->get($serializer)->serialize($post, 'json');

        return new JsonResponse($data);
    }


    /**
     * @Route("/post/{id}",name="post")
     */
    public function index($id): Response
    {
        $post = $this->repository->find($id);
        return $this->render('post/index.html.twig', [
            'post' => $post
        ]);
    }

    /**
     * @Route("/post/{id}",name="redirect")
     */
    public function redirectLino($id){
        $post = $this->repository->find($id);
        return $this->redirect($post->getLink());
    } 

}
