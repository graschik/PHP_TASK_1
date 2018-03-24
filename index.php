<?php

declare(strict_types=1);

require_once('dependency_manager.php');

$packages = array(
    'A' => [
        'name' => 'A',
        'dependencies' => ['B', 'C'],
    ],
    'B' => [
        'name' => 'B',
        'dependencies' => [],
    ],
    'C' => [
        'name' => 'C',
        'dependencies' => ['B', 'D'],
    ],
    'D' => [
        'name' => 'D',
        'dependencies' => [],
    ]
);

try {

    validatePackageDefinition($packages);
    echo '<pre>';
    print_r(getAllPackageDependencies($packages, 'A'));
    echo '<pre>';
} catch (PackageDependenciesException $e) {
    echo $e->getMessage();
}
