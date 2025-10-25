<?php
$jsonPath = "books.json";
if (!file_exists($jsonPath)) {
    die("Error: books.json not found!");
}

$json = file_get_contents($jsonPath);
$books = json_decode($json, true);

if (!$books || !is_array($books)) {
    die("Error: Invalid or empty books.json file.");
}

function display_books($id, $title, $list) {
    if (empty($list) || !is_array($list)) return;

    echo "
    <div class='category' id='cat_$id' data-genre='" . strtolower($title) . "'>
        <h2>" . htmlspecialchars($title) . "</h2>
        <button class='scroll-btn left' onclick=\"scrollRow('$id', -1)\">&#10094;</button>
        <div class='book-list' id='$id'>";

    foreach ($list as $book) {
        $image = htmlspecialchars($book['imageLink'] ?? 'default.jpg');
        $bookTitle = htmlspecialchars($book['title'] ?? 'Unknown Title');
        $author = htmlspecialchars($book['author'] ?? '');
        $year = htmlspecialchars($book['year'] ?? '');
        $genre = htmlspecialchars($book['genre'] ?? $title);
        $description = htmlspecialchars($book['description'] ?? 'No description available');

        // Pass book info to modal using data attributes
        echo "
        <div class='book-card' 
             data-title='$bookTitle'
             data-author='$author'
             data-year='$year'
             data-genre='$genre'
             data-description='$description'
             onclick='showBookModal(this)'>
            <img src='images/$image' alt='$bookTitle'>
            <h3>$bookTitle</h3>
        </div>";
    }

    echo "
        </div>
        <button class='scroll-btn right' onclick=\"scrollRow('$id', 1)\">&#10095;</button>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Documents</title>
<style>
body { margin:0; font-family: Arial,sans-serif; background:#f5f8fb; color:#222; }
.container { padding:20px; }
h1 { text-align:center; margin-bottom:10px; }
.search-bar { text-align:center; margin:30px 0; }
.search-bar label { font-weight:bold; margin-right:8px; font-size:16px; }
.search-bar input { width:60%; min-width:27ch; max-width:500px; padding:10px 14px; border-radius:25px; border:2px solid #007bff; font-size:16px; outline:none; background-color:#fff; color:#222; transition:0.3s; }
.search-bar input:focus { box-shadow:0 0 8px rgba(0,123,255,0.4); }
.category { margin-bottom:60px; position:relative; }
.category h2 { margin:10px 0 15px 20px; }
.book-list { display:flex; overflow-x:auto; gap:15px; scroll-behavior:smooth; padding:10px 20px; }
.book-list::-webkit-scrollbar { display:none; }
.book-card { flex:0 0 auto; width:180px; height:310px; background:white; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.1); text-align:center; padding:10px; transition:transform 0.2s; display:flex; flex-direction:column; justify-content:space-between; cursor:pointer; }
.book-card:hover { transform:scale(1.05); }
.book-card img { width:100%; height:230px; object-fit:cover; border-radius:10px; }
.book-card h3 { font-size:15px; font-weight:600; margin:8px 0 0; line-height:1.3em; height:2.6em; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; white-space:normal; }
.scroll-btn { position:absolute; top:50%; transform:translateY(-50%); background-color:rgba(255,255,255,0.9); border:none; border-radius:50%; width:40px; height:40px; font-size:22px; box-shadow:0 2px 6px rgba(0,0,0,0.2); cursor:pointer; transition:0.2s; z-index:5; }
.scroll-btn:hover { background-color:#007bff; color:white; }
.scroll-btn.left { left:0; }
.scroll-btn.right { right:0; }
@media (max-width:700px) { .scroll-btn { display:none; } .search-bar input { width:80%; } }

/* Modal styles */
#bookModal { display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000; }
#bookModalContent { background:#fff; padding:20px; border-radius:10px; width:90%; max-width:500px; max-height:90%; overflow-y:auto; position:relative; }
#bookModalContent img { width:100%; height:auto; border-radius:8px; margin-bottom:15px; }
#bookModalContent h2 { margin:0 0 10px; }
#bookModalContent p { margin:5px 0; }
#closeModal { position:absolute; top:10px; right:10px; font-size:22px; font-weight:bold; background:none; border:none; cursor:pointer; }
</style>
</head>
<body>

<div class="container">
<h1>GILLO LIBRARIES</h1>

<div class="search-bar">
    <label for="searchInput">Search</label>
    <input type="text" id="searchInput" name="q" autocomplete="off" oninput="runSearch()">
</div>

<?php
display_books("romance","Romance Books", $books["romance_books"] ?? []);
display_books("action", "Action Books", $books["action_books"] ?? []);
display_books("fantasy","Fantasy Books", $books["best_10_fantasy_books"] ?? []);
display_books("comics", "Comics Books", $books["best_10_comics"] ?? []);
?>
</div>

<!-- Modal -->
<div id="bookModal">
    <div id="bookModalContent">
        <button id="closeModal" onclick="closeModal()">&times;</button>
        <img id="modalImage" src="" alt="">
        <h2 id="modalTitle"></h2>
        <p><strong>Author:</strong> <span id="modalAuthor"></span></p>
        <p><strong>Year:</strong> <span id="modalYear"></span></p>
        <p><strong>Genre:</strong> <span id="modalGenre"></span></p>
        <p id="modalDescription"></p>
    </div>
</div>

<script>
function phonetic(str) {
    if (!str) return "";
    str = str.toLowerCase().replace(/[^a-z]/g,"");
    if (!str) return "";
    const map = {b:1,f:1,p:1,v:1,c:2,g:2,j:2,k:2,q:2,s:2,x:2,z:2,d:3,t:3,l:4,m:5,n:5,r:6};
    let code = str[0];
    let prev = map[str[0]] || 0;
    for (let i=1;i<str.length;i++){
        const digit = map[str[i]] || 0;
        if(digit !== prev && digit !==0) code += digit;
        prev = digit;
    }
    return code.padEnd(4,"0").slice(0,4);
}

function runSearch() {
    const query = document.getElementById('searchInput').value.trim().toLowerCase();
    const queryPhonetic = phonetic(query);
    const cards = document.querySelectorAll('.book-card');
    const categories = document.querySelectorAll('.category');

    if (query === "") {
        categories.forEach(cat => cat.style.display = '');
        cards.forEach(card => card.parentElement.style.display = '');
        return;
    }

    cards.forEach(card => {
        const title = card.querySelector('h3').innerText.toLowerCase();
        const text = title;
        const soundMatch = phonetic(title).includes(queryPhonetic);
        const exactMatch = text.includes(query);
        const match = exactMatch || soundMatch;
        card.parentElement.style.display = match ? '' : 'none';
    });

    categories.forEach(cat => {
        const visibleBooks = cat.querySelectorAll('.book-card:not([style*="display: none"])');
        cat.style.display = visibleBooks.length > 0 ? '' : 'none';
    });
}

function scrollRow(id, direction) {
    const row = document.getElementById(id);
    if(!row) return;
    const scrollAmount = 300;
    row.scrollBy({ left: scrollAmount * direction, behavior: 'smooth' });
}

// Show modal with book details
function showBookModal(card) {
    document.getElementById('modalImage').src = card.querySelector('img').src;
    document.getElementById('modalTitle').innerText = card.dataset.title;
    document.getElementById('modalAuthor').innerText = card.dataset.author;
    document.getElementById('modalYear').innerText = card.dataset.year;
    document.getElementById('modalGenre').innerText = card.dataset.genre;
    document.getElementById('modalDescription').innerText = card.dataset.description;
    document.getElementById('bookModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('bookModal').style.display = 'none';
}

// Close modal when clicking outside content
window.onclick = function(e) {
    const modal = document.getElementById('bookModal');
    if(e.target === modal) closeModal();
}
</script>

</body>
</html>
