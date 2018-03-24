<?php

declare(strict_types=1);

require_once 'PackageDependenciesException.php';

function validatePackageDefinition(array $packages): void
{
    nameVerification($packages);
    checkExistenceOfDependencies($packages);
    checkForDependencies($packages);
    checkCyclicDependencies($packages);
}

function getAllPackageDependencies(array $packages, string $packageName): array
{
    $dependencies = array();

    foreach ($packages[$packageName]['dependencies'] as $dependency) {
        $dependencyArray = array();
        $dependencies = array_merge($dependencies, getDependencies($packages, $dependencyArray, $dependency));
    }

    $dependencies = array_unique($dependencies);
    $newDependencies = $dependencies;

    foreach ($dependencies as $dependency) {
        if (empty($packages[$dependency]['dependencies'])) {
            $key = array_search($dependency, $newDependencies);
            unset($newDependencies[$key]);
            array_unshift($newDependencies, $dependency);
        }

    }
    return $newDependencies;
}

function getDependencies(array $packages, array $dependencyArray, string $dependPackage): array
{
    array_unshift($dependencyArray, $dependPackage);

    if (empty($packages[$dependPackage]['dependencies'])) {
        return $dependencyArray;
    }

    foreach ($packages[$dependPackage]['dependencies'] as $dependency) {

        if (!empty($packages[$dependPackage]['dependencies'][$dependency])) {
            array_unshift($dependencyArray, $dependPackage);
        }

        $dependencyArray = getDependencies($packages, $dependencyArray, $dependency);
    }
    return $dependencyArray;
}

function nameVerification(array $packages): void
{
    foreach ($packages as $package => $values)
        if ($package != $values['name'] || $package == '')
            throw new PackageDependenciesException('The key of the array does not match the name');
}

function checkExistenceOfDependencies(array $packages): void
{
    foreach ($packages as $package)
        if (!array_key_exists('dependencies', $package))
            throw new PackageDependenciesException('There are not dependencies field in element ' . $package['name']);
}

function checkForDependencies(array $packages): void
{
    $keys = array_keys($packages);

    foreach ($packages as $package) {
        $dif = array(array_diff($package['dependencies'], $keys));
        if (!empty($dif[0]))
            throw new PackageDependenciesException('There are excess dependencies in element ' . $package['name']);
    }
}

function checkCyclicDependencies(array $packages): void
{
    foreach ($packages as $package) {
        foreach ($package['dependencies'] as $dependency) {
            $dependencyArray = array($package['name']);
            checkCyclic($packages, $dependencyArray, $dependency);
        }
    }
}

function checkCyclic(array $packages, array $dependencyArray, string $dependPackage): void
{
    if (empty($packages[$dependPackage]['dependencies']))
        return;

    array_push($dependencyArray, $dependPackage);
    $array = array();

    foreach ($packages[$dependPackage]['dependencies'] as $dependency) {
        if (in_array($dependency, $dependencyArray) && !empty($packages[$dependency]['dependencies']))
            throw new PackageDependenciesException('There is cyclic depend in element!');

        if (!empty($packages[$dependPackage]['dependencies'][$dependency])) {
            array_push($array, $dependency);
            $array = array_unique($array);
            array_push($dependencyArray, $dependency);
            $dependencyArray = array_unique($dependencyArray);
        }

        checkCyclic($packages, $dependencyArray, $dependency);
    }

    foreach ($array as $value) {
        $key = array_search($value, $array);
        unset($dependencyArray[$key]);
    }
}
