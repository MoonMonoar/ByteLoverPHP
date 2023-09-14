<?php
//Rquires langset.php($lang) and strings.php($strings)
Class Templates {
    static function headMeta(){
        return '<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
        <link rel="apple-touch-icon" sizes="57x57" href="/img/favicons/apple-icon-57x57.png">
        <link rel="apple-touch-icon" sizes="60x60" href="/img/favicons/apple-icon-60x60.png">
        <link rel="apple-touch-icon" sizes="72x72" href="/img/favicons/apple-icon-72x72.png">
        <link rel="apple-touch-icon" sizes="76x76" href="/img/favicons/apple-icon-76x76.png">
        <link rel="apple-touch-icon" sizes="114x114" href="/img/favicons/apple-icon-114x114.png">
        <link rel="apple-touch-icon" sizes="120x120" href="/img/favicons/apple-icon-120x120.png">
        <link rel="apple-touch-icon" sizes="144x144" href="/img/favicons/apple-icon-144x144.png">
        <link rel="apple-touch-icon" sizes="152x152" href="/img/favicons/apple-icon-152x152.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-icon-180x180.png">
        <link rel="icon" type="image/png" sizes="192x192"  href="/img/favicons/android-icon-192x192.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="96x96" href="/img/favicons/favicon-96x96.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
        <link rel="manifest" href="/img/favicons/manifest.json?v=1.2">
        <link rel="stylesheet" href="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.5.0/build/styles/default.min.css">
        <meta name="msapplication-TileImage" content="/img/favicons/ms-icon-144x144.png">
        <meta name="description" content="Start to learn programming from the very basics. Book a course now and attend online classes with quizzes, exams, recoded videos and study materials.">
        <meta property="og:image" content="https://bytelover.com/img/logos/Ogimage.png"/>
        <meta property="og:image:type" content="image/png"/>
        <meta property="og:image:width" content="1200"/>
        <meta property="og:image:height" content="630"/>
        <script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-app.js"></script>
        <script src="https://www.gstatic.com/firebasejs/7.16.1/firebase-messaging.js"></script>';
    }
    static function footerHtml(){
        global $strings, $lang;
        return '<footer class="notranslate">
        <section class="fob">
            <div class="cflex">
                <img class="logo" src="/img/logos/ByteLoverBanner.svg">
            </div>
            <div>
                <div class="b flk mt25 nmt">'.$strings["links"][$lang].'</div>
                <ul>
                <li>
                <a href="/about/?ref=home_header"><i class="fa fa-info-circle"></i>'.$strings["about"][$lang].'</a>
                </li>
                <li>
                <a href="/policies/terms/?ref=footer"><i class="fa fa-file-contract"></i>'.$strings["terms"][$lang].'</a>
                </li>
                <li>
                <a href="/policies/Purchase%20policy.pdf?v=1.16.4.23"><i class="fa fa-shopping-cart"></i>'.$strings["purchase_rules"][$lang].'</a>
                </li>
                <li>
                <a href="/policies/privacy/?ref=footer"><i class="fa-solid fa-cookie"></i>'.$strings["cookies"][$lang].'</a>
                </li>
                </ul>
            </div>
            <div>
                <div class="b flk mt25">'.$strings["contacts"][$lang].'</div>
                <ul>
                <li>
                <a href="//m.me/immo2n" target="_blank"><i class="fa fa-brands fa-facebook-messenger"></i>'.$strings["messenger"][$lang].'</a>
                </li> 
                <li>
                <a href="//facebook.com/immo2n" target="_blank"><i class="fa fa-brands fa-facebook"></i>'.$strings["facebook"][$lang].'</a>
                </li> 
                <li>
                <a href="//moonmonoar.github.io/portfolio/" target="_blank"><i class="fa fa-globe"></i>'.$strings["website"][$lang].'</a>
                </li>
                <li>
                <a href="tel:+8801317215403"><i class="fa fa-phone"></i>+8801317215403</a>
                </li>
                </ul>
            </div>
            <div>
                <div class="b flk mt25">'.$strings["pay_method"][$lang].'</div>
                <div class="ml10 dg mt1e">
                    <img src="/img/logos/Bkash.svg" alt="bKash" class="pyr">
                    <img src="/img/logos/Nagad.svg" alt="Nagad" class="pyr mml15">
                    <img src="/img/logos/Rocket.svg" alt="Rocket" class="pyr mml15">
                </div>
            </div>
        </section>
        <div class="prd">
        <div class="addr">
        '.$strings["full_addr"][$lang].' </div>
        '.$strings["moon_prod"][$lang].'
        </div>
        <script src="//cdn.jsdelivr.net/gh/highlightjs/cdn-release@11.5.0/build/highlight.min.js"></script>    
          <script>hljs.highlightAll()</script>
    </footer>';
    }
    static function tutorialAtricleNotFound(){
        return '<div class="notice-box">
                   <h1>Article Not Found</h1>
                   <p>We\'re sorry, but the article you requested could not be found. It may have been removed or is temporarily unavailable.</p>
                   <p>Please check the URL or return to the <a href="/solo/?ref=not_found">homepage</a> to explore other content.</p>
               </div>';
    }
}