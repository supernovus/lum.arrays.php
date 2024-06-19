<?php

namespace Lum\Tests;

require_once 'vendor/autoload.php';

use Lum\Arrays as A;
use Lum\Test as LT;

$test = new LT();
$test->plan(28);

class Tester
{
  public LT $test;

  public $arrays = 
  [
    [1,2,2,5,7,2,1,2],
    ["foo", "bar", "baz", "foo"],
    [1,2,3,"1","2","3"],
    ["hello"=>"world", "goodbye"=>"world", "foo"=>"bar"],
  ];

  public $msgs =
  [
    'list'  => 'list modified as expected',
    'count' => 'returned expected count', 
  ];

  public bool $strict = true;

  public function __construct(LT $test)
  {
    $this->test = $test;
  }

  public function rm($a, $v, $er, $ea)
  {
    $t = $this->test;
    $msgs = $this->msgs;
    $array = $this->arrays[$a];

    $r = A::remove($array, $v, $this->strict);
    $an = 'A'.$a+1;
    $n = "remove($an, ".json_encode($v).", ".json_encode($this->strict).")";
    $t->is($r, $er, "$n {$msgs['count']}");
    $t->isJSON($array, $ea, "$n {$msgs['list']}");
  }

  public function rma($a, $vs, $er, $ea)
  {
    $t = $this->test;
    $msgs = $this->msgs;
    $array = $this->arrays[$a];

    A::$remove_all_strict = $this->strict;
    $r = A::remove_all($array, ...$vs);
    $an = 'A'.$a+1;
    $n = ($this->strict?'':'strict=false; ')
      ."remove_all($an, ...".json_encode($vs).")";
    $t->is($r, $er, "$n {$msgs['count']}");
    $t->isJSON($array, $ea, "$n {$msgs['list']}");
  }
}

$t = new Tester($test);

$t->rm(0, 2, 4, [1,5,7,1]);
$t->rm(0, "2", 0, $t->arrays[0]);
$t->strict = false;
$t->rm(0, "2", 4, [1,5,7,1]);

$t->strict = true;
$t->rm(1, "foo", 2, ["bar","baz"]);
$t->rm(2, 2, 1, [1,3,"1","2","3"]);
$t->strict = false;
$t->rm(2, 2, 2, [1,3,"1","3"]);
$t->strict = true;
$t->rm(3, "world", 2, ["foo"=>"bar"]);

$t->rma(0, [1,2], 6, [5,7]);
$t->rma(1, ["bar","baz"], 2, ["foo","foo"]);
$t->rma(2, [1,3], 2, [2,"1","2","3"]);
$t->strict = false;
$t->rma(2, [1,3], 4, [2,"2"]);
$t->strict = true;
$t->rma(3, ["world","bar"], 3, []);
$t->rma(3, ["invalid"], 0, $t->arrays[3]);
$t->rma(3, [], 0, $t->arrays[3]);

echo $test->tap();
return $test;
