<?php
/**
 * "The Way Things Were" - old fashioned "Guestbook" PHP example.
 *
 * @author Mike Jones
 * @since  2015-09-09
 */
$link = mysqli_connect(
    "127.0.0.1", // host
    "root",      // username
    "root",      // password
    "guestbook"       // schema name
);

$errors = [];

if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
} // if

/**
 * Retrieve all guestbook posts.
 *
 * @global mysql $link
 * @return array
 */
function entries() {
    global $link;
    $posts = [];

    $query = "SELECT `username`, `content` FROM `guestbook`";
    $result = mysqli_query($link, $query);

    while ($qd = mysqli_fetch_assoc($result)) {
        $posts[] = $qd;
    } // while

    return $posts;
} // entries()

/**
 * Insert a new post into the guestbook's database.
 *
 * @param array $p
 * @global mysql $link
 * @return void
 */
function create_entry($p) {
    global $link;

    $query = "INSERT INTO `guestbook` "
        ."SET `username` = '{$p['username']}', "
        ."`content` = '{$p['content']}'";
    mysqli_query($link, $query);
} // create_entry()

$posts = entries();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $new_post = [
        'username' => $_POST['user_name'],
        'content' => $_POST['content'],
    ];

    foreach ($new_post as $node => $value) {
        if (!$value)
            $errors[] = $node;
    }

    if (empty($errors)) {
        create_entry($new_post);
        header('Location: guestbook.php');
    } // if
} // if
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Guestbook</title>
        <style>
            #container {
                display: block;
                float: left;
                width: 400px;
            }
            label, textarea, input[type=text] {
                display: block;
                width: 99%;
            }
            textarea {
                resize: vertical;
            }
        </style>
    </head>
    <body>
        <div id="container">
<?php
if (!empty($errors)) {
?>
        <p>Your entry has some errors:</p>
        <ul>
<?php
    foreach ($errors as $error) {
?>
            <li><?=$error?></li>
<?php
    } // foreach
?>
        </ul>
<?php
}
?>
            <form method="post">
                <label for="user_name">Name</label>
                <input type="text" name="user_name" id="user_name">

                <label for="content">Message</label>
                <textarea name="content" id="content"></textarea>

                <input type="submit" value="Post" name="post" id="post">
            </form>
<?php
if (!empty($posts)) {
?>
            <ul id="posts">
<?php
    foreach ($posts as $node => $p) {
?>
                <li><?=$p['username']?>: "<?=$p['content']?>"</li>
<?php
    } // foreach $posts
?>
            </ul><!--/ #posts -->
<?php
} // if not empty $posts
?>
        </div><!--/ #content -->
    </body>
</html>
