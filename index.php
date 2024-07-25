<?php
// =============================
// Configuration
// =============================

$browseDirectories = true; // Navigate into sub-folders
$title = "Index of {{path}}";
$subtitle = "{{files}} objects in this folder, {{size}} total"; // Empty to disable
$breadcrumbs = false; // Make links in {{path}}
$showParent = false; // Display a (parent directory) link
$showDirectories = true;
$showDirectoriesFirst = true; // Lists directories first when sorting by name
$showHiddenFiles = false; // Display files starting with "."
$alignment = getenv("ALIGNMENT"); // You can use 'left' or 'center'
$showIcons = true;
$dateFormat = "d/m/y H:i"; // Used in date() function
$sizeDecimals = 1;
$robots = "noindex, nofollow"; // Avoid robots by default
$footerText = getenv("FOOTER_TEXT"); // Display the "Powered by" footer
$openIndex = $browseDirectories && true; // Open index files present in the current directory if $browseDirectories is enabled
$browseDefault = null; // Start on a different "default" directory if $browseDirectories is enabled
$ignore = []; // Names of files and folders to not list (case-sensitive)
$theme = getenv("THEME"); // Theme Light/Dark
$colorPalette = getenv("COLOR_PALETTE"); // Color Palette

// =============================
// =============================

// Who am I?
$_self = basename($_SERVER["PHP_SELF"]);
$_path = str_replace("\\", "/", dirname($_SERVER["PHP_SELF"]));
$_total = 0;
$_total_size = 0;

// Directory browsing
$_browse = null;
if ($browseDirectories) {
    if (!empty($browseDefault) && !isset($_GET["b"])) {
        $_GET["b"] = $browseDefault;
    }
    $_GET["b"] = trim(str_replace("\\", "/", (string) @$_GET["b"]), "/ ");
    $_GET["b"] = str_replace(["/..", "../"], "", (string) @$_GET["b"]); // Avoid going up into filesystem
    if (
        !empty($_GET["b"]) &&
        $_GET["b"] != ".." &&
        is_dir("files/" . $_GET["b"])
    ) {
        $browseIgnored = false;
        foreach (explode("/", $_GET["b"]) as $browseName) {
            if (
                !empty($ignore) &&
                is_array($ignore) &&
                in_array($browseName, $ignore)
            ) {
                $browseIgnored = true;
                break;
            }
        }
        if (!$browseIgnored) {
            $_browse = $_GET["b"];
        } // Avoid browsing ignored folder names
    }
}

// Index open
if (!empty($_browse) && $openIndex) {
    $_index = null;
    if (file_exists("files/" . $_browse . "/index.htm")) {
        $_index = "/index.htm";
    }
    if (file_exists("files/" . $_browse . "/index.html")) {
        $_index = "/index.html";
    }
    if (file_exists("files/" . $_browse . "/index.php")) {
        $_index = "/index.php";
    }
    if (!empty($_index)) {
        header("Location: " . $_browse . $_index);
        exit();
    }
}

// Encoded images generator
if (!empty($_GET["i"])) {
    header("Content-type: image/png");
    switch ($_GET["i"]) {
        case "asc":
            exit(
                base64_decode(
                    "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAAFUlEQVQImWNgoBT8x4JxKsBpAhUAAPUACPhuMItPAAAAAElFTkSuQmCC"
                )
            );
        case "desc":
            exit(
                base64_decode(
                    "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAAF0lEQVQImWNgoBb4j0/iPzYF/7FgCgAADegI+OMeBfsAAAAASUVORK5CYII="
                )
            );
        case "directory":
            exit(
                base64_decode(
                    "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAASklEQVQYlYWPwQ3AMAgDb3Tv5AHdR5OqTaBB8gM4bAGApACPRr/XuujA+vqVcAI3swDYjqRSH7B9oHI8grbTgWN+g3+xq0k6TegCNtdPnJDsj8sAAAAASUVORK5CYII="
                )
            );
        case "file":
            exit(
                base64_decode(
                    "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAPklEQVQYlcXQsQ0AIAhE0b//GgzDWGdjDCJoKck13CsIALi7gJxyVmFmyrsXLHEHD7zBmBbezvoJm4cL0OwYouM4O3J+UDYAAAAASUVORK5CYII="
                )
            );
    }
}

