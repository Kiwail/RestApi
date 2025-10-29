<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Подключаемся к серверу MySQL (без конкретной базы)
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $migrationDir = __DIR__ . '/migrations';

    // Получаем все .sql файлы
    $files = glob($migrationDir . '/*.sql');

    if (empty($files)) {
        echo "Нет SQL-файлов в папке $migrationDir\n";
        exit;
    }

    // Сортируем файлы (чтобы выполнялись в порядке 001, 002, 003)
    sort($files);

    foreach ($files as $file) {
        echo "Выполняется: " . basename($file) . " ... ";

        // Читаем SQL
        $sql = file_get_contents($file);

        // Убираем лишние пробелы и выполняем
        $queries = array_filter(array_map('trim', explode(';', $sql)));

        foreach ($queries as $query) {
            if ($query !== '') {
                $pdo->exec($query);
            }
        }

        echo "✅ Выполнено.\n";
    }

    echo "\nВсе базы данных успешно созданы!\n";

} catch (PDOException $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
}
?>
