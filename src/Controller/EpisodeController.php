<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use ContainerJwVP2bS\PaginatorInterface_82dac15;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EpisodeController extends AbstractController
{

    #[Route("/episode/list", name: "episode_list")]
    public function list(EpisodeRepository $episodeRepository, PaginatorInterface $paginator, Request $request)
    {
         // systeme de pagination de 1-5
        $episodes = $paginator->paginate(
            $episodeRepository->findAll(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render("episode/list.html.twig", [
            'episodes' => $episodes
        ]);
    }

    #[Route("/episode/show/{id}", name: "episode_show")]
    public function show(int $id, EpisodeRepository $episodeRepository)
    {
        $episode = $episodeRepository->findAll($id);

        if (!$episode) {
            $this->addFlash("danger", "L'episode est introuvable.");
            return $this->redirectToRoute("episode_list");
        }
        return $this->render("episode/show.html.twig", [
            'episode' => $episode
        ]);
    }
}
