@extends('base.standard-themed')

@section('title', 'Credits')

@pushonce('css')
    <style>
        p {
            /*    font-family: "Corbel", Arial, sans-serif;*/
            font-size: 20px;
            line-height: 1.3em;
        }

        /*.awardHeaderContainer {*/
        /*    height: auto;*/
        /*    margin: 0 0 20px 0;*/
        /*    padding: 10px 0 20px;*/
        /*    background-image: url(/2020images/btnNav_middle.png);*/
        /*    background-size: contain;*/
        /*    background-repeat: no-repeat;*/
        /*    background-position: top center;*/
        /*    text-align: center;*/
        /*}*/

        /*@keyframes flicker {*/
        /*    0%  { opacity: 0; }*/
        /*    5% { opacity: 0; }*/
        /*    6% { opacity: 0.6; }*/
        /*    10% { opacity: 0.6; }*/
        /*    11% { opacity: 0.2; }*/
        /*    15% { opacity: 0.2; }*/
        /*    16% { opacity: 0.8; }*/
        /*    20% { opacity: 0.8; }*/
        /*    21% { opacity: 0.4; }*/
        /*    26% { opacity: 0.4; }*/
        /*    70% { opacity: 1; }*/
        /*}*/

        .credits .award-name {
            font-size: 40px;
            text-transform: uppercase;
            font-family: Chalk,sans-serif;
            color: white;
        }

        .credits {
            text-align: center;
            /*font-size: 20px;*/
            /*line-height: 30px;*/
            /*color: white;*/
            /*text-shadow: 1px 1px black;*/
            /*font-family: "Corbel", Arial, sans-serif;*/
        }

        .credits-section {
            font-family: "Handwritten",sans-serif;
            font-size: 30px;
            color: #e1e1e1;
            padding: 20px;
            position: relative;
            margin-bottom: 30px;
        }

        .credits-section.condensed p {
            margin-bottom: 0.3rem;
        }

        .credits-section a {
            color: var(--theme-blue);
        }

        .name {
            color: var(--theme-yellow);
            font-weight: bold;

            /*text-decoration: none;*/
            /*font-family: "OratorStd", "Courier New", serif;*/
            /*text-shadow: #00000080 3px 3px 3px;*/
            font-size: 22px;
            line-height: 1.2em;
            padding: 0;

            /*color: #fec544;*/
            /*font-size: 20px;*/
            /*font-family: "Trajan Pro 3", serif;*/
            /*text-shadow: 1px 1px black;*/
            margin-left: 3px;
            /*margin-right: 3px;*/
        }

        .FLAOT {
            display: inline-block;
            margin: 0 30px;
        }

        .specialThanks {
            margin-top: 20px;
            height: 100px;
            margin-bottom: 20px;
        }

        .specialThanks .awardName {
            font-size: 40px;
            line-height: 60px;
        }

        .award-header {
            background-size: contain;
            margin-bottom: 0px;
        }

        .award-header .award-name-container {
            padding: 10px;
        }

        .credits a:hover {
            text-decoration: underline;
            color: white;
        }

        .implying {
            color: lime;
        }

        .poster-background {
            padding: 30px;
        }

        .chalkboard {
            border-image-source: url("/2025images/bamboo-border.webp");
            border-image-slice: 70;
            border-image-width: 26px; 
            border-image-outset: 10px;
            margin: 10px;
            border-image-repeat: round round; 
            background-image: url("/2025images/chalkboard.webp");
            background-size: 100% 100%;
            padding:40px;
            margin-bottom: 30px;
        }
    </style>
@endpushonce

