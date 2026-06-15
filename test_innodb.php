<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=evalens', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Vérifier le moteur InnoDB
    $row = $pdo->query("SHOW VARIABLES LIKE 'innodb_file_per_table'")->fetch(PDO::FETCH_ASSOC);
    echo "innodb_file_per_table : " . ($row['Value'] ?? '?') . "\n";

    $row2 = $pdo->query("SHOW VARIABLES LIKE 'datadir'")->fetch(PDO::FETCH_ASSOC);
    echo "datadir               : " . ($row2['Value'] ?? '?') . "\n";

    $row3 = $pdo->query("SHOW VARIABLES LIKE 'innodb_force_recovery'")->fetch(PDO::FETCH_ASSOC);
    echo "innodb_force_recovery : " . ($row3['Value'] ?? '?') . "\n";

    // Tenter de créer et lire une table de test
    echo "\n--- Test CREATE/INSERT/SELECT avec InnoDB ---\n";
    $pdo->exec("DROP TABLE IF EXISTS test_innodb_check");
    $pdo->exec("CREATE TABLE test_innodb_check (id INT PRIMARY KEY) ENGINE=InnoDB");
    echo "CREATE : OK\n";
    $pdo->exec("INSERT INTO test_innodb_check VALUES (1)");
    echo "INSERT : OK\n";
    $res = $pdo->query("SELECT * FROM test_innodb_check")->fetchAll();
    echo "SELECT : OK (" . count($res) . " ligne(s))\n";
    $pdo->exec("DROP TABLE test_innodb_check");
    echo "DROP   : OK\n";

    // Vérifier la table migrations
    echo "\n--- Table 'migrations' ---\n";
    $tables = $pdo->query("SHOW TABLES LIKE 'migrations'")->fetchAll(PDO::FETCH_COLUMN);
    echo "SHOW TABLES LIKE 'migrations' : " . (empty($tables) ? "absente" : "présente") . "\n";

    if (!empty($tables)) {
        try {
            $rows = $pdo->query("SELECT * FROM migrations")->fetchAll();
            echo "SELECT * FROM migrations : OK (" . count($rows) . " ligne(s))\n";
        } catch (Exception $e2) {
            echo "SELECT * FROM migrations ERREUR : " . $e2->getMessage() . "\n";
        }
    }

} catch (Exception $e) {
    echo "ERREUR : " . $e->getMessage() . "\n";
}
