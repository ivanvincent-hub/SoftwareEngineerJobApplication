<?php
require_once "config/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sql = "INSERT INTO job_applications
            (first_name, last_name, email, phone, education,
             technical_skills, years_experience, portfolio_url)
            VALUES
            (:first_name, :last_name, :email, :phone, :education,
             :technical_skills, :years_experience, :portfolio_url)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":first_name" => trim($_POST["first_name"] ?? ""),
        ":last_name" => trim($_POST["last_name"] ?? ""),
        ":email" => trim($_POST["email"] ?? ""),
        ":phone" => trim($_POST["phone"] ?? ""),
        ":education" => trim($_POST["education"] ?? ""),
        ":technical_skills" => trim($_POST["technical_skills"] ?? ""),
        ":years_experience" => (int)($_POST["years_experience"] ?? 0),
        ":portfolio_url" => trim($_POST["portfolio_url"] ?? "")
    ]);

    header("Location: index.php?added=1");
    exit;
}

$stmt = $pdo->query(
    "SELECT * FROM job_applications ORDER BY id DESC"
);
$applications = $stmt->fetchAll();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Engineer Applications</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Software Engineer Job Application</h1>
    <p>Submit your application using the form below.</p>

    <?php if (isset($_GET["added"])): ?>
        <p class="success">Application added successfully!</p>
    <?php endif; ?>

    <form method="POST" action="index.php">
        <div class="form-grid">
            <label>First Name
                <input type="text" name="first_name" required>
            </label>

            <label>Last Name
                <input type="text" name="last_name" required>
            </label>

            <label>Email
                <input type="email" name="email" required>
            </label>

            <label>Phone
                <input type="text" name="phone" required>
            </label>

            <label>Education
                <input type="text" name="education" required
                       placeholder="e.g. BS Information Technology">
            </label>

            <label>Years of Experience
                <input type="number" name="years_experience"
                       min="0" max="80" value="0" required>
            </label>

            <label class="full">Technical Skills
                <textarea name="technical_skills" required
                          placeholder="e.g. PHP, Java, HTML, MySQL"></textarea>
            </label>

            <label class="full">Portfolio URL (optional)
                <input type="url" name="portfolio_url"
                       placeholder="https://example.com">
            </label>
        </div>

        <button type="submit">Submit Application</button>
    </form>

    <h2>Submitted Applications</h2>

    <div class="table-wrap">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Education</th>
            <th>Technical Skills</th>
            <th>Experience</th>
            <th>Portfolio</th>
            <th>Date Added</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!$applications): ?>
            <tr><td colspan="11">No applications yet.</td></tr>
        <?php endif; ?>

        <?php foreach ($applications as $app): ?>
            <tr>
                <td><?= e($app["id"]) ?></td>
                <td><?= e($app["first_name"]) ?></td>
                <td><?= e($app["last_name"]) ?></td>
                <td><?= e($app["email"]) ?></td>
                <td><?= e($app["phone"]) ?></td>
                <td><?= e($app["education"]) ?></td>
                <td><?= e($app["technical_skills"]) ?></td>
                <td><?= e($app["years_experience"]) ?> years</td>
                <td>
                    <?php if ($app["portfolio_url"]): ?>
                        <a href="<?= e($app["portfolio_url"]) ?>"
                           target="_blank" rel="noopener">View</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= e($app["date_added"]) ?></td>
                <td class="actions">
                    <a class="edit-btn"
                       href="edit.php?id=<?= (int)$app["id"] ?>">Edit</a>

                    <form method="POST" action="delete.php"
                          onsubmit="return confirm('Delete this application?');">
                        <input type="hidden" name="id"
                               value="<?= (int)$app["id"] ?>">
                        <button class="delete-btn" type="submit">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
</body>
</html>