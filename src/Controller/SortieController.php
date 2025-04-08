<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie', name: 'sortie_')]
final class SortieController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/list.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }

	#[Route('/create', name: 'create', methods: ['GET', 'POST'])]
	public function create(Request $request, EntityManagerInterface $em): Response
	{
		$sortie = new Sortie();
		$user = $this->getUser();
		//dd($user);
		$sortie->setOrganisateur($user);
		$form = $this->createForm(SortieType::class, $sortie);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->persist($sortie);
			$em->flush();

			$this->addFlash('success', 'La sortie a été créée');

			return $this->redirectToRoute('sortie_index');
		}

		return $this->render('sortie/create.html.twig', [
			'sortie' => $sortie,
			'form' => $form,
		]);

	}

	#[Route('/{id}', name: 'detail', requirements: ['id' => '\d+'], methods: ['GET'])]
	public function show(Sortie $sortie): Response
	{
		if (!$sortie) {
			throw $this->createNotFoundException('Sortie inconnue');
		}

		return $this->render('sortie/detail.html.twig', [
			'sortie' => $sortie,
		]);
	}

	#[Route('/{id}/editer', name: 'editer', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
	public function edit(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
	{
		$form = $this->createForm(SortieType::class, $sortie);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$em->flush();

			$this->addFlash('success', 'La sortie a été modifiée');

			return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('sortie/edit.html.twig', [
			'sortie' => $sortie,
			'form' => $form,
		]);
	}

	#[Route('/{id}/delete', name: 'supprimer',requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
	public function delete(Request $request, Sortie $sortie, EntityManagerInterface $em): Response
	{
		$em->remove($sortie);
		$em->flush();

		$this->addFlash('success', 'La sortie a été supprimée');

		return $this->redirectToRoute('sortie_index', [], Response::HTTP_SEE_OTHER);
	}

}
