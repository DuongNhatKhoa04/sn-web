<?php
include_once 'classes/Database.php';
$database = new Database();
$db = $database->getConnection();
?>
<DOCTYPE html>
<html>
<head>
    <title>s-news</title>
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/all.min.css">
</head>
<body class="bg-light p-4">
    <div class="container">
        <div class="alert alert-success mb-4">
            <h4><i class="fa-solid fa-check"></i> DA CAP NHAT DATABASE</h4>
            <p>Da tao folder <b>api/</b> va them bang <b>comments</b>.</p>
        </div>
        <h3 class="text-primary mb-3">Tin tuc moi nhat:</h3>
        <div class="row">
        <?php
            $stmt = $db->prepare("SELECT * FROM articles");
            $stmt->execute();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<div class="col-md-4 mb-3"><div class="card h-100 shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title text-primary">' . $row['title'] . '</h5>';
                echo '<h6 class="card-subtitle mb-2 text-muted">' . $row['category'] . ' | View: ' . $row['views'] . '</h6>';
                echo '<p class="card-text">' . $row['summary'] . '</p>';
                echo '</div></div></div>';
            }
        ?>
        </div>
    </div>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
