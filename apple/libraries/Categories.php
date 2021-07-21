<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Categories {

    public function change_id_in_array($categories) {
        $cat_arr = array();
        foreach($categories as $cat) {
            $cat_arr[$cat['category_id']] = $cat;
        }
        return $cat_arr;
    }

    //функция построения хлебных крошек
    public function breadcrumbs($array, $id) {

        $count = count($array);
        $breadcrumbs_array = array();
        for($i = 0; $i < $count; $i++){
            if(!empty($array[$id])){
                $breadcrumbs_array[$array[$id]['category_id']] = $array[$id]['category_title'];
                $id = $array[$id]['parent'];
            }else break;
        }
        return array_reverse($breadcrumbs_array, true);
    }

    //получаем дочерние категории
    public function cats_id($array, $cat_id) {

        $data = '';

        foreach($array as $item) {

            if($item['parent'] == $cat_id) {
                $data .= $item['category_id'] . ",";
                $data .= $this->cats_id($array, $item['category_id']);
            }

        }

        return $data;

    }

    //меняем идентификатор категории и сразу же строим дерево
    public function update_array_identificator($categories) {

        $cat_arr = array();
        foreach($categories as $cat) {
            $cat_arr[$cat['category_id']] = $cat;
        }

        $result_tree = $this->map_tree($cat_arr);

        return $result_tree;

    }
    //формируем дерево категорий и подкатегорий
    public function map_tree($dataset) {
        $tree = array();

        foreach ($dataset as $id=>&$node) {
            if (!$node['parent']){
                $tree[$id] = &$node;
            }else{
                $dataset[$node['parent']]['childs'][$id] = &$node;
            }
        }

        return $tree;
    }

    //делаем из массива строку
    public function categories_to_string($data) {
        $string = '';
        foreach($data as $item){
            $string .= $this->categories_to_template($item);
        }

        return $string;
    }
    public function categories_to_string_mobile($data) {
        $string = '';
        foreach($data as $item){
            $string .= $this->categories_to_template_mobile($item);
        }

        return $string;
    }

    //делаем шаблон строки, чтобы на выходе отдать уже готовую разметку
    private function categories_to_template($category) {

        ob_start();//буфферизуем данные

        //формируем шаблон
        //кусок от открывающего li

        //Написал какую-то х*йню
        if(isset($category["childs"])):
            echo '<div class="card">'; //Категория
            echo '<div class="card-header" id="headingOne">';
            echo '<div class="card-title collapsed" data-toggle="collapse" data-target="#collapseOne'. $category["category_id"] .'" aria-expanded="true" aria-controls="collapseOne1">';
            echo  $category["category_title"]  . '<span style="float: right"> ' .$category['count'] . '</span>';
            echo '</div>';
            echo '</div>';
            echo '<div id="collapseOne'. $category["category_id"] .'" class="collapse" aria-labelledby="headingOne"  style="">';
            echo '<div class="card-body">';
            echo $this->categories_to_string($category["childs"]);
            echo '</div>';
            echo '</div>';
            echo '</div>';



        else:
            echo '<div class="card">'; //Категория
            echo '<div class="card-header card-title" id="headingOne">';
            echo '<a class="card-title collapsed" href="' . site_url("company/category?id=" . $category["category_id"]) . '">';

            echo '<span>'. $category["category_title"] . ' <span class="float-right">(' . $category['count'] . ')</span></span>';
            echo '</a>';
            echo '</div>';
            echo '</div>';



        endif;

        echo '</li>';
        //но она работает

        return ob_get_clean();//отдаем данные и затем уже опустошаем буффер

    }

    //используется в админ-панели, в профиле компании
    public function cat_to_string_checkboxes($data, $company_cats) {

        $string = '';
        foreach($data as $item){

            $string .= $this->categories_with_checkbox_to_template($item, $company_cats);

        }

        return $string;
    }

    private function categories_with_checkbox_to_template($category, $company_cats) {

        ob_start();//буфферизуем данные

        //формируем шаблон

        echo '<li class="';

        if(isset($category["childs"])):
            echo 'have_subcategorie';
        else: echo 'not_cat';
        endif;


        echo '">';//кусок от открывающего li

        echo '<input type="checkbox" name="category_id" id="category_id_' . $category["category_id"] . '" value="' . $category["category_id"] . '"';
        if(!empty($company_cats)) {
            for($i = 0, $iMax = count($company_cats); $i < $iMax; $i++) {
                if($company_cats[$i]["category_id"] == $category["category_id"]) {
                    echo "checked";
                }
            }
        }
        echo '>';
        echo sprintf('<label for="category_id_%s">%s (%s)</label>', $category['category_id'], $category['category_title'], $category['count']);
//        echo ' <label for="category_id_' . $category["category_id"] . '">' . $category["category_title"] . ')</label>';
        if(isset($category["childs"])):

            echo '<ul class="cats">';
            echo $this->cat_to_string_checkboxes($category["childs"], $company_cats);
            echo '</ul>';

        endif;

        echo '</li>';

        return ob_get_clean();//отдаем данные и затем уже опустошаем буффер

    }

    private function categories_to_template_mob($category)
    {
        ob_start();//буфферизуем данные

        if (isset($category["childs"])) {
            echo '<div class="collapsible-header" tabindex="0">';
            echo sprintf('<li><span>%s<span class="float-right"><i class="fa fa-angle-down" aria-hidden="true"></i>(%s)</span></span></li>',
                $category['category_title'],
                $category['count']);
        }
        else {
            echo sprintf('<li><span>%s<span class="float-right">(%s)</span></span></li>',
                $category['category_title'],
                $category['count']);
        }

        if (isset($category["childs"]))
        {
            echo '</div>';
            echo '<div class="collapsible-body">';
            echo '<ul class="list-group">';
            echo $this->cat_to_string($category["childs"]);
            echo "</ul>";
            echo '</div>';
        }

        return ob_get_clean();//отдаем данные и затем уже опустошаем буффер
    }

    public function cat_to_string($cat_list, $prefix)
    {
        $strings = ['top' => '', 'bottom' => ''];
        $counter = 0;
        foreach($cat_list as $item){
            $res = $this->categories_to_template_mobile($item, $counter, $prefix);
            $strings['top'] .= $res['top'];
            $strings['bottom'] .= $res['bottom'];
            $counter++;
        }
        return $strings;
    }

    private function categories_to_template_mobile($category, $num, $prefix = '0')
    {
        $data_menu = "action-share-list-$prefix-$num";

        if (isset($category["childs"]))
            $href = sprintf('<a href="#" data-menu="%s">', $data_menu);
        else
            $href = '<a href="' . site_url("company/category?id=" . $category["category_id"]) . '">';

        $top =  sprintf('
                <div class="col">
                    %s
                    <div class="category-box round-medium shadow-tiny bottom-15">
                        <span class="font-900">%s</span>
                        <span style="color: #6c6c6c">%s</span>
                    </div>
                    </a>
                </div>',
            $href,
            $category['category_title'],
            $category['count']);

        if (isset($category["childs"])) {
            $cats = '';
            foreach ($category["childs"] as $child) {
                $cats .= sprintf('<a href="%s" ><span>%s (%s)</span><i class="fa fa-angle-right"></i></a>',
                    site_url("company/category?id=" . $child["category_id"]),
                    $child['category_title'],
                    $child['count']);
            }
            $bottom = sprintf('
<div id="%s"
     class="menu-box menu-box-detached round-medium"
     data-menu-type="menu-box-bottom"
     data-menu-height="400"
     data-menu-effect="menu-parallax">

    <div class="page-title has-subtitle">
        <div class="page-title-left">
            <a href="#">%s</a>
        </div>
        <div class="page-title-right">
            <a href="#" class="close-menu"><i class="fa fa-times-circle font-20 color-red2-dark"></i></a>
        </div>
    </div>

    <div class="content bottom-0">
        <div class="link-list link-list-1">
        %s
        </div>
    </div>
</div>',
                $data_menu,
                $category['category_title'],
                $cats);
        }

        return ['top' => $top, 'bottom' => $bottom];
    }

}