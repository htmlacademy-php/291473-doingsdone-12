<?php
function select_query($con, $sql, $type = 'all')
{
    mysqli_set_charset($con, "utf8");
    $result = mysqli_query($con, $sql) or trigger_error("Ошибка в запросе к базе данных: " . mysqli_error($con), E_USER_ERROR);

    if ($type == 'assoc') {
        return mysqli_fetch_assoc($result);
    }

    if ($type == 'row') {
        return mysqli_fetch_row($result)[0];
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function open_404_page()
{
    $page_content = include_template('page-404.php');
    $layout_content = include_template('layout.php', [
    'title' => 'doingsdone: страница не найдена',
    'content' => $page_content,
  ]);

    echo($layout_content);
    http_response_code(404);
    exit();
}