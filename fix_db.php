<?php
/**
 * Diagnostic et réparation de la base EvalEns (MariaDB erreur 1932)
 */
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    echo "=== BASES DE DONNÉES ===\n";
    foreach ($pdo->query('SHOW DATABASES')->fetchAll(PDO::FETCH_COLUMN) as $db) {
        echo "  $db\n";
    }

    // Vérifier les tables dans toutes les bases proches du projet
    foreach (['EvalEns', 'evalens', 'evalens_db'] as $dbName) {
        try {
            $pdo2 = new PDO("mysql:host=127.0.0.1;port=3306;dbname=$dbName", 'root', '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
            $tables = $pdo2->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
            echo "\n=== TABLES dans '$dbName' (" . count($tables) . ") ===\n";
            foreach ($tables as $t) {
                echo "  - $t\n";
            }
            if (empty($tables)) {
                echo "  (vide)\n";
            }
        } catch (Exception $e) {
            echo "\n  '$dbName' inaccessible : " . $e->getMessage() . "\n";
        }
    }

    // Vérifier information_schema pour les tables orphelines
    echo "\n=== information_schema.tables pour 'evalens' ===\n";
    $stmt = $pdo->prepare("SELECT table_name, engine FROM information_schema.tables WHERE table_schema = ?");
    $stmt->execute(['evalens']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($rows)) {
        echo "  (aucune entrée)\n";
    } else {
        foreach ($rows as $r) {
            echo "  {$r['table_name']} [{$r['engine']}]\n";
        }
    }

    // Même chose avec casse exacte
    $stmt->execute(['EvalEns']);
    $rows2 = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\n=== information_schema.tables pour 'EvalEns' ===\n";
    if (empty($rows2)) {
        echo "  (aucune entrée)\n";
    } else {
        foreach ($rows2 as $r) {
            echo "  {$r['table_name']} [{$r['engine']}]\n";
        }
    }

} catch (Exception $e) {
    echo "ERREUR CONNEXION: " . $e->getMessage() . "\n";
}
