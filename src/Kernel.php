<?php

namespace App;

use App\Controller\HomeController;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel
{

    private EntityManagerInterface $entityManager;

    public function __construct()
    {
        $this->entityManager = $this->buildEntityManager();

    }

    public function handle(Request $request)
    {
        $response = $this->route($request);
        $response->send();
    }

    private function route(Request $request): Response
    {
        $defaultController = HomeController::class;

        //we get the route here and clean it
        $path = $request->getPathInfo();
        $path = trim($path, "/");

        $className = $defaultController;
        $method = "index";
        if (strlen($path) > 0) {
            // if subroute is not specified, it is merged to /index
            list($controller, $method) = [...explode("/", $path), "index"];
            $className = "App\\Controller\\" . ucfirst($controller) . "Controller";
            if ($className === $defaultController && $method === "index") {
                return new RedirectResponse("/");
            }
        }

        if (!class_exists($className)
            || !method_exists($className, $method)) {
            return new Response("Controller not found", Response::HTTP_NOT_FOUND);
        }

        $container = [
            Request::class => $request,
            EntityManagerInterface::class => $this->getEntityManager(),
        ];

        $resolvedArguments = $this->paramResolver($className, $method, $container);
        return call_user_func_array([new $className(), $method], $resolvedArguments);

    }

    private function paramResolver(string $className, string $method, array $container): array
    {
        $reflexion = new \ReflectionMethod($className, $method);
        $params = $reflexion->getParameters();
        $paramValues = [];

        foreach ($params as $param) {
            if ($param->hasType() && isset($container[$param->getType()->getName()])) {
                $paramValues[$param->getPosition()] = $container[$param->getType()->getName()];
            } else {
                $paramValues[$param->getPosition()] = $container[Request::class]->get($param->getName(), null);
            }
        }

        return $paramValues;
    }


    private function buildEntityManager(): EntityManager
    {
        // Create a simple "default" Doctrine ORM configuration for Annotations
        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: array(__DIR__."/../src"),
            isDevMode: true,
        );

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../db.sqlite',
        ], $config);

        // obtaining the entity manager
        return new EntityManager($connection, $config);
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;

    }


}