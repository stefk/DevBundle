<?php

/***************************************************************
 * This script appends the bundle to be tested to bundles.ini
 * and registers it in the app autoloader (these steps are
 * necessary because the bundle was used as the root package
 * during the execution of composer).
 **************************************************************/

$loader = require __DIR__ . '/autoload.php';

use Claroline\BundleRecorder\Detector\Detector;

// convert errors to exceptions
set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

if (count($argv) < 2) {
    echo "The bundle directory must be passed as argument\n";
    exit(1);
}

$detector = new Detector();
$bundle = $detector->detectBundle($argv[1]);

$bundleFile = __DIR__ . '/config/bundles.ini';
$bundles = file_get_contents($bundleFile);
$bundles .= "\n{$bundle} = true";
file_put_contents($bundleFile, $bundles);

echo "Updated bundles.ini with target bundle:\n{$bundle}";

$bundleParts = explode('\\', $bundle);
array_pop($bundleParts);
$bundleNamespace = implode('\\', $bundleParts);
$loader->add($bundleNamespace, $argv[1]);

echo "Registered bundle in autoloader:\n-{$bundleNamespace}\n-{$argv[1]}";
