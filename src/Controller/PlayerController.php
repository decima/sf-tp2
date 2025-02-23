<?php

namespace App\Controller;


use App\Entity\Game;
use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlayerController extends AbstractController
{

    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /**
         * @todo lister les joueurs
         */
        $players = $entityManager->getRepository(Player::class)->findAll();
        return $this->render("player/index", ["players" => $players]);

    }

    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        $player = new Player();
        if ($request->getMethod() == Request::METHOD_POST) {
            $player->setEmail($request->request->get("email"));
            $player->setUsername($request->request->get("username"));
            $entityManager->persist($player);
            $entityManager->flush();
            return $this->redirectTo("/player");
        }
        return $this->render("player/form", ["player" => $player]);
    }


    public function show($id, EntityManagerInterface $entityManager): Response
    {
        $player = $entityManager->getRepository(Player::class)->find($id);
        return $this->render("player/show", ["player" => $player, "availableGames" => $entityManager->getRepository(Game::class)->findAll()]);
    }


    public function edit($id, Request $request, EntityManagerInterface $entityManager): Response
    {
        $player = $entityManager->getRepository(Player::class)->find($id);

        if ($request->getMethod() == Request::METHOD_POST) {
            $player->setEmail($request->request->get("email"));
            $player->setUsername($request->request->get("username"));
            $entityManager->flush();
            return $this->redirectTo("/player");
        }
        return $this->render("player/form", ["player" => $player]);


    }

    public function delete($id, EntityManagerInterface $entityManager): Response
    {
        $player = $entityManager->getRepository(Player::class)->find($id);

        $entityManager->remove($player);
        $entityManager->flush();
        return $this->redirectTo("/player");

    }

    public function addgame($id, Request $request, EntityManagerInterface $em): Response
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $player = $em->getRepository(Player::class)->find($id);
            $game = $em->getRepository(Game::class)->find($request->get("game"));
            $player->addOwnedGame($game);
            $em->flush();
        }
        return $this->redirectTo("/player/show?id=$id");
    }

}
