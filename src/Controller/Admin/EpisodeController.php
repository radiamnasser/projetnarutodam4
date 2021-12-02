<?php

namespace App\Controller\Admin;

use App\Entity\Episode;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EpisodeController extends AbstractController
{

    #[Route("/Admin/episode/create", name: "admin_episode_create")]
    public function create(Request $request, EntityManagerInterface $em)
    {
        // j'ai besoin de creer une instance de la classe Episode
        $episode = new Episode();

        // j'ai besoin de mon formulaire, a qui je vais donner l'instance de ma class
        $form = $this->createForm(EpisodeType::class, $episode);

        //je demande a mon formulaire de chercher, de s'occuper de la requette 
        $form->handleRequest($request);

        //si tu as bien vu qu'il y avait des donnes a traiiter dans la requette 
        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($episode); // prepare l'episode a etre envoyer en bdd

            $em->flush(); //fait l'envoie

            $this->addFlash("success", "l'épisode " . $episode->getName() . " a bien été ajouté.");

            return $this->redirectToRoute("admin_episode_create");
        }
        return $this->render("admin/episode/create.html.twig", [

            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/episode/list', name: 'admin_episode_list')]
    public function list(EpisodeRepository $episodeRepository)
    {
        // cette ligne fait appel a la base de donnee et recuppere la liste des episodes

        $episodes = $episodeRepository->findAll();

        //dd($episodes);
        // j'envoie la vue les episodes que je viens de recuperer
        return $this->render("admin/episode/list.html.twig", [
            'episodes' => $episodes
        ]);
    }
    #[Route('/admin/episode/show/{id}', name: 'admin_episode_show')]
    public function show(int $id, EpisodeRepository $episodeRepository): Response
    {
        $episode = $episodeRepository->find($id);
        // si l'episode ne peut pas etre trouver en base de donnee alors je redirige vers la listes des episodes
        //et j'affiche un message flash
        if (!$episode) {
            $this->addFlash("danger", "L'episode est introuvable.");
            return $this->redirectToRoute("admin_episode_list");
        }
        return $this->render("admin/episode/show.html.twig", [
            'episode' => $episode
        ]);
    }
    #[Route('/admin/episode/delete/{id}', name: 'admin_episode_delete')]

    public function delete(int $id, EpisodeRepository $episodeRepository, EntityManagerInterface $em): Response

    {
        $episode = $episodeRepository->find($id);

        if (!$episode) {
            $this->addFlash("danger", "Cet épisode est introuvable");
            return $this->redirectToRoute("admin_episode_list");
        }

        $em->remove($episode); // je prepare la requette

        $em->flush(); // je l'envoi

        $this->addFlash("success", "L'episode a bien été supprimé.");

        return $this->redirectToRoute("admin_episode_list");
    }
    // permet de modifier les propriete d'un episode a partir de son id
    #[Route('/admin/episode/edit/{id}', name: 'admin_episode_edit')]
    public function edit(int $id, EpisodeRepository $episodeRepository, EntityManagerInterface $em, Request $request)
    {
        $episode = $episodeRepository->find($id);

        if (!$episode) {
            // si tu ne trouve pas l'espisode affiche moi ce qui suit
            $this->addFlash("danger", "Cet épisode est introuvable en base de donnée");
            //redirection vers la liste des episodes
            return $this->redirectToRoute("admin_episode_list");
        }

        // creation du formulaire
        $form = $this->createForm(EpisodeType::class, $episode);

        // je verifie si l'utilisateur a bien soumis le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $em->flush();// envoyer la modification en bdd

            $this->addFlash("success", "L'épisode a bien été modifié.");

            return $this->redirectToRoute("admin_episode_list");
        }
        // sinon
        return $this->render("admin/episode/edit.html.twig",[
            'form' => $form->createView()
        ]);
    }
}
