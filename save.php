<?php
$pdo = new PDO("mysql:host=localhost;dbname=u82283;charset=utf8","u82283","7013916");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function error($msg) {
    die("<h3 style='color:red'>$msg</h3>");
}

$name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$birth = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$languages = $_POST['languages'] ?? [];
$contract = isset($_POST['contract']) ? 1 : 0;

if (!preg_match("/^[\p{L}\s]{2,150}$/u", $name)) {
    error("Некорректное ФИО");
}

if (!preg_match("/^\+?[0-9\-\s]{7,30}$/", $phone)) {
    error("Некорректный телефон");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error("Некорректный email");
}

if (!$birth) {
    error("Не указана дата рождения");
}

$allowed_gender = ['male','female','other'];
if (!in_array($gender, $allowed_gender)) {
    error("Некорректный пол");
}

if (strlen($bio) < 10) {
    error("Биография слишком короткая");
}

if (!$contract) {
    error("Не подтвержден контракт");
}

if (!is_array($languages) || count($languages) < 1) {
    error("Выберите хотя бы один язык");
}

$stmt = $pdo->query("SELECT id FROM programming_languages");
$valid_ids = array_column($stmt->fetchAll(), 'id');

foreach ($languages as $l) {
    if (!in_array($l, $valid_ids)) {
        error("Недопустимый язык программирования");
    }
}

$stmt = $pdo->prepare("
INSERT INTO applications
(full_name, phone, email, birth_date, gender, bio, contract_accepted)
VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->execute([$name, $phone, $email, $birth, $gender, $bio, $contract]);

$appId = $pdo->lastInsertId();

$stmt = $pdo->prepare("
INSERT INTO application_languages (application_id, language_id)
VALUES (?, ?)
");

foreach ($languages as $langId) {
    $stmt->execute([$appId, $langId]);
}

echo "<h2 style='color:green'>Данные успешно сохранены</h2>";
?>