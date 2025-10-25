<?php
class Node {
    public string $data;
    public ?Node $left = null;
    public ?Node $right = null;

    public function __construct(string $data) {
        $this->data = $data;
    }
}

class BinarySearchTree {
    private ?Node $root = null;

    public function insert(string $data): void {
        $this->root = $this->insertNode($this->root, $data);
    }

    private function insertNode(?Node $node, string $data): Node {
        if ($node === null) {
            return new Node($data);
        }

        if (strcasecmp($data, $node->data) < 0) {
            $node->left = $this->insertNode($node->left, $data);
        } else {
            $node->right = $this->insertNode($node->right, $data);
        }

        return $node;
    }

    public function inorderTraversal(?Node $node = null, array &$result = []): array {
        if ($node === null) {
            $node = $this->root;
        }
        if ($node === null) return $result; // Tree is empty

        if ($node->left) {
            $this->inorderTraversal($node->left, $result);
        }

        $result[] = $node->data;

        if ($node->right) {
            $this->inorderTraversal($node->right, $result);
        }

        return $result;
    }

    public function search(string $data): bool {
        return $this->searchNode($this->root, $data);
    }

    private function searchNode(?Node $node, string $data): bool {
        if ($node === null) return false;

        $cmp = strcasecmp($data, $node->data);

        if ($cmp === 0) return true;

        return $cmp < 0
            ? $this->searchNode($node->left, $data)
            : $this->searchNode($node->right, $data);
    }
}

// --- DEMO / TEST CODE ---
$bst = new BinarySearchTree();

$books = [
    "A Brief History of Time",
    "Becoming",
    "Gone Girl",
    "Harry Potter",
    "Sherlock Holmes",
    "The Hobbit"
];

// Insert books into BST
foreach ($books as $title) {
    $bst->insert($title);
}

// Display books in alphabetical order
echo "Inorder Traversal (Alphabetical):\n";
foreach ($bst->inorderTraversal() as $title) {
    echo $title . "\n";
}

// Search demo
$searches = ["The Hobbit", "Inferno"];
foreach ($searches as $title) {
    echo "\nSearching for \"$title\": " . ($bst->search($title) ? "Found!" : "Not Found.") . "\n";
}
?>
