<?php

namespace App\Http\Controllers;

use App\Enums\SoundtrackSection;
use App\Models\SoundtrackSong;
use App\Settings\SoundtrackSettings;
use Illuminate\Contracts\View\View;

class SoundtrackController extends Controller
{
    public function __construct(
        readonly private SoundtrackSettings $settings,
    ) {
    }

    public function show(): View
    {
        return view('soundtrack', [
            'soundtrack' => $this->settings,
            'sections' => SoundtrackSong::allBySection(),
        ]);
    }
}
