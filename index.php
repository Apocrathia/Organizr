<?php 
//Set some variables
ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL | E_STRICT);
$data = false;
$databaseLocation = "databaseLocation.ini.php";
$needSetup = "Yes";
$tabSetup = "Yes";	
$hasOptions = "No";
$settingsicon = "No";
$settingsActive = "";
$action = "";
$title = "Organizr";
$topbar = "#eb6363"; 
$topbartext = "#FFFFFF";
$bottombar = "#eb6363";
$sidebar = "#000000";
$hoverbg = "#eb6363";
$activetabBG = "#eb6363";
$activetabicon = "#FFFFFF";
$activetabtext = "#FFFFFF";
$inactiveicon = "#FFFFFF";
$inactivetext = "#FFFFFF";
$loadingIcon = "images/organizr.png";
$baseURL = "";
require_once("translate.php");

function registration_callback($username, $email, $userdir){
    
    global $data;
    
    $data = array($username, $email, $userdir);

}

function printArray($arrayName){
    
    foreach ( $arrayName as $item ) :
        
        echo $item . "<br/>";
        
    endforeach;
    
}

function write_ini_file($content, $path) { 
    
    if (!$handle = fopen($path, 'w')) {
        
        return false; 
    
    }
    
    $success = fwrite($handle, $content);
    
    fclose($handle); 
    
    return $success; 

}

function getTimezone(){
    
    if (ini_get('date.timezone')) :
    
        echo ini_get('date.timezone');
    
    elseif (date_default_timezone_get()) :
    
        echo date_default_timezone_get();
    
    else :
    
        echo "America/Los_Angeles";
    
    endif;    
    
}
                
if(isset($_POST['action'])) :

    $action = $_POST['action'];
    
endif;

if($action == "createLocation") :

    $databaseData = '; <?php die("Access denied"); ?>' . "\r\n";

    foreach ($_POST as $postName => $postValue) {
            
        if($postName !== "action") :
        
            if(substr($postValue, -1) == "/") : $postValue = rtrim($postValue, "/"); endif;
        
            $databaseData .= $postName . " = \"" . $postValue . "\"\r\n";
        
        endif;
        
    }

    write_ini_file($databaseData, $databaseLocation);

endif;

if(!file_exists($databaseLocation)) :

    $configReady = "No";
    $userpic = "";

else :

    $configReady = "Yes";

    require_once("user.php");

    $USER = new User("registration_callback");

    date_default_timezone_set(TIMEZONE);

    $dbfile = DATABASE_LOCATION  . constant('User::DATABASE_NAME') . ".db";

    $database = new PDO("sqlite:" . $dbfile);

    $query = "SELECT * FROM users";

    foreach($database->query($query) as $data) {

        $needSetup = "No";

    }

    $db = DATABASE_LOCATION  . constant('User::DATABASE_NAME') . ".db";
    $file_db = new PDO("sqlite:" . $db);
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbTab = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="tabs"');
    $dbOptions = $file_db->query('SELECT name FROM sqlite_master WHERE type="table" AND name="options"');

    foreach($dbTab as $row) :

        if (in_array("tabs", $row)) :

            $tabSetup = "No";

        endif;

    endforeach;

    if($tabSetup == "Yes") :

        $settingsActive = "active";
    
    endif;

    foreach($dbOptions as $row) :

        if (in_array("options", $row)) :

            $hasOptions = "Yes";

        endif;

    endforeach;

    if($tabSetup == "No") :

        if($USER->authenticated && $USER->role == "admin") :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true"');
            $getsettings = $file_db->query('SELECT * FROM tabs WHERE active = "true"');

            foreach($getsettings as $row) :

                if(!empty($row['iconurl']) && $settingsicon == "No") :

                    $settingsicon = "Yes";

                endif;

            endforeach;

        elseif($USER->authenticated && $USER->role == "user") :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true" AND user = "true"');

        else :

            $result = $file_db->query('SELECT * FROM tabs WHERE active = "true" AND guest = "true"');

        endif;

    endif;

    if($hasOptions == "Yes") :

        $resulto = $file_db->query('SELECT * FROM options');

        foreach($resulto as $row) : 

            $title = $row['title'];
            $topbartext = $row['topbartext'];
            $topbar = $row['topbar'];
            $bottombar = $row['bottombar'];
            $sidebar = $row['sidebar'];
            $hoverbg = $row['hoverbg'];
            $activetabBG = $row['activetabBG'];
            $activetabicon = $row['activetabicon'];
            $activetabtext = $row['activetabtext'];
            $inactiveicon = $row['inactiveicon'];
            $inactivetext = $row['inactivetext'];

        endforeach;

    endif;

    $userpic = md5( strtolower( trim( $USER->email ) ) );
    if(!empty(LOADINGICON)) : $loadingIcon = LOADINGICON; endif;

endif;

?>

<!DOCTYPE html>

