
document.addEventListener("DOMContentLoaded", () => {

    // ==========================================
    // TELA HOME / DASHBOARD (index.html)
    // ==========================================
    const listaHoje = document.getElementById("lista-hoje");
    const listaProximos = document.getElementById("lista-proximos");
    const listaRecentes = document.getElementById("lista-recentes");
    const tituloHoje = document.getElementById("titulo-hoje");
    const listaAvisos = document.getElementById("lista-avisos");

    if (listaHoje && listaProximos) {
        const dataAtual = new Date();
        tituloHoje.textContent = `Hoje - ${dataAtual.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })}`;

        async function carregarDashboard() {
            try {
                const resposta = await fetch("../../backend/carregar_dashboard.php", {
                    method: "GET",
                    headers: getAuthHeaders()
                });
                
                const resultado = await resposta.json();

                if (resposta.ok && resultado.sucesso) {
                    
                    listaHoje.innerHTML = "";
                    if (resultado.hoje.length === 0) {
                        listaHoje.innerHTML = "<li>Nenhum atendimento pendente para hoje.</li>";
                    } else {
                        resultado.hoje.forEach(agendamento => {
                            const li = document.createElement("li");
                            const infoJson = JSON.stringify(agendamento).replace(/'/g, "&apos;").replace(/"/g, "&quot;");
                            
                            li.innerHTML = `
                                <time datetime="${agendamento.horario}">${agendamento.horario.substring(0,5)}</time> - 
                                <a href="#" class="link-paciente" data-info="${infoJson}"><strong>${agendamento.paciente_info}</strong></a>
                                <button class="btn-concluir" data-id="${agendamento.id}" title="Marcar atendimento como realizado" style="margin-left:auto; background:none; border:none; color:green; cursor:pointer; font-size: 1.2rem;">✔️</button>
                            `;
                            li.style.display = "flex";
                            li.style.alignItems = "center";
                            li.style.gap = "5px";
                            listaHoje.appendChild(li);
                        });
                    }

                    listaProximos.innerHTML = "";
                    if (resultado.proximos.length === 0) {
                        listaProximos.innerHTML = "<li>Agenda livre nos próximos dias.</li>";
                    } else {
                        resultado.proximos.forEach(agendamento => {
                            const li = document.createElement("li");
                            const dataFormatada = agendamento.data_consulta.split('-').reverse().join('/');
                            const infoJson = JSON.stringify(agendamento).replace(/'/g, "&apos;").replace(/"/g, "&quot;");
                            li.innerHTML = `<span>${dataFormatada} às ${agendamento.horario.substring(0,5)}</span> - <a href="#" class="link-paciente" data-info="${infoJson}">${agendamento.paciente_info}</a>`;
                            listaProximos.appendChild(li);
                        });
                    }

                    if (listaRecentes) {
                        listaRecentes.innerHTML = "";
                        if (resultado.recentes.length === 0) {
                            listaRecentes.innerHTML = "<li>Nenhum paciente atendido recentemente.</li>";
                        } else {
                            resultado.recentes.forEach((agendamento, index) => {
                                const li = document.createElement("li");
                                if (index === 0) {
                                    li.innerHTML = `<strong>${agendamento.paciente_info}</strong> (Último)`;
                                } else {
                                    li.innerHTML = `<del>${agendamento.paciente_info}</del>`;
                                    li.style.color = "#888";
                                }
                                listaRecentes.appendChild(li);
                            });
                        }
                    }

                    document.querySelectorAll('.link-paciente').forEach(link => {
                        link.addEventListener('click', (e) => {
                            e.preventDefault();
                            const dados = JSON.parse(e.target.closest('a').getAttribute('data-info'));
                            const dataBR = dados.data_consulta.split('-').reverse().join('/');
                            alert(`📝 DETALHES DA SESSÃO\n\nPaciente: ${dados.paciente_info}\nData: ${dataBR}\nHorário: ${dados.horario.substring(0,5)}\nTipo: ${dados.tipo_atendimento === 'pacote' ? 'Pacote Mensal' : 'Sessão Avulsa'}\nInformações: ${dados.informacoes || 'Nenhuma.'}`);
                        });
                    });

                    document.querySelectorAll('.btn-concluir').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            const idAgendamento = e.target.getAttribute('data-id');
                            if(confirm("Deseja finalizar esta consulta?")) {
                                try {
                                    const resp = await fetch("../../backend/marcar_atendido.php", {
                                        method: "POST",
                                        headers: getAuthHeaders(),
                                        body: JSON.stringify({ id: idAgendamento })
                                    });
                                    if(resp.ok) {
                                        carregarDashboard(); 
                                    }
                                } catch(err) { alert("Erro ao concluir."); }
                            }
                        });
                    });

                }
            } catch (erro) {
                listaHoje.innerHTML = "<li>Erro de conexão com o banco.</li>";
            }
        }
        carregarDashboard();
    }

    // ==========================================
    // SISTEMA DE AVISOS (Salvos no navegador)
    // ==========================================
    if (listaAvisos) {
        let avisos = JSON.parse(localStorage.getItem("luna_avisos"));
        if (!avisos) {
            avisos = [
                { id: 1, texto: "João faltou à última consulta" },
                { id: 2, texto: "Maria pediu remarcação" },
                { id: 3, texto: "Paciente novo a aguardar cadastro" }
            ];
            localStorage.setItem("luna_avisos", JSON.stringify(avisos));
        }

        function renderizarAvisos() {
            listaAvisos.innerHTML = "";
            if (avisos.length === 0) {
                listaAvisos.innerHTML = "<li>Nenhum aviso pendente.</li>";
                return;
            }
            
            avisos.forEach(aviso => {
                const li = document.createElement("li");
                li.style.display = "flex";
                li.style.justifyContent = "space-between";
                li.style.alignItems = "center";
                li.style.marginBottom = "5px";
                
                li.innerHTML = `
                    <span>${aviso.texto}</span>
                    <button class="btn-excluir-aviso" data-id="${aviso.id}" title="Excluir aviso" style="background:none; border:none; color:red; cursor:pointer; font-weight:bold; font-size:1.1rem;">&times;</button>
                `;
                listaAvisos.appendChild(li);
            });

            document.querySelectorAll(".btn-excluir-aviso").forEach(btn => {
                btn.addEventListener("click", (e) => {
                    const idParaRemover = parseInt(e.target.getAttribute("data-id"));
                    avisos = avisos.filter(a => a.id !== idParaRemover);
                    localStorage.setItem("luna_avisos", JSON.stringify(avisos));
                    renderizarAvisos();
                });
            });
        }
        renderizarAvisos();
    }

