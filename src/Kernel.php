<?php

namespace App;

use App\Controller\HomeController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel
{

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
                /** @todo we called index of $defaultController, make a redirection to / here WITHOUT using the header function. */
                return new RedirectResponse("/");
            }
        }

        if (!class_exists($className)
            || !method_exists($className, $method)) {
            /** @todo return a not found response here (status code 404) */
            return new Response("Controller not found", Response::HTTP_NOT_FOUND);
        }

        $resolvedArguments = $this->paramResolver($className, $method, [Request::class => $request]);
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

}