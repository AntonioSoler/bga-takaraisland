<?php
/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * takaraisland implementation : © Antonio Soler <morgald.es@gmail.com>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * takaraisland game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */

 //trasnslatable text will go here for convenience
 
 $this->resources = array(
    "gallery"     => clienttranslate('Gallery'),
    "rockfall"   => clienttranslate('Rockfall'),
    "experience"     => clienttranslate('Experience'),
    "bat"   => clienttranslate('Bat'),
    "goblin"     => clienttranslate('Goblin'),
    "treasure" => clienttranslate('Treasure'),
    "skeleton"    => clienttranslate('Skeleton'),
	"drake"    => clienttranslate('Drake'),
	"spiders"  => clienttranslate('Spiders'),
	"stone of legent"    => clienttranslate('Stone of Legend'),
	"gold"    => clienttranslate('gold'),
	"score_window_title" => clienttranslate('FINAL SCORE'),
	"win_condition" => clienttranslate('The player with the two stones wins, if 2 players have 1 stone the xp points are the tie breaker')
);

 //
 
 $this->card_types = array(
	1  => array( 'name' => $this->resources["gallery"   ], 'type_id' =>  1, 'isMonster' => 0, 'deep' => 1, 'amount' => 8 ),
	2  => array( 'name' => $this->resources["rockfall"  ], 'type_id' =>  2, 'isMonster' => 0, 'deep' => 1, 'amount' => 4 ),
	3  => array( 'name' => $this->resources["experience"], 'type_id' =>  3, 'isMonster' => 0, 'deep' => 1, 'amount' => 2 ),
	4  => array( 'name' => $this->resources["experience"], 'type_id' =>  4, 'isMonster' => 0, 'deep' => 1, 'amount' => 2 ),
	5  => array( 'name' => $this->resources["bat"       ], 'type_id' =>  5, 'isMonster' => 1, 'deep' => 1, 'amount' => 1 ),
	6  => array( 'name' => $this->resources["bat"       ], 'type_id' =>  6, 'isMonster' => 1, 'deep' => 1, 'amount' => 1 ),
	7  => array( 'name' => $this->resources["gallery"   ], 'type_id' =>  7, 'isMonster' => 0, 'deep' => 2, 'amount' => 7 ),
	8  => array( 'name' => $this->resources["experience"], 'type_id' =>  8, 'isMonster' => 0, 'deep' => 2, 'amount' => 3 ),
	9  => array( 'name' => $this->resources["experience"], 'type_id' =>  9, 'isMonster' => 0, 'deep' => 2, 'amount' => 3 ),
	10 => array( 'name' => $this->resources["goblin"    ], 'type_id' => 10, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 ),
	11 => array( 'name' => $this->resources["goblin"    ], 'type_id' => 11, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 ),
	12 => array( 'name' => $this->resources["goblin"    ], 'type_id' => 12, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 ),
	13 => array( 'name' => $this->resources["treasure"  ], 'type_id' => 13, 'isMonster' => 0, 'deep' => 2, 'amount' => 1 ),
	14 => array( 'name' => $this->resources["rockfall"  ], 'type_id' => 14, 'isMonster' => 0, 'deep' => 2, 'amount' => 1 ),
	15 => array( 'name' => $this->resources["experience"], 'type_id' => 15, 'isMonster' => 0, 'deep' => 3, 'amount' => 4 ),
	16 => array( 'name' => $this->resources["experience"], 'type_id' => 16, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 ),
	17 => array( 'name' => $this->resources["gallery"   ], 'type_id' => 17, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 ),
	18 => array( 'name' => $this->resources["treasure"  ], 'type_id' => 18, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 ),
	19 => array( 'name' => $this->resources["skeleton"  ], 'type_id' => 19, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 ),
	20 => array( 'name' => $this->resources["skeleton"  ], 'type_id' => 20, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 ),
	21 => array( 'name' => $this->resources["drake"     ], 'type_id' => 21, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 ),
	22 => array( 'name' => $this->resources["stone of legent"], 'type_id' => 22, 'isMonster' => 0, 'deep' => 3, 'amount' => 1 ),
	23 => array( 'name' => $this->resources["stone of legent"], 'type_id' => 23, 'isMonster' => 0, 'deep' => 3, 'amount' => 1 ),
);


/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/




