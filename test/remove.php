<?php

namespace Lum\Tests;

require_once 'vendor/autoload.php';

use Lum\Arrays as A;

$t = new \Lum\Test();

$t->plan(28);

$arrays = 
[
  [1,2,2,5,7,2,1,2],
  ["foo", "bar", "baz", "foo"],
  [1,2,3,"1","2","3"],
  ["hello"=>"world", "goodbye"=>"world", "foo"=>"bar"],
];

$msgs =
[
  'list'  => 'list modified as expected',
  'count' => 'returned expected count', 
];

$strict = true;

function rm($a, $v, $er, $ea)
{
  global $arrays, $msgs, $strict, $t;
  $array = $arrays[$a];
  $r = A::remove($array, $v, $strict);
  $an = 'A'.$a+1;
  $n = "remove($an, ".json_encode($v).", ".json_encode($strict).")";
  $t->is($r, $er, "$n ${msgs['count']}");
  $t->isJSON($array, $ea, "$n ${msgs['list']}");
}

function rma($a, $vs, $er, $ea)
{
  global $arrays, $msgs, $strict, $t;
  $array = $arrays[$a];
  A::$remove_all_strict = $strict;
  $r = A::remove_all($array, ...$vs);
  $an = 'A'.$a+1;
  $n = ($strict?'':'strict=false; ')
    ."remove_all($an, ...".json_encode($vs).")";
  $t->is($r, $er, "$n ${msgs['count']}");
  $t->isJSON($array, $ea, "$n ${msgs['list']}");
}

rm(0, 2, 4, [1,5,7,1]);
rm(0, "2", 0, $arrays[0]);
$strict = false;
rm(0, "2", 4, [1,5,7,1]);

$strict = true;
rm(1, "foo", 2, ["bar","baz"]);
rm(2, 2, 1, [1,3,"1","2","3"]);
$strict = false;
rm(2, 2, 2, [1,3,"1","3"]);
$strict = true;
rm(3, "world", 2, ["foo"=>"bar"]);

rma(0, [1,2], 6, [5,7]);
rma(1, ["bar","baz"], 2, ["foo","foo"]);
rma(2, [1,3], 2, [2,"1","2","3"]);
$strict = false;
rma(2, [1,3], 4, [2,"2"]);
$strict = true;
rma(3, ["world","bar"], 3, []);
rma(3, ["invalid"], 0, $arrays[3]);
rma(3, [], 0, $arrays[3]);

echo $t->tap();
return $t;
