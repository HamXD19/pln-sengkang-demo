<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mysqlPdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=pln2', 'root', 'root123', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

$oracleConn = DB::connection('oracle');
$oraclePdo = $oracleConn->getPdo();

$tables = [
    'roles',
    'organization_units',
    'positions',
    'users',
    'organization_hierarchies',
    'meetings',
    'issues',
    'meeting_issues',
    'tasks',
    'escalations',
    'decisions',
    'issue_discussions',
    'information',
];

echo "=== STARTING MYSQL TO ORACLE DATA TRANSFER ===\n\n";

// Disable foreign key constraints on Oracle
try {
    $constraints = $oracleConn->select("SELECT table_name, constraint_name FROM user_constraints WHERE constraint_type = 'R'");
    foreach ($constraints as $c) {
        $oracleConn->statement('ALTER TABLE "' . $c->table_name . '" DISABLE CONSTRAINT "' . $c->constraint_name . '"');
    }
    echo "Foreign key constraints disabled temporarily.\n";
} catch (\Throwable $e) {
    echo "Notice on disabling FKs: " . $e->getMessage() . "\n";
}

foreach ($tables as $table) {
    echo "Processing $table...\n";
    $oracleConn->table($table)->truncate();

    $rows = $mysqlPdo->query("SELECT * FROM `$table`")->fetchAll();
    if (empty($rows)) {
        echo "  -> 0 rows (table empty in MySQL)\n";
        continue;
    }

    $columns = array_keys($rows[0]);
    $colList = implode(', ', array_map(fn($c) => '"' . strtoupper($c) . '"', $columns));
    $paramList = implode(', ', array_map(fn($c) => ":$c", $columns));

    $sql = 'INSERT INTO "' . strtoupper($table) . "\" ($colList) VALUES ($paramList)";
    $stmt = $oraclePdo->prepare($sql);

    foreach ($rows as $row) {
        // Handle nulls and date strings
        foreach ($row as $k => $v) {
            if ($v === '') {
                $row[$k] = null;
            }
        }
        $stmt->execute($row);
    }

    echo "  -> Successfully transferred " . count($rows) . " rows to $table\n";

    // Update Oracle sequence if table has 'id' column
    if (in_array('id', $columns)) {
        try {
            $maxId = (int) $oracleConn->table($table)->max('id');
            $seqName = strtoupper($table) . '_ID_SEQ';
            $seqExists = $oracleConn->select("SELECT sequence_name FROM user_sequences WHERE sequence_name = ?", [$seqName]);
            if (!empty($seqExists)) {
                $oracleConn->statement("DROP SEQUENCE \"$seqName\"");
                $startVal = $maxId + 1;
                $oracleConn->statement("CREATE SEQUENCE \"$seqName\" START WITH $startVal INCREMENT BY 1 NOCACHE");
                echo "     (Sequence $seqName updated to start with $startVal)\n";
            }
        } catch (\Throwable $e) {
            // Sequence might be an identity column sequence
        }
    }
}

// Re-enable foreign key constraints on Oracle
try {
    foreach ($constraints as $c) {
        $oracleConn->statement('ALTER TABLE "' . $c->table_name . '" ENABLE CONSTRAINT "' . $c->constraint_name . '"');
    }
    echo "\nForeign key constraints re-enabled successfully.\n";
} catch (\Throwable $e) {
    echo "Notice on re-enabling FKs: " . $e->getMessage() . "\n";
}

echo "\n=== VERIFYING TRANSFERRED DATA IN ORACLE ===\n";
$userCount = $oracleConn->table('users')->count();
$unitCount = $oracleConn->table('organization_units')->count();
$posCount = $oracleConn->table('positions')->count();
$roleCount = $oracleConn->table('roles')->count();
$meetCount = $oracleConn->table('meetings')->count();
$taskCount = $oracleConn->table('tasks')->count();

echo "Total Users in Oracle: $userCount\n";
echo "Total Organization Units: $unitCount\n";
echo "Total Positions: $posCount\n";
echo "Total Roles: $roleCount\n";
echo "Total Meetings: $meetCount\n";
echo "Total Tasks: $taskCount\n";
echo "=== DATA TRANSFER COMPLETE ===\n";
