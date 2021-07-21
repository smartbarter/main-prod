<?php

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 'On');
ini_set('avito_error.log', $_SERVER['DOCUMENT_ROOT'].'/php_errors.log');

define('DB_HOST','94.130.71.207');
define('DB_DB','wm25573_euroavt');
define('DB_PASS','euroavt');
define('DB_LOGIN','wm25573_euroavt');
define('DB_PORT',3306);
define('DB_PREFIX', 'oc_');
$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASS,DB_DB,DB_PORT);
if (!$link) return false;
//товары
$sqlProducts = "SELECT product_id, price, image, model FROM oc_product ORDER BY price DESC LIMIT 10";
if($resultProducts = mysqli_query($link, $sqlProducts)){
    $i=0;
    while($rowProducts = mysqli_fetch_array($resultProducts)){
        $i++;
//название товара
        $sqlProductInfo = "SELECT description, name FROM oc_product_description WHERE product_id=".$rowProducts['product_id'];
        $resultProductInfo = mysqli_query($link, $sqlProductInfo);
        $rowProductInfo = mysqli_fetch_array($resultProductInfo);
//        фотографии товара
        $sqlProductImg = "SELECT image FROM oc_product WHERE product_id=".$rowProducts['product_id'];
        $resultProductImg = mysqli_query($link, $sqlProductImg);
        $rowProductImg = mysqli_fetch_array($resultProductImg);

echo $rowProduct['image'];
echo $rowProductImg['image'];
echo "<br>";

        $products[$i]['Id']=$rowProducts['product_id'];
        $products[$i]['TypeId']='6-406';
        $products[$i]['Title']=$rowProductInfo['name'];
        $products[$i]['Decription']=$rowProductInfo['description'];
        $products[$i]['Price']=$rowProducts['price'];
//        $products[$i]['']=$row[];
//        $products[$i]['']=$row[];
        }
} else{
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}










?>