// ==========================================
    // TELA DE CADASTRO E EDIÇÃO (listar.html)
    // ==========================================
    const formCadastro = document.querySelector("form[action='#'][method='POST']");
    const tituloCadastro = document.getElementById("titulo-cadastro");

    if (tituloCadastro && formCadastro) {
        
        const urlParams = new URLSearchParams(window.location.search);
        const idEditar = urlParams.get('editar');
        const btnSubmit = formCadastro.querySelector("button[type='submit']");

        
        if (idEditar) {
            tituloCadastro.textContent = "✏️ Editar Paciente";
            btnSubmit.textContent = "Salvar Alterações";

            
            async function carregarDadosParaEdicao() {
                try {
                    const resp = await fetch(`../../backend/buscar_paciente.php?id=${idEditar}`, {
                        headers: getAuthHeaders()
                    });
                    const res = await resp.json();
                    
                    if (resp.ok && res.sucesso) {
                        const p = res.paciente;
                        
                        formCadastro.elements['nome'].value = p.nome || '';
                        formCadastro.elements['nascimento'].value = p.data_nascimento || '';
                        formCadastro.elements['cpf'].value = p.cpf || '';
                        formCadastro.elements['telefone'].value = p.telefone || '';
                        formCadastro.elements['email'].value = p.email || '';
                        formCadastro.elements['endereco'].value = p.endereco || '';
                        formCadastro.elements['naturalidade'].value = p.naturalidade || '';
                        formCadastro.elements['escolaridade'].value = p.escolaridade || '';
                        formCadastro.elements['estado_civil'].value = p.estado_civil || '';
                        formCadastro.elements['religiao'].value = p.religiao || '';
                        formCadastro.elements['indicacao'].value = p.indicacao || '';
                        formCadastro.elements['observacoes'].value = p.observacoes || '';
                    } else {
                        alert("Erro ao carregar paciente.");
                        window.location.href = "agenda.html"; 
                    }
                } catch(err) {
                    console.error("Erro na busca", err);
                }
            }
            carregarDadosParaEdicao();
        }

        
        formCadastro.addEventListener("submit", async (e) => {
            e.preventDefault(); 
            
            const formData = new FormData(formCadastro);
            const dados = Object.fromEntries(formData.entries());
            
            
            if (idEditar) {
                dados.id = idEditar;
            }

            
            const endpoint = idEditar ? "../../backend/editar_paciente.php" : "../../backend/cadastrar_paciente.php";

            try {
                btnSubmit.textContent = "Aguarde...";
                btnSubmit.disabled = true;

                const resposta = await fetch(endpoint, {
                    method: "POST",
                    headers: getAuthHeaders(),
                    body: JSON.stringify(dados)
                });

                const resultado = await resposta.json();

                if (resposta.ok && resultado.sucesso) {
                    alert(resultado.mensagem);
                    
                    
                    if (idEditar) {
                        window.location.href = "agenda.html";
                    } else {
                        formCadastro.reset(); 
                    }
                } else {
                    alert("Erro: " + resultado.mensagem);
                }
            } catch (erro) {
                alert("Erro ao conectar com o servidor.");
            } finally {
                btnSubmit.textContent = idEditar ? "Salvar Alterações" : "Cadastrar";
                btnSubmit.disabled = false;
            }
        });
    }
 // ==========================================
    // TELA DE PACIENTES (agenda.html)
    // ==========================================
    const tabelaPacientes = document.querySelector(".tabela-pacientes tbody");
    const formFiltros = document.querySelector(".filtros");

    if (tabelaPacientes) {
        async function carregarPacientes(queryString = "") {
            try {
                tabelaPacientes.innerHTML = "<tr><td colspan='9' style='text-align:center;'>A carregar dados...</td></tr>";
                
                const url = `../../backend/listar_pacientes.php${queryString ? '?' + queryString : ''}`;
                
                const resposta = await fetch(url, {
                    method: "GET",
                    headers: getAuthHeaders()
                });

                const resultado = await resposta.json();

                if (resposta.ok && resultado.sucesso) {
                    tabelaPacientes.innerHTML = ""; 
                    
                    if (resultado.pacientes.length === 0) {
                        tabelaPacientes.innerHTML = "<tr><td colspan='9' style='text-align:center;'>Nenhum paciente encontrado.</td></tr>";
                        return;
                    }

                    resultado.pacientes.forEach(p => {
                        const tr = document.createElement("tr");
                        tr.innerHTML = `
                            <td>${String(p.prontuario).padStart(4, '0')}</td>
                            <td>${p.nome}</td>
                            <td>--</td>
                            <td>${p.data_nascimento.split('-').reverse().join('/')}</td>
                            <td>${p.cpf}</td>
                            <td>${p.telefone}</td>
                            <td>${p.data_cadastro.split(' ')[0].split('-').reverse().join('/')}</td>
                            <td><a href="agendar.html?paciente=${p.cpf}">Agendar</a></td>
                            <td>
                                <button type="button" class="btn-editar" data-id="${p.prontuario}" style="cursor:pointer;">Editar</button>
                                <button type="button" class="btn-excluir" data-id="${p.prontuario}" style="cursor:pointer; color:white; margin-left:5px;">Excluir</button>
                            </td>
                        `;
                        tabelaPacientes.appendChild(tr);
                    });

                    document.querySelectorAll('.btn-excluir').forEach(btn => {
                        btn.addEventListener('click', async (e) => {
                            const idPaciente = e.target.getAttribute('data-id');
                            if (confirm("Tem certeza que deseja excluir este paciente? Esta ação não pode ser desfeita.")) {
                                try {
                                    const resp = await fetch("../../backend/excluir_paciente.php", {
                                        method: "POST",
                                        headers: getAuthHeaders(),
                                        body: JSON.stringify({ id: idPaciente })
                                    });
                                    const res = await resp.json();
                                    
                                    if (resp.ok && res.sucesso) {
                                        alert(res.mensagem);
                                        carregarPacientes(window.location.search.substring(1));
                                    } else {
                                        alert("Erro: " + res.mensagem);
                                    }
                                } catch(err) {
                                    alert("Erro ao tentar excluir.");
                                }
                            }
                        });
                    });

                    document.querySelectorAll('.btn-editar').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const idPaciente = e.target.getAttribute('data-id');
                            window.location.href = `listar.html?editar=${idPaciente}`;
                        });
                    });

                } else {
                    tabelaPacientes.innerHTML = `<tr><td colspan='9' style='text-align:center; color: red;'>Erro: ${resultado.mensagem}</td></tr>`;
                }
            } catch (erro) {
                tabelaPacientes.innerHTML = "<tr><td colspan='9' style='text-align:center; color: red;'>Erro ao conectar com o servidor.</td></tr>";
            }
        }

        const paramsNaUrl = window.location.search.substring(1);
        carregarPacientes(paramsNaUrl);

        if (formFiltros) {
            formFiltros.addEventListener("submit", (e) => {
                e.preventDefault(); 
                const parametros = new URLSearchParams(new FormData(formFiltros)).toString();
                carregarPacientes(parametros);
            });
        }
    }
    // ==========================================
    // TELA DE AGENDAMENTO (agendar.html)
    // ==========================================
    const tituloAgendamento = document.getElementById("titulo-agendamento");
    const formAgendamento = document.querySelector(".form-agendamento");

    if (tituloAgendamento && formAgendamento) {
        formAgendamento.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(formAgendamento);
            const dados = Object.fromEntries(formData.entries());

            try {
                const btnSubmit = formAgendamento.querySelector(".btn-confirmar");
                btnSubmit.textContent = "A Agendar...";
                btnSubmit.disabled = true;

                const resposta = await fetch("../../backend/agendar_consulta.php", {
                    method: "POST",
                    headers: getAuthHeaders(),
                    body: JSON.stringify(dados)
                });

                const resultado = await resposta.json();

                if (resposta.ok && resultado.sucesso) {
                    alert(resultado.mensagem);
                    formAgendamento.reset();
                } else {
                    alert("Erro: " + resultado.mensagem);
                }
            } catch (erro) {
                alert("Erro de conexão ao tentar agendar.");
            } finally {
                const btnSubmit = formAgendamento.querySelector(".btn-confirmar");
                btnSubmit.textContent = "Confirmar Agendamento";
                btnSubmit.disabled = false;
            }
        });
    }

});