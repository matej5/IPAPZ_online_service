<?php

namespace App\Controller;

use App\Entity\LikeDislike;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use App\Repository\LikeDislikeRepository;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PostController extends AbstractController
{
    /**
     * @Symfony\Component\Routing\Annotation\Route("/", name="post_index")
     * @param      Request $request
     * @param      EntityManagerInterface $entityManager
     * @param      PostRepository $postRepository
     * @return       \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository)
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if (($this->isGranted('ROLE_ADMIN')
                || $this->isGranted('ROLE_BOSS'))
            && ($form->isSubmitted() && $form->isValid())) {
            /**
             * @var Post $post
             */
            $post = new Post();
            if (!empty($form->get('image')->getData())) {
                $file = $form->get('image')->getData();

                $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();

                // moves the file to the directory where brochures are stored
                $file->move(
                    $this->getParameter('post_directory'),
                    $fileName
                );
            } else {
                $fileName = 'post.jpg';
            }

            $post->setImage($fileName);
            $post->setContent($form->get('content')->getData());
            $post->setTitle($form->get('title')->getData());
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'New post created!');
            return $this->redirectToRoute('post_index');
        }

        $posts = $postRepository->getAllInLastWeek();

        return $this->render(
            'post/index.html.twig',
            [
                'form' => $form->createView(),
                'posts' => $posts
            ]
        );
    }

    /**
     * @Symfony\Component\Routing\Annotation\Route("/view/{id}", name="post_view")
     * @param               Post $post
     * @param               Request $request
     * @param               EntityManagerInterface $entityManager
     * @param               LikeDislikeRepository $likeDislikeRepository
     * @return                \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function show(
        Post $post,
        Request $request,
        EntityManagerInterface $entityManager,
        LikeDislikeRepository $likeDislikeRepository
    ) {
        $form = $this->createForm(CommentFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /**
             * @var Comment $comment
             */
            $comment = $form->getData();
            $comment->setUser($this->getUser());
            $post->addComment($comment);
            $date = new \DateTime("now");
            $comment->setCreatedAt($date);
            $entityManager->flush();
            return $this->redirectToRoute(
                'post_view',
                [
                    'id' => $post->getId()
                ]
            );
        }

        $userLikesPost = $likeDislikeRepository->findBy(
            [
                'user' => $this->getUser(),
                'post' => $post
            ]
        );

        return $this->render(
            'post/view.html.twig',
            [
                'post' => $post,
                'commentForm' => $form->createView(),
                'userLikesPost' => $userLikesPost
            ]
        );
    }

    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\Security("user             == post.getUser()")
     * @Symfony\Component\Routing\Annotation\Route("/post/{id}/delete", name="post_delete")
     * @param                      Post $post
     * @param                      EntityManagerInterface $entityManager
     * @return                     \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost(Post $post, EntityManagerInterface $entityManager)
    {
        if ($this->isGranted('ROLE_ADMIN') ||
            ($this->isGranted('ROLE_BOSS') &&
                $post->getUser() == $this->getUser())
        ) {
            $entityManager->remove($post);
            $entityManager->flush();
            $this->addFlash('success', 'Successfully deleted!');
            return $this->redirectToRoute('post_index');
        } else {
            return $this->redirectToRoute('post_index');
        }
    }

    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted("ROLE_USER")
     * @Symfony\Component\Routing\Annotation\Route("/post/{id}/like", name="post_like", methods={"POST"})
     * @param                    Post $post
     * @param                    EntityManagerInterface $entityManager
     * @param                    LikeDislikeRepository $likeDislikeRepository
     * @return                   JsonResponse
     */
    public function likePost(
        Post $post,
        EntityManagerInterface $entityManager,
        LikeDislikeRepository $likeDislikeRepository
    ) {
        $like = $likeDislikeRepository->findOneBy(
            [
                'user' => $this->getUser(),
                'post' => $post
            ]
        );

        if (!$like || $like->getType() == 2) {
            $like = new LikeDislike();
            $like->setUser($this->getUser());
            $like->setType(1);
            $post->addLikeDislike($like);
        } else {
            $post->removeLikeDislike($like);
        }

        $entityManager->flush();
        return new JsonResponse(
            [
                'likes' => $post->getLikeDislikesCount()
            ]
        );
    }

    /**
     * @Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted("ROLE_USER")
     * @Symfony\Component\Routing\Annotation\Route("/post/{id}/dislike", name="post_dislike", methods={"POST"})
     * @param                       Post $post
     * @param                       EntityManagerInterface $entityManager
     * @param                       LikeDislikeRepository $likeDislikeRepository
     * @return                      JsonResponse
     */
    public function dislikePost(
        Post $post,
        EntityManagerInterface $entityManager,
        LikeDislikeRepository $likeDislikeRepository
    ) {
        $like = $likeDislikeRepository->findOneBy(
            [
                'user' => $this->getUser(),
                'post' => $post
            ]
        );

        if (!$like || $like->getType() == 1) {
            $like = new LikeDislike();
            $like->setUser($this->getUser());
            $like->setType(2);
            $post->addLike($like);
        } else {
            $post->removeLike($like);
        }

        $entityManager->flush();
        return new JsonResponse(
            [
                'likes' => $post->getLikesCount()
            ]
        );
    }

    /**
     * @return string
     */
    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}
