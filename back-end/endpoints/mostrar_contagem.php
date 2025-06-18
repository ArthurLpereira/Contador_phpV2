<?php
include(__DIR__ . '/../config/database.php');

header('Content-Type: application/json');

// Query para pegar as contagens por turma no dia atual
$sql = "SELECT 
            turmas_id_turma, 
            SUM(quantidade_turma) AS total_quantidade
        FROM 
            contagens_turmas
        WHERE 
            DATE(data_contagem_turma) = CURDATE()
        GROUP BY 
            turmas_id_turma";

$result = $mysqli->query($sql);

$dados = [];

if ($result && $result->num_rows > 0) {
    while ($linha = $result->fetch_assoc()) {
        $dados[] = [
            'id_turma' => $linha['turmas_id_turma'],
            'total_quantidade' => (int)$linha['total_quantidade']
        ];
    }
}

echo json_encode($dados);
