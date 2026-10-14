<?php
require_once "config/db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid application ID.");
}

$stmt = $pdo->prepare("SELECT * FROM job_applications WHERE id = ?");
$stmt->execute([$id]);
$app = $stmt->fetch();

if (!$app) {
    exit("Application not found.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "UPDATE job_applications SET
            first_name = ?, last_name = ?, email = ?, phone = ?,
            education = ?, technical_skills = ?, years_experience = ?,
            portfolio_url = ?
            WHERE id = ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        trim($_POST["first_name"] ?? ""),
        trim($_POST["last_name"] ?? ""),
        trim($_POST["email"] ?? ""),
        trim($_POST["phone"] ?? ""),
        trim($_POST["education"] ?? ""),
        trim($_POST["technical_skills"] ?? ""),
        (int)($_POST["years_experience"] ?? 0),
        trim($_POST["portfolio_url"] ?? ""),
        $id
    ]);

    header("Location: index.php?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Job Application</h1>

    <form method="POST">
        <div class="form-grid">
            <label>First Name
                <input name="first_name" required
                       value="<?= e($app["first_name"]) ?>">
            </label>

            <label>Last Name
                <input name="last_name" required
                       value="<?= e($app["last_name"]) ?>">
            </label>

            <label>Email
                <input type="email" name="email" required
                       value="<?= e($app["email"]) ?>">
            </label>

            <label>Phone
                <input name="phone" required
                       value="<?= e($app["phone"]) ?>">
            </label>

            <label>Education
                <input name="education" required
                       value="<?= e($app["education"]) ?>">
            </label>

            <label>Years of Experience
                <input type="number" name="years_experience"
                       min="0" max="80" required
                       value="<?= e($app["years_experience"]) ?>">
            </label>

            <label class="full">Technical Skills
                <textarea name="technical_skills" required><?= e($app["technical_skills"]) ?></textarea>
            </label>

            <label class="full">Portfolio URL
                <input type="url" name="portfolio_url"
                       value="<?= e($app["portfolio_url"]) ?>">
            </label>
        </div>

        <button type="submit">Save Changes</button>
        <a href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>