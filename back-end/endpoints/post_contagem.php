<?php
session_start();
include(__DIR__ . '/../config/database.php');
header('Content-Type: application/json');

// Pegando os dados vindos do Postman ou Front-end
$dados = json_decode(file_get_contents('php://input'), true);

// Verifica se veio a quantidade da contagem
if (isset($dados['quant_contagem']) && !empty($dados['quant_contagem'])) {
    $quant_contagem = $dados['quant_contagem'];

    // Verifica se o usuário está logado
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];

        // Prepara o insert no banco
        $stmt = $mysqli->prepare("INSERT INTO contagem (quant_contagem, criacao_contagem, update_contagem, usuarios_id_usuario) VALUES (?, NOW(), NOW(), ?)");

        if ($stmt) {
            $stmt->bind_param("ii", $quant_contagem, $id_usuario);
            $executado = $stmt->execute();

            if ($executado && $stmt->affected_rows > 0) {
                echo json_encode([
                    'mensagem' => 'Contagem registrada com sucesso',
                    'id_usuario' => $id_usuario
                ]);
            } else {
                echo json_encode(['erro' => 'Erro ao registrar a contagem']);
            }

            $stmt->close();
        } else {
            echo json_encode(['erro' => 'Erro ao preparar a query']);
        }
    } else {
        echo json_encode(['erro' => 'Usuário não está logado']);
    }
} else {
    echo json_encode(['erro' => 'Campo quant_contagem é obrigatório']);
}
