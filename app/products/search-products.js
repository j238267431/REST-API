jQuery(function($){

    // когда была нажата кнопка «Поиск товаров»
    $(document).on('submit', '#search-product-form', function(){

        // получаем ключевые слова для поиска
        var keywords = $(this).find(":input[name='keywords']").val();

        // получаем данные из API на основе поисковых ключевых слов
        $.getJSON("http://localhost:8888/product/search.php?s=" + keywords, function(data){

            // шаблон в products.js
            readProductsTemplate(data, keywords);

            // изменяем title
            changePageTitle("Поиск товаров: " + keywords);

        }).fail(function(jqXHR, textStatus, errorThrown) { console.log(textStatus); console.log(errorThrown); })

        // предотвращаем перезагрузку всей страницы
        return false;
    });

});