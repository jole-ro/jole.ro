<?php
/**
 * Jolero Redirect & URL Shortener Script
 * 
 * Handles short links for jole.ro and redirects all other traffic to jolero.eu.
 */

$links = [
    'promo' => 'https://jolero.eu/current-promotion',
    'contact' => 'https://jolero.eu/ro/contact',
    'fb' => 'https://facebook.com/jolero',
];

// Load custom links from JSON if exists
if (file_exists('links.json')) {
    $customLinks = json_decode(file_get_contents('links.json'), true);
    if (is_array($customLinks)) {
        $links = array_merge($links, $customLinks);
    }
}

$requestUri = $_SERVER['REQUEST_URI'];
$path = trim(parse_url($requestUri, PHP_URL_PATH), '/');

$isShortLink = array_key_exists($path, $links);
$destinationUrl = '';

if ($isShortLink) {
    $destinationUrl = $links[$path];
} else {
    // Catch-all: Redirect everything else to jolero.eu
    $destinationBase = 'https://jolero.eu/';
    $destinationUrl = $destinationBase . $path;
    // Add trailing slash if empty (homepage) or if it's a directory-like structure
    if ($path === '' || (strpos($path, '.') === false && substr($path, -1) !== '/')) {
        $destinationUrl .= '/';
    }
}

// Google Analytics ID for jole.ro
$gaId = 'G-1K2DGCC29S';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting... | Jolero</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $gaId; ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag('js', new Date());
        gtag('config', '<?php echo $gaId; ?>', {
            'transport_type': 'beacon',
            'page_path': '<?php echo $requestUri; ?>'
        });
    </script>

    <?php if ($isShortLink): ?>
        <!-- Redirect for short links with a slight delay to ensure tracking -->
        <meta http-equiv="refresh" content="0.5;url=<?php echo $destinationUrl; ?>">
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo $destinationUrl; ?>";
            }, 500);
        </script>
    <?php else: ?>
        <!-- Catch-all redirect -->
        <meta http-equiv="refresh" content="0;url=<?php echo $destinationUrl; ?>">
        <script>
            window.location.href = "<?php echo $destinationUrl; ?>";
        </script>
    <?php endif; ?>

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .bg-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center">
        <div class="mb-8 animate-pulse">
            <div class="w-16 h-16 bg-gradient rounded-full mx-auto flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6">
                    </path>
                </svg>
            </div>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-2">Redirecting You</h1>
        <p class="text-gray-600 mb-6">Connecting you to our high-performance digital presence...</p>
        <p class="text-sm text-gray-400">If you are not redirected automatically, <a
                href="<?php echo $destinationUrl; ?>" class="text-indigo-600 underline">click here</a>.</p>
    </div>
</body>

</html>
<?php
exit;
