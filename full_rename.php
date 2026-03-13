<?php
// NSS Skin & Beauty - Final Global Renaming Script
$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    '>NS Beauty<' => '>NSS Skin & Beauty<',
    'NS Beauty' => 'NSS Skin & Beauty',
    'nsbeauty.com' => 'nssskinbeauty.com',
    'admin@nsbeauty.com' => 'admin@nssskinbeauty.com',
    'info@nsbeauty.com' => 'info@nssskinbeauty.com',
    ' instagram.com/nsbeauty' => ' instagram.com/nssskinbeauty',
    ' facebook.com/nsbeauty' => ' facebook.com/nssskinbeauty',
    ' twitter.com/nsbeauty' => ' twitter.com/nssskinbeauty',
    'NS</span><span class="logo-beauty">Beauty' => 'NSS</span><span class="logo-beauty">Skin & Beauty',
    'logo-ns">NS</span>' => 'logo-ns">NSS</span>',
    'logo-beauty">Beauty' => 'logo-beauty">Skin & Beauty',
    "'NS'" => "'NSS'",
    '"NS"' => '"NSS"',
];

echo "<h2>Starting Final Renaming Process...</h2>";

foreach ($files as $file) {
    if ($file->isDir()) continue;
    
    $path = $file->getRealPath();
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    
    // Only process relevant text files
    if (in_array($ext, ['php', 'sql', 'html', 'css', 'js'])) {
        // Skip this script itself
        if (basename($path) === basename(__FILE__)) continue;

        $content = file_get_contents($path);
        $originalContent = $content;
        
        foreach ($replacements as $old => $new) {
            $content = str_replace($old, $new, $content);
        }
        
        if ($content !== $originalContent) {
            file_put_contents($path, $content);
            echo "<div style='color:green'>✓ Updated: " . str_replace($dir, '', $path) . "</div>";
        }
    }
}

echo "<h3>Global renaming completed!</h3>";
echo "<p>Next steps:</p>";
echo "<ul>
    <li>Run <b>update_db.php</b> to update your database values.</li>
    <li>Logout and Login again to refresh your session name.</li>
</ul>";
?>
