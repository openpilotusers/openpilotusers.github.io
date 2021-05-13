<?php
if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
}

error_reporting(E_ALL ^ E_WARNING);

# Constants
define("USER_AGENT", $_SERVER['HTTP_USER_AGENT']);
define("IS_NEOS", (str_contains(USER_AGENT, "Wget") or str_contains(USER_AGENT, "NEOSSetup")));
define("DEFAULT_STOCK_BRANCH", "release2");

define("WEBSITE_URL", "http://multikyd.iptime.org:8181");
define("BASE_DIR", "/fork");

$url = "/";
if (array_key_exists("url", $_GET)) {
    $url = $_GET["url"];
}

list($username, $branch) = explode("/", $url);  # todo: clip these strings at the max length in index (to show up on the webpage)
#list($username, $branch, $loading_msg) = explode("/", $url);  # todo: clip these strings at the max length in index (to show up on the webpage)

$username = substr(strtolower($username), 0, 250);  # 5 less than max
$branch = substr(trim($branch), 0, 250);
$branch = $branch == "_" ? "" : $branch;
#$loading_msg = substr(trim($loading_msg), 0, 250);
#$supplied_loading_msg = $loading_msg != "";  # to print secret message

# Aliases
if (in_array($username, array("dragonpilot", "dp"))) {
    $username = "dragonpilot-community";
    if ($branch == "") $branch = "devel-i18n";  # default is normally docs
#    if ($loading_msg == "") $loading_msg = "dragonpilot";
}
if (in_array($username, array("stock", "commaai"))) {
    $username = "commaai";
    if ($branch == "") $branch = DEFAULT_STOCK_BRANCH;
#    if ($loading_msg == "") $loading_msg = "openpilot";
}
if (in_array($username, array("shane", "sa", "shanesmiskol"))) {
    $username = "shanesmiskol";
#    if ($loading_msg == "") $loading_msg = "Stock Additions";
}

#if ($loading_msg == "") {  # if not an alias with custom msg and not specified use username
    $loading_msg = $username;
#} else {  # make sure we encode spaces, neos setup doesn't like spaces (branch and username shouldn't have spaces)
#	$loading_msg = str_replace(" ", "%20", $loading_msg);
#}

if (IS_NEOS) {  # if NEOS or wget serve file immediately. commaai/stock if no username provided
    if ($username == "") {
        $username = "commaai";
        $branch = DEFAULT_STOCK_BRANCH;
#        $loading_msg = "openpilot";
    }
    header("Location: " . BASE_DIR . "/build.php?username=" . $username . "&branch=" . $branch);
    #header("Location: " . BASE_DIR . "/build.php?username=" . $username . "&branch=" . $branch . "&loading_msg=" . $loading_msg);
    return;
}

# Draws visual elements for website
echo '<head>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<style>
body {background-image: linear-gradient(#F9DEC9, #99B2DD); font-family: "Roboto", sans-serif; color: #30323D; text-align: center;}
span { color: #6369D1; }
a { text-decoration: none; color: #6369D1;}
button {background-color: #cb99c5; border-radius: 4px; border: 5px; padding: 10px 12px; box-shadow:0px 4px 0px #AD83A8; display: inline-block; color: white;top: 1px; outline: 0px transparent !important;}
button:active {border-radius: 4px; border: 5px; padding: 10px 12px; box-shadow:0px 2px 2px #BA8CB5; background-color: #BA8CB5; display: inline-block; top: 1px, outline: 0px transparent !important;}
</style>
<title>fork installer generator</title>
<link rel="icon" type="image/x-icon" href="' . BASE_DIR . '/favicon.ico">
</head>';

echo '</br></br><a href="' . BASE_DIR . '"><h1 style="color: #30323D;">🍴 custom openpilot fork installer generator-inator 🍴</h1></a>';

if ($username == "") {
    echo "</br><h2>Enter this URL in NEOS during setup with the format: <a href='" . BASE_DIR . "/shanesmiskol/stock_additions'><span>" . WEBSITE_URL . BASE_DIR . "/username/branch</span></a></h2>";
    echo "<h3>Or complete the request on your desktop to download a custom installer.</h3>";
    echo '<h3 style="position: absolute; bottom: 0; left: 0; width: 100%; text-align: center;"><a href="https://github.com/ShaneSmiskol/openpilot-installer-generator" style="color: 30323D;">💾 Installer Generator GitHub Repo</a></h3>';
    exit;
}

echo '<h3>Given fork username: <a href="https://github.com/' . $username . '/openpilot_084">' . $username . '</a></h3>';


if ($branch != "") {
    echo '<h3>Given branch: <a href="https://github.com/'.$username.'/openpilot_084/tree/'.$branch.'">' . $branch . '</a></h3>';
} else {
    echo '<h3>❗ No branch supplied, git will use default GitHub branch ❗</h3>';
}

#if ($loading_msg != "" and $supplied_loading_msg) {
#    echo '<h3>You\'ve discovered a hidden secret!</br>When using this binary, this custom message will be shown: <span>Installing ' . $loading_msg . '</span></h3>';
#}

echo '<html>
    <body>
        <form method="post">
        <button class="button" name="download">Download Custom Installer Binary</button>
    </form>
    <h5>Or enter this URL on the setup screen in NEOS.</h5>
    </body>
</html>';

if(array_key_exists('download', $_POST)) {
    header("Location: " . BASE_DIR . "/build.php?username=" . $username . "&branch=" . $branch);
    #header("Location: " . BASE_DIR . "/build.php?username=" . $username . "&branch=" . $branch . "&loading_msg=" . $loading_msg);

    exit;
}
?>
