<?php
session_start(); // Inicia a sessão se precisar de variáveis de sessão ou tokens.
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Relatório de Contagem</title>
    <style>
        /* --- Estilos Gerais da Main (Ajustados para o espaçamento lateral da imagem) --- */
        main {
            display: flex;
            justify-content: center;
            /* Reduzindo o padding lateral da main para que o max-width do relatório faça o trabalho */
            padding: 10px;
            /* Um padding geral pequeno, o espaçamento maior virá do max-width do relatório */
            background-color: #f0f2f5;
            min-height: calc(100vh - 150px);
            box-sizing: border-box;
        }

        /* --- Estilos para a Seção Principal do Relatório com Tabelas (Quadrado Azul - Ajustado para altura e largura da imagem) --- */
        #relatorio-contagens-tabelas {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            /* Max-width ajustado para corresponder visualmente à imagem */
            max-width: 1050px;
            /* Um pouco mais largo que 1000px, para o conteúdo caber bem */
            /* Padding vertical e lateral ajustado para corresponder à imagem */
            padding: 10px 15px;
            /* Mais compacto, mas com um pouco de respiro */
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        /* --- Estilos para o Cabeçalho do Relatório (Ajustes para alinhamento e espaçamento) --- */
        .header-relatorio {
            text-align: center;
            /* Margem inferior ajustada */
            margin-bottom: 20px;
            /* Espaço entre o título do relatório e as tabelas */
            width: 100%;
        }

        .header-relatorio h1 {
            font-size: 2.8em;
            /* Levemente menor que 3em para compactar */
            color: #333;
            margin-bottom: 8px;
            /* Reduzindo espaço abaixo do H1 */
            font-weight: 700;
        }

        .header-relatorio p {
            font-size: 1.2em;
            /* Levemente menor que 1.3em */
            color: #666;
            margin: 0;
        }

        /* --- Contêiner das Tabelas de Categoria (Layout de Grid Fino - Quadrado Verde) --- */
        #tabelas-contagem-container {
            display: grid;
            /* Ajuste para ter 4 colunas bem apertadas, como na imagem */
            /* minmax(220px, 1fr) permite que encolham até 220px e cresçam */
            /* O 1fr garante que dividam o espaço igualmente */
            grid-template-columns: repeat(4, 1fr);
            /* FORÇA 4 colunas em telas maiores */
            gap: 15px;
            /* Reduzido o gap para que as tabelas fiquem mais próximas, como na imagem */
            width: 100%;
            margin-bottom: 25px;
            margin-left: 200px;
            /* Espaço entre as tabelas e o total geral */
            /* Sem padding extra aqui, o padding já está no .tabela-categoria-wrapper */
        }

        /* --- Wrapper para cada Tabela de Categoria (Card de Tabela) --- */
        .tabela-categoria-wrapper {
            background-color: #fdfdfd;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            /* Sombra mais sutil, como na imagem */
            padding: 15px;
            /* Padding interno ajustado para compactar */
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-sizing: border-box;
            /* Altura será determinada pelo conteúdo, mas o grid garantirá alinhamento */
        }

        .tabela-categoria-wrapper:hover {
            transform: translateY(-3px);
            /* Efeito hover mais sutil */
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        }

        .tabela-categoria-wrapper h3 {
            font-size: 1.6em;
            /* Um pouco menor para compactar */
            color: #222;
            margin-bottom: 10px;
            /* Espaço abaixo do título da categoria */
            font-weight: 600;
            border-bottom: 2px solid #ff9900;
            padding-bottom: 8px;
            width: 90%;
            text-align: center;
        }

        /* --- Estilos para as Tabelas Internas --- */
        .tabela-contagem {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            /* Espaço abaixo da tabela antes do total */
            font-size: 1em;
            /* Um pouco menor para compactar */
        }

        .tabela-contagem thead {
            background-color: transparent;
            /* Remove background-color do thead */
            color: #333;
            /* Cor do texto mais escura para o cabeçalho */
            /* Adicionando borda superior vermelha como na imagem */
            border-top: 5px solid #E44D26;
            /* Cor vermelha do cabeçalho da imagem */
            border-radius: 5px 5px 0 0;
            /* Arredondar só os cantos superiores */
            overflow: hidden;
            /* Para a borda radius funcionar */
        }

        .tabela-contagem th {
            padding: 10px 8px;
            /* Ajusta padding do cabeçalho da tabela */
            text-align: center;
            /* Centraliza o texto do cabeçalho */
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
            color: #fff;
            /* Texto branco no cabeçalho */
            background-color: #E44D26;
            /* Fundo vermelho do cabeçalho da imagem */
            border-bottom: none;
            /* Remove borda inferior do th */
        }

        /* Primeiro th e último th para bordas arredondadas e que não vaze o background */
        .tabela-contagem th:first-child {
            border-top-left-radius: 5px;
        }

        .tabela-contagem th:last-child {
            border-top-right-radius: 5px;
        }


        .tabela-contagem td {
            padding: 8px 8px;
            /* Padding menor nas células de dados */
            text-align: center;
            /* Centraliza o texto das células */
            border-bottom: 1px solid #eee;
            /* Linhas mais claras */
        }

        .tabela-contagem tbody tr:nth-child(even) {
            background-color: #fcfcfc;
            /* Zebra striping mais sutil */
        }

        .tabela-contagem tbody tr:hover {
            background-color: #f5f5f5;
            /* Efeito hover mais sutil */
        }

        /* Última linha da tabela sem borda inferior */
        .tabela-contagem tbody tr:last-child td {
            border-bottom: none;
        }


        /* --- Estilos para o Total dentro de cada Tabela de Categoria --- */
        .total-categoria-wrapper {
            margin-top: auto;
            /* Empurra para baixo */
            width: 100%;
            /* Ocupa a largura total do card */
            display: flex;
            justify-content: center;
            /* Centraliza o "Total" e o valor */
            align-items: center;
            padding-top: 10px;
            /* Espaço acima do total */
            border-top: none;
            /* Remove a linha tracejada */
        }

        .total-categoria-wrapper span:first-child {
            /* O texto "Total:" */
            font-size: 1.2em;
            /* Menor que 1.3em */
            font-weight: 600;
            color: #444;
            margin-right: 10px;
            /* Espaço entre "Total:" e o valor */
        }

        .valor-total-categoria {
            background-color: #ff9900;
            color: #fff;
            font-size: 1.6em;
            /* Levemente menor que 1.8em */
            font-weight: bold;
            border-radius: 50px;
            padding: 6px 18px;
            /* Padding ajustado para o visual da imagem */
            display: inline-block;
            min-width: 60px;
            /* Reduzido min-width */
            text-align: center;
            box-shadow: 0 2px 6px rgba(255, 153, 0, 0.4);
            /* Sombra mais suave */
        }

        /* --- Estilos para o Total Geral Final --- */
        #total-geral-final {
            background-color: #ff6600;
            color: #fff;
            padding: 18px 50px;
            /* Padding reduzido para compactar */
            border-radius: 60px;
            /* Levemente menor que 70px */
            font-size: 2.5em;
            /* Levemente menor que 3em */
            font-weight: bold;
            box-shadow: 0 6px 20px rgba(255, 102, 0, 0.6);
            /* Sombra mais suave */
            display: flex;
            align-items: center;
            justify-content: center;
            /* Centraliza o total geral */
            gap: 15px;
            /* Gap menor */
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            /* Sombra de texto mais sutil */
            margin-top: 30px;
            /* Espaço entre as tabelas e o total geral */
        }

        #total-geral-final h2 {
            margin: 0;
            font-size: 1em;
            display: flex;
            align-items: baseline;
            gap: 10px;
        }

        #valor-total-geral {
            font-size: 1em;
        }

        /* --- Mensagens de Estado (Mantido) --- */
        .mensagem-sem-dados,
        .mensagem-erro-dados {
            text-align: center;
            font-size: 1.3em;
            color: #888;
            margin: 30px auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .mensagem-erro-dados {
            color: #d9534f;
            border-color: #d9534f;
            background-color: #fcf8f8;
        }

        /* --- Estilos Responsivos (Ajustados para o novo design e proporções) --- */
        /* O grid-template-columns com repeat(4, 1fr) vai forçar 4 colunas.
   O conteúdo das tabelas pode precisar encolher ainda mais para caber. */

        @media (max-width: 1050px) {

            /* Reduzindo de 1200px para 1050px, quando as 4 colunas começam a ficar apertadas */
            #tabelas-contagem-container {
                gap: 10px;
                /* Reduz ainda mais o gap */
                grid-template-columns: repeat(4, 1fr);
                /* Mantém 4 colunas, mas elas encolhem mais */
            }

            .tabela-categoria-wrapper {
                padding: 10px;
                /* Reduz padding interno do card */
            }

            .tabela-contagem th,
            .tabela-contagem td {
                padding: 6px 5px;
                /* Reduz mais o padding da tabela */
                font-size: 0.85em;
                /* Reduz o tamanho da fonte da tabela */
            }

            .valor-total-categoria {
                font-size: 1.4em;
                /* Reduz fonte do total categoria */
                padding: 5px 15px;
            }
        }

        @media (max-width: 900px) {

            /* Transição para 2 colunas para tablets e telas médias */
            #tabelas-contagem-container {
                grid-template-columns: repeat(2, 1fr);
                /* 2 colunas */
                gap: 20px;
                /* Aumenta um pouco o gap para 2 colunas */
            }

            #relatorio-contagens-tabelas {
                max-width: 700px;
                /* Limita a largura em 2 colunas */
            }

            .tabela-categoria-wrapper {
                padding: 15px;
                /* Aumenta um pouco o padding do card para 2 colunas */
            }

            .tabela-contagem th,
            .tabela-contagem td {
                padding: 8px 8px;
                /* Ajusta padding da tabela */
                font-size: 1em;
                /* Volta ao tamanho original ou próximo */
            }

            .valor-total-categoria {
                font-size: 1.6em;
                /* Volta ao tamanho original ou próximo */
                padding: 6px 18px;
            }
        }

        @media (max-width: 600px) {

            /* Transição para 1 coluna para celulares */
            #tabelas-contagem-container {
                grid-template-columns: 1fr;
                /* 1 coluna */
                gap: 15px;
                /* Gap para 1 coluna */
            }

            #relatorio-contagens-tabelas {
                max-width: 380px;
                /* Largura máxima para 1 coluna */
                padding: 15px 10px;
                /* Padding no quadrado azul */
            }

            main {
                padding: 10px;
                /* Padding geral na main */
            }

            .tabela-categoria-wrapper {
                padding: 10px;
                /* Reduz padding interno do card */
            }

            .tabela-contagem th,
            .tabela-contagem td {
                padding: 6px;
                /* Ajusta padding da tabela */
                font-size: 0.9em;
            }

            .valor-total-categoria {
                font-size: 1.4em;
                padding: 5px 12px;
            }

            #total-geral-final {
                font-size: 1.8em;
                padding: 15px 30px;
            }
        }

        /* Ajustes gerais para fontes e padding em telas muito pequenas */
        @media (max-width: 400px) {
            .header-relatorio h1 {
                font-size: 1.5em;
            }

            .header-relatorio p {
                font-size: 0.8em;
            }

            #total-geral-final {
                font-size: 1.2em;
                padding: 10px 20px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div id="fig_top"></div>
    </header>

    <div class="menu">
        <button class="hamburguer">
            <div id="barra1" class="barra"></div>
            <div id="barra2" class="barra"></div>
            <div id="barra3" class="barra"></div>
        </button>

        <nav>
            <ul>
                <li><a href="estoque.php">Estoque</a></li>
                <li><a href="calendario.php">Calendário</a></li>
                <li><a href="relatorio.php">Contagem</a></li>
                <li><a href="#" id="logout-btn">Sair</a></li>
            </ul>
        </nav>
    </div>

    <main>
        <section id="relatorio-contagens-tabelas">
            <div class="header-relatorio">
                <h1>Relatório de Contagem</h1>
                <p>Data: <span id="data-relatorio">Carregando...</span></p>
            </div>

            <div id="tabelas-contagem-container">
                <p>Carregando dados das contagens...</p>
            </div>

            <div id="total-geral-final">
                <h2>Total Geral: <span id="valor-total-geral">0</span></h2>
            </div>
        </section>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="../assets/losangos_bottom.png" alt="Rodapé decorativo" id="losangos">
    </footer>

    <script>
        // --- Funções do Menu Hambúrguer (Mantidas) ---
        document.addEventListener('DOMContentLoaded', () => {
            const abrir_menu = document.querySelector('.hamburguer');
            const menu = document.querySelector('.menu');

            if (abrir_menu && menu) {
                abrir_menu.addEventListener('click', () => {
                    abrir_menu.classList.toggle('aberto');
                    menu.classList.toggle('ativo');
                });
            }

            // --- Lógica do botão de logout (Mantida) ---
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', async function(e) {
                    e.preventDefault();
                    const currentRefreshToken = sessionStorage.getItem('refresh_token');

                    if (!currentRefreshToken) {
                        alert('Não há token para logout.');
                        sessionStorage.clear();
                        window.location.href = 'index.php';
                        return;
                    }

                    try {
                        const response = await fetch('../../back-end/endpoints/logout.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                refreshToken: currentRefreshToken
                            })
                        });
                        const data = await response.json();

                        if (data.mensagem || response.ok) {
                            alert(data.mensagem || "Logout realizado com sucesso.");
                        } else {
                            alert('Erro ao fazer logout: ' + (data.erro || "Erro desconhecido"));
                            console.error("Erro de logout:", data);
                        }
                    } catch (err) {
                        alert('Erro na requisição de logout: ' + err.message);
                        console.error("Erro de rede no logout:", err);
                    } finally {
                        sessionStorage.clear();
                        window.location.href = 'index.php';
                    }
                });
            }

            // --- Lógica para Carregar e Exibir os Dados do Relatório em Tabelas ---
            const tabelasContagemContainer = document.getElementById('tabelas-contagem-container');
            const valorTotalGeralElement = document.getElementById('valor-total-geral');
            const dataRelatorioElement = document.getElementById('data-relatorio');
            let totalGeralAcumulado = 0; // Usado para o total geral final

            // Função para formatar a data para exibição (ex: 2025-06-18 para 18/06/2025)
            function formatarDataParaExibicao(dataISO) {
                if (!dataISO) return 'N/A';
                try {
                    const [ano, mes, dia] = dataISO.split('-');
                    return `${dia}/${mes}/${ano}`;
                } catch (e) {
                    console.error("Erro ao formatar data:", e);
                    return dataISO; // Retorna original se houver erro
                }
            }

            // Pega a data da URL ou usa a data atual se nenhuma for fornecida
            const urlParams = new URLSearchParams(window.location.search);
            let dataParaBackend = urlParams.get('data') || '';

            if (dataParaBackend) {
                dataRelatorioElement.textContent = formatarDataParaExibicao(dataParaBackend);
            } else {
                // Se nenhuma data foi selecionada na URL, usa a data atual do cliente para exibir e para a requisição
                const hoje = new Date();
                const ano = hoje.getFullYear();
                const mes = String(hoje.getMonth() + 1).padStart(2, '0');
                const dia = String(hoje.getDate()).padStart(2, '0');
                dataParaBackend = `${ano}-${mes}-${dia}`;
                dataRelatorioElement.textContent = formatarDataParaExibicao(dataParaBackend);
            }

            // Função assíncrona para buscar e renderizar os dados
            async function carregarRelatorio() {
                try {
                    // Fetch dos dados do backend
                    const response = await fetch('../../back-end/endpoints/mostrar_contagem.php' + (dataParaBackend ? `?data=${dataParaBackend}` : ''));

                    if (!response.ok) {
                        const errorText = await response.text();
                        throw new Error(`HTTP error! Status: ${response.status} - ${errorText}`);
                    }

                    const dadosContagem = await response.json();

                    tabelasContagemContainer.innerHTML = ''; // Limpa o conteúdo de carregamento

                    if (dadosContagem.length === 0) {
                        tabelasContagemContainer.innerHTML = '<p class="mensagem-sem-dados">Nenhuma contagem encontrada para esta data.</p>';
                        valorTotalGeralElement.textContent = '0';
                        return; // Sai da função se não houver dados
                    }

                    totalGeralAcumulado = 0; // Reseta o total geral antes de somar novamente

                    // Loop para criar uma tabela para cada categoria
                    dadosContagem.forEach(categoriaData => {
                        const categoriaNome = categoriaData.categoria;
                        let totalCategoria = 0;

                        // Cria a estrutura da tabela para a categoria
                        const tabelaHtml = `
                            <div class="tabela-categoria-wrapper">
                                <h3>${categoriaNome}</h3>
                                <table class="tabela-contagem">
                                    <thead>
                                        <tr>
                                            <th>Ano/Turma</th>
                                            <th>Contagem</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${categoriaData.turmas.map(turma => {
                                            totalCategoria += turma.total_quantidade;
                                            return `
                                                <tr>
                                                    <td>${turma.nome_turma}</td>
                                                    <td>${turma.total_quantidade}</td>
                                                </tr>
                                            `;
                                        }).join('')}
                                    </tbody>
                                </table>
                                <div class="total-categoria-wrapper">
                                    <span>Total:</span>
                                    <span class="valor-total-categoria">${totalCategoria}</span>
                                </div>
                            </div>
                        `;
                        tabelasContagemContainer.insertAdjacentHTML('beforeend', tabelaHtml);
                        totalGeralAcumulado += totalCategoria; // Soma ao total geral acumulado
                    });

                    valorTotalGeralElement.textContent = totalGeralAcumulado; // Atualiza o total geral na tela

                } catch (error) {
                    console.error("Erro ao carregar dados da contagem:", error);
                    tabelasContagemContainer.innerHTML = `<p class="mensagem-erro-dados" style="color: red;">Erro ao carregar contagens: ${error.message}</p>`;
                    valorTotalGeralElement.textContent = "Erro";
                }
            }

            // Chama a função para carregar o relatório quando a página carregar
            carregarRelatorio();
        });
    </script>
</body>

</html>