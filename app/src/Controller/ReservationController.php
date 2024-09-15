<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/reservation')]
class ReservationController extends AbstractController
{
    private $entityManager;
    private $reservationRepository;


    public function __construct(
        ReservationRepository $rsRepository,
        EntityManagerInterface $entityManager,
    ) {
        $this->reservationRepository = $rsRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/{id}', name: 'getReservationById', methods: ['GET'])]
    public function getReservationById(int $id): JsonResponse
    {
        $reservations = $this->reservationRepository->findById($id);

        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'reservationDate' => $reservation->getReservationDate()?->format('Y-m-d'),
                'clientName' => $reservation->getClientName(),
                'clientPhoneNumber' => $reservation->getClientPhoneNumber(),
                'clientEmail' => $reservation->getClientEmail(),
                'event' => $reservation->getEvent(),
                'product' => $reservation->getProduct(),
                'status' => $reservation->getStatus(),
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/event/{id}', name: 'getReservationsByEvent', methods: ['GET'])]
    public function getReservationByEvent(int $id): JsonResponse
    {
        $reservations = $this->reservationRepository->findByEvent($id);

        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'reservationDate' => $reservation->getReservationDate()?->format('Y-m-d'),
                'clientName' => $reservation->getClientName(),
                'clientPhoneNumber' => $reservation->getClientPhoneNumber(),
                'clientEmail' => $reservation->getClientEmail(),
                'event' => $reservation->getEvent(),
                'product' => $reservation->getProduct(),
                'status' => $reservation->getStatus(),
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/product/{id}', name: 'getReservationByProduct', methods: ['GET'])]
    public function getReservationByProduct(int $id): JsonResponse
    {
        $reservations = $this->reservationRepository->findByProduct($id);

        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'reservationDate' => $reservation->getReservationDate()?->format('Y-m-d'),
                'clientName' => $reservation->getClientName(),
                'clientPhoneNumber' => $reservation->getClientPhoneNumber(),
                'clientEmail' => $reservation->getClientEmail(),
                'event' => $reservation->getEvent(),
                'product' => $reservation->getProduct(),
                'status' => $reservation->getStatus(),
            ];
        }

        return new JsonResponse($data, JsonResponse::HTTP_OK);
    }

    #[Route('/{id}', name: 'update_reservation', methods: ['PUT'])]
    public function updateReservation(Request $request, int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);
        if (!$reservation) {
            return new JsonResponse(['message' => 'reservation not found']);
        }

        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse(['message' => 'Invalid JSON'], 400);
        }

        $reservation->setReservationDate(new \DateTime());
        if (isset($data['status'])) {
            $reservation->setStatus($data['status']);
        }

        $this->entityManager->persist($reservation);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'reservation updated successfully']);
    }

    #[Route('/{id}', name: 'delete_Reservation', methods: ['DELETE'])]
    public function deleteReservation(int $id): JsonResponse
    {
        $reservation = $this->reservationRepository->find($id);

        if (!$reservation) {
            return new JsonResponse(['message' => 'reservation not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($reservation);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'reservation deleted successfully']);
    }



}