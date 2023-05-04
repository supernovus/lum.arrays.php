<?php

namespace Lum;

/**
 * Some useful static utility functions that work on Arrays.
 */
class Arrays
{
  /**
   * Swap the positions of elements in an array.
   *
   * @param array   &$array      The array to operate on.
   * @param mixed   $pos1        First position or key to swap.
   * @param mixed   $pos2        Second position or key to swap.
   *
   * @return void
   */
  public static function swap (array &$array, $pos1, $pos2)
  {
    $temp = $array[$pos2];
    $array[$pos2] = $array[$pos1];
    $array[$pos1] = $temp;
  }

  /**
   * Rename an array key.
   *
   * @param array   &$array     The array to operate on.
   * @param string  $curname    Current key name.
   * @param string  $newname    New key name.
   * @param bool    $overwrite  Optional, overwrite existing keys.
   *
   * @return bool  `true` on success, `false` on failure.
   */
  public static function rename_key 
  ( array &$array, 
    string $curname, 
    string $newname, 
    bool $overwrite=false): bool
  {
    if (array_key_exists($oldname, $array))
    {
      if ($overwrite || !isset($array[$newname]))
      {
        $array[$newname] = $array[$curname];
        unset($array[$curname]);
        return true;
      }
    }
    return false;
  }

  /**
   * Remove a value from an array.
   *
   * Every matching instance of the value will be removed.
   * For associative arrays it uses `unset()`.
   * For flat lists it uses `array_splice()`.
   *
   * @param  array  &$array  Array to remove the value from.
   * @param  mixed  $value   Value to remove from array.
   * @param  bool   $strict  Use strict comparisons.
   *                         Default: `true`.
   *
   * @return int  The number of values removed.
   */
  public static function remove
  (
    array &$array, 
    mixed $value, 
    bool $strict=true): int
  {
	  $removed = 0;

    if (array_is_list($array))
    {
      while (($key = array_search($value, $array, $strict)) !== false)
      {
        array_splice($array, $key, 1);
        ++$removed;
      }
    }
    else
    {
      foreach(array_keys($array, $value, $strict) as $key)
      {
        unset($array[$key]);
        ++$removed;
      }
    }

    return $removed;
  }

  /**
   * Should `remove_all()` use strict checking? Default: `true`
   */
  public static bool $remove_all_strict = true;

  /**
   * Remove multiple values from an array.
   *
   * This is a wrapper around the `remove()` function, and calls
   * it for each value passed.
   *
   * @param array  &$array  The array to remove values from.
   * @param mixed  ...$values  Values to remove from the array.
   *
   * @return int  The total number of values removed.
   *              May be more than the number of `$values` passed if
   *              any of those values were found more than once.
   *              May also be less if some of those values weren't found.
   *
   */
  public static function remove_all(array &$array, ...$values): int
  {
    $strict = self::$remove_all_strict;
    $removed = 0;
    foreach ($values as $value)
    {
      $removed += self::remove($array, $value, $strict);
    }
    return $removed;
  }

  /**
   * Generate a Cartesian product from a set of arrays.
   *
   * Taken from http://www.theserverpages.com/php/manual/en/ref.array.php
   * Reformatted to fit with Lum.
   *
   * @param array  $arrays An array of arrays to get the product of.
   *
   * @return array  An array of arrays representing the product.
   */
  public static function cartesian_product($arrays) 
  {
    //returned array...
    $cartesic = [];
   
    //calculate expected size of cartesian array...
    $size = (sizeof($arrays)>0) ? 1 : 0;
    foreach ($arrays as $array)
    {
      $size = $size * sizeof($array);
    }
    for ($i=0; $i<$size; $i++) 
    {
      $cartesic[$i] = [];
       
      for ($j=0; $j<sizeof($arrays); $j++)
      {
        $current = current($arrays[$j]); 
        array_push($cartesic[$i], $current);    
      }
      // Set cursor on next element in the arrays, beginning with the last array
      for ($j=(sizeof($arrays)-1); $j>=0; $j--)
      {
        //if next returns true, then break
        if (next($arrays[$j])) 
        {
          break;
        } 
        else 
        { // If next returns false, then reset and go on with previous array.
          reset($arrays[$j]);
        }
      }
    }
    return $cartesic;
  }

  /**
   * Generate a set of subsets of a fixed size.
   *
   * Based on example from:
   * stackoverflow.com/questions/7327318/power-set-elements-of-a-certain-length
   *
   * @param array  $array      Array to find subsets of.
   * @param int    $size       Size of subsets we want.
   *
   * @return array  An array of subsets.
   */
  public static function subsets ($array, $size)
  {
    if (count($array) < $size) return [];
    if (count($array) == $size) return [$array];

    $x = array_pop($array);
    if (is_null($x)) return [];

    return array_merge
    ( 
      self::subsets($array, $size), 
      self::merge_into_each($x, self::subsets($array, $size-1))
    );
  }

  /**
   * Merge an item into a set of arrays.
   *
   * A part of subsets(), taken from same example.
   *
   * @param mixed  $x       Item to merge into arrays.
   * @param array  $arrays  Array of arrays to merge $x into.
   *
   * @return array  A copy of `$arrays`, with merged data.
   */
  public static function merge_into_each ($x, $arrays)
  {
    foreach ($arrays as &$array) array_push($array, $x);
    return $arrays;
  }

  /**
   * Generate a powerset.
   *
   * Generate an array of arrays representing the powerset of elements
   * from the original array.
   *
   * Based on code from:
   * http://bohuco.net/blog/2008/11/php-arrays-power-set-and-all-permutations/
   *
   * @param array  $array  The input array to generate powerset from.
   *
   * @return array  An array of arrays representing the powerset.
   */
  public static function powerset (array $array): array
  {
    $results = [[]];
    foreach ($array as $j => $element)
    {
      $num = count($results);
      for ($i=0; $i<$num; $i++)
      {
        array_push($results, array_merge([$element], $results[$i]));
      }
    }
    return $results;
  }

  /**
   * Another powerset algorithm.
   * Found in a few places on the net.
   */
  public static function powerset2 (array $in, int $minLength=1): array
  {
    $count = count($in);
    $members = pow(2,$count);
    $return = [];
    for ($i = 0; $i < $members; $i++)
    {
      $b = sprintf("%0".$count."b",$i);
      $out = [];
      for ($j = 0; $j < $count; $j++)
      {
        if ($b[$j] == '1') $out[] = $in[$j];
      }
      if (count($out) >= $minLength)
      {
        $return[] = $out;
      }
    }
    return $return; 
  }

  /**
   * Is passed variable an associative array?
   */
  public static function is_assoc (&$array): bool
  {
    return (is_array($array) && !array_is_list($array));
  }

  /**
   * Is passed variable a flat list?
   */
  public static function is_flat (&$array): bool
  {
    return (is_array($array) && array_is_list($array));
  }

  // TODO: add permutations and other useful helpers.

}
