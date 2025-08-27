$(document).ready(function() {
    var table = $('#data_table').DataTable({
        ajax: {
            url: './api/categorias.php?listar_categorias',
            dataSrc: 'data',
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', xhr.responseText);
                alert('Erro ao carregar dados: ' + xhr.responseText);
            }
        },
        processing: true,
        serverSide: false, // Desabilitar serverSide temporariamente para debug
        language: {
            url: './js/datatables/pt_br.json'
        },
        layout: {
            topStart: null,
            bottom: 'paging',
            bottomStart: "info",
            bottomEnd: null
        },
        columns: [
            {
                data: "category_id",
                className: "text-center"
            },
            {
                data: "category_name",
                className: "text-center"
            },
            {
                orderable: false,
                data: "type",
                className: "text-center"
            },
            {
                data: "is_adult",
                className: "text-center"
            },
            {
                orderable: false,
                data: "bg",
                className: "text-center"
            },
            {
                orderable: false,
                data: null,
                className: "text-center",
                render: function(data, type, row, meta) {
                    return meta.row + 1; // Posição na tabela (1, 2, 3...)
                }
            },
            {
                orderable: false,
                data: "acao",
                className: "text-center acao"
            }
        ],
        order: [[0, 'asc']]
    });

    // Filtro por tipo na DataTable
    $(document).on('click', '.filter-buttons button', function() {
        $('.filter-buttons button').removeClass('active');
        $(this).addClass('active');
        var type = $(this).data('type');
        if (type === 'all') {
            table.column(2).search('').draw();
        } else if (type === 'live' || type === 'movie' || type === 'series') {
            table.column(2).search('^' + type + '$', true, false).draw();
        }
    });

    // Carregar listas para drag-and-drop
    async function carregarListasOrdenacao() {
        try {
            const resp = await fetch('./api/categorias.php?listar_categorias');
            const json = await resp.json();
            const data = json.data || [];
            const lists = {
                live: document.getElementById('list-live'),
                movie: document.getElementById('list-movie'),
                series: document.getElementById('list-series')
            };
            Object.values(lists).forEach(ul => { if (ul) ul.innerHTML = ''; });
            data.forEach(item => {
                const li = document.createElement('li');
                li.className = 'categoria-item';
                li.setAttribute('data-id', item.category_id);
                li.innerHTML = `
                    <span>${item.category_name}</span>
                    <button class="btn btn-sm btn-outline-secondary" onclick="modal_master('api/categorias.php', 'edite_categorias', '${item.category_id}')">
                        Editar
                    </button>
                `;
                if (lists[item.type]) {
                    lists[item.type].appendChild(li);
                }
            });

            ['list-live','list-movie','list-series'].forEach(id => {
                const el = document.getElementById(id);
                if (el && !el._sortableInit) {
                    Sortable.create(el, {
                        animation: 150,
                        ghostClass: 'bg-light',
                        group: { name: el.dataset.type, put: false, pull: false }
                    });
                    el._sortableInit = true;
                }
            });
        } catch (e) {
            console.error(e);
            SweetAlert2('Erro', 'Falha ao carregar categorias para ordenação', 'error');
        }
    }

    carregarListasOrdenacao();
    $('#sortable-container').removeClass('d-none');

    // Salvar ordem consolidada por tipo
    $('#save-order').on('click', async function() {
        const payload = {};
        ['list-live','list-movie','list-series'].forEach(id => {
            const el = document.getElementById(id);
            if (!el) return;
            const type = el.dataset.type;
            payload[type] = Array.from(el.querySelectorAll('li')).map((li, idx) => ({ id: li.getAttribute('data-id'), ordem: idx + 1 }));
        });
        try {
            const resp = await fetch('./api/salvar-ordem.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const json = await resp.json();
            if (json.icon === 'success') {
                SweetAlert2('Sucesso', json.msg || 'Ordem salva com sucesso', 'success');
                table.ajax.reload(null, false);
            } else {
                SweetAlert2('Erro', json.msg || 'Falha ao salvar ordem', 'error');
            }
        } catch (e) {
            console.error(e);
            SweetAlert2('Erro', 'Falha ao salvar ordem', 'error');
        }
    });

    // Função para mover categoria (up/down)
    window.moverCategoria = function(id, direcao) {
        $.post('./api/mover-categoria.php', {
            id: id,
            direcao: direcao
        })
        .done(function(response) {
            if(response.success) {
                $('#data_table').DataTable().ajax.reload(null, false);
                SweetAlert2('Sucesso', 'Ordem atualizada com sucesso', 'success');
            } else {
                SweetAlert2('Erro', response.message || 'Falha ao mover categoria', 'error');
            }
        })
        .fail(function() {
            SweetAlert2('Erro', 'Falha na comunicação com o servidor', 'error');
        });
    };
});