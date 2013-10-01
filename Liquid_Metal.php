<?php
/*
	A PHP port of LiquidMetal.js, version: 1.2.1 (2012-04-21)
	
	A mimetic poly-alloy of Quicksilver's scoring algorithm, essentially
	LiquidMetal.
	
	For usage and examples, visit:
	http://github.com/kjantzer/liquidmetal-php
	
	Licensed under the MIT:
	http://www.opensource.org/licenses/mit-license.php
	
	Copyright (c) 2009-2012, Ryan McGeary (ryan -[at]- mcgeary [*dot*] org)
	PHP port by Kevin Jantzer
*/


class Liquid_Metal {

  private static $SCORE_NO_MATCH = 0.0;
  private static $SCORE_MATCH = 1.0;
  private static $SCORE_TRAILING = 0.8;
  private static $SCORE_TRAILING_BUT_STARTED = 0.9;
  private static $SCORE_BUFFER = 0.85;
  private static $WORD_SEPARATORS = " \t_-";

  private static $lastScore = null;
  private static $lastScoreArray = null;


  public static function score($string, $abbrev) {
      // short circuits
      if (strlen($abbrev) === 0) return self::$SCORE_TRAILING;
      if (strlen($abbrev) > strlen($string)) return self::$SCORE_NO_MATCH;

      // match & score all
      $scores = array();
      $allScores = array();
      $search = strtolower($string);
      $abbrev = strtolower($abbrev);
      self::_score_all($string, $search, $abbrev, -1, 0, $scores, $allScores);
      
      // complete miss
      if (count($allScores) == 0) return 0;

      // sum per-character $scores into overall $scores,
      // selecting the maximum score
      $maxScore = 0.0;
      $maxArray = array();
      
      for ($i = 0; $i < count($allScores); $i++) {
        $scores = $allScores[$i];
        $scoreSum = 0.0;
        for ($j = 0; $j < strlen($string); $j++) { $scoreSum += $scores[$j]; }
        if ($scoreSum > $maxScore) {
          $maxScore = $scoreSum;
          $maxArray = $scores;
        }
      }

      // normalize max score by $string length
      // s. t. the perfect match score = 1
      $maxScore /= strlen($string);

      // record maximum score & score $array, return
      self::$lastScore = $maxScore;
      self::$lastScoreArray = $maxArray;
      return $maxScore;
  }
    
    
  public static function _score_all($string, $search, $abbrev, $searchIndex, $abbrIndex, &$scores, &$allScores) {
     
      // save completed match $scores at end of $search
      if ($abbrIndex == strlen($abbrev)) {
        // add trailing score for the remainder of the match
        $started = ($search{0} == $abbrev{0});
        $trailScore = $started ? self::$SCORE_TRAILING_BUT_STARTED : self::$SCORE_TRAILING;
        self::fill_array($scores, $trailScore, count($scores), strlen($string));
        // save score clone (since reference is persisted in $scores)
        $allScores[] = array_slice($scores, 0);
        return;
      }

      // consume current char to match
      $c = $abbrev{$abbrIndex};
      $abbrIndex++;

      // cancel match if a character is missing
      if( $searchIndex >= 0){
	      $index = strpos($search, $c, $searchIndex);
	      if ($index === false) return;
      }

      // match all instances of the $abbreviaton char
      $scoreIndex = $searchIndex; // score section to update
      while (($index = strpos($search, $c, $searchIndex+1)) !== false) {
        // score this match according to context
        if (self::is_new_word($string, $index)) {
          $scores[$index-1] = 1;
          self::fill_array($scores, self::$SCORE_BUFFER, $scoreIndex+1, $index-1);
        }
        else if (self::is_uppercase($string, $index)) {
          self::fill_array($scores, self::$SCORE_BUFFER, $scoreIndex+1, $index);
        }
        else {
          self::fill_array($scores, self::$SCORE_NO_MATCH, $scoreIndex+1, $index);
        }
        $scores[$index] = self::$SCORE_MATCH;

        // consume matched $string and continue $search
        $searchIndex = $index;
        self::_score_all($string, $search, $abbrev, $searchIndex, $abbrIndex, $scores, $allScores);
      }
  }
  
  
  private static function is_uppercase($string, $index) {
    $c = $string{$index};
    return ("A" <= $c && $c <= "Z");
  }

  private static function is_new_word($string, $index) {
    $c = $string{$index-1};
    return $c && (strpos(self::$WORD_SEPARATORS, $c) != -1);
  }

  private static function fill_array(&$array, $value, $from, $to) {
    for ($i = $from; $i < $to; $i++) { $array[$i] = $value; }
    return $array;
  }
	
	
}



