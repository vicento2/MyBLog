<?php
// Database connection
$conn = new mysqli("localhost", "username", "password", "blog_db");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission to create a new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'createPost') {
    $category = $conn->real_escape_string($_POST['postCategory']);
    $title = $conn->real_escape_string($_POST['postTitle']);
    $description = $conn->real_escape_string($_POST['postDescription']);

    $sql = "INSERT INTO posts (category, title, description) VALUES ('$category', '$title', '$description')";
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    exit;
}

// Fetch all posts
$posts = [];
$result = $conn->query("SELECT * FROM posts ORDER BY date_posted DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
    <title>PHP Blog</title>
    <link rel="stylesheet" href="css.css">
    <script>
        async function createPost(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('postForm'));
            formData.append('action', 'createPost');

            const response = await fetch('', {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();
            if (result.success) {
                alert('Post created successfully!');
                location.reload();
            } else {
                alert('Error creating post: ' + result.error);
            }
        }
    </script>
</head>

<body>
    <header>
        <h1 class="logo"><a href="#">Your Blog</a></h1>
        <nav>
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="#" id="createPostBtn" onclick="document.getElementById('createPostModal').style.display='flex'">Create Post</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="post-container">
            <?php foreach ($posts as $post): ?>
                <div class="post-box">
                    <h1 class="post-title"><?= htmlspecialchars($post['title']) ?></h1>
                    <h2 class="category"><?= htmlspecialchars($post['category']) ?></h2>
                    <span class="post-date"><?= htmlspecialchars(date('d M Y', strtotime($post['date_posted']))) ?></span>
                    <p class="post-description"><?= htmlspecialchars(substr($post['description'], 0, 100)) ?>...</p>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <div id="createPostModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('createPostModal').style.display='none'">&times;</span>
            <h2>Create New Post</h2>
            <form id="postForm" onsubmit="createPost(event)">
                <label for="postCategory">Category</label>
                <input type="text" id="postCategory" name="postCategory" required><br>
                <label for="postTitle">Title</label>
                <input type="text" id="postTitle" name="postTitle" required><br>
                <label for="postDescription">Description</label>
                <textarea id="postDescription" name="postDescription" required></textarea><br>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <footer>
        <p>© 2025 Your Blog. All Rights Reserved.</p>
    </footer>
</body>

</html>
