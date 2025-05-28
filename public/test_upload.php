<?php
$targetDir = '/home/seblin/projet-symfony/arcadiazoo/public/uploads/animals/';
$testFile = $targetDir . 'test_upload.txt';
$currentUser = posix_getpwuid(posix_geteuid())['name'];
echo "Utilisateur PHP : $currentUser\n";
echo "Cible : $testFile\n";
echo "Dossier existant : " . (is_dir($targetDir) ? 'Oui' : 'Non') . "\n";
echo "Écriture possible : " . (is_writable($targetDir) ? 'Oui' : 'Non') . "\n";
clearstatcache(true, $targetDir);
if (is_writable($targetDir)) {
    if (file_put_contents($testFile, 'Test')) {
        echo "Écriture réussie\n";
    } else {
        echo "Échec de l’écriture : " . error_get_last()['message'] . "\n";
    }
} else {
    echo "Dossier non inscriptible après clearstatcache\n";
}
?>