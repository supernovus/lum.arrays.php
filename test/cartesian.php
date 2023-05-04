<?php 

namespace Lum\Tests;

require_once 'vendor/autoload.php';

use Lum\Arrays as A;

$t = new \Lum\Test();

$t->plan(2);

$src1 = 
[
  ['male','female','other'],
  ['< 19', '19-25', '25-50', '> 50'],
];

$want1 = [["male","< 19"],["male","19-25"],["male","25-50"],["male","> 50"],["female","< 19"],["female","19-25"],["female","25-50"],["female","> 50"],["other","< 19"],["other","19-25"],["other","25-50"],["other","> 50"]];

$res1 = A::cartesian_product($src1);

$t->isJSON($res1, $want1);

$src2 =
[
  ['male','female']
];

$res2 = A::cartesian_product($src2);

$want2 = [["male"],["female"]];

$t->isJSON($res2, $want2);

echo $t->tap();
return $t;

