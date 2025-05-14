<?php

include(__DIR__ . '/../config/database.php');
header('Content-Type: application/json');

$dados = json_decode(file_get_contents('php://input'), true);

if (isset($dados['nome'], $dados['senha'], $dados['confirmacao'])) {
    $nome = $mysqli->real_escape_string($dados['nome']);
    $senha = $dados['senha'];
    $confirmacao = $dados['confirmacao'];

    if ($senha === $confirmacao) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nome_usuario, senha_usuario, nivel_usuario, ativo_usuario) 
                VALUES ('$nome', '$senhaHash', '1', '1')";
        $query = $mysqli->query($sql);

        if ($query && $mysqli->affected_rows > 0) {
            echo json_encode(['mensagem' => 'Usuário cadastrado com sucesso']);
        } else {
            echo json_encode(['erro' => 'Erro ao cadastrar o usuário']);
        }
    } else {
        echo json_encode(['erro' => 'As senhas não coincidem']);
    }
} else {
    echo json_encode(['erro' => 'Dados incompletos']);
}
