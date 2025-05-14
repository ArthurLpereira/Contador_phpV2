<?php

include(__DIR__ . '/../config/database.php');
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['nome'], $dados['senha'])) {
    $nome = $mysqli->real_escape_string($dados['nome']);
    $senha = $dados['senha'];

    $sql = "SELECT * FROM usuarios WHERE nome_usuario = '$nome'";
    $query = $mysqli->query($sql);

    if ($query && $query->num_rows === 1) {
        $usuario = $query->fetch_assoc();

        if (password_verify($senha, $usuario['senha_usuario'])) {
            $data = date('Ymd');
            $ano = date('Y');
            $token = "{$data}_{$nome}_{$ano}_devtheblaze";

            echo json_encode([
                'usuario' => $usuario,
            ]);
        } else {
            echo json_encode(['erro' => 'Senha incorreta']);
        }
    } else {
        echo json_encode(['erro' => 'Usuário não encontrado']);
    }
} else {
    echo json_encode(['erro' => 'Dados incompletos']);
}
