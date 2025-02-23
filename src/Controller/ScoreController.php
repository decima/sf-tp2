<?php

namespace App\Controller;


use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Score;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class ScoreController extends AbstractController
{


    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {

        $scores = $entityManager->getRepository(Score::class)->findAll();

        $games = $entityManager->getRepository(Game::class)->findAll();
        $players = $entityManager->getRepository(Player::class)->findAll();

        return $this->render("score/index", ["scores" => $scores,
            "games" => $games, "players" => $players]);
    }

    public function add(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->getMethod() == Request::METHOD_POST) {
            $game = $entityManager->getRepository(Game::class)->find($request->get("game"));
            $player = $entityManager->getRepository(Player::class)->find($request->get("player"));
            $score = new Score();
            $score->setPlayer($player);
            $score->setGame($game);
            $score->setScore($request->get("score"));
            $entityManager->persist($score);
            $entityManager->flush();
        }

        return $this->redirectTo("/score");

    }

}