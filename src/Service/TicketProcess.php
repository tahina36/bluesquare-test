<?php


namespace App\Service;


use App\Entity\Comment;
use App\Entity\Event;
use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\CommentRepository;
use App\Repository\EventRepository;
use App\Repository\EventTypeRepository;
use App\Repository\TicketRepository;

class TicketProcess
{
    private $messages = [];
    private $ticketRepository;
    private $eventTypeRepository;
    private $eventRepository;
    private $commentRepository;

    public function __construct(TicketRepository $ticketRepository, EventTypeRepository $eventTypeRepository, EventRepository $eventRepository,
    CommentRepository $commentRepository) {
        $this->ticketRepository = $ticketRepository;
        $this->eventTypeRepository = $eventTypeRepository;
        $this->eventRepository = $eventRepository;
        $this->commentRepository = $commentRepository;
    }


    public function process(Ticket $ticket, User $user, $event, $params = []) {
        switch ($event) {
            case "ticket_creation":
                $this->create($ticket, $user, $event);
                break;
            case "new_comment":
                $this->processComment($params["comment"], $ticket, $user, $event);
                break;
            case "file_upload":
                break;
            case "edit":
                $this->processEdit($params["oldTicket"], $ticket, $user, $event);
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
            $event->setEventType($eventType);
            $this->eventRepository->save($event, true);
            $this->ticketRepository->save($ticket);
        }
        else {
            $this->messages[] = "Erreur lors du chargement des types d'evenement";
        }
    }

    public function processComment(Comment $comment,Ticket $ticket, User $user, $event)
    {
        $eventType = $this->eventTypeRepository->findOneBy(["name" => $event]);
        if ($eventType) {
            $event = new Event();
            $event->setUser($user);
            $event->setCreatedAt(new \DateTimeImmutable());
            $event->setTicket($ticket);
            $event->setEventType($eventType);
            $this->eventRepository->save($event, true);

            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($user);
            $comment->setEvent($event);
            $this->commentRepository->save($comment);
        }
        else {
            $this->messages[] = "Erreur lors du chargement des types d'evenement";
        }
    }

    private function processEdit(Ticket $oldTicket, Ticket $ticket, User $user, $event)
    {
        if ($oldTicket->getStatus() != $ticket->getStatus()) {
            $eventType = $this->eventTypeRepository->findOneBy(["name" => "status_updated"]);
            if ($eventType) {
                $event = new Event();
                $event->setUser($user);
                $event->setCreatedAt(new \DateTimeImmutable());
                $event->setTicket($ticket);
                if ($ticket->getStatus() == 1) {
                    $value = "Ouvert";
                }
                elseif ($ticket->getStatus() == 2) {
                    $value = "En attente du client";
                }
                else {
                    $value = "Tests client";
                }
                $event->setValue($value);
                $event->setEventType($eventType);
                $this->eventRepository->save($event, true);
            }
        }
        if ($oldTicket->getPriority() != $ticket->getPriority()) {
            $eventType = $this->eventTypeRepository->findOneBy(["name" => "priority_updated"]);
            if ($eventType) {
                $eventTwo = new Event();
                $eventTwo->setUser($user);
                $eventTwo->setCreatedAt(new \DateTimeImmutable());
                $eventTwo->setTicket($ticket);
                $eventTwo->setEventType($eventType);
                if ($ticket->getPriority() == 1) {
                    $value = "Faible";
                }
                elseif ($ticket->getPriority() == 2) {
                    $value = "Moyenne";
                }
                else {
                    $value = "Haute";
                }
                $eventTwo->setValue($value);
                $this->eventRepository->save($eventTwo, true);
            }
        }
        $this->ticketRepository->save($ticket);
    }
}