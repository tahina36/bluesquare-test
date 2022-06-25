<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\GroupRepository;
use App\Repository\ProjectRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    private $paginator;
    private $projectRepository;

    public function __construct(PaginatorInterface $paginator, ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
        $this->paginator = $paginator;
    }

    public function listProjects(Request $request) {
        $pagination = $this->paginator->paginate(
            $this->projectRepository->findAllProjects($this->getUser()->getId(), false),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('projects/index.html.twig', [
            'projects' => $pagination
        ]);
    }

    public function createTicket(Request $request) {
        $ticket = new Ticket();
        $ticketForm = $this->createForm(TicketType::class, $ticket, ['action' => $this->generateUrl('app_create_ticket'), 'method' => 'post']);
        $ticketForm->handleRequest($request);

        if ($ticketForm->isSubmitted()){
            if ($ticketForm->isValid()) {
                $this->ticketProcess->process($ticket);
                if ($this->ticketProcess->isHasError()) {
                    foreach ($this->ticketProcess->getMessages() as $msg) {
                        $this->addFlash('danger', $msg);
                    }
                }
                else {
                    $this->ticketRepository->save($trip);
                    $this->addFlash('success', 'Ticket enregistré avec succès');
                    return $this->redirectToRoute('home_tickets');
                }
            }
            else {
                $this->addFlash('danger', 'Nous avons rencontré un soucis durant l\'enregistrement de votre ticket.');
            }
        }
        return new Response($this->renderView('create_ticket.html.twig', ['ticketForm' => $ticketForm->createView()]));
    }
}