//Скрипты применяемые во всех кабинетах - админа, компании и т.д.

//ajax поиск компании в шапке кабинета компании
$('#search_field').keyup(function () {
    var search = $(this).val();
    var type_search = $('#type_search').val();
    if (search.length > 2) {
        load_data(search, type_search);
        $('#reset_icon').css({ 'display': 'block' });
    } else if (search == '') {
        hide_search_result_list();
    }
});

$('#reset_icon').on('click', function () {
    hide_search_result_list();
});

function hide_search_result_list() {
    $('#search_result').delay(100).fadeOut(500);
    $('#search_result').html('');
    $('#search_field').val('');
    $('#reset_icon').hide('fast');
}

function load_data(query, type_search) {

    var data = {
        'search_field': query,
        'type_search': type_search
    };

    $.ajax({

        url: base_url + 'api/search/search_company',
        type: 'POST',
        dataType: 'JSON',
        data: data,
        cache: false,
        beforeSend: function (xhr) {
            $('#loader').show();
        },
        success: function (data) {
            $('#loader').hide();
            //вставляем хэш
            insert_csrf_hash(data);

            $('#search_result').css({ 'display': 'block' });
            $('#search_result').html(data.search_result);
        }

    });

}
