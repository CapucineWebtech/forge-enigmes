<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Devis;
use App\Form\ContactType;
use App\Form\DevisType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
    private $em;

    /**
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em
    )
    {
        $this->em = $em;
    }

    #[Route('/', name: 'app_index')]
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($contact);
            $this->em->flush();

            $this->addFlash(
                'successContact',
                "Formulaire de contact envoyer"
            );
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/devis', name: 'app_devis')]
    public function devis(Request $request): Response
    {
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $dateString = date('Y-m-d');
            $date = \DateTime::createFromFormat('Y-m-d', $dateString);
            $devis->setDate($date);
            $this->em->persist($devis);
            $this->em->flush();

            $this->addFlash(
                'successDevis',
                "Devis envoyer"
            );
            return $this->redirectToRoute('app_devis');
        }

        return $this->render('devis.html.twig', [
            'form' => $form->createView()
        ]);
    }
}