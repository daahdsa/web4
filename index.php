<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8">
<title>Заявка</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
<h1>Форма заявки</h1>

<form method="POST" action="save.php">

<label>ФИО</label>
<input type="text" name="full_name" required>

<label>Телефон</label>
<input type="tel" name="phone" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Дата рождения</label>
<input type="date" name="birth_date" required>

<label>Пол</label>
<div class="row">
  <label><input type="radio" name="gender" value="male" required> Мужской</label>
  <label><input type="radio" name="gender" value="female"> Женский</label>
  <label><input type="radio" name="gender" value="other"> Другое</label>
</div>

<label>Любимые языки программирования</label>
<select name="languages[]" multiple size="6" required>
  <?php
try {
     $pdo = new PDO(
        "mysql:host=localhost;dbname=u82283;charset=utf8",
        "u82283",
        "7013916"
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT * FROM programming_languages");

    foreach ($stmt as $row) {
        echo "<option value='{$row['id']}'>{$row['name']}</option>";
    }

} catch (Exception $e) {
    echo "Ошибка БД: " . $e->getMessage();
}
  ?>
</select>

<label>Биография</label>
<textarea name="bio" required></textarea>

<label>
<input type="checkbox" name="contract" value="1" required>
С контрактом ознакомлен
</label>

<button type="submit">Сохранить</button>

</form>
</div>

</body>
</html>