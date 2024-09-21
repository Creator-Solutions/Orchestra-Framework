<?php

define('PHPNEXUS_VERSION', '0.0.1');

// Define the classmap for namespaces
$classmap = [
    'app' => __DIR__ . '/app/',           // Use lowercase 'app' if your namespaces are lowercase
    'Orchestra' => __DIR__ . '/Orchestra/',
];

// Register the autoload function
spl_autoload_register(function(string $classname) use ($classmap) {
    // Explode the fully qualified class name into parts (namespace and class name)
    $parts = explode('\\', $classname);

    // Extract the root namespace (e.g., 'app' or 'Orchestra')
    $namespace = array_shift($parts);

    // Map the namespace to a file path if it exists in the classmap
    if (!array_key_exists($namespace, $classmap)) {
        return;
    }

    // Rebuild the path to the class file from the remaining namespace parts
    $path = implode(DIRECTORY_SEPARATOR, $parts);

    // Construct the full path to the class file (e.g., 'User.php')
    $file = $classmap[$namespace] . $path . '.php';

    // Check if the file exists, and include it if found
    if (file_exists($file)) {
        require_once $file;
    } else {
        // Optionally log or handle class not found
        error_log("Class file not found: $file");
    }
});