@section('content')
    <div class="center-container" style="padding-top: 68px;">
        @include('common.header')

        <div class="credits">
            <div class="row">
                <div class="col-md-6">

                    <div class="credits-section chalkboard">
                        <h3 class="award-name">Production Team</h3>

                        <p>
                            Directed by <span class="name">CounterTunes</span>
                        </p>

                        <p>
                            Produced by
                            <span class="name">CrazedJew</span>
                        </p>

                        <p>
                            Writing and research by
                            <span class="name">Donny Q</span>,
                            <span class="name">beatstar</span>,
                            <span class="name">CrazedJew</span>,
                            <span class="name">Degen</span>,
                            <span class="name">ehStuGatz</span>,
                            <span class="name">PixelAnon</span>,
                            <span class="name">Hg80</span>,
                            <span class="name">Hoffmann</span>,
                            <span class="name">Importuno</span>,
                            <span class="name">ReploidSham</span>
                            &amp;
                            <span class="name">Anonymous</span>
                        </p>

                        <p>
                            Website by
                            <span class="name">Clamburger</span>,
                            <span class="name">Lamer Gamer</span>
                            &amp;
                            <span class="name">ZedZagg</span>
                        </p>

                        <p>
                            Developer outreach handled by
                            <span class="name">beatstar</span>,
                            <span class="name">Donny Q</span>
                            &amp;
                            <span class="name">Nine Ten</span>
                        </p>
                    </div>

                    <div class="credits-section chalkboard">
                        <h3 class="award-name">Video Team</h3>

                        <p>
                            Video production and editing by
                            <span class="name">asperagus</span>,
                            <span class="name">beatstar</span>,
                            <span class="name">CounterTunes</span>,
                            <span class="name">Donny Q</span>,
                            <span class="name">Nanon</span>,
                            <span class="name">CrazedJew</span>
                            <span class="name">Nora</span>
                            &amp;
                            <span class="name">Anonymous</span>
                        </p>

                        <p>
                            Art and graphics by
                            <span class="name">Lamer Gamer</span>,
                            <span class="name">DraguO DoT</span>,
                            <span class="name">asperagus</span>
                            &amp;
                            <span class="name">Anonymous</span>
                        </p>
                    </div>

                    <div class="credits-section chalkboard">
                        <h3 class="award-name">Audio Team</h3>

                        <p>
                            Voiced by
                            <span class="name">Charm</span>,
                            <span class="name">cormano</span>,
                            <span class="name">CounterTunes</span>,
                            <span class="name">Donny Q</span>,
                            <span class="name">Gherrit White</span>,
                            <span class="name">PixelAnon</span>,
                            <span class="name">Imakuni</span>,
                            <span class="name">Nine Ten</span>,
                            <span class="name">ReploidSham</span>,
                            <span class="name">StephanosRex</span>,
                            <span class="name">CrazedJew</span>,
                            <span class="name">John C</span>,
                            <span class="name">Nora</span>
                            &amp;
                            <span class="name">Anonymous</span>
                        </p>
                        <p>
                            Music and audio by
                            <span class="name">fv.exe</span>,
                            <span class="name">Jab50Yen</span>,
                            <span class="name">beat_shobon</span>
                            &amp;
                            <span class="name">W.T. Snacks</span>
                        </p>
                    </div>

                    <div class="credits-section chalkboard">
                        <h3 class="award-name">/agdg/ Trailers</h3>

                        <div class="condensed">
                            <p><em><a target="_blank" href="https://aurorabase.itch.io/peaceincarnate">Peace Incarnate</a></em> &ndash; <span class="name">Aurora Base</span></p>
                            <p><em><a target="_blank" href="https://store.steampowered.com/app/3293420/JENNY">JENNY</a></em> &ndash; <span class="name">Impossible Things</span></p>
                            <p><em><a target="_blank" href="https://itch.io/profile/hypnic-jerk">John, Heaven Beyond</a></em> &ndash; <span class="name">Hypnic Jerk Software</span></p>
                            <p><em><a target="_blank" href="https://store.steampowered.com/app/3646460/Devil_Spire_Falls/">Devil Spire Falls</a></em> &ndash; <span class="name">Ithiro</span></p>
                            <p><em><a target="_blank" href="https://store.steampowered.com/app/2918930/Many_Mini_Typing_Games/">Many Mini Typing Games</a></em> &ndash; <span class="name">GirambQuamb</span></p>
                            <p><em><a target="_blank" href="https://store.steampowered.com/app/3712860/Wonder_of_Blue/">Wonder of Blue</a></em> &ndash; <span class="name">Velveteen</span></p>
                        </div>
                    </div>

                </div>

                <div class="col-md-6">

                    <div class="credits-section chalkboard">
                        <h3 class="award-name">Skits</h3>

                        <div class="condensed">
                            <p><em>Cringe Hill Z</em> &ndash; <span class="name">Neatoburrito Productions</span></p>
                            <p><em>Samus PTSD from MetSoy</em> &ndash; <span class="name">Neatoburrito Productions</span></p>
                            <p><em>Ode to France</em> &ndash; <span class="name">asperagus</span></p>
                            <p><em>OldFrens</em> &ndash; <span class="name">/v/3 Team</span></p>
                            <p><em>In Memoriam 2025</em> &ndash; <span class="name">AlciPolanco</span></p>
                            <p><em>Sonic somewhat Unleashed</em> &ndash; <span class="name">EarthQuake Jake</span></p>
                            <p><em>Sam Bridges Heavy Delivery For Strangers</em> &ndash; <span class="name">Neatoburrito Productions</span></p>
                            <p><em>Slicin' my log </em> &ndash; <span class="name">910</span></p>
                            <p><em>Stan</em> &ndash; <span class="name">nora</span></p>
                            <p><em>Dragdog</em> &ndash; <span class="name">nora</span></p>
                            <p><em>bonfire</em> &ndash; <span class="name">nora</span></p>
                            <p><em>DarksydeNil Gachi Stream</em> &ndash; <span class="name">CrazedJew</span></p>
                        </div>
                    </div>


                    <div class="credits-section chalkboard">
                        <h3 class="award-name">Bumpers</h3>
                        <div class="condensed">
                            <p><em>TTGL</em> &ndash; <span class="name">Nanon</span></p>
                            <p><em>"misc bumpers? I dunno man" (sic)</em> &ndash; <span class="name">nora</span></p>
                            <p><em>Water Spout</em> &ndash; <span class="name">asperagus</span></p>
                            <p><em>Compact Desert Island (/v/GA Throwback)</em> &ndash; <span class="name">asperagus</span></p>
                            <p><em>Wave Bumper</em> &ndash; <span class="name">Anonymous</span></p>
                        </div>
                    </div>
                </div>

                <div class="credits-section chalkboard">
                    <h3 class="award-name">Special Thanks</h3>

                    <div class="condensed">
                        <p><span class="name">PhoneEatingBear</span></p>
                        <p><span class="name">Ipswich City Council</span></p>
                        <p><span class="name">Supremax</span></p>
                        <p><span class="name">Stuff3</span></p>
                        <p><span class="name">Clamburger, for 15 years of service</span></p>
                        <p><span class="name">/v/</span></p>
                        <p><span class="name">...and <strong>(You)!</strong></span></p>
                    </div>
                </div>

                <div class="col">
                    <div class="credits-section" style="margin-top: 60px; color: white;">
                        <p style="margin-bottom: 60px;">
                            This awards show contains chemicals known to the State of California to cause cancer and birth defects or other reproductive harm.
                        </p>

                        <p class="implying">&gt;they contribute to a shitty online video game award show</p>
                        <p class="implying">&gt;they take their jobs very seriously</p>
                        <p class="implying">&gt;they do it for free</p>

                        <img src="{{ asset('img/for-free.jpg') }}" style="width: 200px; -webkit-mask-image: url(/2024images/paper-edge-mask.png); -webkit-mask-size: 100% 100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
