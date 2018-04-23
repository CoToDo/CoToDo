<?php

namespace App\Controller;

use App\Entity\Comment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/comments")
 */
class CommentController extends Controller
{
    /**
     * @Route("/{id}", name="comment_delete", methods="DELETE")
     * @Security("has_role('ROLE_USER')")
     * @Security("comment.isAuthor(user)")
     */
    public function delete(Comment $comment): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute('project_task_show', ['idp' => $comment->getTask()->getProject()->getId(), 'id' => $comment->getTask()->getId()]);
    }
}
