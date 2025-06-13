<?php
// Inclui o arquivo de configuração do banco de dados
// O caminho '__DIR__ . /../../back-end/config/database.php' assume que
// estoque.php está em 'front-end/pages/' e database.php em 'back-end/config/'
include(__DIR__ . '/../../back-end/config/database.php');

// Inicializa a variável $dados como um array vazio para evitar erros caso não haja resultados
$dados = [];
$erro_php = null; // Variável para armazenar mensagens de erro PHP

// Conexão com o banco
if ($mysqli->connect_error) {
    $erro_php = 'Erro na conexão com o banco de dados: ' . $mysqli->connect_error;
} else {
    // --- Lógica para buscar itens (GET) ---
    // Verifica se foi passado o parâmetro GET "alimento" do formulário
    // Usamos o operador null coalescing (?? '') para PHP 7+ para garantir que é uma string
    $nome_item_busca = isset($_GET['alimento']) ? trim($_GET['alimento']) : '';

    // Prepara a query base
    // Usando id_estoque no SELECT para corresponder à sua coluna real
    $sql = "SELECT id_estoque, nome_item_estoque, tipo_movimentacao_estoque, quantidade_estoque, unidade_estoque FROM estoque";
    $params = [];
    $types = '';

    if ($nome_item_busca !== '') { // Se houver um termo de busca
        $sql .= " WHERE nome_item_estoque LIKE ?";
        $params[] = '%' . $nome_item_busca . '%';
        $types .= 's'; // 's' para string
    }
    $sql .= " ORDER BY nome_item_estoque ASC"; // Adiciona ordenação para melhor visualização

    $stmt = $mysqli->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            // Usa call_user_func_array para bind_param com array dinâmico (necessário para PHP < 8.1 com bind_param dinâmico)
            // Função refValues é necessária se o seu PHP for anterior ao 8.1 e você estiver usando array de parâmetros.
            call_user_func_array([$stmt, 'bind_param'], array_merge([$types], refValues($params)));
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        while ($linha = $resultado->fetch_assoc()) {
            $dados[] = $linha;
        }
        $stmt->close();
    } else {
        $erro_php = 'Erro ao preparar a query de busca: ' . $mysqli->error;
    }
}

