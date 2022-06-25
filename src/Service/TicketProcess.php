<?php


namespace App\Service;


use App\Entity\Event;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\EventRepository;
use App\Repository\EventTypeRepository;
use App\Repository\TicketRepository;

class TicketProcess
{
    private $messages = [];
    private $ticketRepository;
    private $eventTypeRepository;
    private $eventRepository;

    public function __construct(TicketRepository $ticketRepository, EventTypeRepository $eventTypeRepository, EventRepository $eventRepository) {
        $this->ticketRepository = $ticketRepository;
        $this->eventTypeRepository = $eventTypeRepository;
        $this->eventRepository = $eventRepository;
    }


    public function process(Ticket $ticket, User $user, $event) {
        switch ($event) {
            case "ticket_creation":
                $this->create($ticket, $user, $event);
                break;
            case "new_comment":
                break;
            case "file_upload":
                break;
            case "status_updated":
                break;
        }
    }

    public function isHasError()
    {
        if (count($this->messages) == 0) {
            return false;
        }
        return true;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    private function create(Ticket $ticket, User $user, $event)
    {
        $ticket->setCreatedAt(new \DateTimeImmutable());
        $ticket->setUpdatedAt(new \DateTime());
        $ticket->setUser($user);
        $eventType = $this->eventTypeRepository->findOneBy(["name" => $event]);
        if ($eventType) {
            $event = new Event();
            $event->setUser($user);
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setTicket($ticket);
            $this->eventRepository->save($event, true);
            $this->ticketRepository->save($ticket);
        }
        else {
            $this->messages[] = "Erreur lors du chargement des types d'evenement";
        }
    }
}