<?php
$host    = 'localhost';
$db      = 'teste';
$user    = 'root';
$pass    = 'Mateus141@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 2) Recebendo os dados (ex: vindos de um formulário POST)
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException("Nome ou e-mail inválidos.");
    }

    // 3) Inserindo com Prepared Statement
    $sqlInsert = "INSERT INTO alunos (name, email) VALUES (:name, :email)";
    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':name'  => $nome,
        ':email' => $email
    ]);

    $ultimoId = $pdo->lastInsertId();

    // Buscando o último registro inserido
    $sqlSelect = "SELECT id, name, email, country FROM alunos WHERE id = :id";
    $stmtSelect = $pdo->prepare($sqlSelect);
    $stmtSelect->execute([':id' => $ultimoId]);

    $ultimoAluno = $stmtSelect->fetch();

    echo "<h3>Aluno cadastrado com sucesso!</h3>";
    echo "<pre>";
    print_r(array_map('htmlspecialchars', $ultimoAluno));
    echo "</pre>";

} catch (PDOException $e) {
    echo "Erro de conexão ou SQL: " . htmlspecialchars($e->getMessage());
} catch (InvalidArgumentException $e) {
    echo "Erro de validação: " . htmlspecialchars($e->getMessage());
}
?>