<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class StaticController extends Controller
{
    public function privacy(): View
    {
        return view('privacy');
    }

    public function soundtrack(): View
    {
        $preshow = [
//            ['vgas2021 mix', 'beat_shobon', 'Preshow'],
//            ['MAIN MIX FOR FINAL EXPORT_2', 'fv.exe', 'Preshow'],
//            ['vga 2021', 'nostalgia_junkie', 'Preshow'],
//            ['vga2021', 'W.T. Snacks', 'Preshow'],
        ];

        $tracks = [
            ['Kokomo (Instrumental)', 'The Beach Boys', 'Most Hated Award'],
            ['Magic!', 'リアムMAZE1981', 'Least Worst Award'],
            ['Dire, Dire Docks', 'Koji Kondo', 'Water Connoisseur Award'],
            ['Uwaki Na Vacance', 'Kimini Mune Kyun', 'Redemption Arc Award'],
            ['A Line In The Sand', 'Tim & Geoff Follin', 'VR Award'],
            ['Bodies', 'Drowning Pool', 'Me at the Zoo Award'],
            ['Once Upon a Katamari', 'ICE CUBE SOUL', 'Bluetooth Speaker Award'],
            ['Kirby Air Riders', 'Skyah', 'Bluetooth Speaker Award'],
            ['Deltrarune', 'Black Knife', 'Bluetooth Speaker Award'],
            ['Clair Obscur Expedition 33', 'Paintress', 'Bluetooth Speaker Award'],
            ['Hollow Knight Silksong', 'Red Maiden', 'Bluetooth Speaker Award'],
            ['Clair Obscur Expedition 33', 'We Lost', 'Bluetooth Speaker Award'],
            ['Incognito', 'Stone Cold Heart', 'The Little Game that Could Award'],
            ['Peter Gunn Theme x Every Breath You Take', 'Henry Mancini x The Police', 'Hate Machine Award'],
            ['Opening', 'Puyo Puyo Sun (Saturn)', 'Pixels Are Art XIII'],
            ['Violet Eyed Beauty', 'Gen Taneichi', 'Kamige Award'],
            ['Turtle Bay Beach', 'Jonathan Wandag', 'Bikini Season Award'],
            ['Got the Grip', 'TeddyLoid ft. NANO', 'Bikini Season Award'],
            ['True Survivor', 'David Hasselhoff', 'Hasselhoff Award'],
            ['RazQ', 'Boy♂Next♂Ecuador', 'Hasselhoff Award'],
            ['Sem Você', 'Harper Rey', 'Game Play-ger'],
            ['You', 'Harper Rey ft. Matilda Gustafsson', 'Game Play-ger'],
            ['Ocean Shores', 'Garry Smith', 'Press X To Win The Award'],
            ['Mr. Driller G', 'Go Shiina & Maki Watabe', 'Treasure Island Award'],
            ['Fat Lip', 'Sum 41', 'Shitty Deflated Beach Ball Award'],
            ['Pick Up the Pieces', 'Akira Ishikawa', 'Shitty Deflated Beach Ball Award'],
            ['Lovers Paradise', 'Seaside Lovers', 'IP Twist Award'],
            ['Love Story', 'Layo & Bushwacka!', 'Most Hated of Least Worst Award'],
            ['Human Index', 'DV-i ft. K.K. Togashi', 'A E S T H E T I C S Award'],
            ['Kitsch', 'Yu Miyake', 'ASSthetics Award'],
            ['Opening Theme', 'Dead in the Water', 'Silent Protagonist Award'],
            ['Seto\'s Arrival', 'Yu-Gi-Oh: Duelists of the Roses', 'Scrappy Doo Award'],
            ['King\'s Dead', 'Jay Rock ft. Kendrick Lamar', 'Scrappy Doo Award'],
            ['Solo', 'RiveR', 'Scrappy Doo Award'],
            ['The One Reason', 'Tom & Jerry', 'Why Award'],
            ['Night of knights', 'ZUN & beatMARIO', 'Seal of Quality Award'],
            ['La Promesa', 'Daniel Indart & Jesús Alejandro Perez', 'Please Be Good Award'],
            ['Hazardous Environments', 'Kelly Bailey', 'Please Be Good Award'],
            ['Foxtrot Uniform Charlie Kilo', 'Bloodhound Gang', 'Humidity Lover Award'],
            ['Gelato Beach', 'Super Mario Sunshine', 'Humidity Lover Award'],
            ['We Were Set Up', 'Tangerine Dream, Woody Jackson, The Alchemist, Oh No & DJ Shadow', 'Blue Checkmark Award'],
            ['Beautiful Ruin (Summer Salt)', 'Masafumi Takada', 'Woah! It\'s a Big One Award'],
            ['and... Fish Hits!', 'Kenichi Tokoi', 'Woah! It\'s a Big One Award'],
            ['Your Eyes', 'Tatsuro Yamashita', 'Outro & Credits'],
            ['Lost in Time', 'Whitley', 'Intermission'],
            ['Steal My Sunshine', 'Len', 'Intermission'],
            ['In the Summertime', 'Thirsty Merc', 'Intermission'],
            ['Surfin\' Bird', 'Trashmen', 'Intermission'],
            ['Nostalgia of Island', 'Tatsuro Yamashita', 'Trailer']
        ];

        return view('soundtrack', [
            'preshow' => $preshow,
            'tracks' => $tracks,
        ]);
    }

    public function credits(): View
    {
        return view('credits');
    }

    public function trailers(): View
    {
        return view('trailers');
    }

    public function resultRedirect(): RedirectResponse
    {
        return redirect()->route('results');
    }

    public function promo(): View
    {
        return view('promo');
    }

    public function version(): View
    {
        $path = base_path('.git/');

        if (config('app.commit')) {
            $commit = config('app.commit');
            $commitSource = 'build';
        } else {
            if (file_exists($path)) {
                $head = trim(substr(file_get_contents($path . 'HEAD'), 4));
                $commit = trim(file_get_contents(sprintf($path . $head)));
                $commitSource = 'file';
            } else {
                $commit = $commitSource = null;
            }
        }

        return view('version', [
            'commit' => $commit,
            'commitSource' => $commitSource,
        ]);
    }
}
