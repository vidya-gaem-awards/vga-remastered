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
            ['Dire, Dire Docks', 'Super Mario 64', 'Water Connoisseur Award'],
            ['Uwaki Na Vacance', 'Kimini Mune Kyun', 'Redemption Arc Award'],
            ['A Line In The Sand', 'Tim & Geoff Follin (Plok!)', '/vr/ Award'],
            ['Bodies', 'Drowning Pool', 'Me at the Zoo Award'],
            ['Incognito', 'Stone Cold Heart', 'The Little Game that Could Award'],
            ['Peter Gunn Theme x Every Breath You Take (from The Sopranos)', 'Henry Mancini x The Police', 'Hate Machine Award'],
            ['Opening', 'Puyo Puyo Sun (Saturn)', 'Pixels Are Art XIII'],
            ['Violet Eyed Beauty (from Dead or Alive Paradise)', 'Gen Taneichi', 'Kamige Award'],
            ['Turtle Bay Beach (from HuniePop), Got the Grip (from New Panty and Stocking with Garterbelt)', 'Jonathan Wandag, TeddyLoid ft. NANO', 'Bikini Season Award'],
            ['True Survivor', 'David Hasselhoff', 'Hasselhoff Award'],
            ['Sem Você, You', 'Harper Rey, Harper Rey ft. Matilda Gustafsson', 'Game Play-ger'],
            ['Ocean Shores (from Rocket Power: Beach Bandits)', 'Garry Smith', 'Press X To Win The Award'],
            ['Mr. Driller G', 'Go Shiina & Maki Watabe', 'Treasure Island Award'],
            ['Pick Up the Pieces', 'Sum 41, Akira Ishikawa', 'Shitty Deflated Beach Ball Award'],
            ['Lovers Paradise', 'Seaside Lovers', 'IP Twist Award'],
            ['Love Story', 'Layo & Bushwacka!', 'Most Hated of Least Worst Award'],
            ['Human Index', 'DV-i ft. K.K. Togashi', 'A E S T H E T I C S Award'],
            ['Kitsch (from Tekken 4)', 'Yu Miyake', 'ASSthetics Award'],
            ['Opening Theme', 'Dead in the Water', 'Silent Protagonist Award'],
            ['Seto\'s Arrival (noms), King\'s Dead (speech), Solo (speech 2)', 'Yu-Gi-Oh: Duelists of the Roses, Jay Rock ft. Kendrick Lamar, RiveR', 'Scrappy Doo Award'],
            ['The One Reason', 'Tom & Jerry', 'Why Award'],
            ['Night of knights', 'ZUN & beatMARIO', 'Seal of Quality Award'],
            ['La Promesa (from Tropico: Paradise Island), Hazardous Environments (from Half-Life)', 'Daniel Indart & Jesús Alejandro Perez, Kelly Bailey', 'Please Be Good Award'],
            ['Foxtrot Uniform Charlie Kilo / Gelato Beach', 'Bloodhound Gang, Super Mario Sunshine', 'Humidity Lover Award'],
            ['We Were Set Up (from Music of Grand Theft Auto V - The Score)', 'Tangerine Dream, Woody Jackson, The Alchemist, Oh No & DJ Shadow', 'Blue Checkmark Award'],
            ['Beautiful Ruin (Summer Salt) (from Danganronpa 2: Goodbye Despair), and... Fish Hits! (from Sonic Adventure)', 'Masafumi Takada, Kenichi Tokoi', 'Woah! It&#x27;s a Big One Award'],
            ['Your Eyes', 'Tatsuro Yamashita', 'Outro & Credits'],
            ['Lost in Time, Steal My Sunshine, In the Summertime, Surfin\' Bird', 'Whitley, Len, Thirsty Merc, Trashmen', 'Intermission'],
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
