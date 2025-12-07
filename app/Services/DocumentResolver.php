<?php

namespace App\Services;

use App\Http\Controllers\Documents\BaseDocumentController;
use App\Http\Controllers\Documents\DefaultController;
use App\Models\Document;

class DocumentResolver
{
    protected array $controllerMap = [
        'DefaultController' => DefaultController::class,
        'BlogController' => \App\Http\Controllers\Documents\BlogController::class,
        'ProductController' => \App\Http\Controllers\Documents\ProductController::class,
    ];

    public function resolveController(Document $document): BaseDocumentController
    {
        $controllerName = $document->controller;

        if (!$controllerName || !isset($this->controllerMap[$controllerName])) {
            return app(DefaultController::class);
        }

        $controllerClass = $this->controllerMap[$controllerName];

        return app($controllerClass);
    }

    public function getAvailableControllers(): array
    {
        return array_keys($this->controllerMap);
    }

    public function registerController(string $name, string $class): void
    {
        if (!is_subclass_of($class, BaseDocumentController::class)) {
            throw new \InvalidArgumentException("Controller must extend BaseDocumentController");
        }

        $this->controllerMap[$name] = $class;
    }
}
