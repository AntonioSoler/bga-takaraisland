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
    "gallery"         => clienttranslate('Gallery'),
    "rockfall"        => clienttranslate('Rockfall'),
    "experience"      => clienttranslate('Experience'),
    "bat"             => clienttranslate('Bat'),
    "goblin"          => clienttranslate('Goblin'),
    "treasure"        => clienttranslate('Treasure'),
    "skeleton"        => clienttranslate('Skeleton'),
	"drake"           => clienttranslate('Drake'),
	"stone of legend" => clienttranslate('Stone of Legend'),
	"gold"            => clienttranslate('gold'),
	"playertile1"     => clienttranslate('player tile 1'),
	"playertile2"     => clienttranslate('player tile 2'),
	"playertile3"     => clienttranslate('player tile 3'),
	"wound"           => clienttranslate('wound'),
	"sword"           => clienttranslate('sword'),
	"expert"          => clienttranslate('expert'),
	"tablet"          => clienttranslate('tablet'),
	"map"             => clienttranslate('map'),
	"mimichest"       => clienttranslate('mimic chest'),
	"idol"            => clienttranslate('idol'),
	"skull"           => clienttranslate('skull'),
	"score_window_title" => clienttranslate('FINAL SCORE'),
	"win_condition" => clienttranslate('The player with the two stones wins, if 2 players have 1 stone the xp points are the tie breaker')
);

 //
 
 $this->card_types = array(
	1  => array( 'name' => $this->resources["gallery"        ], 'type_id' =>  1, 'isMonster' => 0, 'deep' => 1, 'amount' => 8 , 'gold' =>  2, 'xp'=> 0, 'life'=> 0 ),
	2  => array( 'name' => $this->resources["rockfall"       ], 'type_id' =>  2, 'isMonster' => 0, 'deep' => 1, 'amount' => 4 , 'gold' =>  2, 'xp'=> 0, 'life'=> 0 ),
	3  => array( 'name' => $this->resources["experience"     ], 'type_id' =>  3, 'isMonster' => 0, 'deep' => 1, 'amount' => 2 , 'gold' =>  0, 'xp'=> 1, 'life'=> 0 ),
	4  => array( 'name' => $this->resources["experience"     ], 'type_id' =>  4, 'isMonster' => 0, 'deep' => 1, 'amount' => 2 , 'gold' =>  0, 'xp'=> 1, 'life'=> 1 ),
	5  => array( 'name' => $this->resources["bat"            ], 'type_id' =>  5, 'isMonster' => 1, 'deep' => 1, 'amount' => 1 , 'gold' =>  0, 'xp'=> 2, 'life'=> 1 ),
	6  => array( 'name' => $this->resources["bat"            ], 'type_id' =>  6, 'isMonster' => 1, 'deep' => 1, 'amount' => 1 , 'gold' =>  5, 'xp'=> 1, 'life'=> 1 ),
	7  => array( 'name' => $this->resources["gallery"        ], 'type_id' =>  7, 'isMonster' => 0, 'deep' => 2, 'amount' => 7 , 'gold' =>  4, 'xp'=> 0, 'life'=> 0 ),
	8  => array( 'name' => $this->resources["experience"     ], 'type_id' =>  8, 'isMonster' => 0, 'deep' => 2, 'amount' => 3 , 'gold' =>  0, 'xp'=> 2, 'life'=> 0 ),
	9  => array( 'name' => $this->resources["experience"     ], 'type_id' =>  9, 'isMonster' => 0, 'deep' => 2, 'amount' => 3 , 'gold' =>  0, 'xp'=> 2, 'life'=> 1 ),
	10 => array( 'name' => $this->resources["goblin"         ], 'type_id' => 10, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 , 'gold' => 10, 'xp'=> 1, 'life'=> 2 ),
	11 => array( 'name' => $this->resources["goblin"         ], 'type_id' => 11, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 , 'gold' =>  0, 'xp'=> 3, 'life'=> 2 ),
	12 => array( 'name' => $this->resources["goblin"         ], 'type_id' => 12, 'isMonster' => 1, 'deep' => 2, 'amount' => 1 , 'gold' =>  5, 'xp'=> 2, 'life'=> 2 ),
	13 => array( 'name' => $this->resources["treasure"       ], 'type_id' => 13, 'isMonster' => 0, 'deep' => 2, 'amount' => 1 , 'gold' =>  0, 'xp'=> 0, 'life'=> 0 ),
	14 => array( 'name' => $this->resources["rockfall"       ], 'type_id' => 14, 'isMonster' => 0, 'deep' => 2, 'amount' => 1 , 'gold' =>  2, 'xp'=> 0, 'life'=> 0 ),
	15 => array( 'name' => $this->resources["experience"     ], 'type_id' => 15, 'isMonster' => 0, 'deep' => 3, 'amount' => 4 , 'gold' =>  0, 'xp'=> 4, 'life'=> 1 ),
	16 => array( 'name' => $this->resources["experience"     ], 'type_id' => 16, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 , 'gold' =>  0, 'xp'=> 4, 'life'=> 0 ),
	17 => array( 'name' => $this->resources["gallery"        ], 'type_id' => 17, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 , 'gold' =>  6, 'xp'=> 0, 'life'=> 0 ),
	18 => array( 'name' => $this->resources["treasure"       ], 'type_id' => 18, 'isMonster' => 0, 'deep' => 3, 'amount' => 3 , 'gold' =>  0, 'xp'=> 0, 'life'=> 0 ),
	19 => array( 'name' => $this->resources["skeleton"       ], 'type_id' => 19, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 , 'gold' =>  5, 'xp'=> 3, 'life'=> 3 ),
	20 => array( 'name' => $this->resources["skeleton"       ], 'type_id' => 20, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 , 'gold' => 15, 'xp'=> 1, 'life'=> 3 ),
	21 => array( 'name' => $this->resources["drake"          ], 'type_id' => 21, 'isMonster' => 1, 'deep' => 3, 'amount' => 1 , 'gold' => 20, 'xp'=> 5, 'life'=> 4 ),
	22 => array( 'name' => $this->resources["stone of legend"], 'type_id' => 22, 'isMonster' => 0, 'deep' => 3, 'amount' => 1 , 'gold' =>  0, 'xp'=> 10, 'life'=> 0 ),
	23 => array( 'name' => $this->resources["stone of legend"], 'type_id' => 23, 'isMonster' => 0, 'deep' => 3, 'amount' => 1 , 'gold' =>  0, 'xp'=> 10, 'life'=> 0 )
);                                                                                                                                        

 
 $this->token_types = array(
 	1  => array( 'name' => $this->resources["playertile1"    ], 'type_id' => 1  ),
	2  => array( 'name' => $this->resources["playertile2"    ], 'type_id' => 2  ),
	3  => array( 'name' => $this->resources["playertile3"    ], 'type_id' => 3  ),
	4  => array( 'name' => $this->resources["sword"          ], 'type_id' => 4  ),
	5  => array( 'name' => $this->resources["wound"          ], 'type_id' => 5  ),
	6  => array( 'name' => $this->resources["experience"     ], 'type_id' => 6  ),
	7  => array( 'name' => $this->resources["expert"         ], 'type_id' => 7  ),
	8  => array( 'name' => $this->resources["expert"         ], 'type_id' => 8  ),
	9  => array( 'name' => $this->resources["expert"         ], 'type_id' => 9  ),
	10 => array( 'name' => $this->resources["expert"         ], 'type_id' => 10 ),
	11 => array( 'name' => $this->resources["stone of legend"], 'type_id' => 11 ),
	12 => array( 'name' => $this->resources["stone of legend"], 'type_id' => 12 )
);


 $this->treasure_types = array(
	1  => array( 'name' => $this->resources["tablet"        ], 'type_id' =>  1, 'isMonster' => 0, 'gold' =>  0, 'xp'=>  8, 'life'=> 0 ),
	2  => array( 'name' => $this->resources["map"           ], 'type_id' =>  2, 'isMonster' => 0, 'gold' =>  5, 'xp'=>  2, 'life'=> 0 ),
	3  => array( 'name' => $this->resources["mimichest"     ], 'type_id' =>  3, 'isMonster' => 1, 'gold' =>  0, 'xp'=>  2, 'life'=> 2 ),
	4  => array( 'name' => $this->resources["gold"          ], 'type_id' =>  4, 'isMonster' => 0, 'gold' => 10, 'xp'=>  0, 'life'=> 0 ),
	5  => array( 'name' => $this->resources["idol"          ], 'type_id' =>  5, 'isMonster' => 0, 'gold' =>  0, 'xp'=>  4, 'life'=> 0 ),
	6  => array( 'name' => $this->resources["skull"         ], 'type_id' =>  6, 'isMonster' => 0, 'gold' => 20, 'xp'=> -2, 'life'=> 0 ),
	7  => array( 'name' => $this->resources["idol"          ], 'type_id' =>  7, 'isMonster' => 0, 'gold' =>  0, 'xp'=>  4, 'life'=> 0 ),
	8  => array( 'name' => $this->resources["idol"          ], 'type_id' =>  8, 'isMonster' => 0, 'gold' =>  0, 'xp'=>  4, 'life'=> 0 )
	); 

/*

Example:

$this->card_types = array(
    1 => array( "card_name" => ...,
                ...
              )
);

*/




