<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\Type\TicketType;
use App\Repository\TicketRepository;
use App\Service\TicketProcess;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
}