<html lang="en" class="no-js">

    <head>
        
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <meta name="apple-mobile-web-app-capable" content="yes" />   
        <meta name="mobile-web-app-capable" content="yes" /
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="msapplication-tap-highlight" content="no" />

        <title><?=$title;?><?php if($title !== "Organizr") :  echo " - Organizr"; endif; ?></title>

        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/font-awesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/mdi/css/materialdesignicons.min.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/metisMenu/dist/metisMenu.min.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/Waves/dist/waves.min.css"> 
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css"> 

        <link rel="stylesheet" href="<?=$baseURL;?>js/selects/cs-select.css">
        <link rel="stylesheet" href="<?=$baseURL;?>js/selects/cs-skin-elastic.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/google-material-color/dist/palette.css">
        
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/sweetalert/dist/sweetalert.css">
        <link rel="stylesheet" href="<?=$baseURL;?>bower_components/smoke/dist/css/smoke.min.css">


        <script src="<?=$baseURL;?>js/menu/modernizr.custom.js"></script>
        <script type="text/javascript" src="<?=$baseURL;?>js/sha1.js"></script>
		<script type="text/javascript" src="<?=$baseURL;?>js/user.js"></script>

        <link rel="stylesheet" href="<?=$baseURL;?>css/style.css">

        <link rel="icon" type="image/png" href="<?=$baseURL;?>images/favicon/android-chrome-192x192.png" sizes="192x192">
        <link rel="apple-touch-icon" sizes="180x180" href="<?=$baseURL;?>images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" href="<?=$baseURL;?>images/favicon/favicon-32x32.png" sizes="32x32">
        <link rel="icon" type="image/png" href="<?=$baseURL;?>images/favicon/favicon-16x16.png" sizes="16x16">
        <link rel="manifest" href="<?=$baseURL;?>images/favicon/manifest.json">
        <link rel="mask-icon" href="<?=$baseURL;?>images/favicon/safari-pinned-tab.svg" color="#2d89ef">
        <link rel="shortcut icon" href="<?=$baseURL;?>images/favicon/favicon.ico">
        <meta name="msapplication-config" content="<?=$baseURL;?>images/favicon/browserconfig.xml">
        <meta name="theme-color" content="#2d89ef">
        <link rel="stylesheet" type="text/css" href="css/addtohomescreen.css">
        <script src="js/addtohomescreen.js"></script>
        
        <!--[if lt IE 9]>
        <script src="bower_components/html5shiv/dist/html5shiv.min.js"></script>
        <script src="bower_components/respondJs/dest/respond.min.js"></script>
        <![endif]-->
        
    </head>

    <body style="overflow: hidden">

        <!--Preloader-->
        <div id="preloader" class="preloader table-wrapper">
            
            <div class="table-row">
                
                <div class="table-cell">
                    
                    <div class="la-ball-scale-multiple la-3x" style="color: <?=$topbar;?>">
                        
                        <div></div>
                        <div></div>
                        <div></div>
                        <logo class="logo"><img height="192px" src="<?=$loadingIcon;?>"></logo>
                    
                    </div>
                
                </div>
            
            </div>
        
        </div>

        <div id="main-wrapper" class="main-wrapper">
            
            <style>
                .bottom-bnts a {
                    
                    background: <?=$bottombar;?> !important;
                    color: <?=$topbartext;?> !important;
                
                }.bottom-bnts {
                    
                    background-color: <?=$bottombar;?> !important;
                
                }.gn-menu-main {
                    
                   
                    background-color: <?=$topbar;?>;
                
                }.gn-menu-main ul.gn-menu {
                    
                    background: <?=$sidebar;?>;
                
                }.gn-menu-wrapper {
                
                    background: <?=$sidebar;?>;
                
                }.gn-menu i {
                    
                    height: 18px;
                    width: 52px;
                
                }.la-timer.la-dark {
                    
                    color: <?=$topbartext;?>
                
                }.refresh-preloader {
                    
                    background: <?=$topbartext;?>;
                
                }.la-timer {
                    
                    width: 75px;
                    height: 75px;
                    padding-top: 20px;
                    border-radius: 100px;
                    background: <?=$sidebar;?>;
                    border: 2px solid <?=$topbar;?>;
                
                }@media screen and (min-width:737px){
                    
                    .tab-item:hover a {
                    
                        color: <?=$sidebar;?> !important;
                        background: <?=$hoverbg;?>;
                        border-radius: 100px 0 0 100px;
                    
                    }
        
                }.gn-menu li.active > a {
                    
                    color: <?=$activetabtext;?> !important;
                    background: <?=$activetabBG;?>;
                    border-radius: 100px 0 0 100px;
                
                }.active {
                    
                    display: block;
                
                }.hidden {
                    
                    display: none;
                    
                }.errorz {
                	
                	background-image: linear-gradient(red, red), linear-gradient(#d2d2d2, #d2d2d2);
            	    outline: none;
            	    animation: input-highlight .5s forwards;
            	    box-shadow: none;
            	    padding-left: 0;
            	    border: 0;
            	    border-radius: 0;
            	    background-size: 0 2px,100% 1px;
            	    background-repeat: no-repeat;
            	    background-position: center bottom,center calc(100% - 1px);
            	    background-color: transparent;
            	    box-shadow: none;
                	    
                }.gn-menu li.active i.fa {
                    
                    color: <?=$activetabicon;?>;
                    
                }.gn-menu li i.fa {
                    
                    color: <?=$inactiveicon;?>;
                    
                }.gn-menu-main ul.gn-menu a {
                    
                    color: <?=$inactivetext;?>;
                }li.dropdown.some-btn .mdi {
                    
                    color: <?=$topbartext;?>;
                    
                }.nav>li>a:focus, .nav>li>a:hover {
                    
                    text-decoration: none;
                    background-color: transparent;
                    
                }div#preloader {
                    
                    background-color: <?=$topbartext;?>;
                    
                }.iframe {
                    
                    -webkit-overflow-scrolling: touch;

                }.iframe iframe{

                }#menu-toggle span {
                    background: <?=$topbartext;?>;
                }logo.logo {
                    
                    opacity: 0.5;
                    filter: alpha(opacity=50);

                }
                
            </style>

            <ul id="gn-menu" class="gn-menu-main">
                
                <li class="gn-trigger">
                    
                    <a id="menu-toggle" class="menu-toggle gn-icon gn-icon-menu">
                        
                        <div class="hamburger">
                            
                            <span></span>
                            <span></span>
                            <span></span>
                        
                        </div>
                        
                        <div class="cross">
                            
                            <span></span>
                            <span></span>
                        
                        </div>
                    
                    </a>
                    
                    <nav class="gn-menu-wrapper">
                        
                        <div class="gn-scroller" id="gn-scroller">
                            
                            <ul class="gn-menu metismenu">

                                <!--Start Tab List-->
                                
                                <?php if($tabSetup == "No") : foreach($result as $row) : 
                                
                                if($row['defaultz'] == "true") : $defaultz = "active"; else : $defaultz = ""; endif;?>
                                
                                <li window="<?=$row['window'];?>" class="tab-item <?=$defaultz;?>" id="<?=$row['url'];?>x" name="<?php echo strtolower($row['name']);?>">
                                    
                                    <a class="tab-link">
                                        
                                        <?php if($row['iconurl']) : ?>
                                        
                                            <i style="font-size: 19px; padding: 0 10px; font-size: 19px;">
                                                <img src="<?=$row['iconurl'];?>" style="height: 30px; width: 30px; margin-top: -2px;">
                                            </i>
                                        
                                        <?php else : ?>
                                        
                                            <i class="fa <?=$row['icon'];?> fa-lg"></i>
    
                                        <?php endif; ?>
                                        
                                        <?=$row['name'];?>
                                    
                                    </a>

                                </li>
                                
                                <?php endforeach; endif;?>
                                
                                <?php if($configReady == "Yes") : if($USER->authenticated && $USER->role == "admin") :?>
                                <li class="tab-item <?=$settingsActive;?>" id="settings.phpx">
                                                            
                                    <a class="tab-link">
                                        
                                        <?php if($settingsicon == "Yes") :
                                        
                                            echo '<i style="font-size: 19px; padding: 0 10px; font-size: 19px;">
                                                <img id="settings-icon" src="images/settings.png" style="height: 30px; margin-top: -2px;"></i>';
                                        
                                        else :
                                        
                                            echo '<i id="settings-icon" class="fa fa-cog"></i>';
                                        
                                        endif; ?>
                                        
                                        <?php echo $language->translate("SETTINGS");?>
                                    
                                    </a>
                                
                                </li>
                                <?php endif; endif;?>
                                
                                <!--End Tab List-->
                           
                            </ul>
                        
                        </div>

                        <!-- /gn-scroller -->
                        <div class="bottom-bnts">
                            
                            <a class="fix-nav"><i class="mdi mdi-pin"></i></a>
                        
                        </div>
                    
                    </nav>
                
                </li>

                <li class="top-clock">
                    
                    <?php 
                    
                    if($configReady == "Yes") : 
                    
                        if(empty(TITLELOGO)) : 
                    
                            echo "<span><span style=\"color: topbartext\"><b>$title</b></span></span>"; 
                    
                        else : 
                    
                            echo "<img height='50px' width='250px' src='" . TITLELOGO . "'>";
                    
                        endif;
                    
                    else :
                    
                        echo "<span><span style=\"color: topbartext\"><b>$title</b></span></span>"; 
                    
                    endif;
                    
                    ?>
                
                </li>

                <li class="pull-right">
                    
                    <ul class="nav navbar-right right-menu">
                        
                        <li class="dropdown notifications">
                            
                            <?php if($configReady == "Yes") : if(!$USER->authenticated) : ?>
                            
                            <a class="log-in">
                            
                            <?php endif; endif;?>
                            
                            <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
                            
                            <a class="show-members">
                                
                            <?php endif; endif;?>
                                
                                <i class="userpic"><img src="https://www.gravatar.com/avatar/<?=$userpic;?>?s=40&d=mm" class="img-circle"></i> 
                                
                            </a>
                            
                        </li>
                        
                        <li class="dropdown some-btn">
                            
                            <a class="fullscreen">
                                
                                <i class="mdi mdi-fullscreen"></i>
                            
                            </a>
                        </li>
                        
                        <li class="dropdown some-btn">
                            
                            <a id="reload" class="refresh">
                               
                                <i class="mdi mdi-refresh"></i>
                           
                            </a>
                        
                        </li>
                    
                    </ul>
                
                </li>
            
            </ul>

            <!--Content-->
            <div id="content" class="content" style="">
                <script>addToHomescreen();</script>

                <!--Load Framed Content-->
                <?php if($needSetup == "Yes" && $configReady == "Yes") : ?>
                <div class="table-wrapper">

                    <div class="table-row">

                        <div class="table-cell text-center">

                            <div class="login i-block">

                                <div class="content-box">

                                    <div class="green-bg biggest-box">

                                        <h1 class="zero-m text-uppercase"><?php echo $language->translate("CREATE_ADMIN");?></h1>

                                    </div>

                                    <div class="big-box text-left registration-form">

                                        <h4 class="text-center"><?php echo $language->translate("CREATE_ACCOUNT");?></h4>

                                        <form class="controlbox" name="new user registration" id="registration" action="" method="POST" data-smk-icon="glyphicon-remove-sign">

                                            <input type="hidden" name="op" value="register"/>
                                            <input type="hidden" name="sha1" value=""/>

                                            <div class="form-group">

                                                <input type="text" class="form-control material" name="username" autofocus placeholder="<?php echo $language->translate("USERNAME");?>" autocorrect="off" autocapitalize="off" minlength="3" maxlength="16" required>

                                            </div>

                                            <div class="form-group">

                                                <input type="email" class="form-control material" name="email" placeholder="<?php echo $language->translate("EMAIL");?>">

                                            </div>

                                            <div class="form-group">

                                                <input type="password" class="form-control material" name="password1" placeholder="<?php echo $language->translate("PASSWORD");?>" data-smk-strongPass="weak" required>

                                            </div>

                                            <div class="form-group">

                                                <input type="password" class="form-control material" name="password2" placeholder="<?php echo $language->translate("PASSWORD_AGAIN");?>">

                                            </div>

                                            <button id="registerSubmit" type="submit" class="btn green-bg btn-block btn-warning text-uppercase waves waves-effect waves-float" value="Register"><?php echo $language->translate("REGISTER");?></button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <?php endif; ?>
                
                
                <?php if($needSetup == "Yes" && $configReady == "No") : ?>
                <div class="table-wrapper">

                    <div class="table-row">

                        <div class="table-cell text-center">

                            <div class="login i-block">

                                <div class="content-box">

                                    <div class="green-bg biggest-box">

                                        <h1 class="zero-m text-uppercase"><?php echo $language->translate("DATABASE_PATH");?></h1>

                                    </div>

                                    <div class="big-box text-left">

                                        <h3 class="text-center"><?php echo $language->translate("SPECIFY_LOCATION");?></h3>
                                        <h5 class="text-left"><strong><?php echo $language->translate("CURRENT_DIRECTORY");?>: <?php echo __DIR__; ?> <br><?php echo $language->translate("PARENT_DIRECTORY");?>: <?php echo dirname(__DIR__); ?></strong></h5>
                                        
                                        <form class="controlbox" name="setupDatabase" id="setupDatabase" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                            
                                            <input type="hidden" name="action" value="createLocation" />

                                            <div class="form-group">

                                                <input type="text" class="form-control material" name="databaseLocation" autofocus value="<?php echo dirname(__DIR__);?>" autocorrect="off" autocapitalize="off" required>
                                                
                                                <h5><?php echo $language->translate("SET_DATABASE_LOCATION");?></h5>
                                                
                                                <input type="text" class="form-control material" name="timezone" autofocus value="<?php echo getTimezone();?>" autocorrect="off" autocapitalize="off" required>
                                                
                                                <h5><?php echo $language->translate("SET_TIMEZONE");?></h5>
                                                
                                                <?php 
                                                
                                                if(file_exists(dirname(__DIR__) . '/users.db') || file_exists(__DIR__ . '/users.db')) : 
                                                
                                                echo '<h5 class="text-center red">';
                                                echo $language->translate("DONT_WORRY");
                                                echo '</h5>'; 
                                                
                                                endif;?>

                                            </div>

                                            <button id="databaseLocationSubmit" type="submit" class="btn green-bg btn-block btn-sm text-uppercase waves waves-effect waves-float" value="Save Location"><?php echo $language->translate("SAVE_LOCATION");?></button>

                                        </form>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>
                <?php endif; ?>
                
                
                <?php if($configReady == "Yes") : if(!$USER->authenticated && $tabSetup == "Yes" && $needSetup == "No") :?>
                <div class="table-wrapper">
                
                    <div class="table-row">
                
                        <div class="table-cell text-center">
                        
                            <div class="login i-block">
                                
                                <div class="content-box">
                                    
                                    <div class="blue-bg biggest-box">
                
                                        <h1 class="zero-m text-uppercase"><?php echo $language->translate("AWESOME");?></h1>
                
                                    </div>
                
                                    <div class="big-box text-left">
                
                                        <h4 class="text-center"><?php echo $language->translate("TIME_TO_LOGIN");?></h4>
                                        
                                        <button type="submit" class="btn log-in btn-block btn-primary text-uppercase waves waves-effect waves-float"><?php echo $language->translate("LOGIN");?></button>
                						                                    
                                    </div>
                                
                                </div>
                            
                            </div>
                        
                        </div>
                    
                    </div>
                
                </div>
                <?php endif; endif; ?>
                <?php if($tabSetup == "No" && $needSetup == "No") :?>        
                <div id="tabEmpty" class="table-wrapper" style="display: none; background:<?=$sidebar;?>;">
                
                    <div class="table-row">
                
                        <div class="table-cell text-center">
                        
                            <div class="login i-block">
                                
                                <div class="content-box">
                                    
                                    <div class="biggest-box" style="background:<?=$topbar;?>;">
                
                                        <h1 class="zero-m text-uppercase" style="color:<?=$topbartext;?>;"><?php echo $language->translate("HOLD_UP");?></h1>
                
                                    </div>
                
                                    <div class="big-box text-left">
                
                                        <center><img src="images/sowwy.png" style="height: 200px;"></center>
                                        <h2 class="text-center"><?php echo $language->translate("LOOKS_LIKE_YOU_DONT_HAVE_ACCESS");?></h2>
                                        
                                        <?php if(!$USER->authenticated) : ?>
                                        <button style="background:<?=$topbar;?>;" type="submit" class="btn log-in btn-block btn-primary text-uppercase waves waves-effect waves-float"><text style="color:<?=$topbartext;?>;"><?php echo $language->translate("LOGIN");?></text></button>
                                        <?php endif; ?>
        						                                    
                                    </div>
                                
                                </div>
                            
                            </div>
                        
                        </div>
                    
                    </div>
                
                </div>
                <?php endif;?>
                <!--End Load Framed Content-->
            
            </div>
            <!--End Content-->

            <!--Welcome notification-->
            <div id="welcome"></div>
            
            <div id="members-sidebar" style="background: <?=$sidebar;?>;" class="members-sidebar">
                
                <h4 class="pull-left zero-m"><?php echo $language->translate("OPTIONS");?></h4>
                
                <span class="close-members-sidebar"><i class="fa fa-remove fa-lg pull-right"></i></span>
                
                <div class="clearfix"><br/></div>
                
                <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
                
                <br>
                
                <div class="content-box profile-sidebar box-shadow">
                
                    <img src="https://www.gravatar.com/avatar/<?=$userpic;?>?s=100&d=mm" class="img-responsive img-circle center-block" alt="user" https:="" www.gravatar.com="" avatar="">
                
                    <div class="profile-usertitle">
                
                        <div class="profile-usertitle-name">
                
                            <?php echo strtoupper($USER->username); ?>
                
                        </div>
                
                        <div class="profile-usertitle-job">
                
                            <?php echo strtoupper($USER->role); ?>
                
                        </div>
                
                    </div>
                
                    <div id="buttonsDiv" class="profile-userbuttons">
                
                        <button id="editInfo" type="button" class="btn btn-primary text-uppercase waves waves-effect waves-float"><?php echo $language->translate("EDIT_INFO");?></button>
                
                        <button type="button" class="logout btn btn-warning waves waves-effect waves-float"><?php echo $language->translate("LOGOUT");?></button>
                
                    </div>
                    
                    <div id="editInfoDiv" style="display: none" class="profile-usertitle">
                         
                        <form class="content-form form-inline" name="update" id="update" action="" method="POST">

                            <input type="hidden" name="op" value="update"/>
                            <input type="hidden" name="sha1" value=""/>
                            <input type="hidden" name="role" value="<?php echo $USER->role; ?>"/>

                            <div class="form-group">

                                <input autocomplete="off" type="text" value="<?php echo $USER->email; ?>" class="form-control" name="email" placeholder="<?php echo $language->translate("EMAIL_ADDRESS");?>">

                            </div>

                            <div class="form-group">

                                <input autocomplete="off" type="password" class="form-control" name="password1" placeholder="<?php echo $language->translate("PASSWORD");?>">

                            </div>

                            <div class="form-group">

                                <input autocomplete="off" type="password" class="form-control" name="password2" placeholder="<?php echo $language->translate("PASSWORD_AGAIN");?>">

                            </div>

                            <br>

                            <div class="form-group">

                                <input type="button" class="btn btn-success text-uppercase waves-effect waves-float" value="<?php echo $language->translate("UPDATE");?>" onclick="User.processUpdate()"/>
                                
                                <button id="goBackButtons" type="button" class="btn btn-primary text-uppercase waves waves-effect waves-float"><?php echo $language->translate("GO_BACK");?></button>

                            </div>

                        </form>

                    </div>
                    
                </div>

                <?php endif; endif;?>

            </div>

        </div>
        <?php if($configReady == "Yes") : if(!$USER->authenticated && $configReady == "Yes") : ?>
        <div class="login-modal modal fade">
            
            <div style="background:<?=$sidebar;?>;" class="table-wrapper">
                
                <div class="table-row">
                    
                    <div class="table-cell text-center">
                        
                        <button style="color:<?=$topbartext;?>;" type="button" class="close" data-dismiss="modal" aria-label="Close">
                            
                            <span aria-hidden="true">&times;</span>
                        
                        </button>
                        
                        <div class="login i-block">
                            
                            <div class="content-box">
                                
                                <div style="background:<?=$topbar;?>;" class="biggest-box">

                                    <h1 style="color:<?=$topbartext;?>;" class="zero-m text-uppercase"><?php echo $language->translate("WELCOME");?></h1>

                                </div>
                                
                                <div class="big-box text-left login-form">

                                    <?php if($USER->error!="") : ?>
                                    <p class="error">Error: <?php echo $USER->error; ?></p>
                                    <?php endif; ?>
                                    
                                    <form name="log in" id="login" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                        
                                        <h4 class="text-center"><?php echo $language->translate("LOGIN");?></h4>
                                        
                                        <div class="form-group">
                                            
                                            <input type="hidden" name="op" value="login">
				                            <input type="hidden" name="sha1" value="">
                                            <input type="hidden" name="rememberMe" value="false"/>
                                            <input type="text" class="form-control material" name="username" placeholder="<?php echo $language->translate("USERNAME");?>" autocomplete="off" autocorrect="off" autocapitalize="off" value="" autofocus required>
                                        
                                        </div>
                                        
                                        <div class="form-group">
                                            
                                            <input type="password" class="form-control material" name="password1" value="" autocomplete="off" placeholder="<?php echo $language->translate("PASSWORD");?>" required>
                                        
                                        </div>
                                        
                                        <div class="form-group">
                                            
                                            <div class="i-block"> <input id="rememberMe" name="rememberMe" class="switcher switcher-success switcher-medium pull-left" value="true" type="checkbox" checked=""> 
                                                
                                                <label for="rememberMe" class="pull-left"></label>
                                            
                                                <label class="pull-right"> &nbsp; <?php echo $language->translate("REMEMBER_ME");?></label>
                                            
                                            </div>

                                        </div>

                                        <button id="loginSubmit" style="background:<?=$topbar;?>;" type="submit" class="btn btn-block btn-info text-uppercase waves" value="log in" onclick="User.processLogin()"><text style="color:<?=$topbartext;?>;"><?php echo $language->translate("LOGIN");?></text></button>

                                    </form> 
                                    
                                    <button id="switchForgot" style="background:<?=$topbartext;?>;" class="btn btn-block btn-info text-uppercase waves"><text style="color:<?=$topbar;?>;"><?php echo $language->translate("FORGOT_PASSWORD");?></text></button>
                                    
                                    <form style="display: none;" name="forgotPassword" id="forgotPassword" action="" method="POST" data-smk-icon="glyphicon-remove-sign">
                                        
                                        <h4 class="text-center"><?php echo $language->translate("FORGOT_PASSWORD");?></h4>
                                        
                                        <div class="form-group">
                                            
                                            <input type="hidden" name="op" value="reset">
                                            <input type="text" class="form-control material" name="email" placeholder="<?php echo $language->translate("EMAIL");?>" autocorrect="off" autocapitalize="off" value="" autofocus required>
                                        
                                        </div>

                                        <button style="background:<?=$topbar;?>;" type="submit" class="btn btn-block btn-info text-uppercase waves" value="reset password"><text style="color:<?=$topbartext;?>;"><?php echo $language->translate("RESET_PASSWORD");?></text></button>

                                    </form> 
                                    
                                </div>
                            
                            </div>
                       
                        </div>
                    
                    </div>
                
                </div>
            
            </div>
        
        </div>
        <?php endif; endif;?>
        <?php if($configReady == "Yes") : if($USER->authenticated) : ?>
        <div style="background:<?=$topbar;?>;" class="logout-modal modal fade">
            
            <div class="table-wrapper" style="background: <?=$topbar;?>">
            
                <div class="table-row">
                
                    <div class="table-cell text-center">
                    
                        <div class="login i-block">
                        
                            <div class="content-box">
                            
                                <div style="background:<?=$topbartext;?>;" class="biggest-box">
                                
                                    <form name="log out" id="logout" action="" method="POST">
                                        
				                        <input type="hidden" name="op" value="logout">
                                        
                                        <input type="hidden" name="username"value="<?php echo $_SESSION["username"]; ?>" >
			
                                        <h3 style="color:<?=$topbar;?>;" class="zero-m text-uppercase"><?php echo $language->translate("DO_YOU_WANT_TO_LOGOUT");?></h3>
                                        
                                        <a style="color:<?=$topbar;?>;" id="logoutSubmit" class="i-block" data-dismiss="modal"><?php echo $language->translate("YES_WORD");?></a>
                                        
                                        <a style="color:<?=$topbar;?>;" class="i-block" data-dismiss="modal"><?php echo $language->translate("NO_WORD");?></a>
                                
                                    </form>
                                    
                                </div>
                            
                            </div>
                    
                        </div>
                
                    </div>
            
                </div>
        
            </div>
    
        </div>
        <?php endif; endif;?>

        <!--Scripts-->
        <script src="<?=$baseURL;?>bower_components/jquery/dist/jquery.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/metisMenu/dist/metisMenu.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/Waves/dist/waves.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/moment/min/moment.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/jquery.nicescroll/jquery.nicescroll.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/slimScroll/jquery.slimscroll.min.js"></script>
        <script src="<?=$baseURL;?>bower_components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.js"></script>
        <script src="<?=$baseURL;?>bower_components/cta/dist/cta.min.js"></script>

        <!--Menu-->
        <script src="<?=$baseURL;?>js/menu/classie.js"></script>
        <script src="<?=$baseURL;?>js/menu/gnmenu.js"></script>

        <!--Selects-->
        <script src="<?=$baseURL;?>js/selects/selectFx.js"></script>
        
        <script src="<?=$baseURL;?>bower_components/sweetalert/dist/sweetalert.min.js"></script>

        <script src="<?=$baseURL;?>bower_components/smoke/dist/js/smoke.min.js"></script>

        <!--Notification-->
        <script src="<?=$baseURL;?>js/notifications/notificationFx.js"></script>

        <!--Custom Scripts-->
        <script src="<?=$baseURL;?>js/common.js"></script>

        <script>

        var fixed = document.getElementById('gn-scroller');
            
        fixed.addEventListener('touchmove', function(e) {

            e.preventDefault();

        }, false);    
            
        function setHeight() {
            
            windowHeight = $(window).innerHeight();
            
            $("div").find(".iframe").css('height', windowHeight - 56 + "px");
            
            $('#content').css('height', windowHeight - 56 + "px");
            
        };
            
        $('#loginSubmit').click(function() {
            
            if ($('#login').smkValidate()) {
                
                console.log("validated");
                
            }
            
            console.log("didnt validate");
            
        });
            
        $('#registerSubmit').click(function() {
            
            if ($('#registration').smkValidate()) {
                
                console.log("validated");
                
            }
            
            console.log("didnt validate");
            User.processRegistration();
            
        });
            
        $("#editInfo").click(function(){

            $( "div[id^='editInfoDiv']" ).toggle();
            $( "div[id^='buttonsDiv']" ).toggle();
     
        });
            
        $("#goBackButtons").click(function(){

            $( "div[id^='editInfoDiv']" ).toggle();
            $( "div[id^='buttonsDiv']" ).toggle();
     
        });
            
        $("#switchForgot").click(function(){

            $( "form[id^='login']" ).toggle();
            $( "form[id^='forgotPassword']" ).toggle();
            $("#switchForgot").toggle();
     
        });
            
        //Sign in
        $(".log-in").click(function(e){
            
            var e1 = document.querySelector(".log-in"),
            
                e2 = document.querySelector(".login-modal");
            
            cta(e1, e2, {relativeToWindow: true}, function () {
                
                $('.login-modal').modal("show");
            
            });

            e.preventDefault();
        
        });

        //Logout
        $(".logout").click(function(e){
        var el1 = document.querySelector(".logout"),
        el2 = document.querySelector(".logout-modal");
        cta(el1, el2, {relativeToWindow: true}, function () {
        $('.logout-modal').modal("show");
        });

        e.preventDefault();
        });

        //Members Sidebar
        $(".show-members").click(function(e){
        var e_s1 = document.querySelector(".show-members"),
        e_s2 = document.querySelector("#members-sidebar");

        cta(e_s1, e_s2, {relativeToWindow: true}, function () {
        $('#members-sidebar').addClass('members-sidebar-open');
        });

        e.preventDefault();
        });

        $('.close-members-sidebar').click(function(){
        $('#members-sidebar').removeClass('members-sidebar-open');
        });

        $(document).ready(function(){
            
            defaultTab = $("li[class^='tab-item active']").attr("id");
           
            if (defaultTab){
           
                defaultTab = defaultTab.substr(0, defaultTab.length-1);
           
            }else{
           
                defaultTabNone = $("li[class^='tab-item']").attr("id");
                
                if (defaultTabNone){
                
                    $("li[class^='tab-item']").first().attr("class", "tab-item active");
                    defaultTab = defaultTabNone.substr(0, defaultTabNone.length-1);
           
                }
            
            }

            if (defaultTab){

                $("#content").html('<div class="iframe active" data-content-url="'+defaultTab+'"><iframe scrolling="auto" sandbox="allow-forms allow-same-origin allow-pointer-lock allow-scripts allow-popups allow-modals allow-top-navigation" allowfullscreen="true" webkitallowfullscreen="true" mozallowfullscreen="true" frameborder="0" style="width:100%; height:100%;" src="'+defaultTab+'"></iframe></div>');
            }
            
            if (defaultTab == null){
             
                $("div[id^='tabEmpty']").show();
                <?php if($needSetup == "No" && $configReady == "Yes") : if(!$USER->authenticated) : ?>
                $('.login-modal').modal("show");
                <?php endif; endif; ?>
                
            }
            
            if ($(location).attr('hash')){
            
                var getHash = $(location).attr('hash').substr(1).replace("%20", " ").replace("_", " ");

                var gotHash = getHash.toLowerCase();

                var getLiTab = $("li[name^='" + gotHash + "']");
                
                getLiTab.trigger("click");

            }   

            setHeight();

        }); 
            
        $(function () {
            <?php if(!empty($USER->info_log)) : ?>
            $.smkAlert({
                position: 'top-left',
                text: '<?php echo printArray($USER->info_log);?>',
                type: 'info'
                
            });
            <?php endif; ?>
            
            <?php if(!empty($USER->error_log)) : ?>
            $.smkAlert({
                position: 'top-left',
                text: '<?php echo printArray($USER->error_log); ?>',
                type: 'warning'
                
            });
            <?php endif; ?>

        });
            
        $('#reload').on('click tap', function(){

            $("i[class^='mdi mdi-refresh']").attr("class", "mdi mdi-refresh fa-spin");

            var activeFrame = $('#content').find('.active').children('iframe');

            activeFrame.attr('src', activeFrame.attr('src'));

            var refreshBox = $('#content').find('.active');

            $("<div class='refresh-preloader'><div class='la-timer la-dark'><div></div></div></div>").appendTo(refreshBox).fadeIn(10);

            setTimeout(function(){

                var refreshPreloader = refreshBox.find('.refresh-preloader'),
                deletedRefreshBox = refreshPreloader.fadeOut(300, function(){

                    refreshPreloader.remove();
                    $("i[class^='mdi mdi-refresh fa-spin']").attr("class", "mdi mdi-refresh");

                });

            },500);

        });
            
        $("li[id^='settings.phpx']").on('click tap', function(){

            $("img[id^='settings-icon']").attr("class", "fa-spin");
            $("i[id^='settings-icon']").attr("class", "fa fa-cog fa-spin");

            setTimeout(function(){

                $("img[id^='settings-icon']").attr("class", "");
                $("i[id^='settings-icon']").attr("class", "fa fa-cog");

            },1000);

        });

        $('#logoutSubmit').on('click tap', function(){

            $( "#logout" ).submit();

        });
            
        $(window).resize(function(){
            
            setHeight();

        });
            
        $("li[class^='tab-item']").on('click vclick', function(){
                
            var thisidfull = $(this).attr("id");

            var thisid = thisidfull.substr(0, thisidfull.length-1);

            var currentframe = $("div[data-content-url^='"+thisid+"']");

            if (currentframe.attr("class") == "iframe active") {

                console.log(thisid + " is active already");

            }else if (currentframe.attr("class") == "iframe hidden") {

                console.log(thisid + " is active already but hidden");

                $("div[class^='iframe active']").attr("class", "iframe hidden");

                currentframe.attr("class", "iframe active");
                
                setHeight();

                $("li[class^='tab-item active']").attr("class", "tab-item");

                $(this).attr("class", "tab-item active");

            }else {

                
                
                if ($(this).attr("window") == "true") {
                    
                    window.open(thisid,'_blank');
                    
                }else {
                
                    console.log(thisid + " make new div");

                    $("div[class^='iframe active']").attr("class", "iframe hidden");

                    $( '<div class="iframe active" data-content-url="'+thisid+'"><iframe scrolling="auto" sandbox="allow-forms allow-same-origin allow-pointer-lock allow-scripts allow-popups allow-modals" allowfullscreen="true" webkitallowfullscreen="true" frameborder="0" style="width:100%; height:100%;" src="'+thisid+'"></iframe></div>' ).appendTo( "#content" );

                    setHeight();

                    $("li[class^='tab-item active']").attr("class", "tab-item");

                    $(this).attr("class", "tab-item active");
                    
                }

            }

        });
        </script>


    </body>

</html>