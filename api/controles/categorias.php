<?php

function delete_tudo($tb) {
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $resposta = []; 

    $sql = "SELECT * FROM admin WHERE id = :admin_id AND token = :token";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
    $stmt->bindParam(':token', $token, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->fetch(PDO::FETCH_ASSOC)) {

        if ($tb === 'canais') {
            $resposta['msg'] = "Todos os canais deletados com sucesso!";
            $sql_delete_streams = "DELETE FROM streams WHERE stream_type = 'live'";
            $sql_delete_categoria = "DELETE FROM categoria WHERE type = 'live'";
        } elseif ($tb === 'filmes') {
            $resposta['msg'] = "Todos os filmes deletados com sucesso!";
            $sql_delete_streams = "DELETE FROM streams WHERE stream_type = 'movie'";
            $sql_delete_categoria = "DELETE FROM categoria WHERE type = 'movie'";
        } elseif ($tb === 'series') {
            $resposta['msg'] = "Todas as séries deletadas com sucesso!";
            $sql_delete_series = "DELETE FROM series";
            $sql_delete_episodes = "DELETE FROM series_episodes";
            $sql_delete_seasons = "DELETE FROM series_seasons";
            $sql_delete_categoria = "DELETE FROM categoria WHERE type = 'series'";
        } else {
            $resposta['msg'] = "Todo o conteúdo limpo com sucesso!";
            $sql_delete_streams = "DELETE FROM streams";
            $sql_delete_series = "DELETE FROM series";
            $sql_delete_episodes = "DELETE FROM series_episodes";
            $sql_delete_seasons = "DELETE FROM series_seasons";
            $sql_delete_categoria = "DELETE FROM categoria";
        }

        if (isset($sql_delete_streams)) {
            $stmt_streams = $conexao->prepare($sql_delete_streams);
            $stmt_streams->execute();
        }
        if (isset($sql_delete_series)) {
            $stmt_series = $conexao->prepare($sql_delete_series);
            $stmt_series->execute();
        }
        if (isset($sql_delete_episodes)) {
            $stmt_episodes = $conexao->prepare($sql_delete_episodes);
            $stmt_episodes->execute();
        }
        if (isset($sql_delete_seasons)) {
            $stmt_seasons = $conexao->prepare($sql_delete_seasons);
            $stmt_seasons->execute();
        }
        if (isset($sql_delete_categoria)) {
            $stmt_categoria = $conexao->prepare($sql_delete_categoria);
            $stmt_categoria->execute();
        }

        $tabelas = ['streams', 'series', 'series_episodes', 'series_seasons', 'categoria'];
        foreach ($tabelas as $tabela) {
            $sql_check_empty = "SELECT COUNT(*) AS total FROM $tabela";
            $stmt_check_empty = $conexao->prepare($sql_check_empty);
            $stmt_check_empty->execute();
            $total = $stmt_check_empty->fetch(PDO::FETCH_ASSOC)['total'];

            if ($total == 0) {
                $sql_reset_ai = "ALTER TABLE $tabela AUTO_INCREMENT = 1";
                $stmt_reset_ai = $conexao->prepare($sql_reset_ai);
                $stmt_reset_ai->execute();
            }
        }

        $resposta['title'] = "Sucesso!";
        $resposta['icon'] = "success";
        $resposta['data_table'] = "atualizar";
    } else {
        $resposta['title'] = "Erro!";
        $resposta['msg'] = "Admin não autorizado ou token inválido.";
        $resposta['icon'] = "error";
    }
    return $resposta;
}

