<?php

namespace App\Controller\frontend;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\EventRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingController extends AbstractController
{
    #[Route('/book/event/{id}', name: 'book_event', methods: ['GET', 'POST'])]
    public function bookEvent(int $id, Request $request, EntityManagerInterface $entityManager, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
        if (!$event) {
            throw $this->createNotFoundException('Event not found');
        }

        $reservation = new Reservation();
        $reservation->setEvent($event);  
        $reservation->setProduct(null);
        
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setReservationDate(new \DateTime());
            $reservation->setStatus('New');
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Event reservation created successfully.');
            return $this->redirectToRoute('book_event', ['id' => $id]);
        }

        return $this->render('frontend/booking/book_event.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
        ]);
    }

    #[Route('/book/product/{id}', name: 'book_product', methods: ['GET', 'POST'])]
    public function bookProduct(int $id, Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepo): Response
    {
        $product = $productRepo->find($id);
        if (!$product) {
            throw $this->createNotFoundException('Product not found');
        }

        $reservation = new Reservation();
        $reservation->setProduct($product);  
        $reservation->setEvent(null);

        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $reservation->setReservationDate(new \DateTime());
            $reservation->setStatus('New');
            $entityManager->persist($reservation);
            $entityManager->flush();

            $this->addFlash('success', 'Product reservation created successfully.');
            return $this->redirectToRoute('book_product', ['id' => $id]);
        }

        return $this->render('frontend/booking/book_product.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }
}