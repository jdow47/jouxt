<?php
session_start();
if (isset($_SESSION['nivel_admin']) && $_SESSION['nivel_admin'] == 0) {
    header("Location: ./clientes.php");
    exit();
}
require_once("menu.php");
?>
<h4 class="align-items-center d-flex justify-content-between mb-4 text-muted text-uppercase">
  LISTAR Categorias
  <button type="button" class="btn btn-outline-success fa-plus fas" onclick='modal_master("api/categorias.php", "add_categoria", "add")'></button>

</h4>

<!-- Filtros por tipo -->
<div class="filter-buttons d-flex gap-2 mb-3">
  <button class="btn btn-sm btn-outline-primary active" data-type="all">Todas</button>
  <button class="btn btn-sm btn-outline-primary" data-type="live">Canais</button>
  <button class="btn btn-sm btn-outline-primary" data-type="movie">Filmes</button>
  <button class="btn btn-sm btn-outline-primary" data-type="series">Séries</button>
</div>

<!-- Container para drag-and-drop por tipo (stack vertical responsivo) -->
<div id="sortable-container" class="d-none">
  <div class="row">
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Canais</h5>
        </div>
        <div class="card-body">
          <ul id="list-live" class="sortable-list" data-type="live"></ul>
        </div>
      </div>
    </div>
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Filmes</h5>
        </div>
        <div class="card-body">
          <ul id="list-movie" class="sortable-list" data-type="movie"></ul>
        </div>
      </div>
    </div>
    <div class="col-12 mb-4">
      <div class="card">
        <div class="card-header">
          <h5 class="mb-0">Séries</h5>
        </div>
        <div class="card-body">
          <ul id="list-series" class="sortable-list" data-type="series"></ul>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-end">
        <button id="save-order" class="btn btn-success btn-sm">Salvar ordem</button>
      </div>
    </div>
  </div>
</div>

<style>
.sortable-list {
  min-height: 100px;
  border: 1px dashed #ccc;
  padding: 10px;
  margin-bottom: 15px;
  list-style: none;
}

.categoria-item {
  background: #f8f9fa;
  padding: 8px 12px;
  margin: 5px 0;
  border-radius: 4px;
  cursor: move;
  border: 1px solid #dee2e6;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.categoria-item:hover {
  background: #e9ecef;
}

.sortable-ghost {
  opacity: 0.5;
  background: #007bff !important;
}

@media (max-width: 768px) {
  .filter-buttons {
    flex-wrap: wrap;
  }
  
  .filter-buttons .btn {
    margin-bottom: 5px;
  }
}
</style>

<table id="data_table" class="display overflow-auto table" style="width: 100%;">
  <thead class="table-dark">
    <tr><!--<th></th> descomentar para usar childs -->
      <th style="min-width: 75px;">#</th>
      <th>Nome</th>
      <th>Tipo</th>
      <th>adulto</th>
      <th>BG SSIPTV</th>
      <th style="min-width: 120px;">Ordem</th>
      <th style="min-width: 191px;">Ações</th>
    </tr>
  </thead>
</table>


<script src="//cdn.datatables.net/2.0.7/js/dataTables.js"></script>
<script src="//cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script src="./js/sweetalert2.js"></script>
<script src="./js/categorias.js?sfd"></script>
<script src="./js/custom.js"></script>

</div>
</main>

<!-- Modal master -->
<div class="modal fade" id="modal_master" tabindex="-1" aria-labelledby="modal_master" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="d-block modal-header" id="modal_master-header">
        <h5 class="float-start modal-title" id="modal_master-titulo"></h5>
        <button type="button" class="fa btn text-white fa-close fs-6 float-end" data-bs-dismiss="modal" aria-label="Close"></button>
        </button>
      </div>
      <form id="modal_master_form" onsubmit="event.preventDefault();" autocomplete="off">
        <div id="modal_master-body" class="modal-body overflow-auto" style="max-height: 421px;"></div>
        <div id="modal_master-footer" class="modal-footer"></div>
      </form>
    </div>
  </div>
</div>
<!-- Modal master Fim-->

</body>
</html>