function add_categoria()
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT * 
            FROM admin 
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_add_categoria" value="">';
        $modal_body .= '<div class="form-group pb-2"> Nome:</div>';
        $modal_body .= '<input type="text" class="form-control" name="nome" value="">';

        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                                <label for="tipo">Tipo:</label>
                                <select class="form-select form-control" name="tipo">
                                    <option value="live"> Ao Vivo </option>
                                    <option value="movie"> Filmes </option>
                                    <option value="series"> Series </option>
                                </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                                <label for="adulto">Adulto:</label>
                                <select class="form-select form-control" name="adulto">
                                    <option value="0" selected> Não </option>
                                    <option value="1"> Sim </option>
                                </select>
                        </div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-group pb-2"> Background SSIPTV:</div>';
        $modal_body .= '<input type="text" class="form-control" name="gb_ssiptv" value="" placeholder="Foto de fundo para a categora no ssiptv">';   

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"categorias.php\")' class='btn btn-success waves-effect waves-light' >Adicionar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white m-2",
            'modal_titulo'=> "Adicionar Categorias",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_add_categoria($category_name, $type, $is_adult, $bg)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT * 
            FROM admin 
            WHERE id = :admin_id AND token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':token', $token);
        $stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        // CORREÇÃO: Adicionar ordem automaticamente
        try {
            // Buscar próxima ordem para o tipo
            $ordem_stmt = $conexao->prepare("SELECT COALESCE(MAX(ordem), 0) + 1 as proxima_ordem FROM categoria WHERE admin_id = :admin_id AND type = :type");
            $ordem_stmt->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            $ordem_stmt->bindParam(':type', $type, PDO::PARAM_STR);
            $ordem_stmt->execute();
            $ordem_result = $ordem_stmt->fetch(PDO::FETCH_ASSOC);
            $ordem = $ordem_result['proxima_ordem'];
        } catch (Exception $e) {
            $ordem = 1; // fallback
        }

        $sql_insert = "INSERT INTO categoria (nome, type, is_adult, bg, admin_id, ordem) 
                        VALUES (:category_name, :type, :is_adult, :bg, :admin_id, :ordem)";
        $stmt_insert = $conexao->prepare($sql_insert);

        $stmt_insert->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        $stmt_insert->bindParam(':type', $type, PDO::PARAM_STR); 
        $stmt_insert->bindParam(':is_adult', $is_adult, PDO::PARAM_INT); 
        $stmt_insert->bindParam(':admin_id', $admin_id, PDO::PARAM_INT); 
        $stmt_insert->bindParam(':bg', $bg, PDO::PARAM_STR);
        $stmt_insert->bindParam(':ordem', $ordem, PDO::PARAM_INT);

        if ($stmt_insert->execute()) {
            $lastInsertedId = $conexao->lastInsertId();

            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Categoria criada com sucesso";
            $resposta['icon'] = "success";

            return $resposta;
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao criar Categoria";
            $resposta['icon'] = "error";

            return $resposta;
        }

    } else {
        return 0;
    }
}

function delete_categorias($id, $name)
{

        $modal_body = "<input type=\"hidden\"  name=\"confirme_delete_categorias\" value='$id'></div>";
        $modal_body .= "<input type=\"hidden\"   name=\"name\" value='$name'></div>";
        $modal_body .= "Tem certeza de que deseja excluir a categoria ($name) ?";

        $modal_footer = "<button type='button' class='btn btn-primary btn-sm' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button><button type='button' class='btn btn-danger btn-sm' onclick='enviardados(\"modal_master_form\", \"categorias.php\")'>EXCLUIR</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-danger text-white m-2",
            'modal_titulo'=> "Excluir Categoria",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

    return $resposta;
}

function confirme_delete_categorias($id, $name)
{
    $conexao = conectar_bd();
    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";

    $sql = "SELECT c.*, a.id as admin_id
            FROM categoria c 
            LEFT JOIN admin a ON c.admin_id = a.id  
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);
        
        // CORREÇÃO: Exclusão segura com transação
        try {
            $conexao->beginTransaction();
            
            // Deletar conteúdos vinculados baseado no tipo
            if ($type === 'series') {
                // Deletar episódios e temporadas das séries desta categoria
                $conexao->prepare("DELETE FROM series_episodes WHERE category_id = ?")->execute([$id]);
                $conexao->prepare("DELETE FROM series_seasons WHERE category_id = ?")->execute([$id]);
                $conexao->prepare("DELETE FROM series WHERE category_id = ?")->execute([$id]);
            } else {
                // Deletar streams (canais e filmes) desta categoria
                $conexao->prepare("DELETE FROM streams WHERE category_id = ?")->execute([$id]);
            }

            // Deletar a categoria
            $sql_delete = "DELETE FROM categoria WHERE id = :id AND admin_id = :admin_id";
            $stmt_delete = $conexao->prepare($sql_delete);
            $stmt_delete->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt_delete->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);
            
            if ($stmt_delete->execute()) {
                $conexao->commit();
                $resposta['title'] = "Sucesso!";
                $resposta['msg'] = "Categoria e todos os conteúdos vinculados deletados com sucesso!";
                $resposta['icon'] = "success";
            } else {
                $conexao->rollBack();
                $resposta['title'] = "Erro!";
                $resposta['msg'] = "Erro ao deletar categoria.";
                $resposta['icon'] = "error";
            }
        } catch (Exception $e) {
            $conexao->rollBack();
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao deletar categoria: " . $e->getMessage();
            $resposta['icon'] = "error";
        }

        return $resposta;
    } else {
        return 0;
    }
}

