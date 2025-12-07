<?php

namespace App\Http\Controllers\Documents;

class DefaultController extends BaseDocumentController
{
    protected function getComponent(): string
    {
        return 'Documents/Default';
    }
}
