<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScrapeController extends Controller
{
    public function scrape(){
        
        // scraping from https://www.beautyhaul.com/product/skincare/all

        $httpClient = new Client();
        $response = $httpClient -> request('GET', 'https://www.beautyhaul.com/product/skincare/all');

        // using CSS Selector instead of XPath
        // $prices = [];   
        // $response -> filter('div[class="row products mb-0"] div a div[class="caption"] div[class="price"] span') -> each(function ($node) use (&$prices){
        //     $prices[] = $node -> text();
        // });
        
        $productNames = [];
        $response -> filter('div[class="row products mb-0"] div a div[class="caption"] div[class="title"]') -> each(function ($node) use (&$productNames){
            $productNames[] = $node -> text();
        });

        $imageUrls = [];
        $response -> filter('div[class="row products mb-0"] div a div[class="image"] picture img') -> each(function ($node) use (&$imageUrls){
            $imageUrls[] = $node -> extract(array('src'))[0];
        });

        $brands = [];
        $response -> filter('div[class="row products mb-0"] div a div[class="caption"] div[class="brand"]') -> each(function ($node) use (&$brands){
            $brands[] = $node -> text();
        });
        
        $descriptions = [];
        $response -> filter('div[class="row products mb-0"] div a') -> each(function ($node) use (&$descriptions, $httpClient){
            $descriptions[] = $httpClient -> click($node->link()) -> filter('.col-xxl-15 div div[class="accordion-item open"] div[class="text col-sm-13 offset-sm-4"] div[class="mb-4"]') -> text();
        });

        // $productIngredients = [[]];
        // $response -> filter('div[class="row products mb-0"] div a') -> each(function ($node) use (&$productIngredients, $httpClient){
        //     // $productIngredients[] = $httpClient -> click($node->link()) -> filter('.ing-tags div') -> each(function ($innernode) use (&$productIngredients){
        //     //     $productIngredients[[]] = $innernode -> text();
        //     // });
        //     $productIngredients[] = $httpClient -> click($node->link()) -> filter('.ing-tags div') -> text();
        // });

        foreach($productNames as $key => $productName){
            $ingredients = [];
            $ingredientsString = "";
            // foreach($productIngredients[$key] as $ingrs){
            //      $ingredients = $ingrs;
            //      foreach($ingredients as $ingr){
            //          $ingredientsString . $ingr . '<br>';
            //      }
            // }
            echo 'PRODUCT ' . $productName . '<br>FROM ' . $brands[$key] . '<br>IMAGE URL ' . $imageUrls[$key] . '<br>DESCRIPTION<br>' . $descriptions[$key] . '<br>INGREDIENTS: ' . $ingredientsString . '<br><br><br>'; 
        }

    }
}
