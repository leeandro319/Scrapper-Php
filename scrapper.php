<?php 
require "vendor/autoload.php";

use GuzzleHttp\Client;
use Sunra\PhpSimple\HtmlDomParser;

$client = new Client([
  'base_uri' => 'https://www.guiamais.com.br/',
  'verify' => false,
  'headers'=>[
      'User-Agent'=>'Mozilla/5.0 (X11; CrOS x86_64 8172.45.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.64 Safari/537.36',
  ]      
]);

$URL = "https://www.guiamais.com.br/encontre?searchbox=true&what=&where=Santo+Andr%C3%A9%2C+SP&page=1";

$html = $client->request("GET",$URL)->getBody();
$dom = HtmlDomParser::str_get_html($html);


foreach($dom->find("meta[itemprop=url]") as $link){
    $urlEmpresa = $link->content;

    $html = $client->request("GET", $urlEmpresa)->getBody();
    $domEmpresa = HtmlDomParser::str_get_html($html);

    $basicsInfo = $domEmpresa->find('div.basicsInfo', 0);
    $extendedInfo = $domEmpresa->find('div.extendedInfo', 0);

    $titulo = $basicsInfo->find('h1',0)->plaintext;
    $categorias = html_entity_decode(trim($basicsInfo->find('p.category',0)->plaintext));
   
    $endereco = preg_replace('/\s+/',' ',html_entity_decode(trim($extendedInfo->find('.advAddress',0)->plaintext)));    
    
    $telefones = [];

    foreach($extendedInfo->find('li.detail')as $li){
      $telefones[]=trim($li->plaintext);
    }

    echo $titulo.PHP_EOL.$categorias.PHP_EOL.$endereco.PHP_EOL;
    echo '<pre>';
    print_r($telefones);
    echo '</pre>';

    echo PHP_EOL.PHP_EOL.PHP_EOL.PHP_EOL;
  }