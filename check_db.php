<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=EvalEns', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "=== TABLES IN EvalEns ===\n";
    $stmt = $pdo->query('SHOW TABLES');
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    if (empty($tables)) {
        echo "(aucune table)\n";
    } else {
        foreach ($tables as $t) {
            echo "  - $t\n";
        }
    }
    echo "\n=== ENGINE STATUS ===\n";
    foreach ($tables as $t) {
        $info = $pdo->query("SHOW TABLE STATUS LIKE '$t'")->fetch(PDO::FETCH_ASSOC);
        $engine = $info['Engine'] ?? '?';
        $rows   = $info['Rows'] ?? '?';
        echo "  $t  [engine=$engine, rows≈$rows]\n";
    }
} catch (Exception $e) {
    echo "ERREUR: " . $e->getMessage() . "\n";
}
