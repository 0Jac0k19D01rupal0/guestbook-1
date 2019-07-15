<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentController extends AbstractController
{
    /**
     * @Route("/comment/create/{message}", name="comment_create_form")
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function create(Request $request, Message $message, UserInterface $user = null)
    {
        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment, [
            'action' => $this->generateUrl('comment_create_form', [
                'message' => $message->getId()
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setUsername($user->getUsername());
            $comment->setCreatedAt(new \DateTime('now'));
            $comment->setMessage($message);

            $em = $this->getDoctrine()->getManager();

            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('message', ['message' => $message->getId()]);
        }

        return $this->render('comment/form.html.twig', [
            'comment_form' => $form->createView(),
            'message' => $message
        ]);
    }
}
