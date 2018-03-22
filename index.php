<?php

declare(strict_types=1);

require_once('dependency_manager.php');

$packages = array(
    'A' => [
        'name' => 'A',
        'dependencies' => ['B', 'C','F'],
    ],
    'B' => [
        'name' => 'B',
        'dependencies' => ['G'],
    ],
    'C' => [
        'name' => 'C',
        'dependencies' => ['B', 'D'],
    ],
    'D' => [
        'name' => 'D',
        'dependencies' => ['E'],
    ],
    'E' => [
        'name' => 'E',
        'dependencies' => ['G'],
    ],
    'F' => [
        'name' => 'F',
        'dependencies' => ['E','C'],
    ],
    'G' => [
        'name' => 'G',
        'dependencies' => [],
    ],
    'H' => [
        'name' => 'H',
        'dependencies' => ['A'],
    ],
);

validatePackageDefinition($packages);

echo '<pre>';
print_r(getAllPackageDependencies($packages,'A'));
echo '<pre>';
