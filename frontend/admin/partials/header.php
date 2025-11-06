<?php
// It is assumed that a session has been started on the page including this partial

// Set a default title if not provided
$page_title = $page_title ?? "Admin Dashboard";

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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide icons loaded once for admin pages -->
    <script src="https://cdn.jsdelivr.net/npm/lucide@latest/dist/lucide.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.lucide) {
                try {
                    lucide.replace({
                        'strokeWidth': 2
                    });
                } catch (e) {
                    console.warn('Lucide replace failed', e);
                }
            }
        });
    </script>
</head>

<body class="bg-gray-100 flex font-sans text-gray-800">