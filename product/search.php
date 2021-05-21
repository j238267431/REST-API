<?php
// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// подключение необходимых файлов
include_once '../config/core.php';
include_once '../config/database.php';
include_once '../objects/product.php';
include_once '../shared/utilities.php';

$utilities = new Utilities();
// создание подключения к БД
$database = new Database();
$db = $database->getConnection();

// инициализируем объект
$product = new Product($db);

// получаем ключевые слова
$keywords=isset($_GET["s"]) ? $_GET["s"] : "";

// запрос товаров
$stmt = $product->search($keywords, $from_record_num, $records_per_page);
$num = $stmt->rowCount();

// проверяем, найдено ли больше 0 записей
if ($num>0) {

    // массив товаров
    $products_arr=array();
    $products_arr["records"]=array();

    // получаем содержимое нашей таблицы
    // fetch() быстрее чем fetchAll()
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        // извлечём строку
        extract($row);

        $product_item=array(
            "id" => $id,
            "name" => $name,
            "description" => html_entity_decode($description),
            "price" => $price,
            "category_id" => $category_id,
            "category_name" => $category_name
        );

        array_push($products_arr["records"], $product_item);
    }

    // подключим пагинацию
    $total_rows=$product->countSearch($keywords);
    $page_url="{$home_url}product/search.php?";
    $paging=$utilities->getPaging($page, $total_rows, $records_per_page, $page_url, $keywords);
    $products_arr["paging"]=$paging;
    $products_arr["keywords"]="";
    if(!empty($keywords)){
        $products_arr["keywords"]=$keywords;
    }


    // код ответа - 200 OK
    http_response_code(200);

    // покажем товары
    echo json_encode($products_arr);
}

else {
    // код ответа - 404 Ничего не найдено
    http_response_code(404);

    // скажем пользователю, что товары не найдены
    echo json_encode(array("message" => "Товары не найдены."), JSON_UNESCAPED_UNICODE);
}