// I'm not sure this function is really needed...
function ls($path, $show_folders = false, $show_hidden = false)
{
    global $_self, $_total, $_total_size, $ignore;
    $ls = [];
    $ls_d = [];
    if (($dh = @opendir($path)) === false) {
        return $ls;
    }
    if (substr($path, -1) != "/") {
        $path .= "/";
    }
    while (($file = readdir($dh)) !== false) {
        if ($file == $_self) {
            continue;
        }
        if ($file == "." || $file == "..") {
            continue;
        }
        if (!$show_hidden) {
            if (substr($file, 0, 1) == ".") {
                continue;
            }
        }
        if (!empty($ignore) && is_array($ignore) && in_array($file, $ignore)) {
            continue;
        }
        $isdir = is_dir($path . $file);
        if (!$show_folders && $isdir) {
            continue;
        }
        $item = [
            "name" => $file,
            "isdir" => $isdir,
            "size" => $isdir ? 0 : filesize($path . $file),
            "time" => filemtime($path . $file),
        ];
        if ($isdir) {
            $ls_d[] = $item;
        } else {
            $ls[] = $item;
        }
        $_total++;
        $_total_size += $item["size"];
    }
    return array_merge($ls_d, $ls);
}

// Get the list of files
$items = ls(
    "./files" . (empty($_browse) ? "" : "/" . $_browse),
    $showDirectories,
    $showHiddenFiles
);

// Sort it
function sortByName($a, $b)
{
    global $showDirectoriesFirst;
    return ($a["isdir"] == $b["isdir"] || !$showDirectoriesFirst
            ? strtolower($a["name"]) > strtolower($b["name"])
            : $a["isdir"] < $b["isdir"])
        ? 1
        : -1;
}
function sortBySize($a, $b)
{
    return ($a["isdir"] == $b["isdir"]
            ? $a["size"] > $b["size"]
            : $a["isdir"] < $b["isdir"])
        ? 1
        : -1;
}
function sortByTime($a, $b)
{
    return $a["time"] > $b["time"] ? 1 : -1;
}
switch (@$_GET["s"]) {
    case "size":
        $_sort = "size";
        usort($items, "sortBySize");
        break;
    case "time":
        $_sort = "time";
        usort($items, "sortByTime");
        break;
    default:
        $_sort = "name";
        usort($items, "sortByName");
        break;
}

// Reverse?
$_sort_reverse = @$_GET["r"] == "1";
if ($_sort_reverse) {
    $items = array_reverse($items);
}

// Add parent
if ($showParent && $_path != "/" && empty($_browse)) {
    array_unshift($items, [
        "name" => "..",
        "isparent" => true,
        "isdir" => true,
        "size" => 0,
        "time" => 0,
    ]);
}

// Add parent in case of browsing a sub-folder
if (!empty($_browse)) {
    array_unshift($items, [
        "name" => "..",
        "isparent" => false,
        "isdir" => true,
        "size" => 0,
        "time" => 0,
    ]);
}

// 37.6 MB is better than 39487001
function humanizeFilesize($val, $round = 0)
{
    $unit = ["", "K", "M", "G", "T", "P", "E", "Z", "Y"];
    do {
        $val /= 1024;
        array_shift($unit);
    } while ($val >= 1000);
    return sprintf("%." . intval($round) . "f", $val) .
        " " .
        array_shift($unit) .
        "B";
}

// Titles parser
function getTitleHTML($title, $breadcrumbs = false)
{
    global $_path, $_browse, $_total, $_total_size, $sizeDecimals;
    $title = htmlentities(
        str_replace(
            ["{{files}}", "{{size}}"],
            [$_total, humanizeFilesize($_total_size, $sizeDecimals)],
            $title
        )
    );
    $path = htmlentities($_path);
    if ($breadcrumbs) {
        $path = sprintf(
            '<a href="%s">%s</a>',
            htmlentities(buildLink(["b" => ""])),
            $path
        );
    }
    if (!empty($_browse)) {
        if ($_path != "/") {
            $path .= "/";
        }
        $browseArray = explode("/", trim($_browse, "/"));
        foreach ($browseArray as $i => $part) {
            if ($breadcrumbs) {
                $path .= sprintf(
                    '<a href="%s">%s</a>',
                    htmlentities(
                        buildLink([
                            "b" => implode(
                                "/",
                                array_slice($browseArray, 0, $i + 1)
                            ),
                        ])
                    ),
                    htmlentities($part)
                );
            } else {
                $path .= htmlentities($part);
            }
            if (count($browseArray) > $i + 1) {
                $path .= "/";
            }
        }
    }
    return str_replace("{{path}}", $path, $title);
}

