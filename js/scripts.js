// Scripts do Sistema de Agendamento de Eventos (JavaScript puro)

// ---------- Funções de apoio ----------

// Mostra a mensagem de erro embaixo do campo
function mostrarErro(campo, mensagem) {
    var caixa = campo.closest('.campo');
    var span = caixa.querySelector('.erro-campo');
    campo.classList.add('invalido');
    if (span) {
        span.textContent = mensagem;
    }
}

// Remove a mensagem de erro do campo
function limparErro(campo) {
    var caixa = campo.closest('.campo');
    var span = caixa.querySelector('.erro-campo');
    campo.classList.remove('invalido');
    if (span) {
        span.textContent = '';
    }
}

// Confere o formato básico de e-mail: algo@algo.algo
function emailValido(email) {
    var regra = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regra.test(email);
}

// Verifica campos obrigatórios vazios. Retorna true se todos foram preenchidos.
function verificarObrigatorios(formulario) {
    var valido = true;
    var campos = formulario.querySelectorAll('[required]');

    for (var i = 0; i < campos.length; i++) {
        if (campos[i].value.trim() === '') {
            mostrarErro(campos[i], 'Este campo é obrigatório.');
            valido = false;
        } else {
            limparErro(campos[i]);
        }
    }
    return valido;
}

// Confere o e-mail de um formulário (se o campo existir e estiver preenchido)
function verificarEmail(formulario) {
    var email = formulario.querySelector('#email');
    if (email && email.value.trim() !== '' && !emailValido(email.value.trim())) {
        mostrarErro(email, 'Digite um e-mail válido, como nome@exemplo.com.');
        return false;
    }
    return true;
}

// Coloca o foco no primeiro campo com erro
function focarPrimeiroErro(formulario) {
    var primeiro = formulario.querySelector('.invalido');
    if (primeiro) {
        primeiro.focus();
    }
}

// ---------- Formulário de login ----------
var formLogin = document.getElementById('formLogin');
if (formLogin) {
    formLogin.addEventListener('submit', function (evento) {
        var ok = verificarObrigatorios(formLogin);
        ok = verificarEmail(formLogin) && ok;

        if (!ok) {
            evento.preventDefault();
            focarPrimeiroErro(formLogin);
        }
    });
}

// ---------- Formulário de cadastro de usuário ----------
var formCadastro = document.getElementById('formCadastro');
if (formCadastro) {
    formCadastro.addEventListener('submit', function (evento) {
        var ok = verificarObrigatorios(formCadastro);
        ok = verificarEmail(formCadastro) && ok;

        var senha = document.getElementById('senha');
        var confirmacao = document.getElementById('confirmacao');

        if (senha.value !== '' && senha.value.length < 6) {
            mostrarErro(senha, 'A senha deve ter pelo menos 6 caracteres.');
            ok = false;
        }

        if (confirmacao.value !== '' && senha.value !== confirmacao.value) {
            mostrarErro(confirmacao, 'As senhas não são iguais.');
            ok = false;
        }

        if (!ok) {
            evento.preventDefault();
            focarPrimeiroErro(formCadastro);
        }
    });
}

// ---------- Formulário de cadastro de evento ----------
var formEvento = document.getElementById('formEvento');
if (formEvento) {
    formEvento.addEventListener('submit', function (evento) {
        var ok = verificarObrigatorios(formEvento);

        var data = document.getElementById('data');
        if (data.value !== '') {
            // Data de hoje no formato AAAA-MM-DD
            var hoje = new Date();
            var mes = String(hoje.getMonth() + 1).padStart(2, '0');
            var dia = String(hoje.getDate()).padStart(2, '0');
            var hojeTexto = hoje.getFullYear() + '-' + mes + '-' + dia;

            if (data.value < hojeTexto) {
                mostrarErro(data, 'A data não pode estar no passado.');
                ok = false;
            }
        }

        if (!ok) {
            evento.preventDefault();
            focarPrimeiroErro(formEvento);
        }
    });
}

// Limpa o erro assim que a pessoa começa a corrigir o campo
var todosCampos = document.querySelectorAll('.formulario input, .formulario textarea');
for (var i = 0; i < todosCampos.length; i++) {
    todosCampos[i].addEventListener('input', function () {
        limparErro(this);
    });
}

// ---------- Mostrar / ocultar senha ----------
var botoesSenha = document.querySelectorAll('.mostrar-senha');
for (var j = 0; j < botoesSenha.length; j++) {
    botoesSenha[j].addEventListener('click', function () {
        var campo = document.getElementById(this.dataset.alvo);
        if (campo.type === 'password') {
            campo.type = 'text';
            this.textContent = 'Ocultar';
        } else {
            campo.type = 'password';
            this.textContent = 'Mostrar';
        }
    });
}

// ---------- Confirmação antes de cancelar agendamento ----------
var formsCancelar = document.querySelectorAll('.form-cancelar');
for (var k = 0; k < formsCancelar.length; k++) {
    formsCancelar[k].addEventListener('submit', function (evento) {
        var confirmou = confirm('Tem certeza que deseja cancelar este agendamento?');
        if (!confirmou) {
            evento.preventDefault();
        }
    });
}

// ---------- Menu no celular (abrir / fechar) ----------
var botaoMenu = document.getElementById('botaoMenu');
var menu = document.getElementById('menuPrincipal');
if (botaoMenu && menu) {
    botaoMenu.addEventListener('click', function () {
        var aberto = menu.classList.toggle('aberto');
        botaoMenu.setAttribute('aria-expanded', aberto ? 'true' : 'false');
    });
}

// ---------- Busca na lista de eventos ----------
var busca = document.getElementById('busca');
if (busca) {
    busca.addEventListener('input', function () {
        var termo = busca.value.trim().toLowerCase();
        var itens = document.querySelectorAll('#listaEventos .cartao-evento');
        var visiveis = 0;

        for (var m = 0; m < itens.length; m++) {
            var texto = itens[m].dataset.busca.toLowerCase();
            if (texto.indexOf(termo) !== -1) {
                itens[m].classList.remove('oculto');
                visiveis++;
            } else {
                itens[m].classList.add('oculto');
            }
        }

        document.getElementById('semResultados').classList.toggle('oculto', visiveis > 0);
    });
}

// ---------- Fecha as mensagens de sucesso sozinhas ----------
var alertasSucesso = document.querySelectorAll('.alerta-sucesso');
for (var n = 0; n < alertasSucesso.length; n++) {
    (function (alerta) {
        setTimeout(function () {
            alerta.classList.add('sumindo');
        }, 4000);
    })(alertasSucesso[n]);
}
