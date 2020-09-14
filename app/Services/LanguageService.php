<?php


namespace App\Services;

use App\Models\Language;
use Illuminate\Http\Request;

class LanguageService
{
    public function getLanguages()
    {
        $languages = Language::all();
        return $languages;
    }


}