// Link builder
function buildLink($changes)
{
    global $_self;
    $params = $_GET;
    foreach ($changes as $k => $v) {
        if (is_null($v)) {
            unset($params[$k]);
        } else {
            $params[$k] = $v;
        }
    }
    foreach ($params as $k => $v) {
        $params[$k] = urlencode($k) . "=" . urlencode($v);
    }
    return empty($params) ? $_self : $_self . "?" . implode("&", $params);
}
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>

	<meta charset="UTF-8">
	<meta name="robots" content="<?php echo htmlentities($robots); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title><?php echo getTitleHTML($title); ?></title>

	<style>
		<?php include "css/colors/" . $colorPalette . "-" . $theme . ".css"; ?>
		<?php include "css/style.css"; ?>

		ul#header li .asc span {
			background-image: url('<?php echo $_self; ?>?i=asc');
		}

		ul#header li .desc span {
			background-image: url('<?php echo $_self; ?>?i=desc');
		}

		ul li.item .directory {
			background-image: url('<?php echo $_self; ?>?i=directory');
		}

		ul li.item .file {
			background-image: url('<?php echo $_self; ?>?i=file');
		}
	</style>


</head>
<body <?php if ($alignment == "left") {
    echo 'id="left"';
} ?>>

	<div id="wrapper">

		<h1><?php echo getTitleHTML($title, $breadcrumbs); ?></h1>
		<h2><?php echo getTitleHTML($subtitle, $breadcrumbs); ?></h2>

		<ul id="header">

			<li>
				<a href="<?php echo buildLink([
        "s" => "size",
        "r" => !$_sort_reverse && $_sort == "size" ? "1" : null,
    ]); ?>" class="size <?php if ($_sort == "size") {
    echo $_sort_reverse ? "desc" : "asc";
} ?>"><span>Size</span></a>
				<a href="<?php echo buildLink([
        "s" => "time",
        "r" => !$_sort_reverse && $_sort == "time" ? "1" : null,
    ]); ?>" class="date <?php if ($_sort == "time") {
    echo $_sort_reverse ? "desc" : "asc";
} ?>"><span>Last modified</span></a>
				<a href="<?php echo buildLink([
        "s" => null,
        "r" => !$_sort_reverse && $_sort == "name" ? "1" : null,
    ]); ?>" class="name <?php if ($_sort == "name") {
    echo $_sort_reverse ? "desc" : "asc";
} ?>"><span>Name</span></a>
			</li>

		</ul>

		<ul>

			<?php foreach ($items as $item): ?>

				<li class="item">

					<span class="size"><?php echo $item["isdir"]
         ? "-"
         : humanizeFilesize($item["size"], $sizeDecimals); ?></span>

					<span class="date"><?php echo @$item["isparent"] || empty($item["time"])
         ? "-"
         : date($dateFormat, $item["time"]); ?></span>

					<?php if ($item["isdir"] && $browseDirectories && !@$item["isparent"]) {
         if ($item["name"] == "..") {
             $itemURL = buildLink([
                 "b" => substr($_browse, 0, strrpos($_browse, "/")),
             ]);
         } else {
             $itemURL = buildLink([
                 "b" =>
                     (empty($_browse) ? "" : (string) $_browse . "/") .
                     $item["name"],
             ]);
         }
     } else {
         $itemURL =
             "files/" .
             (empty($_browse)
                 ? ""
                 : str_replace(
                         ["%2F", "%2f"],
                         "/",
                         rawurlencode((string) $_browse)
                     ) . "/") .
             rawurlencode($item["name"]);
     } ?>

					<a href="<?php echo htmlentities($itemURL); ?>" class="name <?php if (
    $showIcons
) {
    echo $item["isdir"] ? "directory" : "file";
} ?>"><?php echo htmlentities($item["name"]) .
    ($item["isdir"] ? " /" : ""); ?></a>

				</li>

			<?php endforeach; ?>

		</ul>

		<p id="footer">
			<?php echo $footerText; ?>
		</p>

	</div>

</body>
</html>
