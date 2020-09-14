<?php

namespace App\Http\Controllers\Api;

use App\Services\LanguageService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LanguagesController extends ApiController
{
    private $languageService;
    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function getLanguages()
    {
        $languages = $this->languageService->getLanguages();
        return $this->ApiResponseData($languages, 200);
    }
}
