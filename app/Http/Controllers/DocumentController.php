<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Services\DocumentResolver;
use Inertia\Response;

class DocumentController extends Controller
{
    public function __construct(
        protected DocumentResolver $resolver
    ) {}

    public function show(Document $document): Response
    {
        $controller = $this->resolver->resolveController($document);

        return $controller->show($document);
    }
}
