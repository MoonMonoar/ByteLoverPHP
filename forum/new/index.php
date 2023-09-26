<?php
ob_start();
session_start();
require_once '../../php/global.php';
require_once '../../php/dbconfig.php';
require_once '../../php/strings.php';
require_once '../../php/langset.php';
require_once '../../php/templates.php';
require_once '../../php/autologin.php';
global $lang ,$strings, $global_key_theme, $script_version, $alter_lang_link, $alter_lang_name;
$templates = new Templates();
$security = new Security();
$links = new Links();
$dbconfig = new DBconfig();
$courses = new Courses();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang;?>">

<head>
    <title>
        <?php echo $strings["page_title"][$lang];?>
    </title>
    <?php
    echo $templates->headMeta();
    ?>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/icons/fontawesome-6.3.0/css/all.min.css">
    <link rel="stylesheet" href="/plugins/izi/css/iziToast.min.css">
    <?php
    if($global_key_theme == "dark"){
        ?>
    <link rel="stylesheet" href="/css/app-dark.css?v=<?php echo $script_version?>">
    <meta name="theme-color" content="#212121">
    <meta name="msapplication-TileColor" content="#212121">
    <?php
    }
    else {
        ?>
    <link rel="stylesheet" href="/css/app-light.css?v=<?php echo $script_version;?>">
    <meta name="theme-color" content="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <?php
    }
    ?>
    <link rel="stylesheet" href="/css/app.css?v=<?php echo $script_version;?>">
    <link rel="stylesheet" href="/css/icons/fontawesome-6.3.0/css/all.min.css">
    <link rel="stylesheet" href="/plugins/tags/main.css">
</head>

