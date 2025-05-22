<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Estoque</title>
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
                <li><a href="index.php">Sair</a></li>
            </ul>
        </nav>
    </div>
    <main>
        <section id="conjunto-categorias">
            <div class="box-categorias">
                <h1>Fundamental 1A</h1>
                <div class="botoes-anos">
                    <button class="btn_contagens">1º Ano</button>
                    <button class="btn_contagens">2º Ano</button>
                </div>
            </div>
        </section>

        <div id="btn_feito">
            <button>Feito</button>
        </div>
    </main>

    <footer>
        <div id="fig_bottom"></div>
        <img src="../assets/losangos_bottom.png" alt="" id="losangos">
    </footer>

    <dialog id="modal" class="modal-contagem">
        <div class="content">
            <div class="titulo" id="titulo-modal">4º Ano</div>

            <div class="contador">
                <button class="btn-icon" onclick="alterarValor(-1)">
                    <i class="fa-solid fa-circle-minus" style="color: red;"></i>
                </button>

                <input id="valor" type="number" value="32" min="0" max="33" style="width: 60px; text-align: center; font-weight: bold; font-size: 20px; border: none; background: transparent;" />

                <button class="btn-icon" onclick="alterarValor(1)">
                    <i class="fa-solid fa-circle-plus" style="color: green;"></i>
                </button>
            </div>

            <button class="btn-feito" onclick="document.getElementById('modal').close()">Feito</button>
        </div>
    </dialog>

    <script>
        const abrir_menu = document.getElementsByClassName('hamburguer')[0];
        const menu = document.getElementsByClassName('menu')[0];
        abrir_menu.addEventListener('click', () => {
            abrir_menu.classList.toggle('aberto');
            menu.classList.toggle('ativo');
        });

        function alterarValor(delta) {
            const input = document.getElementById('valor');
            const min = parseInt(input.min) || 0;
            const max = parseInt(input.max) || Infinity;
            let valor = parseInt(input.value) || 0;

            valor = Math.min(max, Math.max(min, valor + delta));
            input.value = valor;
        }



        const botoesAnos = document.querySelectorAll('.btn_contagens');
        const modal = document.getElementById('modal');
        const tituloModal = document.getElementById('titulo-modal');
        const inputValor = document.getElementById('valor');

        botoesAnos.forEach(botao => {
            botao.addEventListener('click', () => {
                const textoBotao = botao.textContent.trim();
                tituloModal.textContent = textoBotao;
                inputValor.value = 0; // zera o valor sempre que abre
                modal.showModal();
            });
        });

        window.onclick = function(event) {
            const dialog = document.getElementById('modal');
            if (event.target === dialog) {
                dialog.close();
            }
        };
    </script>
</body>

</html>