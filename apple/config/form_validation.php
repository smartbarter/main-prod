<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//правила валидации
$config = array(
    //валидация формы регистрации
    'registration_company' => array(
        array(
            'field' => 'company_name',
            'label' => 'Название компании',
            'rules' => 'trim|required|min_length[3]|max_length[255]',
        ),
        array(
            'field' => 'company_city',
            'label' => 'Город',
            'rules' => 'trim|required|min_length[3]|max_length[50]',
        ),
        array(
            'field' => 'company_phone',
            'label' => 'Телефон',
            'rules' => 'trim|required|min_length[3]|max_length[16]|numeric',
        ),
        // array(
        //     'field' => 'company_email',
        //     'label' => 'email',
        //     'rules' => 'trim|valid_email'
        // ),
        array(
            'field' => 'company_contact_name',
            'label' => 'Имя контактного лица',
            'rules' => 'trim|required|min_length[2]|max_length[150]',
        ),
//        array(
//            'field' => 'company_password',
//            'label' => 'Пароль',
//            'rules' => 'trim|required|min_length[6]|max_length[25]'
//        ),
        array(
            'field' => 'company_description',
            'label' => 'Описание деятельности компании',
            'rules' => 'trim|required|min_length[3]|max_length[3000]',
        ),
        array(
            'field' => 'barter_limit',
            'label' => 'Месячный заработок бартерных единиц',
            'rules' => 'trim|numeric|required',
        ),
        array(
            'field' => 'who_invited_company',
            'label' => 'Телефон пригласившей компании',
            'rules' => 'trim|min_length[3]|max_length[16]|numeric',
        ),
        array(
            'field' => 'barter_limit',
            'label' => 'бартерные единицы',
            'rules' => 'trim|numeric|required',
        ),
        array(
            'field' => 'accept_rules',
            'label' => 'Согласие с офертой',
            'rules' => 'trim|required',
        ),
        //скрытые поля формы
        array(
            'field' => 'name_city_company',
            'label' => 'Город',
            'rules' => 'trim|required|min_length[3]|max_length[50]',
        ),
        array(
            'field' => 'id_city_company_kladr',
            'label' => 'Город',
            'rules' => 'trim|numeric',
        ),
        array(
            'field' => 'zip_city_company',
            'label' => 'Город',
            'rules' => 'trim|numeric',
        ),
    ),

    //валидация формы авторизации
    'login_validation' => array(
        array(
            'field' => 'company_login_phone',
            'label' => 'Телефон',
            'rules' => 'trim|required|min_length[3]|max_length[16]|numeric',
        ),
        array(
            'field' => 'company_login_password',
            'label' => 'Пароль',
            'rules' => 'trim|required|min_length[6]|max_length[25]',
        ),
    ),

    //валидация формы восстановления пароля
    'recover_pass_validation_step_one' => array(
        array(
            'field' => 'recover_password_phone',
            'label' => 'Телефон',
            'rules' => 'trim|required|min_length[3]|max_length[16]|numeric',
        ),
    ),
    'recover_pass_calidation_step_two' => array(
        array(
            'field' => 'activation_code',
            'label' => 'Код восстановления',
            'rules' => 'trim|required|min_length[1]|max_length[6]|numeric',
        ),
    ),
    'recover_pass_calidation_step_three' => array(
        array(
            'field' => 'update_password',
            'label' => 'Пароль',
            'rules' => 'trim|required|min_length[6]|max_length[25]',
        ),
        array(
            'field' => 'user_pass_phone',
            'label' => 'Телефон',
            'rules' => 'trim|required|min_length[3]|max_length[16]|numeric',
        ),
    ),
    //валидация опции изменения работы компании при достижении лимита
    'work_sverh_limit' => array(
        array(
            'field' => 'sverh_limit',
            'label' => 'сверх лимита',
            'rules' => 'trim|numeric|integer|required',
        ),
    ),
    //валидация статуса сделки
    'update_deal_status' => array(
        array(
            'field' => 'status_deal',
            'label' => 'статус сделки',
            'rules' => 'trim|numeric|integer|required',
        ),
        array(
            'field' => 'deal_id',
            'label' => 'идентификатор сделки',
            'rules' => 'trim|numeric|integer|required',
        ),
        array(
            'field' => 'buyer_deal_id',
            'label' => 'идентификатор покупателя',
            'rules' => 'trim|required|min_length[15]|max_length[15]|alpha_numeric',
        ),
    ),
    //валидация суммы сделки
    'sum_new_deal' => array(
        array(
            'field' => 'sum_deal',
            'label' => 'сумма сделки',
            'rules' => 'trim|numeric|required',
        ),
        array(
            'field' => 'seller_deal_id',
            'label' => 'идентификатор продавца',
            'rules' => 'trim|required|min_length[15]|max_length[15]|alpha_numeric',
        ),
        array(
            'field' => 'comment_deal',
            'label' => 'комментарий к заказу',
            'rules' => 'trim|max_length[255]',
        ),
    ),
    //валидация суммы сделки
    'sum_new_deal_light' => array(
        array(
            'field' => 'sum_deal',
            'label' => 'сумма сделки',
            'rules' => 'trim|numeric|required',
        ),
        array(
            'field' => 'comment_deal',
            'label' => 'комментарий к заказу',
            'rules' => 'trim|max_length[255]',
        ),
        array(
            'field' => 'coupon',
            'label' => 'номер купона',
            'rules' => 'trim|numeric',
        ),
    ),

    //Валидация обновления пароля компании
    'update_company_data_password' => array(
        array(
            'field' => 'password1',
            'label' => 'Пароль',
            'rules' => 'trim|min_length[6]|max_length[25]',
        ),
        array(
            'field' => 'password2',
            'label' => 'Повтор пароля',
            'rules' => 'trim|min_length[6]|max_length[25]',
        ),
    ),

    //Валидация обновления лимита компании
    'update_company_data_limit' => array(
        array(
            'field' => 'sverh_limit',
            'label' => 'Сверх лимита',
            'rules' => 'trim|numeric|integer|required',
        ),
        array(
            'field' => 'month_limit',
            'label' => 'Лимит',
            'rules' => 'numeric|greater_than_equal_to[5000]',
        ),
    ),
    //Валидация обновления основных данных компании
    'update_company_data_light' => array(
        array(
            'field' => 'company_name',
            'label' => 'Название компании',
            'rules' => 'trim|required|min_length[3]|max_length[255]',
        ),
        array(
            'field' => 'contact_name',
            'label' => 'Имя контактного лица',
            'rules' => 'trim|required|min_length[2]|max_length[150]',
        ),
        array(
            'field' => 'contact_phone',
            'label' => 'Контактный телефон',
            'rules' => 'trim|min_length[3]|max_length[16]|numeric',
        ),
        array(
            'field' => 'company_adress',
            'label' => 'Адрес компании',
            'rules' => 'trim|min_length[3]|max_length[500]',
        ),
        array(
            'field' => 'city_name',
            'label' => 'Город',
            'rules' => 'trim|required|min_length[3]|max_length[50]',
        ),
    ),
    //валидация формы обновления данных компании
    'update_company_data' => array(
        array(
            'field' => 'company_name',
            'label' => 'Название компании',
            'rules' => 'trim|required|min_length[3]|max_length[255]',
        ),
        array(
            'field' => 'month_limit',
            'label' => 'Лимит',
            'rules' => 'numeric',
        ),
        array(
            'field' => 'contact_name',
            'label' => 'Имя контактного лица',
            'rules' => 'trim|required|min_length[2]|max_length[150]',
        ),
        array(
            'field' => 'contact_phone',
            'label' => 'Контактный телефон',
            'rules' => 'trim|min_length[3]|max_length[16]|numeric',
        ),
        array(
            'field' => 'company_adress',
            'label' => 'Адрес компании',
            'rules' => 'trim|min_length[3]|max_length[500]',
        ),
        array(
            'field' => 'company_site',
            'label' => 'Сайт компании',
            'rules' => 'trim|min_length[3]|max_length[255]|valid_url',
        ),
        array(
            'field' => 'password',
            'label' => 'Пароль',
            'rules' => 'trim|min_length[6]|max_length[25]',
        ),
    ),

    //валидация формы добавления манагера
    'new_manager_add' => array(
        array(
            'field' => 'manager_name',
            'label' => 'Имя менеджера',
            'rules' => 'trim|required|min_length[2]|max_length[150]',
        ),
        array(
            'field' => 'manager_phone',
            'label' => 'Телефон',
            'rules' => 'trim|required|min_length[3]|max_length[16]|numeric',
        ),
        array(
            'field' => 'manager_pass',
            'label' => 'Пароль',
            'rules' => 'trim|required|min_length[6]|max_length[25]',
        ),
    ),

);//общий массив