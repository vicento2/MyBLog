<?php
// Connect to the database
$host = 'localhost';  // Update with your DB host
$username = 'root';   // Update with your DB username
$password = '';       // Update with your DB password
$dbname = 'blog';     // Update with your DB name

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch posts from the database
$sql = "SELECT * FROM posts ORDER BY created_at DESC";
$result = $conn->query($sql);
$posts = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
}

// Handle form submission for adding new posts
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $category = $_POST['category'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $date = date("Y-m-d H:i:s");

    $insert_sql = "INSERT INTO posts (category, title, description, created_at) VALUES ('$category', '$title', '$description', '$date')";

    if ($conn->query($insert_sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $insert_sql . "<br>" . $conn->error;
    }
}

// Handle post deletion
if (isset($_GET['delete_id'])) {
    $post_id = $_GET['delete_id'];
    $delete_sql = "DELETE FROM posts WHERE id = '$post_id'";

    if ($conn->query($delete_sql) === TRUE) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error: " . $delete_sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Management</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header>
        <div class="logo">
            <h1>My Blog</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="post-container">
            <!-- Display posts from the database -->
            <?php foreach ($posts as $post): ?>
                <div class="post-box" data-id="<?= htmlspecialchars($post['id']); ?>">
                    <h1 class="post-title"><?= htmlspecialchars($post['title']); ?></h1>
                    <h2 class="category"><?= htmlspecialchars($post['category']); ?></h2>
                    <span class="post-date"><?= htmlspecialchars($post['created_at']); ?></span>
                    <p class="post-description"><?= htmlspecialchars(substr($post['description'], 0, 100)); ?>...</p>
                    <a href="?delete_id=<?= $post['id']; ?>" class="delete-post">Delete</a>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="createPostBtn">Create Post</button>

        <!-- Modal for Creating a New Post -->
        <div id="createPostModal" class="modal">
            <div class="modal-content">
                <span id="closeModal" class="close">&times;</span>
                <h2>Create New Post</h2>
                <form action="<?= $_SERVER['PHP_SELF']; ?>" method="POST" id="postForm">
                    <label for="category">Category</label>
                    <input type="text" name="category" id="category" required><br>
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" required><br>
                    <label for="description">Description</label>
                    <textarea name="description" id="description" required></textarea><br>
                    <button type="submit" name="submit_post" id="postSubmitBtn">Submit</button>
                </form>
            </div>
        </div>
    </main>

    <!-- Include your JavaScript file -->
    <script src="ber.js"></script>
</body>
</html>