// Função auxiliar para bind_param (necessária para passar referências em PHP < 8.1)
function refValues($arr)
{
    if (strnatcmp(phpversion(), '5.3') >= 0) // PHP 5.3+
    {
        $refs = array();
        foreach ($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

// Fecha a conexão com o banco de dados (se ainda estiver aberta)
if ($mysqli && !$mysqli->connect_error) { // Garante que $mysqli existe e está conectado antes de tentar fechar
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css?v=1.5">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Estoque</title>
    <style>
        /* (Seus estilos CSS existentes aqui. Mantenha-os como estão.) */
        /* style.css - ADIÇÕES E AJUSTES ESPECÍFICOS PARA A TELA DE ESTOQUE COM IDS */

        /* Variáveis de Cores (Verifique se já existem no seu :root e remova duplicatas) */
        :root {
            --cor-principal-gradiente: linear-gradient(to right, #C31818, #F37430);
            /* Seu gradiente principal */
            --cor-botoes-hover: linear-gradient(to right, #A31515, #D05A20);
            /* Gradiente levemente mais escuro para hover */
            --cor-fundo-claro: #FFEEDA;
            /* Cor de fundo do seu body */
            --cor-fundo-secao: white;
            /* Cor de fundo para seções como o estoque */
            --cor-texto-claro: white;
            --cor-texto-escuro: #333;
            /* Cor para títulos e textos gerais */
            --borda-padrao: #ccc;
            /* Cor de borda padrão dos seus inputs */
            --sombra-leve: 0 4px 8px rgba(0, 0, 0, 0.1);
            --sombra-media: 6px 6px 15px rgba(0, 0, 0, 0.3);
            /* Sua sombra padrão */
            --cor-tabela-linha-par: #f8f8f8;
            /* Um cinza bem clarinho para linhas alternadas */
            --cor-tabela-hover: #D0E8F2;
            /* Sua cor de hover de tabela */
        }

        /* Estilos para a seção de estoque principal */
        #estoque-section {
            background-color: var(--cor-fundo-secao);
            padding: 40px;
            border-radius: 50px;
            box-shadow: var(--sombra-media);
            width: 70vw;
            max-width: 960px;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            /* O main#main-content já centraliza */
            margin-top: 50px;
            /* Adiciona uma margem superior para afastar do header */
            margin-bottom: 50px;
            /* Adiciona uma margem inferior para afastar do footer */
        }

        #estoque-section table {
            margin-left: 100px;

        }

        /* Título principal da seção de estoque */
        #estoque-section>h1 {
            font-size: 40px;
            margin-top: 20px;
            text-transform: uppercase;
            color: var(--cor-texto-escuro);
            width: 100%;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Contêiner para os elementos de topo (Busca e Cadastro) */
        #estoque-top-controls {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            top: 70px;
        }

        /* Formulário de Busca (lado esquerdo) */
        #estoque-search-form {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-grow: 1;
            justify-content: flex-start;
            max-width: 60%;
        }

        /* Input Container do formulário de busca */
        #estoque-input-group {
            width: 100%;
            height: 45px;
            padding: 0 10px;
            display: flex;
            align-items: center;
            position: relative;
            border: 1px solid var(--borda-padrao);
            border-radius: 5px;
        }

        #estoque-input-group i {
            position: absolute;
            left: 10px;
            color: #aaa;
            font-size: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Input de busca */
        #estoque-alimento-input {
            width: calc(100% - 35px);
            height: 100%;
            padding: 0 10px 0 35px;
            border: none;
            border-radius: 5px;
            font-size: 20px;
            box-shadow: none;
            outline: none;
            background-color: transparent;
        }

        #estoque-alimento-input:focus {
            border-color: #66afe9;
        }

        /* Botão de Busca e Cadastro */
        #estoque-search-button,
        #estoque-add-button {
            width: auto;
            height: 45px;
            background: var(--cor-principal-gradiente);
            color: var(--cor-texto-claro);
            border: none;
            font-size: 25px;
            border-radius: 5px;
            cursor: pointer;
            transition-duration: 0.5s;
            box-shadow: none;
            padding: 0 20px;
            margin: 0;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        #estoque-add-button {
            position: relative;
            bottom: 30px;
        }

        #estoque-search-button:hover,
        #estoque-add-button:hover {
            transform: scale(1.03);
        }

        /* Mensagens de erro/informação */
        #estoque-error-message,
        #estoque-no-results-message,
        #estoque-initial-message {
            text-align: center;
            margin-top: 20px;
            font-weight: bold;
            width: 100%;
            position: relative;
            bottom: 30px;
            padding: 10px;
            border-radius: 5px;
        }

        #estoque-error-message {
            color: red;
            background-color: #fdd;
            border: 1px solid #f99;
        }

        #estoque-no-results-message,
        #estoque-initial-message {
            color: gray;
            background-color: #eee;
            border: 1px solid #ddd;
        }

        /* Tabela de Resultados */
        #estoque-results-container {
            margin-top: 20px;
            width: 100%;
            text-align: center;
            position: relative;
            bottom: 30px;
        }

        #estoque-results-container h2 {
            color: var(--cor-texto-escuro);
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: bold;
        }

        #estoque-data-table {
            width: 100%;
            margin: 0 auto;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: var(--sombra-leve);
            border-radius: 10px;
            overflow: hidden;
        }

        #estoque-data-table th,
        #estoque-data-table td {
            border: none;
            padding: 15px;
            text-align: left;
            font-size: 1.2em;
        }

        #estoque-data-table th {
            background: var(--cor-principal-gradiente);
            color: var(--cor-texto-claro);
            font-weight: bold;
            text-transform: uppercase;
            font-size: 1.1em;
            letter-spacing: 0.5px;
        }

        /* Bordas arredondadas para o cabeçalho da tabela */
        #estoque-data-table thead tr:first-child th:first-child {
            border-top-left-radius: 10px;
        }

        #estoque-data-table thead tr:first-child th:last-child {
            border-top-right-radius: 10px;
        }

        #estoque-data-table tbody tr:nth-child(even) {
            background-color: var(--cor-fundo-claro);
        }

        #estoque-data-table tbody tr:nth-child(odd) {
            background-color: var(--cor-fundo-secao);
        }

        #estoque-data-table tbody tr:hover {
            background-color: var(--cor-tabela-hover);
            transition: background-color 0.2s ease;
        }

        /* Linha divisória entre as linhas do corpo da tabela */
        #estoque-data-table tbody tr:not(:last-child) {
            border-bottom: 1px solid var(--borda-padrao);
        }

        /* Estilos para os botões de Ação na Tabela */
        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            text-decoration: none;
            color: white;
            margin: 0 5px;
            border: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .action-button i {
            pointer-events: none;
        }

        .edit-button {
            background-color: #007bff;
        }

        .edit-button:hover {
            background-color: #0056b3;
            transform: scale(1.1);
        }

        .delete-button {
            background-color: #dc3545;
        }

        .delete-button:hover {
            background-color: #bd2130;
            transform: scale(1.1);
        }

        /* Estilos para o Modal de Confirmação */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: var(--cor-fundo-secao);
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--sombra-media);
            text-align: center;
            max-width: 500px;
            width: 90%;
            position: relative;
            transform: scale(0.8);
            opacity: 0;
            transition: transform 0.3s ease-out, opacity 0.3s ease-out;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1);
            opacity: 1;
        }

        .modal-content h3 {
            font-size: 2em;
            color: var(--cor-texto-escuro);
            margin-bottom: 20px;
        }

        .modal-content p {
            font-size: 1.2em;
            color: var(--cor-texto-escuro);
            margin-bottom: 30px;
        }

        /* Garante que os botões dentro do modal-buttons fiquem centralizados se a div for menor */
        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }

        .modal-button {
            /* ESTILOS PARA TODOS OS BOTÕES DE MODAIS */
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 1.2em;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            text-decoration: none;

            /* ADICIONADO PARA CENTRALIZAR O TEXTO DOS BOTÕES */
            display: flex;
            justify-content: center;
            align-items: center;
            white-space: nowrap;
            /* Evita que o texto quebre em várias linhas */
        }

        .modal-button.confirm {
            background: linear-gradient(to right, #dc3545, #bd2130);
            color: white;
        }

        .modal-button.confirm:hover {
            background: linear-gradient(to right, #bd2130, #a71d2a);
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .modal-button.cancel {
            background: var(--cor-principal-gradiente);
            color: white;
        }

        .modal-button.cancel:hover {
            background: var(--cor-botoes-hover);
            transform: scale(1.05);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Esconder o overlay se não estiver ativo para acessibilidade */
        .modal-overlay:not(.active) {
            pointer-events: none;
            visibility: hidden;
        }

        /* Estilos para grupos de formulário dentro dos modais */
        .form-group {
            margin-bottom: 20px;
            text-align: left;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: var(--cor-texto-escuro);
            font-size: 1.1em;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--borda-padrao);
            border-radius: 8px;
            font-size: 1em;
            box-sizing: border-box;
        }


        /* ESTILOS ADICIONADOS/AJUSTADOS PARA O MODAL DE CADASTRO */
        #cadastro-modal-overlay .modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-top: 40px;
            /* Adiciona um padding maior no topo para o título não ficar grudado */
            padding-bottom: 30px;
        }

        #cadastro-modal-title {
            margin-bottom: 70px;
            /* Aumenta a margem para empurrar o formulário para baixo */
            font-size: 2.2em;
            color: var(--cor-texto-escuro);
            width: 100%;
            text-align: center;
        }

        #cadastro-item-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
            /* Adiciona padding lateral ao formulário para não encostar nas bordas do modal */
            box-sizing: border-box;
        }

        #cadastro-item-form .form-group {
            margin-bottom: 10px;
            /* Espaçamento entre os campos */
            width: 100%;
        }

        /* Estilos específicos para o botão de Cadastrar por ID */
        #cadastro-submit-button {
            padding: 12px 50px;
            font-size: 1.3em;
            /* text-align: center; REMOVIDO pois flexbox fará a centralização */
        }

        /* ESTILOS ADICIONADOS/AJUSTADOS PARA O MODAL DE EDIÇÃO */
        /* Altere a margin-bottom do título do modal de edição, se desejar */
        #edicao-modal-title {
            margin-bottom: 25px;
            /* Ajustado para espaçamento similar ao de cadastro antes da alteração do padding */
        }


        /* Media Queries para Responsividade */
        @media screen and (max-width: 768px) {
            #estoque-section {
                padding: 20px;
                width: 90vw;
                border-radius: 30px;
            }

            #estoque-section>h1 {
                font-size: 30px;
                bottom: 30px;
            }

            #estoque-top-controls {
                flex-direction: column;
                align-items: center;
                gap: 15px;
                bottom: 0;
                margin-bottom: 20px;
            }

            #estoque-search-form {
                flex-direction: column;
                width: 100%;
                max-width: 100%;
                gap: 10px;
            }

            #estoque-input-group {
                width: 100%;
                height: auto;
            }

            #estoque-alimento-input {
                width: calc(100% - 35px);
                font-size: 16px;
                padding: 10px 10px 10px 35px;
            }

            #estoque-input-group i {
                left: 15px;
                font-size: 18px;
            }

            #estoque-search-button,
            #estoque-add-button {
                width: 80%;
                height: 40px;
                font-size: 18px;
                padding: 10px 15px;
            }

            #estoque-results-container {
                margin-top: 20px;
                bottom: 0;
            }

            #estoque-results-container h2 {
                font-size: 1.8em;
            }

            #estoque-data-table {
                font-size: 0.9em;
                min-width: 500px;
            }

            #estoque-data-table th,
            #estoque-data-table td {
                padding: 12px 10px;
            }

            /* Ajuste para o padding do main para evitar que a seção cole nas bordas da tela */
            #main-content {
                padding: 15px;
            }
        }

        @media screen and (max-width: 480px) {
            #estoque-section {
                padding: 15px;
                border-radius: 20px;
            }

            #estoque-section>h1 {
                font-size: 24px;
                bottom: 10px;
            }

            #estoque-top-controls {
                gap: 10px;
            }

            #estoque-alimento-input {
                font-size: 14px;
                padding: 8px 8px 8px 30px;
            }

            #estoque-input-group i {
                left: 10px;
                font-size: 16px;
            }

            #estoque-search-button,
            #estoque-add-button {
                width: 90%;
                height: 35px;
                font-size: 16px;
                padding: 8px 10px;
            }

            #estoque-results-container h2 {
                font-size: 1.5em;
                margin-bottom: 15px;
            }

            #estoque-data-table {
                font-size: 0.75em;
                min-width: 350px;
            }

            #estoque-data-table th,
            #estoque-data-table td {
                padding: 8px 6px;
            }

            #estoque-error-message,
            #estoque-no-results-message,
            #estoque-initial-message {
                font-size: 0.9em;
                bottom: 15px;
            }
        }

        /* ESTILOS ADICIONADOS/AJUSTADOS PARA O MODAL DE CADASTRO */
        #cadastro-modal-overlay .modal-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-top: 40px;
            /* Adiciona um padding maior no topo para o título não ficar grudado */
            padding-bottom: 30px;
        }

        #cadastro-modal-title {
            margin-bottom: 70px;
            /* Aumenta a margem para empurrar o formulário para baixo */
            font-size: 2.2em;
            color: var(--cor-texto-escuro);
            width: 100%;
            text-align: center;
        }

        #cadastro-item-form {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px;
            /* Adiciona padding lateral ao formulário para não encostar nas bordas do modal */
            box-sizing: border-box;
        }

        #cadastro-item-form .form-group {
            margin-bottom: 10px;
            /* Espaçamento entre os campos */
            width: 100%;
        }

        /* Estilos específicos para o botão de Cadastrar por ID */
        #cadastro-submit-button {
            padding: 12px 50px;
            font-size: 1.3em;
            /* text-align: center; REMOVIDO pois flexbox fará a centralização */
        }

        /* ESTILOS ADICIONADOS/AJUSTADOS PARA O MODAL DE EDIÇÃO */
        /* Altere a margin-bottom do título do modal de edição, se desejar */
        #edicao-modal-title {
            margin-bottom: 25px;
            /* Ajustado para espaçamento similar ao de cadastro antes da alteração do padding */
        }


        /* Media Queries para responsividade do modal de cadastro */
        @media screen and (max-width: 480px) {
            #cadastro-modal-overlay .modal-content {
                padding-top: 30px;
                padding-bottom: 20px;
            }

            #cadastro-modal-title {
                font-size: 1.8em;
                margin-bottom: 20px;
            }

            #cadastro-item-form {
                padding: 0 15px;
            }

            #cadastro-submit-button {
                padding: 10px 20px;
                font-size: 1em;
            }

            #cadastro-modal-overlay .modal-buttons {
                /* Mais específico para o modal de cadastro */
                flex-direction: column;
                gap: 10px;
                margin-top: 20px;
            }

            #cadastro-modal-overlay .modal-buttons .modal-button {
                /* Mais específico para os botões do modal de cadastro */
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header>
        <div id="fig_top" role="img" aria-label="Top decoration"></div>
    </header>

    <div class="menu">
        <button class="hamburguer" aria-expanded="false" aria-controls="main-navigation-list">
            <span class="sr-only">Abrir/Fechar Menu</span>
            <div id="barra1" class="barra"></div>
            <div id="barra2" class="barra"></div>
            <div id="barra3" class="barra"></div>
        </button>

        <nav>
            <ul id="main-navigation-list">
                <li><a href="estoque.php">Estoque</a></li>
                <li><a href="calendario.php">Calendário</a></li>
                <li><a href="relatorio.php">Contagem</a></li>
                <li><a href="index.php">Sair</a></li>
            </ul>
        </nav>
    </div>

    <main id="main-content">
        <section id="estoque-section">
            <h1>Gerenciamento de Estoque</h1>

            <div id="estoque-top-controls">
                <form id="estoque-search-form" action="estoque.php" method="GET" role="search">
                    <div id="estoque-input-group" class="input-container">
                        <label for="estoque-alimento-input" class="sr-only">Digite o alimento para buscar</label>
                        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        <input type="search" name="alimento" id="estoque-alimento-input"
                            placeholder="Pesquisar item..."
                            value="<?php echo htmlspecialchars($nome_item_busca); ?>">
                    </div>
                    <button type="submit" id="estoque-search-button">Buscar</button>
                </form>

                <button id="estoque-add-button" class="btn-cadastro">
                    <i class="fa-solid fa-plus" aria-hidden="true"></i> Novo Item
                </button>
            </div>

            <?php if ($erro_php): ?>
                <p id="estoque-error-message" class="mensagem-erro" role="alert" aria-live="assertive">
                    <?php echo $erro_php; ?>
                </p>
            <?php endif; ?>

            <div id="estoque-results-container">
                <?php if (!empty($dados)): ?>
                    <h2>Itens no Estoque:</h2>
                    <table id="estoque-data-table">
                        <thead>
                            <tr>
                                <th scope="col">Nome do Item</th>
                                <th scope="col">Tipo de Movimentação</th>
                                <th scope="col">Quantidade</th>
                                <th scope="col">Unidade</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dados as $item): ?>
                                <tr id="item-<?php echo htmlspecialchars($item['id_estoque']); ?>">
                                    <td><?php echo htmlspecialchars($item['nome_item_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['tipo_movimentacao_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantidade_estoque']); ?></td>
                                    <td><?php echo htmlspecialchars($item['unidade_estoque']); ?></td>
                                    <td>
                                        <button class="action-button edit-button" data-id="<?php echo htmlspecialchars($item['id_estoque']); ?>" title="Editar Item">
                                            <i class="fa-solid fa-pencil"></i>
                                        </button>
                                        <button class="action-button delete-button" data-id="<?php echo htmlspecialchars($item['id_estoque']); ?>" title="Excluir Item">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($nome_item_busca !== ''): ?>
                    <p id="estoque-no-results-message" class="mensagem-info">Nenhum item encontrado com o nome "<?php echo htmlspecialchars($nome_item_busca); ?>".</p>
                <?php else: ?>
                    <p id="estoque-initial-message" class="mensagem-info">Não há itens no estoque. Comece buscando ou adicionando um item.</p>
                <?php endif; ?>
            </div>

        </section>
    </main>

    <footer>
        <div id="fig_bottom" role="img" aria-label="Bottom decoration"></div>
        <img src="../assets/losangos_bottom.png" alt="Padrão decorativo de losangos" id="losangos">
    </footer>

    <div id="delete-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="modal-title" aria-describedby="modal-description">
        <div class="modal-content">
            <h3 id="modal-title">Confirmar Exclusão</h3>
            <p id="modal-description">Tem certeza de que deseja excluir este item do estoque?</p>
            <div class="modal-buttons">
                <button id="confirm-delete-button" class="modal-button confirm">Excluir</button>
                <button id="cancel-delete-button" class="modal-button cancel">Cancelar</button>
            </div>
        </div>
    </div>
    <div id="cadastro-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="cadastro-modal-title">
        <div class="modal-content">
            <h3 id="cadastro-modal-title">Cadastrar Novo Item</h3>
            <form id="cadastro-item-form">
                <div class="form-group">
                    <label for="cadastro-nome">Nome do Item:</label>
                    <input type="text" id="cadastro-nome" name="nome_item_estoque" required>
                </div>
                <div class="form-group">
                    <label for="cadastro-tipo">Tipo de Movimentação:</label>
                    <select id="cadastro-tipo" name="tipo_movimentacao_estoque" required>
                        <option value="">Selecione</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="cadastro-quantidade">Quantidade:</label>
                    <input type="number" id="cadastro-quantidade" name="quantidade_estoque" min="0" required>
                </div>
                <div class="form-group">
                    <label for="cadastro-unidade">Unidade:</label>
                    <input type="text" id="cadastro-unidade" name="unidade_estoque" placeholder="Ex: kg, un, litros" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" id="cadastro-submit-button" class="modal-button confirm">Cadastrar</button>
                    <button type="button" id="cancel-cadastro-button" class="modal-button cancel">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <div id="edicao-modal-overlay" class="modal-overlay" aria-hidden="true" role="dialog" aria-labelledby="edicao-modal-title">
        <div class="modal-content">
            <h3 id="edicao-modal-title">Editar Item</h3>
            <form id="edicao-item-form">
                <input type="hidden" id="edicao-id" name="id_estoque">
                <div class="form-group">
                    <label for="edicao-nome">Nome do Item:</label>
                    <input type="text" id="edicao-nome" name="nome_item_estoque" required>
                </div>
                <div class="form-group">
                    <label for="edicao-tipo">Tipo de Movimentação:</label>
                    <select id="edicao-tipo" name="tipo_movimentacao_estoque" required>
                        <option value="">Selecione</option>
                        <option value="entrada">Entrada</option>
                        <option value="saida">Saída</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edicao-quantidade">Quantidade:</label>
                    <input type="number" id="edicao-quantidade" name="quantidade_estoque" min="0" required>
                </div>
                <div class="form-group">
                    <label for="edicao-unidade">Unidade:</label>
                    <input type="text" id="edicao-unidade" name="unidade_estoque" placeholder="Ex: kg, un, litros" required>
                </div>
                <div class="modal-buttons">
                    <button type="submit" class="modal-button confirm">Salvar </button>
                    <button type="button" id="cancel-edicao-button" class="modal-button cancel">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        const abrir_menu = document.getElementsByClassName('hamburguer')[0];
        const menu = document.getElementsByClassName('menu')[0];
        abrir_menu.addEventListener('click', () => {
            abrir_menu.classList.toggle('aberto');
            menu.classList.toggle('ativo');
        });

        // --- Lógica do Modal de Confirmação de Exclusão ---
        const deleteModalOverlay = document.getElementById('delete-modal-overlay');
        const confirmDeleteButton = document.getElementById('confirm-delete-button');
        const cancelDeleteButton = document.getElementById('cancel-delete-button');
        let itemIdToDelete = null;

        function showDeleteModal(itemId) {
            itemIdToDelete = itemId;
            deleteModalOverlay.classList.add('active');
            deleteModalOverlay.setAttribute('aria-hidden', 'false');
            cancelDeleteButton.focus();
        }

        function hideDeleteModal() {
            deleteModalOverlay.classList.remove('active');
            deleteModalOverlay.setAttribute('aria-hidden', 'true');
            itemIdToDelete = null;
        }

        // Delegar evento para botões de exclusão (importante para elementos adicionados dinamicamente)
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-button') || event.target.closest('.delete-button')) {
                const button = event.target.closest('.delete-button');
                const itemId = button.dataset.id;
                showDeleteModal(itemId);
            }
        });

        cancelDeleteButton.addEventListener('click', hideDeleteModal);
        deleteModalOverlay.addEventListener('click', function(event) {
            if (event.target === deleteModalOverlay) {
                hideDeleteModal();
            }
        });

        confirmDeleteButton.addEventListener('click', async () => {
            if (itemIdToDelete) {
                try {
                    const formData = new FormData();
                    formData.append('id', itemIdToDelete);

                    // ATENÇÃO: CAMINHO CORRIGIDO PARA excluir_estoque.php
                    const response = await fetch('../../back-end/endpoints/excluir_estoque.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        alert(result.message);
                        const rowToRemove = document.getElementById(`item-${itemIdToDelete}`);
                        if (rowToRemove) {
                            rowToRemove.remove();
                        }
                        updateTableEmptyState(); // Atualiza a mensagem se a tabela ficar vazia
                    } else {
                        alert('Erro ao excluir item: ' + result.message);
                    }
                } catch (error) {
                    console.error('Erro ao fazer a requisição de exclusão:', error);
                    alert('Ocorreu um erro ao tentar excluir o item. Por favor, tente novamente.');
                } finally {
                    hideDeleteModal();
                }
            }
        });

        // --- Lógica para o Modal de Cadastro ---
        const cadastroModalOverlay = document.getElementById('cadastro-modal-overlay');
        const estoqueAddButton = document.getElementById('estoque-add-button'); // O botão "Novo Item"
        const cadastroItemForm = document.getElementById('cadastro-item-form');
        const cancelCadastroButton = document.getElementById('cancel-cadastro-button');

        function showCadastroModal() {
            cadastroModalOverlay.classList.add('active');
            cadastroModalOverlay.setAttribute('aria-hidden', 'false');
            document.getElementById('cadastro-nome').focus();
        }

        function hideCadastroModal() {
            cadastroModalOverlay.classList.remove('active');
            cadastroModalOverlay.setAttribute('aria-hidden', 'true');
            cadastroItemForm.reset(); // Limpa o formulário ao fechar
        }

        estoqueAddButton.addEventListener('click', showCadastroModal);
        cancelCadastroButton.addEventListener('click', hideCadastroModal);
        cadastroModalOverlay.addEventListener('click', function(event) {
            if (event.target === cadastroModalOverlay) {
                hideCadastroModal();
            }
        });

        cadastroItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                // ATENÇÃO: CAMINHO CORRIGIDO PARA cadastrar_estoque.php
                const response = await fetch('../../back-end/endpoints/cadastrar_estoque.php', { // MUDADO DE /api/ PARA /endpoints/
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    hideCadastroModal();
                    reloadTableData(); // Recarrega a tabela para mostrar o novo item
                } else {
                    alert('Erro ao cadastrar item: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao cadastrar item:', error);
                alert('Ocorreu um erro ao tentar cadastrar o item. Por favor, tente novamente.');
            }
        });

        // --- Lógica para o Modal de Edição ---
        const edicaoModalOverlay = document.getElementById('edicao-modal-overlay');
        const edicaoItemForm = document.getElementById('edicao-item-form');
        const cancelEdicaoButton = document.getElementById('cancel-edicao-button');
        const edicaoIdInput = document.getElementById('edicao-id');
        const edicaoNomeInput = document.getElementById('edicao-nome');
        const edicaoTipoSelect = document.getElementById('edicao-tipo');
        const edicaoQuantidadeInput = document.getElementById('edicao-quantidade');
        const edicaoUnidadeInput = document.getElementById('edicao-unidade');

        function showEdicaoModal(itemData) {
            // Preenche o formulário do modal com os dados do item
            edicaoIdInput.value = itemData.id_estoque;
            edicaoNomeInput.value = itemData.nome_item_estoque;
            edicaoTipoSelect.value = itemData.tipo_movimentacao_estoque;
            edicaoQuantidadeInput.value = itemData.quantidade_estoque;
            edicaoUnidadeInput.value = itemData.unidade_estoque;

            edicaoModalOverlay.classList.add('active');
            edicaoModalOverlay.setAttribute('aria-hidden', 'false');
            edicaoNomeInput.focus();
        }

        function hideEdicaoModal() {
            edicaoModalOverlay.classList.remove('active');
            edicaoModalOverlay.setAttribute('aria-hidden', 'true');
            edicaoItemForm.reset();
        }

        // Delegar evento para botões de edição (importante para elementos adicionados dinamicamente)
        document.addEventListener('click', async function(event) {
            if (event.target.classList.contains('edit-button') || event.target.closest('.edit-button')) {
                const button = event.target.closest('.edit-button');
                const itemId = button.dataset.id;

                try {
                    // *** CAMINHO CORRIGIDO: CHAMANDO atualizar_estoque.php VIA GET PARA BUSCAR ***
                    const response = await fetch(`../../back-end/endpoints/atualizar_estoque.php?id=${itemId}`);
                    if (!response.ok) {
                        throw new Error(`Erro HTTP! Status: ${response.status}`);
                    }
                    const itemData = await response.json();

                    if (itemData.success) {
                        showEdicaoModal(itemData.data);
                    } else {
                        alert('Erro ao buscar dados do item: ' + itemData.message);
                    }
                } catch (error) {
                    console.error('Erro ao buscar item para edição:', error);
                    alert('Ocorreu um erro ao buscar os dados do item para edição. Por favor, verifique o console para mais detalhes.');
                }
            }
        });

        cancelEdicaoButton.addEventListener('click', hideEdicaoModal);
        edicaoModalOverlay.addEventListener('click', function(event) {
            if (event.target === edicaoModalOverlay) {
                hideEdicaoModal();
            }
        });

        edicaoItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            try {
                // CAMINHO CORRIGIDO PARA atualizar_estoque.php (já estava correto para POST)
                const response = await fetch('../../back-end/endpoints/atualizar_estoque.php', { // Mantém endpoints/
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    alert(result.message);
                    hideEdicaoModal();
                    reloadTableData(); // Recarrega a tabela para mostrar as alterações
                } else {
                    alert('Erro ao atualizar item: ' + result.message);
                }
            } catch (error) {
                console.error('Erro ao atualizar item:', error);
                alert('Ocorreu um erro ao tentar atualizar o item. Por favor, tente novamente.');
            }
        });

        // --- Função para recarregar os dados da tabela via AJAX ---
        async function reloadTableData() {
            const searchInput = document.getElementById('estoque-alimento-input');
            const searchTerm = searchInput ? searchInput.value : '';

            try {
                // Adiciona um parâmetro 'ajax=1' para que o PHP saiba que é uma requisição AJAX
                const response = await fetch(`estoque.php?alimento=${encodeURIComponent(searchTerm)}&ajax=1`);
                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }
                const html = await response.text();

                // Criar um elemento temporário para parsear o HTML
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = html;

                // Encontrar o container da tabela e substituir seu conteúdo
                const newTableContainer = tempDiv.querySelector('#estoque-results-container');
                const currentTableContainer = document.getElementById('estoque-results-container');

                if (newTableContainer && currentTableContainer) {
                    currentTableContainer.innerHTML = newTableContainer.innerHTML;
                    // Os event listeners para os botões delete e edit agora são delegados,
                    // então não precisam ser re-adicionados aqui após a substituição do HTML.
                } else {
                    console.error('Não foi possível encontrar o container da tabela para atualização.');
                }
            } catch (error) {
                console.error('Erro ao recarregar dados da tabela:', error);
                alert('Ocorreu um erro ao recarregar a tabela de estoque.');
            }
        }

        // Função para atualizar o estado da tabela (se está vazia ou não)
        function updateTableEmptyState() {
            const tableBody = document.getElementById('estoque-data-table') ? document.getElementById('estoque-data-table').querySelector('tbody') : null;
            const resultsContainer = document.getElementById('estoque-results-container');

            if (tableBody && tableBody.children.length === 0) {
                resultsContainer.innerHTML = '<p id="estoque-initial-message" class="mensagem-info">Não há itens no estoque. Comece buscando ou adicionando um item.</p>';
            } else if (!tableBody) {
                // Se a tabela nem existir, significa que já está na mensagem de "não há itens"
            }
        }
        // Chamar ao carregar a página para garantir que a mensagem inicial esteja correta
        updateTableEmptyState();

        // Opcional: Ajuste para que a busca também recarregue a tabela via AJAX
        document.getElementById('estoque-search-form').addEventListener('submit', async function(event) {
            event.preventDefault(); // Impede o envio padrão que recarrega a página
            await reloadTableData(); // Recarrega a tabela usando a função AJAX
        });
    </script>
</body>

</html>