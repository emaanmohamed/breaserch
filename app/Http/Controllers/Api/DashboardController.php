<?php

namespace App\Http\Controllers\Api;

use App\Services\DocumentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {

    }
}
