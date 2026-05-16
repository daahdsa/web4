<?php
header('Content-Type: text/html; charset=UTF-8');

function setError($field, $value = '') {

    setcookie($field . '_error', '1');

    if ($value !== '') {
        setcookie($field . '_value', $value);
    }
}


$pdo = new PDO("mysql:host=localhost;dbname=u82283;charset=utf8","u82283","7013916");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$birth = $_POST['birth_date'] ?? '';
$gender = $_POST['gender'] ?? '';
$bio = trim($_POST['bio'] ?? '');
$languages = $_POST['languages'] ?? [];
$contract = isset($_POST['contract']) ? 1 : 0;

$hasErrors = false;

if (!preg_match("/^[\p{L}\s]{2,150}$/u", $name)) {
    $hasErrors = true;
    setError('name', $name);
}

if (!preg_match("/^\+?[0-9\-\s]{7,30}$/", $phone)) {
      $hasErrors = true;
    setError('phone', $phone);
}

if (!preg_match('/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/', $email)) {
    $hasErrors = true;
        setError('email', $email);

}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $birth)) {

    setError('birth_date', $birth);
    $hasErrors = true;
}

if (!preg_match('/^(male|female|other)$/', $gender)) {

    setError('gender', $gender);
    $hasErrors = true;
}

if (!preg_match('/^[\p{L}\p{N}\s.,!?()\-]{10,2000}$/u', $bio)) {

    setError('bio', $bio);
    $hasErrors = true;
}

if (empty($languages)) {

    setcookie('languages_error', '1');
    $hasErrors = true;
}

if (!$contract) {

    setcookie('contract_error', '1');
    $hasErrors = true;
}

if ($hasErrors) {

    header('Location: index.php');
    exit();
}

$stmt = $pdo->query("SELECT id FROM programming_languages");

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

setcookie('full_name_value', $name, 1799913600);
setcookie('phone_value', $phone, 1799913600);
setcookie('email_value', $email, 1799913600);
setcookie('birth_date_value', $birth, 1799913600);
setcookie('gender_value', $gender, 1799913600);
setcookie('bio_value', $bio, 1799913600);
setcookie('languages_value', json_encode($languages), 1799913600);
setcookie('contract_value', '1', 1799913600);

setcookie('save', '1');

header('Location: index.php');
exit();
?>