<body class="forum-base">

    <header>
        <div class="pad header_divs">
            <div>
                <a href="/" title="<?php echo $strings["prompt_home"][$lang];?>">
                    <img id="desk_image" src="/img/logos/ByteLoverBanner.svg" alt="Banner">
                    <img id="mobi_image" src="/img/logos/ByteLoverLogo.svg" alt="Logo">
                </a>
            </div>
            <div class="header_links lflex">
                <div class="top_links">
                    <a href="<?php echo $alter_lang_link;?>"><?php echo $alter_lang_name;?></a>
                    <a href="/courses/?ref=home_header"><?php echo $strings["courses"][$lang];?></a>
                    <a href="/dashboard/?push=<?php echo uniqid();?>"><?php echo $strings["dashboard"][$lang];?></a>
                </div>
                <div class="top_menu">
                    <i class="fa fa-list mb"></i>
                </div>
            </div>
            <div class="header_opts rflex">
                <div class="ml12">
                    <a href="<?php echo $links->themeLink();?>">
                        <button class="ub cflex nob rob">
                            <?php
                        if($global_key_theme == "dark"){
                            echo '<i class="fa-solid fa-sun"></i>';
                        }
                        else {
                            echo '<i class="fa-solid fa-moon"></i>';
                        }
                        ?>
                        </button>
                    </a>
                </div>
                <div class="ml12">
                    <a href="tel:+8801317215403">
                        <button class="ub cflex" title="<?php echo $strings["prompt_support"][$lang];?>">
                            <span><?php echo $strings["call_now"][$lang];?></span><i class="fa fa-phone"></i>
                        </button>
                    </a>
                </div>
                <div class="ml12">
                    <a href="/signup/?ref=header">
                        <button class="ub cflex" title="<?php echo $strings["prompt_signup"][$lang];?>">
                            <span><?php echo $strings["joinus"][$lang];?></span><i class="fa fa-user-plus"></i>
                        </button>
                    </a>
                </div>
                <div>
                    <a href="/login/?ref=header">
                        <button class="ub cflex" title="<?php echo $strings["prompt_login"][$lang];?>">
                            <span><?php
                                if(!isset($_SESSION['user_id'])){
                                    echo $strings["login"][$lang];
                                    echo '</span> <i class="fa fa-sign-in"></i>';
                                }
                                else {
                                    $profile = new Profile();
                                    echo $profile->getUsername($dbconfig->getConnection(), $_SESSION['user_id']);
                                    echo '</span>';
                                    ?>
                                <img class="h_ui"
                                    src="<?php echo $profile->getImage($dbconfig->getConnection(), $_SESSION['user_id']);?>">
                                <?php
                                }
                                ?>
                        </button>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section id="main_body" class="dash filler single">

        <div class="add_problem">
            <div class="new_problem">
                <div class="new_prb_t">Submit problem</div>
                <div class="prob_draft mb10">
                    <i class="fa fa-check-circle mr2"></i> Drafted
                </div>
                <div class="area">
                    <div class="prob_mes">
                        Problem statement
                    </div>
                    <label for="problemTitle"></label>
                    <input onkeyup="draft()" type="text" class="ptitEdit" maxlength="80" id="problemTitle"
                        placeholder="C nested loop issue..." />
                </div>
                <div class="area">
                    <div class="prob_mes mt10">
                        Problem explanation
                    </div>
                    <div class="optionList">
                        <button id="insC" class="btn btn-primary apb pao"><i class="fa fa-code"></i> Insert
                            code</button>
                        <button id="rmvC" class="btn btn-primary apb pao ml5"><i class="fa fa-eraser"></i> Remove
                            code</button>
                    </div>
                    <label for="problemContent"></label>
                    <textarea onpaste="openCoder(event)" onkeyup="draft()" id="problemContent" class="problem_body"
                        placeholder="Explain the problem and insert code if necessary"></textarea>
                </div>

                <div class="area mt5">
                    <div class="prob_mes">
                        Add files (Photos only)
                    </div>

                    <div class="horizontalScrollbar" id="prob-imgs"></div>

                    <div class="mb-3">
                        <label for="prob-file" class="form-label"></label>
                        <input accept="image/*" class="form-control pfw" type="file" id="prob-file">
                    </div>
                    
                </div>

                <div class="area mt5">
                    <div class="prob_mes">
                        Add tags (e.g C, C++, Java)
                    </div>
                    <div id="tags"></div>
                </div>

                <div class="area mt5 cflex pf-sb">
                    <button class="btn btn-primary apb pao fsb fnsb">
                        Submit
                    </button>
                </div>

            </div>
        </div>

    <div class="code_editor hide">
        <div class="ceog">
            <div>
                <div class="horizontalScrollbar editorOptions">

                        <div data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Ctrl+I" class="ceom mr5 actb" id="insertCode">
                            <i class="fa fa-check mr5"></i>Insert
                        </div>

                        <div class="ceom mr5">
                            &middot
                        </div>
                    
                        <div class="dropdown ceom nopad">
                            <div class="ceom actb dt" data-bs-toggle="dropdown" aria-expanded="false"> File </div>

                                <ul class="dropdown-menu">
                                    <li id="editor_down" class="emlib actb"><i class="fa-solid fa-download mr5"></i> Download <span class="hint_text ml5">Ctrl+S</span></li>
                                </ul>
                        </div>
                    
                        <div class="dropdown ceom">

                            <div class="ceom actb dt" data-bs-toggle="dropdown" aria-expanded="false"> View </div>

                                <ul class="dropdown-menu">
                                    <li>
                                    <div class="cmt2">
                                    <i class="fa-solid fa-code mr5"></i> Editor Language
                                    </div>
                                    <label for="editor-lang"></label>
                                    <select id="editor-lang">
                                        <option value="ace/mode/c_cpp">C and C++</option>
                                        <option value="ace/mode/python">Python</option>
                                        <option value="ace/mode/java">Java</option>
                                        <option value="ace/mode/kotlin">Kotlin</option>
                                        <option value="ace/mode/html">HTML</option>
                                        <option value="ace/mode/html_elixir">HTML (Elixir)</option>
                                        <option value="ace/mode/html_ruby">HTML (Ruby)</option>
                                        <option value="ace/mode/abap">ABAP</option>
                                        <option value="ace/mode/abc">ABC</option>
                                        <option value="ace/mode/actionscript">ActionScript</option>
                                        <option value="ace/mode/ada">ADA</option>
                                        <option value="ace/mode/alda">Alda</option>
                                        <option value="ace/mode/apache_conf">Apache Conf</option>
                                        <option value="ace/mode/apex">Apex</option>
                                        <option value="ace/mode/aql">AQL</option>
                                        <option value="ace/mode/asciidoc">AsciiDoc</option>
                                        <option value="ace/mode/asl">ASL</option>
                                        <option value="ace/mode/assembly_x86">Assembly x86</option>
                                        <option value="ace/mode/autohotkey">AutoHotkey / AutoIt</option>
                                        <option value="ace/mode/batchfile">BatchFile</option>
                                        <option value="ace/mode/bibtex">BibTeX</option>
                                        <option value="ace/mode/c9search">C9Search</option>
                                        <option value="ace/mode/cirru">Cirru</option>
                                        <option value="ace/mode/clojure">Clojure</option>
                                        <option value="ace/mode/cobol">Cobol</option>
                                        <option value="ace/mode/coffee">CoffeeScript</option>
                                        <option value="ace/mode/coldfusion">ColdFusion</option>
                                        <option value="ace/mode/crystal">Crystal</option>
                                        <option value="ace/mode/csharp">C#</option>
                                        <option value="ace/mode/csound_document">Csound Document</option>
                                        <option value="ace/mode/csound_orchestra">Csound</option>
                                        <option value="ace/mode/csound_score">Csound Score</option>
                                        <option value="ace/mode/css">CSS</option>
                                        <option value="ace/mode/curly">Curly</option>
                                        <option value="ace/mode/cuttlefish">Cuttlefish</option>
                                        <option value="ace/mode/d">D</option>
                                        <option value="ace/mode/dart">Dart</option>
                                        <option value="ace/mode/diff">Diff</option>
                                        <option value="ace/mode/dockerfile">Dockerfile</option>
                                        <option value="ace/mode/dot">Dot</option>
                                        <option value="ace/mode/drools">Drools</option>
                                        <option value="ace/mode/edifact">Edifact</option>
                                        <option value="ace/mode/eiffel">Eiffel</option>
                                        <option value="ace/mode/ejs">EJS</option>
                                        <option value="ace/mode/elixir">Elixir</option>
                                        <option value="ace/mode/elm">Elm</option>
                                        <option value="ace/mode/erlang">Erlang</option>
                                        <option value="ace/mode/forth">Forth</option>
                                        <option value="ace/mode/fortran">Fortran</option>
                                        <option value="ace/mode/fsharp">FSharp</option>
                                        <option value="ace/mode/fsl">FSL</option>
                                        <option value="ace/mode/ftl">FreeMarker</option>
                                        <option value="ace/mode/gcode">Gcode</option>
                                        <option value="ace/mode/gherkin">Gherkin</option>
                                        <option value="ace/mode/gitignore">Gitignore</option>
                                        <option value="ace/mode/glsl">Glsl</option>
                                        <option value="ace/mode/gobstones">Gobstones</option>
                                        <option value="ace/mode/golang">Go</option>
                                        <option value="ace/mode/graphqlschema">GraphQLSchema</option>
                                        <option value="ace/mode/groovy">Groovy</option>
                                        <option value="ace/mode/haml">HAML</option>
                                        <option value="ace/mode/handlebars">Handlebars</option>
                                        <option value="ace/mode/haskell">Haskell</option>
                                        <option value="ace/mode/haskell_cabal">Haskell Cabal</option>
                                        <option value="ace/mode/haxe">haXe</option>
                                        <option value="ace/mode/hjson">Hjson</option>
                                        <option value="ace/mode/ini">INI</option>
                                        <option value="ace/mode/io">Io</option>
                                        <option value="ace/mode/ion">Ion</option>
                                        <option value="ace/mode/jack">Jack</option>
                                        <option value="ace/mode/jade">Jade</option>
                                        <option value="ace/mode/javascript">JavaScript</option>
                                        <option value="ace/mode/jexl">JEXL</option>
                                        <option value="ace/mode/json">JSON</option>
                                        <option value="ace/mode/json5">JSON5</option>
                                        <option value="ace/mode/jsoniq">JSONiq</option>
                                        <option value="ace/mode/jsp">JSP</option>
                                        <option value="ace/mode/jssm">JSSM</option>
                                        <option value="ace/mode/jsx">JSX</option>
                                        <option value="ace/mode/julia">Julia</option>
                                        <option value="ace/mode/latex">LaTeX</option>
                                        <option value="ace/mode/latte">Latte</option>
                                        <option value="ace/mode/less">LESS</option>
                                        <option value="ace/mode/liquid">Liquid</option>
                                        <option value="ace/mode/lisp">Lisp</option>
                                        <option value="ace/mode/livescript">LiveScript</option>
                                        <option value="ace/mode/log">Log</option>
                                        <option value="ace/mode/logiql">LogiQL</option>
                                        <option value="ace/mode/logtalk">Logtalk</option>
                                        <option value="ace/mode/lsl">LSL</option>
                                        <option value="ace/mode/lua">Lua</option>
                                        <option value="ace/mode/luapage">LuaPage</option>
                                        <option value="ace/mode/lucene">Lucene</option>
                                        <option value="ace/mode/makefile">Makefile</option>
                                        <option value="ace/mode/markdown">Markdown</option>
                                        <option value="ace/mode/mask">Mask</option>
                                        <option value="ace/mode/matlab">MATLAB</option>
                                        <option value="ace/mode/maze">Maze</option>
                                        <option value="ace/mode/mediawiki">MediaWiki</option>
                                        <option value="ace/mode/mel">MEL</option>
                                        <option value="ace/mode/mips">MIPS</option>
                                        <option value="ace/mode/mixal">MIXAL</option>
                                        <option value="ace/mode/mushcode">MUSHCode</option>
                                        <option value="ace/mode/mysql">MySQL</option>
                                        <option value="ace/mode/nginx">Nginx</option>
                                        <option value="ace/mode/nim">Nim</option>
                                        <option value="ace/mode/nix">Nix</option>
                                        <option value="ace/mode/nsis">NSIS</option>
                                        <option value="ace/mode/nunjucks">Nunjucks</option>
                                        <option value="ace/mode/objectivec">Objective-C</option>
                                        <option value="ace/mode/ocaml">OCaml</option>
                                        <option value="ace/mode/odin">Odin</option>
                                        <option value="ace/mode/partiql">PartiQL</option>
                                        <option value="ace/mode/pascal">Pascal</option>
                                        <option value="ace/mode/perl">Perl</option>
                                        <option value="ace/mode/pgsql">pgSQL</option>
                                        <option value="ace/mode/php">PHP</option>
                                        <option value="ace/mode/php_laravel_blade">PHP (Blade Template)</option>
                                        <option value="ace/mode/pig">Pig</option>
                                        <option value="ace/mode/plsql">PLSQL</option>
                                        <option value="ace/mode/powershell">Powershell</option>
                                        <option value="ace/mode/praat">Praat</option>
                                        <option value="ace/mode/prisma">Prisma</option>
                                        <option value="ace/mode/prolog">Prolog</option>
                                        <option value="ace/mode/properties">Properties</option>
                                        <option value="ace/mode/protobuf">Protobuf</option>
                                        <option value="ace/mode/prql">PRQL</option>
                                        <option value="ace/mode/puppet">Puppet</option>
                                        <option value="ace/mode/qml">QML</option>
                                        <option value="ace/mode/r">R</option>
                                        <option value="ace/mode/raku">Raku</option>
                                        <option value="ace/mode/razor">Razor</option>
                                        <option value="ace/mode/rdoc">RDoc</option>
                                        <option value="ace/mode/red">Red</option>
                                        <option value="ace/mode/rhtml">RHTML</option>
                                        <option value="ace/mode/robot">Robot</option>
                                        <option value="ace/mode/rst">RST</option>
                                        <option value="ace/mode/ruby">Ruby</option>
                                        <option value="ace/mode/rust">Rust</option>
                                        <option value="ace/mode/sac">SaC</option>
                                        <option value="ace/mode/sass">SASS</option>
                                        <option value="ace/mode/scad">SCAD</option>
                                        <option value="ace/mode/scala">Scala</option>
                                        <option value="ace/mode/scheme">Scheme</option>
                                        <option value="ace/mode/scrypt">Scrypt</option>
                                        <option value="ace/mode/scss">SCSS</option>
                                        <option value="ace/mode/sh">SH</option>
                                        <option value="ace/mode/sjs">SJS</option>
                                        <option value="ace/mode/slim">Slim</option>
                                        <option value="ace/mode/smarty">Smarty</option>
                                        <option value="ace/mode/smithy">Smithy</option>
                                        <option value="ace/mode/snippets">snippets</option>
                                        <option value="ace/mode/soy_template">Soy Template</option>
                                        <option value="ace/mode/space">Space</option>
                                        <option value="ace/mode/sparql">SPARQL</option>
                                        <option value="ace/mode/sql">SQL</option>
                                        <option value="ace/mode/sqlserver">SQLServer</option>
                                        <option value="ace/mode/stylus">Stylus</option>
                                        <option value="ace/mode/svg">SVG</option>
                                        <option value="ace/mode/swift">Swift</option>
                                        <option value="ace/mode/tcl">Tcl</option>
                                        <option value="ace/mode/terraform">Terraform</option>
                                        <option value="ace/mode/tex">Tex</option>
                                        <option value="ace/mode/text">Text</option>
                                        <option value="ace/mode/textile">Textile</option>
                                        <option value="ace/mode/toml">Toml</option>
                                        <option value="ace/mode/tsx">TSX</option>
                                        <option value="ace/mode/turtle">Turtle</option>
                                        <option value="ace/mode/twig">Twig</option>
                                        <option value="ace/mode/typescript">Typescript</option>
                                        <option value="ace/mode/vala">Vala</option>
                                        <option value="ace/mode/vbscript">VBScript</option>
                                        <option value="ace/mode/velocity">Velocity</option>
                                        <option value="ace/mode/verilog">Verilog</option>
                                        <option value="ace/mode/vhdl">VHDL</option>
                                        <option value="ace/mode/visualforce">Visualforce</option>
                                        <option value="ace/mode/wollok">Wollok</option>
                                        <option value="ace/mode/xml">XML</option>
                                        <option value="ace/mode/xquery">XQuery</option>
                                        <option value="ace/mode/yaml">YAML</option>
                                        <option value="ace/mode/zeek">Zeek</option>
                                        <option value="ace/mode/django">Django</option>
                                    </select>
                                    </li>
                                    <li>
                                    <div class="cmt2">
                                    <i class="fa-solid fa-palette mr5"></i> Editor Theme
                                    </div>
                                    <label for="editor-themes"></label>
                                    <select id="editor-themes">
                                        <optgroup label="Dark">
                                            <option value="ace/theme/ambiance">Ambiance</option>
                                            <option value="ace/theme/chaos">Chaos</option>
                                            <option value="ace/theme/clouds_midnight">Clouds Midnight</option>
                                            <option value="ace/theme/dracula">Dracula</option>
                                            <option value="ace/theme/cobalt">Cobalt</option>
                                            <option value="ace/theme/gruvbox">Gruvbox</option>
                                            <option value="ace/theme/gob">Green on Black</option>
                                            <option value="ace/theme/idle_fingers">idle Fingers</option>
                                            <option value="ace/theme/kr_theme">krTheme</option>
                                            <option value="ace/theme/merbivore">Merbivore</option>
                                            <option value="ace/theme/merbivore_soft">Merbivore Soft</option>
                                            <option value="ace/theme/mono_industrial">Mono Industrial</option>
                                            <option value="ace/theme/monokai">Monokai</option>
                                            <option value="ace/theme/nord_dark">Nord Dark</option>
                                            <option value="ace/theme/one_dark">One Dark</option>
                                            <option value="ace/theme/pastel_on_dark">Pastel on dark</option>
                                            <option value="ace/theme/solarized_dark">Solarized Dark</option>
                                            <option value="ace/theme/terminal">Terminal</option>
                                            <option value="ace/theme/tomorrow_night">Tomorrow Night</option>
                                            <option value="ace/theme/tomorrow_night_blue">Tomorrow Night Blue</option>
                                            <option value="ace/theme/tomorrow_night_bright">Tomorrow Night Bright</option>
                                            <option value="ace/theme/tomorrow_night_eighties">Tomorrow Night 80s</option>
                                            <option value="ace/theme/twilight">Twilight</option>
                                            <option value="ace/theme/vibrant_ink">Vibrant Ink</option>
                                            <option value="ace/theme/github_dark">GitHub Dark</option>
                                        </optgroup>
                                        <optgroup label="Bright">
                                            <option value="ace/theme/chrome">Chrome</option>
                                            <option value="ace/theme/clouds">Clouds</option>
                                            <option value="ace/theme/crimson_editor">Crimson Editor</option>
                                            <option value="ace/theme/dawn">Dawn</option>
                                            <option value="ace/theme/dreamweaver">Dreamweaver</option>
                                            <option value="ace/theme/eclipse">Eclipse</option>
                                            <option value="ace/theme/github">GitHub</option>
                                            <option value="ace/theme/iplastic">IPlastic</option>
                                            <option value="ace/theme/solarized_light">Solarized Light</option>
                                            <option value="ace/theme/textmate">TextMate</option>
                                            <option value="ace/theme/tomorrow">Tomorrow</option>
                                            <option value="ace/theme/xcode">XCode</option>
                                            <option value="ace/theme/kuroir">Kuroir</option>
                                            <option value="ace/theme/katzenmilch">KatzenMilch</option>
                                            <option value="ace/theme/sqlserver">SQL Server</option>
                                        </optgroup>
                                    </select>
                                    </li>
                                    <li>
                                    <div class="cmt2">
                                    <i class="fa-solid fa-font mr5"></i> Font Size
                                    </div>
                                    <label for="editor-fontsize"></label>
                                    <select id="editor-fontsize">
                                        <?php
                                    for ($size = 12; $size <= 24; $size++) {
                                        echo "<option value=\"$size\">$size px</option>";
                                    }
                                    ?>
                                    </select>
                                    </li>
                                </ul>

                        </div>

                        <div class="dropdown ceom mr5">
                            <div class="ceom actb dt" data-bs-toggle="dropdown" aria-expanded="false"> Run </div>

                                <ul class="dropdown-menu runl">
                                    <li>
                                        <a target="_blank" href="https://www.online-cpp.com/"><i class="fa-solid fa-c mr5"></i> C compiler(Online CPP)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/cpp-programming/online-compiler/"><i class="fa-solid fa-c mr5"></i> C++ compiler(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/csharp-programming/online-compiler/"><i class="fa-solid fa-c mr5"></i> C# compiler(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/python-programming/online-compiler/"><i class="fa-brands fa-python mr5"></i> Python compiler(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/java-programming/online-compiler/"><i class="fa-brands fa-java mr5"></i> Java compiler(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/html/online-compiler/"><i class="fa-brands fa-html5 mr5"></i> HTML IDE(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/javascript/online-compiler/"><i class="fa-brands fa-js mr5"></i> JavaScript IDE(Programiz)</a>
                                    </li>
                                    <li>
                                        <a target="_blank" href="https://www.programiz.com/r/online-compiler/"><i class="fa-solid fa-r mr5"></i> R compiler(Programiz)</a>
                                    </li>
                                </ul>
                        </div>

                        <div class="ceom ml10 actb" id="lang-name" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="To switch, View &#12297;Editor language">
                            <i class="fa-solid fa-code mr5"></i> Text
                        </div>
                    
                </div>
            </div>
            <div class="cflex">
                <i class="fa fa-times mr5 actb cic" id="closeCoder"></i>
            </div>
        </div>
        <div id="editor"></div>

    </div>

    </section>

    <?php echo $templates->footerHtml();?>
    <script src="/js/jquery.min.js?v=1"></script>
    <script src="//cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="//cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    <script src="/plugins/izi/js/iziToast.min.js"></script>
    <script src="/plugins/tags/main.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.js"></script>
    <script src="/js/min/index.js?v=<?php echo $script_version;?>"></script>
    <script src="/plugins/nudity/nude.min.js"></script>
    <script src="/js/min/forumAdd.js?v=<?php echo $script_version;?>"></script>
    <script src="/js/min/app.js?v=<?php echo $script_version;?>"></script>
</body>

</html>
<?php
$html = ob_get_clean();
echo Minify::html($html);