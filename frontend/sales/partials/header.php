<?php
// It is assumed that a session has been started on the page including this partial

// Set a default title if not provided
$page_title = $page_title ?? "Sales Dashboard";

// Get the current script\'s filename
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - Aquaflow</title>
    <link rel="shortcut icon" href="../../favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="../css/tailwind.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="bg-gray-100 flex font-sans text-gray-800">

