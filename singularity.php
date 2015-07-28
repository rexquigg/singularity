<?php
/*
Singularity

By: Rex Quigg <rexquigg@gmail.com>

This is free and unencumbered software released into the public domain.

Anyone is free to copy, modify, publish, use, compile, sell, or
distribute this software, either in source code form or as a compiled
binary, for any purpose, commercial or non-commercial, and by any
means.

In jurisdictions that recognize copyright laws, the author or authors
of this software dedicate any and all copyright interest in the
software to the public domain. We make this dedication for the benefit
of the public at large and to the detriment of our heirs and
successors. We intend this dedication to be an overt act of
relinquishment in perpetuity of all present and future rights to this
software under copyright law.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR
OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
OTHER DEALINGS IN THE SOFTWARE.

For more information, please refer to <http://unlicense.org/>
*/

require_once( 'bitcoin.inc' );

$common6 = file_get_contents( 'common6.txt' );
$bitcoin = new Bitcoin();

function find_address( $seeking ) {
  global $bitcoin;

  $decoded = $bitcoin->decodeBase58( $seeking );
  if( ! $decoded ) {
    return false; //"invalid characters";
  }
  $length = strlen($decoded);
  if( ! ($length == 50 ) ) {
    return false; //$length." data length impossible";
  }

  $hash160 = substr($decoded, 2, 40);
  return $bitcoin->hash160ToAddress( $hash160 );
}

function capitalizeAndFind( $goal, $start ) {
  global $common6;

  // add capitalization at start point
  $split = substr( $goal, $start );
  $newGoal = substr( $goal, 0, $start ) . ucfirst($split);

  // generate address with correct checksum
  $result = find_address( $newGoal );

  if( $result ) {
    $last6 = substr( $result, -6 );

    if( stripos( $common6, $last6 ) !== false ) {
      echo $result, "\n";
    }

    for( $i=$start+1; $i<28; $i++) {
      $changing = substr($newGoal, $i, 1);
      if( ctype_lower( $changing ) ) {
        capitalizeAndFind( $newGoal, $i );
      }
    }
  }
}

$input = null;
if( array_key_exists(1, $argv) ) {
  $input = $argv[1];
}
$goal = find_address( $input );

if( $goal ) {
  echo "Seeking Address Matching: ", $goal, "\n\n";
  capitalizeAndFind( $goal, 1 );
}
else {
  $decoded = $bitcoin->decodeBase58( $input );
  $length = strlen($decoded);
  if( !$input ) {
    echo "Error: Missing address pattern.\n";
  }
  else if( ! $decoded ) {
    echo "Error: Invalid characters in address pattern.\n";
  }
  else if( ! ($length == 50 ) ) {
    echo "Error: Address with decoded length of ", $length, " is impossible.\n";
    echo "Please try again, the length must be exactly 50.\n";
  } else {
    echo "Unknown Error.\n";
  }
  echo "Use: ", $argv[0], " 1PartiaLaddressearchargument999999\n";
}
