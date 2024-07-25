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
            if ($theme == "light") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAAFUlEQVQImWNgoBT8x4JxKsBpAhUAAPUACPhuMItPAAAAAElFTkSuQmCC"
                    )
                );
            } else {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAA0GVYSWZJSSoACAAAAAoAAAEEAAEAAAAHAAAAAQEEAAEAAAAHAAAAAgEDAAMAAACGAAAAEgEDAAEAAAABAAAAGgEFAAEAAACMAAAAGwEFAAEAAACUAAAAKAEDAAEAAAACAAAAMQECAA0AAACcAAAAMgECABQAAACqAAAAaYcEAAEAAAC+AAAAAAAAAAgACAAIAEgAAAABAAAASAAAAAEAAABHSU1QIDIuMTAuMzgAADIwMjQ6MDc6MjYgMDM6NDU6NDkAAQABoAMAAQAAAAEAAAAAAAAAC8fgeQAAAYRpQ0NQSUNDIHByb2ZpbGUAAHicfZE9SMNAHMVfU6UiFQcrqAhmqOJgFxVxrFUoQoVSK7TqYHLpFzRpSFJcHAXXgoMfi1UHF2ddHVwFQfADxNnBSdFFSvxfUmgR48FxP97de9y9A4R6malmRxRQNctIxWNiJrsqBl4RwCD6MY4RiZn6XDKZgOf4uoePr3cRnuV97s/Ro+RMBvhE4ijTDYt4g3hm09I57xOHWFFSiM+JJwy6IPEj12WX3zgXHBZ4ZshIp+aJQ8RioY3lNmZFQyWeJg4rqkb5QsZlhfMWZ7VcZc178hcGc9rKMtdpDiOORSwhCREyqiihDAsRWjVSTKRoP+bhH3L8SXLJ5CqBkWMBFaiQHD/4H/zu1sxPTbpJwRjQ+WLbH6NAYBdo1Gz7+9i2GyeA/xm40lr+Sh2Y/SS91tLCR0DvNnBx3dLkPeByBxh40iVDciQ/TSGfB97P6JuyQN8t0L3m9tbcx+kDkKauEjfAwSEwVqDsdY93d7X39u+ZZn8/06lyzZGNgXIAAA14aVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJYTVAgQ29yZSA0LjQuMC1FeGl2MiI+CiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOmQxOGMxMDIzLWI0ODUtNDg4NS05ODVmLTI1N2Y5YjJlM2UwNCIKICAgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo2ZmEzYzc3Yi03MDgxLTRmNWItOGVmYS04NWUzOGFiZWNjMjYiCiAgIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDplYjc0YjU4MS1kNTllLTRiODEtYTBjZC1jNmZjZjMyYmNiNWIiCiAgIGRjOkZvcm1hdD0iaW1hZ2UvcG5nIgogICBHSU1QOkFQST0iMi4wIgogICBHSU1QOlBsYXRmb3JtPSJMaW51eCIKICAgR0lNUDpUaW1lU3RhbXA9IjE3MjE5NDU3NTA2MTcxNzciCiAgIEdJTVA6VmVyc2lvbj0iMi4xMC4zOCIKICAgdGlmZjpPcmllbnRhdGlvbj0iMSIKICAgeG1wOkNyZWF0b3JUb29sPSJHSU1QIDIuMTAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjQ6MDc6MjZUMDM6NDU6NDkrMDU6MzAiCiAgIHhtcDpNb2RpZnlEYXRlPSIyMDI0OjA3OjI2VDAzOjQ1OjQ5KzA1OjMwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiCiAgICAgIHN0RXZ0OmNoYW5nZWQ9Ii8iCiAgICAgIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6Mjg4YjI1Y2MtNGYzNy00M2NmLThiZDUtYzFiMTNhNWM4MjdhIgogICAgICBzdEV2dDpzb2Z0d2FyZUFnZW50PSJHaW1wIDIuMTAgKExpbnV4KSIKICAgICAgc3RFdnQ6d2hlbj0iMjAyNC0wNy0yNlQwMzo0NTo1MCswNTozMCIvPgogICAgPC9yZGY6U2VxPgogICA8L3htcE1NOkhpc3Rvcnk+CiAgPC9yZGY6RGVzY3JpcHRpb24+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz5UCdg3AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH6AcZFg8y/hZlxwAAACZJREFUCNdjYCAXMDIwMDD8////P4YEIyMjI4yDrABZnAGbAsoAAPJqC/8TrwAaAAAAAElFTkSuQmCC"
                    )
                );
            }
        case "desc":
            if ($theme == "light") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAAF0lEQVQImWNgoBb4j0/iPzYF/7FgCgAADegI+OMeBfsAAAAASUVORK5CYII="
                    )
                );
            } elseif ($theme == "dark") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAcAAAAHCAYAAADEUlfTAAAA0GVYSWZJSSoACAAAAAoAAAEEAAEAAAAHAAAAAQEEAAEAAAAHAAAAAgEDAAMAAACGAAAAEgEDAAEAAAABAAAAGgEFAAEAAACMAAAAGwEFAAEAAACUAAAAKAEDAAEAAAACAAAAMQECAA0AAACcAAAAMgECABQAAACqAAAAaYcEAAEAAAC+AAAAAAAAAAgACAAIAEgAAAABAAAASAAAAAEAAABHSU1QIDIuMTAuMzgAADIwMjQ6MDc6MjYgMDM6NDY6MDMAAQABoAMAAQAAAAEAAAAAAAAAOM59wQAAAYRpQ0NQSUNDIHByb2ZpbGUAAHicfZE9SMNAHMVfU6UiFQcrqAhmqOJgFxVxrFUoQoVSK7TqYHLpFzRpSFJcHAXXgoMfi1UHF2ddHVwFQfADxNnBSdFFSvxfUmgR48FxP97de9y9A4R6malmRxRQNctIxWNiJrsqBl4RwCD6MY4RiZn6XDKZgOf4uoePr3cRnuV97s/Ro+RMBvhE4ijTDYt4g3hm09I57xOHWFFSiM+JJwy6IPEj12WX3zgXHBZ4ZshIp+aJQ8RioY3lNmZFQyWeJg4rqkb5QsZlhfMWZ7VcZc178hcGc9rKMtdpDiOORSwhCREyqiihDAsRWjVSTKRoP+bhH3L8SXLJ5CqBkWMBFaiQHD/4H/zu1sxPTbpJwRjQ+WLbH6NAYBdo1Gz7+9i2GyeA/xm40lr+Sh2Y/SS91tLCR0DvNnBx3dLkPeByBxh40iVDciQ/TSGfB97P6JuyQN8t0L3m9tbcx+kDkKauEjfAwSEwVqDsdY93d7X39u+ZZn8/06lyzZGNgXIAAA14aVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJYTVAgQ29yZSA0LjQuMC1FeGl2MiI+CiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOjQzNGRlZjY1LTRmNTctNGRlYy1iODM2LTdkOWU2NjhjNmZiOCIKICAgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3NmJiODA3MS05OGNmLTQ5ZmUtYTE4ZC1iMzI1ZWFhODBlZWYiCiAgIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpiYTdhZWFiNy1lN2I2LTQwZjAtYjRmNS1hMDIyYTlkZjU2NmYiCiAgIGRjOkZvcm1hdD0iaW1hZ2UvcG5nIgogICBHSU1QOkFQST0iMi4wIgogICBHSU1QOlBsYXRmb3JtPSJMaW51eCIKICAgR0lNUDpUaW1lU3RhbXA9IjE3MjE5NDU3NjQyOTQxNzIiCiAgIEdJTVA6VmVyc2lvbj0iMi4xMC4zOCIKICAgdGlmZjpPcmllbnRhdGlvbj0iMSIKICAgeG1wOkNyZWF0b3JUb29sPSJHSU1QIDIuMTAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjQ6MDc6MjZUMDM6NDY6MDMrMDU6MzAiCiAgIHhtcDpNb2RpZnlEYXRlPSIyMDI0OjA3OjI2VDAzOjQ2OjAzKzA1OjMwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiCiAgICAgIHN0RXZ0OmNoYW5nZWQ9Ii8iCiAgICAgIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MWQwYjRmYTYtYmFiMi00NzQ5LWIyYzAtOGQ4ZjhiYjZkNzhkIgogICAgICBzdEV2dDpzb2Z0d2FyZUFnZW50PSJHaW1wIDIuMTAgKExpbnV4KSIKICAgICAgc3RFdnQ6d2hlbj0iMjAyNC0wNy0yNlQwMzo0NjowNCswNTozMCIvPgogICAgPC9yZGY6U2VxPgogICA8L3htcE1NOkhpc3Rvcnk+CiAgPC9yZGY6RGVzY3JpcHRpb24+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz4e4B3dAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH6AcZFhAE/Pb+wAAAACFJREFUCNdjYKAK+P///39kPiM2CUZGRka4JLoOZAXkAQDxSAv/cDPYvgAAAABJRU5ErkJggg=="
                    )
                );
            }
        case "directory":
            if ($theme == "light") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAASklEQVQYlYWPwQ3AMAgDb3Tv5AHdR5OqTaBB8gM4bAGApACPRr/XuujA+vqVcAI3swDYjqRSH7B9oHI8grbTgWN+g3+xq0k6TegCNtdPnJDsj8sAAAAASUVORK5CYII="
                    )
                );
            } elseif ($theme == "dark") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAA0GVYSWZJSSoACAAAAAoAAAEEAAEAAAAKAAAAAQEEAAEAAAAKAAAAAgEDAAMAAACGAAAAEgEDAAEAAAABAAAAGgEFAAEAAACMAAAAGwEFAAEAAACUAAAAKAEDAAEAAAACAAAAMQECAA0AAACcAAAAMgECABQAAACqAAAAaYcEAAEAAAC+AAAAAAAAAAgACAAIAEgAAAABAAAASAAAAAEAAABHSU1QIDIuMTAuMzgAADIwMjQ6MDc6MjYgMDM6MTk6MTIAAQABoAMAAQAAAAEAAAAAAAAAuAPtUwAAAYRpQ0NQSUNDIHByb2ZpbGUAAHicfZE9SMNAHMVfU6UiFQcrqAhmqOJgFxVxrFUoQoVSK7TqYHLpFzRpSFJcHAXXgoMfi1UHF2ddHVwFQfADxNnBSdFFSvxfUmgR48FxP97de9y9A4R6malmRxRQNctIxWNiJrsqBl4RwCD6MY4RiZn6XDKZgOf4uoePr3cRnuV97s/Ro+RMBvhE4ijTDYt4g3hm09I57xOHWFFSiM+JJwy6IPEj12WX3zgXHBZ4ZshIp+aJQ8RioY3lNmZFQyWeJg4rqkb5QsZlhfMWZ7VcZc178hcGc9rKMtdpDiOORSwhCREyqiihDAsRWjVSTKRoP+bhH3L8SXLJ5CqBkWMBFaiQHD/4H/zu1sxPTbpJwRjQ+WLbH6NAYBdo1Gz7+9i2GyeA/xm40lr+Sh2Y/SS91tLCR0DvNnBx3dLkPeByBxh40iVDciQ/TSGfB97P6JuyQN8t0L3m9tbcx+kDkKauEjfAwSEwVqDsdY93d7X39u+ZZn8/06lyzZGNgXIAAA14aVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJYTVAgQ29yZSA0LjQuMC1FeGl2MiI+CiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOjc1NGJlNzcwLThhNmEtNDIxMC1hMGY3LTk5ODlhZDU2YjNlMiIKICAgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo0ODg0NWJhOC01Y2EwLTQ2ZTQtYmQxOS03YTQ3NDQ0NTAxNzMiCiAgIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo1Nzg0NTc1ZC0zNzYzLTRiMTYtOTliZi0zNGI0OTA5NDEyZTUiCiAgIGRjOkZvcm1hdD0iaW1hZ2UvcG5nIgogICBHSU1QOkFQST0iMi4wIgogICBHSU1QOlBsYXRmb3JtPSJMaW51eCIKICAgR0lNUDpUaW1lU3RhbXA9IjE3MjE5NDQxNTM4MTM3NzAiCiAgIEdJTVA6VmVyc2lvbj0iMi4xMC4zOCIKICAgdGlmZjpPcmllbnRhdGlvbj0iMSIKICAgeG1wOkNyZWF0b3JUb29sPSJHSU1QIDIuMTAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjQ6MDc6MjZUMDM6MTk6MTIrMDU6MzAiCiAgIHhtcDpNb2RpZnlEYXRlPSIyMDI0OjA3OjI2VDAzOjE5OjEyKzA1OjMwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiCiAgICAgIHN0RXZ0OmNoYW5nZWQ9Ii8iCiAgICAgIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6MTgwM2I4MzItNmJkNC00NGQzLWFhOTUtMzk4Yjg5OTE3ZWEwIgogICAgICBzdEV2dDpzb2Z0d2FyZUFnZW50PSJHaW1wIDIuMTAgKExpbnV4KSIKICAgICAgc3RFdnQ6d2hlbj0iMjAyNC0wNy0yNlQwMzoxOToxMyswNTozMCIvPgogICAgPC9yZGY6U2VxPgogICA8L3htcE1NOkhpc3Rvcnk+CiAgPC9yZGY6RGVzY3JpcHRpb24+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz5MIaXqAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH6AcZFTENC/Pt3gAAAFxJREFUGNN9kMERwCAIBE8mHVmHPdGTtEFNl9dlHI3sS3TlBgAA7s4Vdyc2TIe2gD9I8nYvSnH/9KiYczIza1sDVc43TO+9jDfF3gS9GQBkJsYY7SZHxLmKHXV8Aea7ZBk0TQrBAAAAAElFTkSuQmCC"
                    )
                );
            }
        case "file":
            if ($theme == "light") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAAPklEQVQYlcXQsQ0AIAhE0b//GgzDWGdjDCJoKck13CsIALi7gJxyVmFmyrsXLHEHD7zBmBbezvoJm4cL0OwYouM4O3J+UDYAAAAASUVORK5CYII="
                    )
                );
            } elseif ($theme == "dark") {
                exit(
                    base64_decode(
                        "iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAA0GVYSWZJSSoACAAAAAoAAAEEAAEAAAAKAAAAAQEEAAEAAAAKAAAAAgEDAAMAAACGAAAAEgEDAAEAAAABAAAAGgEFAAEAAACMAAAAGwEFAAEAAACUAAAAKAEDAAEAAAACAAAAMQECAA0AAACcAAAAMgECABQAAACqAAAAaYcEAAEAAAC+AAAAAAAAAAgACAAIAEgAAAABAAAASAAAAAEAAABHSU1QIDIuMTAuMzgAADIwMjQ6MDc6MjYgMDM6MjM6MzYAAQABoAMAAQAAAAEAAAAAAAAApxs+kAAAAYRpQ0NQSUNDIHByb2ZpbGUAAHicfZE9SMNAHMVfU6UiFQcrqAhmqOJgFxVxrFUoQoVSK7TqYHLpFzRpSFJcHAXXgoMfi1UHF2ddHVwFQfADxNnBSdFFSvxfUmgR48FxP97de9y9A4R6malmRxRQNctIxWNiJrsqBl4RwCD6MY4RiZn6XDKZgOf4uoePr3cRnuV97s/Ro+RMBvhE4ijTDYt4g3hm09I57xOHWFFSiM+JJwy6IPEj12WX3zgXHBZ4ZshIp+aJQ8RioY3lNmZFQyWeJg4rqkb5QsZlhfMWZ7VcZc178hcGc9rKMtdpDiOORSwhCREyqiihDAsRWjVSTKRoP+bhH3L8SXLJ5CqBkWMBFaiQHD/4H/zu1sxPTbpJwRjQ+WLbH6NAYBdo1Gz7+9i2GyeA/xm40lr+Sh2Y/SS91tLCR0DvNnBx3dLkPeByBxh40iVDciQ/TSGfB97P6JuyQN8t0L3m9tbcx+kDkKauEjfAwSEwVqDsdY93d7X39u+ZZn8/06lyzZGNgXIAAA14aVRYdFhNTDpjb20uYWRvYmUueG1wAAAAAAA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/Pgo8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJYTVAgQ29yZSA0LjQuMC1FeGl2MiI+CiA8cmRmOlJERiB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPgogIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiCiAgICB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIKICAgIHhtbG5zOnN0RXZ0PSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VFdmVudCMiCiAgICB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iCiAgICB4bWxuczpHSU1QPSJodHRwOi8vd3d3LmdpbXAub3JnL3htcC8iCiAgICB4bWxuczp0aWZmPSJodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyIKICAgIHhtbG5zOnhtcD0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLyIKICAgeG1wTU06RG9jdW1lbnRJRD0iZ2ltcDpkb2NpZDpnaW1wOmY0YTRkM2UzLTdiMWEtNDMxNi05YmI3LTRjNWQ3Y2NjMGMyMCIKICAgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo1YzdjYjljMC02Mjg2LTQ2OWEtOWY5ZS0yMTU5ZTcxN2NhOTMiCiAgIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo5N2UyN2FhYi1lOWFlLTRiYTUtYmY2MC1iZjZlOWVlNzI1NmEiCiAgIGRjOkZvcm1hdD0iaW1hZ2UvcG5nIgogICBHSU1QOkFQST0iMi4wIgogICBHSU1QOlBsYXRmb3JtPSJMaW51eCIKICAgR0lNUDpUaW1lU3RhbXA9IjE3MjE5NDQ0MTczMzMzMjAiCiAgIEdJTVA6VmVyc2lvbj0iMi4xMC4zOCIKICAgdGlmZjpPcmllbnRhdGlvbj0iMSIKICAgeG1wOkNyZWF0b3JUb29sPSJHSU1QIDIuMTAiCiAgIHhtcDpNZXRhZGF0YURhdGU9IjIwMjQ6MDc6MjZUMDM6MjM6MzYrMDU6MzAiCiAgIHhtcDpNb2RpZnlEYXRlPSIyMDI0OjA3OjI2VDAzOjIzOjM2KzA1OjMwIj4KICAgPHhtcE1NOkhpc3Rvcnk+CiAgICA8cmRmOlNlcT4KICAgICA8cmRmOmxpCiAgICAgIHN0RXZ0OmFjdGlvbj0ic2F2ZWQiCiAgICAgIHN0RXZ0OmNoYW5nZWQ9Ii8iCiAgICAgIHN0RXZ0Omluc3RhbmNlSUQ9InhtcC5paWQ6NGQ4YmZmYTktYzNkZC00NDc1LWE2MTctM2VkYzNjZTkzOWMyIgogICAgICBzdEV2dDpzb2Z0d2FyZUFnZW50PSJHaW1wIDIuMTAgKExpbnV4KSIKICAgICAgc3RFdnQ6d2hlbj0iMjAyNC0wNy0yNlQwMzoyMzozNyswNTozMCIvPgogICAgPC9yZGY6U2VxPgogICA8L3htcE1NOkhpc3Rvcnk+CiAgPC9yZGY6RGVzY3JpcHRpb24+CiA8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9InciPz4d09ogAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH6AcZFTUlWiqAIAAAAElJREFUGNOdj8EJACAMA6/i/kt0mGYrfQlSrIj3bA6SAhARI8OJPXD3kW8AjQIzs10uxSz3k7Dqrxur7Fr99My32AEklTslATABfcw5wzJalD4AAAAASUVORK5CYII="
                    )
                );
            }
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
