<?php

namespace App\Http\Controllers;

use App\Settings\AppSettings;
use Illuminate\Contracts\View\View;

class LauncherController extends Controller
{
    public function __construct(
        private readonly AppSettings $settings,
    ) {
    }

    public function countdown(): View
    {
        if ($this->settings->stream_time) {
            $streamDate = $this->settings->stream_time->setTimezone('America/New_York');
        } else {
            $streamDate = null;
        }

        $timezones = [
            'Honolulu' => 'Pacific/Honolulu',
            'Anchorage' => 'America/Anchorage',
            'Seattle (PST)' => 'America/Los_Angeles',
            'Denver (MST)' => 'America/Denver',
            'Chicago (CST)' => 'America/Chicago',
            'New York (EST)' => 'America/New_York',
            'Rio de Janeiro' => 'America/Sao_Paulo',
            'London (GMT)' => 'Europe/London',
            'Paris (CET)' => 'Europe/Paris',
            'Athens (EET)' => 'Europe/Athens',
            'Moscow' => 'Europe/Moscow',
            'Singapore' => 'Asia/Singapore',
            'Japan Time' => 'Asia/Tokyo',
            'Brisbane (AEST)' => 'Australia/Brisbane',
            'Sydney (AEDT)' => 'Australia/Sydney',
            'Auckland' => 'Pacific/Auckland',
        ];

        $otherTimezonesLink = sprintf(
            'https://www.timeanddate.com/worldclock/fixedtime.html?msg=' . year() . '+Vidya+Gaem+Awards&iso=%s&p1=179',
            $streamDate ? $streamDate->format("Y-m-d\TH:i:s") : ''
        );

        return view('countdown', [
            'streamDate' => $streamDate,
            'timezones' => $timezones,
            'otherTimezonesLink' => $otherTimezonesLink,
        ]);
    }

    public function stream(): View
    {
        if ($this->settings->stream_time) {
            $streamDate = $this->settings->stream_time->setTimezone('America/New_York');
            $showCountdown = $streamDate->isFuture();
        } else {
            $streamDate = null;
            $showCountdown = false;
        }

        return view('stream', [
            'streamDate' => $streamDate,
            'countdown' => $showCountdown,
        ]);
    }

    public function finished(): View
    {
        return view('finished');
    }
}
