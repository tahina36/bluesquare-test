<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\Type\CommentType;
use App\Form\Type\TicketType;
use App\Repository\TicketRepository;
use App\Service\TicketProcess;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class TicketController extends AbstractController
{
    private $paginator;
    private $ticketRepository;
    private $ticketProcess;

    public function __construct(PaginatorInterface $paginator, TicketRepository $ticketRepository, TicketProcess $ticketProcess)
    {
        $this->ticketRepository = $ticketRepository;
        $this->paginator = $paginator;
        $this->ticketProcess = $ticketProcess;
    }

    public function listTickets(Request $request, $projectId) {
        $pagination = $this->paginator->paginate(
            $this->ticketRepository->findAllTickets($projectId, true),
            $request->query->getInt('page', 1),
            10
        );
        return new Response($this->renderView('tickets/index.html.twig', [
            'tickets' => $pagination,
            'projectId' => $projectId
        ]));
    }

    public function createTicket(Request $request, $projectId) {
        $ticket = new Ticket();
        $ticketForm = $this->createForm(TicketType::class, $ticket, ['action' => $this->generateUrl('app_create_ticket', ['projectId' => $projectId] ), 'method' => 'post', 'user' => $this->getUser()]);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted()){
            if ($ticketForm->isValid()) {
                $this->ticketProcess->process($ticket, $this->getUser(), "ticket_creation");
                if ($this->ticketProcess->isHasError()) {
                    foreach ($this->ticketProcess->getMessages() as $msg) {
                        $this->addFlash('danger', $msg);
                    }
                }
                else {
                    $this->addFlash('success', 'Ticket enregistré avec succès');
                    return $this->redirectToRoute('app_list_ticket', ['projectId' => $projectId]);
                }
            }
            else {
                $this->addFlash('danger', 'Nous avons rencontré un soucis durant l\'enregistrement de votre ticket.');
            }
        }
        return new Response($this->renderView('tickets/create_ticket.html.twig', ['ticketForm' => $ticketForm->createView()]));
    }

    public function detailTicket(Request $request, $projectId, $ticketId) {
        $comment = new Comment();
        $ticket = $this->ticketRepository->getTicketWithId($this->getUser()->getId(), $projectId, $ticketId);
        if (!$ticket) {
            throw new NotFoundHttpException("ticket not found");
        }
        $commentForm = $this->createForm(CommentType::class, $comment, ['action' => $this->generateUrl('app_detail_ticket', ['projectId' => $projectId, 'ticketId' => $ticketId] ), 'method' => 'post']);
        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted()){
            if ($commentForm->isValid()) {
                $this->ticketProcess->process($ticket, $this->getUser(), "new_comment", ["comment" => $comment]);
                if ($this->ticketProcess->isHasError()) {
                    foreach ($this->ticketProcess->getMessages() as $msg) {
                        $this->addFlash('danger', $msg);
                    }
                }
                else {
                    $this->addFlash('success', 'commentaire posté avec succès');
                    return $this->redirectToRoute('app_detail_ticket', ['projectId' => $projectId, 'ticketId' => $ticketId] );
                }
            }
            else {
                $this->addFlash('danger', 'Nous avons rencontré un soucis durant l\'enregistrement de votre commentaire.');
            }
        }

        return new Response($this->renderView('tickets/detail_ticket.html.twig', [
            'ticket' => $ticket,
            'projectId' => $projectId,
            'commentForm' => $commentForm->createView()
        ]));
    }
}