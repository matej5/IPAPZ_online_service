<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\LikeDislike;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use App\Repository\LikeDislikeRepository;
use App\Repository\PostRepository;
use App\Repository\ReceiptRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\DateTime;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="post_index")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param PostRepository $postRepository
     * @param ReceiptRepository $receiptRepository
     * @return Response
     */
    public function index(Request $request, EntityManagerInterface $entityManager, PostRepository $postRepository, ReceiptRepository $receiptRepository)
    {
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);
        if (($this->isGranted('ROLE_ADMIN') || $this->isGranted('ROLE_BOSS')) && ($form->isSubmitted() && $form->isValid())) {
            /** @var Post $post */
            $post = $form->getData();
            $post->setUser($this->getUser());
            $entityManager->persist($post);
            $entityManager->flush();
            $this->addFlash('success', 'New post created!');
            return $this->redirectToRoute('post_index');
        }
        $posts = $postRepository->getAllInLastWeek();

        $user = $this->getUser();

        return $this->render('post/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }

    /**
     * @Route("/view/{id}", name="post_view")
     * @param Post $post
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LikeDislikeRepository $likeDislikeRepository
     * @return Response
     */
    public function show(Post $post, Request $request, EntityManagerInterface $entityManager, LikeDislikeRepository $likeDislikeRepository)
    {
        $form = $this->createForm(CommentFormType::class);
        $form->handleRequest($request);
        if ($this->isGranted('ROLE_USER') && $form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            $comment = $form->getData();
            $comment->setUser($this->getUser());
            $post->addComment($comment);
            $date = new \DateTime();
            $comment->setCreatedAt($date);
            $entityManager->flush();
            return $this->redirectToRoute('post_view', [
                'id' => $post->getId()
            ]);
        }
        $userLikesPost = $likeDislikeRepository->findBy([
            'user' => $this->getUser(),
            'post' => $post
        ]);
        return $this->render('post/view.html.twig', [
            'post' => $post,
            'commentForm' => $form->createView(),
            'userLikesPost' => $userLikesPost
        ]);
    }

    /**
     * @Security("user == post.getUser()")
     * @Route("/post/{id}/delete", name="post_delete")
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deletePost(Post $post, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($post);
        $entityManager->flush();
        $this->addFlash('success', 'Successfully deleted!');
        return $this->redirectToRoute('post_index');
    }

    /**
     * @IsGranted("ROLE_USER")
     * @Route("/post/{id}/like", name="post_like", methods={"POST"})
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @param LikeDislikeRepository $likeDislikeRepository
     * @return JsonResponse
     */
    public function likePost(Post $post, EntityManagerInterface $entityManager, LikeDislikeRepository $likeDislikeRepository)
    {
        $like = $likeDislikeRepository->findOneBy([
            'user' => $this->getUser(),
            'post' => $post
        ]);

        if (!$like || $like->getType()==2) {
            $like = new LikeDislike();
            $like->setUser($this->getUser());
            $like->setType(1);
            $post->addLikeDislike($like);
        } else {
            $post->removeLikeDislike($like);
        }

        $entityManager->flush();
        return new JsonResponse([
            'likes' => $post->getLikeDislikesCount()
        ]);
    }



    /**
     * @IsGranted("ROLE_USER")
     * @Route("/post/{id}/dislike", name="post_dislike", methods={"POST"})
     * @param Post $post
     * @param EntityManagerInterface $entityManager
     * @param LikeDislikeRepository $likeDislikeRepository
     * @return JsonResponse
     */
    public function dislikePost(Post $post, EntityManagerInterface $entityManager, LikeDislikeRepository $likeDislikeRepository)
    {
        $like = $likeDislikeRepository->findOneBy([
            'user' => $this->getUser(),
            'post' => $post
        ]);

        if (!$like || $like->getType() == 1) {
            $like = new PostLike();
            $like->setUser($this->getUser());
            $like->setType(2);
            $post->addLike($like);
        } else {
            $post->removeLike($like);
        }

        $entityManager->flush();
        return new JsonResponse([
            'likes' => $post->getLikesCount()
        ]);
    }
}
