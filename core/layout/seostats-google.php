<?php
/*
 * LibreCMS - Copyright (C) Diemen Design 2018
 * This software may be modified and distributed under the terms
 * of the MIT license (http://opensource.org/licenses/MIT).
 */
if(!defined('DS'))define('DS', DIRECTORY_SEPARATOR);
require_once realpath('..'.DS.'SEOstats'.DS.'bootstrap.php');
require'..'.DS.'db.php';
$id=isset($_POST['id'])?filter_input(INPUT_POST,'id',FILTER_SANITIZE_NUMBER_INT):filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
$u=isset($_POST['u'])?filter_input(INPUT_POST,'t',FILTER_SANITIZE_STRING):filter_input(INPUT_GET,'u',FILTER_SANITIZE_STRING);
$t=isset($_POST['t'])?filter_input(INPUT_POST,'t',FILTER_SANITIZE_STRING):filter_input(INPUT_GET,'t',FILTER_SANITIZE_STRING);
$config=$db->query("SELECT seoKeywords FROM config WHERE id=1")->fetch(PDO::FETCH_ASSOC);
if($t=='menu'){
  $s=$db->prepare("SELECT seoKeywords FROM menu WHERE id=:id");
  $s->execute(array(':id'=>$id));
  $r=$s->fetch(PDO::FETCH_ASSOC);
  $k=$r['seoKeywords'];
}elseif($t=='content'){
  $s=$db->prepare("SELECT seoKeywords FROM content WHERE id=:id");
  $s->execute(array(':id'=>$id));
  $r=$s->fetch(PDO::FETCH_ASSOC);
  $k=$r['seoKeywords'];
}else$k=$config['seoKeywords'];
$k=str_replace(',',' ',$k);
use \SEOstats\Services\Google as Google;
$pr=Google::getPageRank($u);
$ps=Google::getPagespeedAnalysis($u);
$si=Google::getSiteindexTotal($u);
$bl=Google::getBacklinksTotal($u);
echo'<div class="row"><div class="col-xs-12 col-sm-3"><div class="panel panel-default"><div class="panel-body"><span class="text-black" style="font-size:1">Page Rank:<span id="google-pagerank" class="pull-right">'.$pr.'</span></span></div></div></div><div class="col-xs-12 col-sm-3"><div class="panel panel-default"><div class="panel-body"><span class="text-black" style="font-size:1">Page Speed:<span id="google-pagespeed" class="pull-right">'.$ps.'</span></span></div></div></div><div class="col-xs-12 col-sm-3"><div class="panel panel-default"><div class="panel-body"><span class="text-black" style="font-size:1">Pages Indexed:<span id="google-indexed" class="pull-right">'.$si.'</span></span></div></div></div><div class="col-xs-12 col-sm-3"><div class="panel panel-default"><div class="panel-body"><span class="text-black" style="font-size:1em">Total Back Links:<span id="google-indexed" class="pull-right">'.$bl.'</span></span></div></div></div></div>';
if($k!=''){
echo'<legend class="control-legend">Keywords used for the below results:<small> '.str_replace($k).'</small></legend><table class="table table-condensed"><thead><tr><th class="col-xs-2 text-center">Result Order</th><th>Site</th></tr></thead><tbody>';
$serps=Google::getSerps($k);
$i=1;
foreach($serps as$seo){
  echo'<tr'.($u==$seo['url']?' class="bg-success"':'').'><td class="text-center">'.$i.'</td><td><a target="_blank" href="'.$seo['url'].'" rel="nofollow">'.$seo['headline'].'</a></td></tr>';
  $i++;
}
echo'</tbody></table>';
}else
echo'<legend class="control-legend">No Keywords provided for Search Results</legend>';