function edite_categorias($id)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT c.*, a.id as admin_id
            FROM categoria c 
            LEFT JOIN admin a ON c.admin_id = a.id  
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();
    $tipo = ['live', 'movie', 'series'];
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        extract($row);
        $planoOptions = "";
        $planoOptions2 = "";

        foreach ($tipo as $tipos) {
            $label = '';
            switch ($tipos) {
                case 'live':
                    $label = 'Ao vivo';
                    break;
                case 'movie':
                    $label = 'Filmes';
                    break;
                case 'series':
                    $label = 'Séries';
                    break;
            }

            if ($tipos == $type) {
                $planoOptions .= '<option value="'.$type.'" selected> '.$label.' </option>';
            } else {
                $planoOptions2 .= '<option value="'.$tipos.'"> '.$label.' </option>';
            }
        }

        $modal_body = '';
        $modal_body .= '<input type="hidden" name="confirme_editar_categoria" value="'.$id.'">';
        $modal_body .= '<div class="form-group pb-2"> Nome:</div>';
        $modal_body .= '<input type="text" class="form-control" name="nome" value="'.$nome.'">';

        $modal_body .= '<div class="form-row row">';
            $modal_body .= '<div class="form-group col-md col">
                                <label for="tipo">Tipo:</label>
                                <select class="form-select form-control" name="tipo">
                                    '.$planoOptions.' '.$planoOptions2.'
                                </select>
                            </div>';
            $modal_body .= ' <div class="form-group col-md col">
                                <label for="adulto">Adulto:</label>
                                <select class="form-select form-control" name="adulto">
                                    <option value="'.$is_adult.'"">('. ($is_adult == 0 ? "NÃO" : "SIM") .')</option>';
            switch ($is_adult) {
                case "0":
                    $modal_body .= "<option value='1'>Mudar PARA ( SIM )</option>";
                    break;
                case "1":
                    $modal_body .= "<option value='0'>Mudar PARA ( NAO )</option>";
                    break;
            }
            $modal_body .= '</select></div>';
        $modal_body .= '</div>';

        $modal_body .= '<div class="form-group pb-2"> Background SSIPTV:</div>';
        $modal_body .= '<input type="text" class="form-control" name="gb_ssiptv" value="'.($bg ?? '').'" placeholder="Foto de fundo para a categora no ssiptv">';    

        $modal_footer = "<button type='button' onclick='enviardados(\"modal_master_form\", \"categorias.php\")' class='btn btn-success waves-effect waves-light' >Editar</button><button type='button' class='btn btn-danger' data-bs-dismiss='modal' aria-label='Close'>Cancelar</button>";

        $resposta = [
            'modal_header_class'=> "d-block modal-header bg-success text-white m-2",
            'modal_titulo'=> "Editar Categorias",
            'modal_body'=> $modal_body,
            'modal_footer'=> $modal_footer
        ];

        return $resposta;
    } else {
        return 0;
    }
}

function confirme_editar_categoria($id, $category_name, $type, $is_adult, $bg)
{
    $conexao = conectar_bd();

    $token = isset($_SESSION['token']) ? $_SESSION['token'] : "0";
    $admin_id = isset($_SESSION['admin_id']) ? $_SESSION['admin_id'] : null;

    $sql = "SELECT c.*, a.id as admin_id
            FROM categoria c 
            LEFT JOIN admin a ON c.admin_id = a.id  
            WHERE c.id = :id AND a.token = :token";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':token', $token);
        $stmt->execute();

    $resposta = []; 

    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

        $sql_update = "UPDATE categoria 
                       SET nome = :category_name, 
                           type = :type, 
                           is_adult = :is_adult, 
                           bg = :bg 
                       WHERE id = :category_id AND admin_id = :admin_id";
        $stmt_update = $conexao->prepare($sql_update);

        $stmt_update->bindParam(':category_name', $category_name, PDO::PARAM_STR);
        $stmt_update->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt_update->bindParam(':is_adult', $is_adult, PDO::PARAM_INT);
        $stmt_update->bindParam(':bg', $bg, PDO::PARAM_STR);
        $stmt_update->bindParam(':category_id', $id, PDO::PARAM_INT);
        $stmt_update->bindParam(':admin_id', $admin_id, PDO::PARAM_INT);

        if ($stmt_update->execute()) {
            // CORREÇÃO: Atualizar is_adult nos conteúdos vinculados
            try {
                $tabela = ($type === 'series') ? 'series' : 'streams';
                $sql_update_content = "UPDATE $tabela 
                           SET is_adult = :is_adult
                           WHERE category_id = :category_id";
                $stmt_update_content = $conexao->prepare($sql_update_content);
                $stmt_update_content->bindParam(':is_adult', $is_adult, PDO::PARAM_INT);
                $stmt_update_content->bindParam(':category_id', $id, PDO::PARAM_INT);
                $stmt_update_content->execute();
            } catch (Exception $e) {
                // Continuar mesmo se a atualização do conteúdo falhar
            }
            
            $resposta['title'] = "Concluído!";
            $resposta['msg'] = "Categoria atualizada com sucesso";
            $resposta['icon'] = "success";
        } else {
            $resposta['title'] = "Erro!";
            $resposta['msg'] = "Erro ao atualizar Categoria";
            $resposta['icon'] = "error";
        }

        return $resposta;

    } else {
        return 0;
    }
}
?>
