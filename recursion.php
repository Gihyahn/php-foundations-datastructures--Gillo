<?php
$library = [
    "Fiction" => [
        "Fantasy" => ["Harry Potter", "The Hobbit"],
        "Mystery" => ["Sherlock Holmes", "Gone Girl"],
    ],
    "Non-Fiction" => [
        "Science" => ["A Brief History of Time", "The Selfish Gene"],
        "Biography" => ["Steve Jobs", "Becoming"],
    ],
];

function displayLibrary(array $library, int $indent = 0): void {
    foreach ($library as $key => $value) {
        // If numeric key, treat as book title
        if (is_int($key)) {
            echo str_repeat(' ', $indent) . $value . PHP_EOL;
            continue;
        }

        // Print category or subcategory name
        echo str_repeat(' ', $indent) . $key . PHP_EOL;

        // If value is an array, recursively display it
        if (is_array($value)) {
            $isList = array_values($value) === $value; // check if numeric array
            if ($isList) {
                foreach ($value as $book) {
                    echo str_repeat(' ', $indent + 2) . $book . PHP_EOL;
                }
            } else {
                displayLibrary($value, $indent + 2);
            }
        }
    }
}

// Only run when executed from CLI
if (php_sapi_name() === 'cli' && realpath($argv[0]) === __FILE__) {
    echo "Library Structure:\n";
    displayLibrary($library);
}
?>
