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
 * states.inc.php
 *
 * takaraisland game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

$machinestates = array(

    // The initial state. Please do not modify.
    1 => array(
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => array( "" => 2 )
    ),
    
    2 => array(
        "name" => "startturn",
		"description" => clienttranslate('a new turn starts...'),
        "type" => "game",
        "action" => "ststartturn",
        "updateGameProgression" => true,
        "transitions" => array( "playermove" => 3, "gameEndScoring" => 90 ) //game ends if  2 stones found or 4/5 decks are depleted
    ),

    3 => array(
        "name" => "playermove",  
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is deciding where to send the adventurers'),
		"descriptionmyturn" => clienttranslate('${you} you need to decide where to send your adventurers'),
		"action" => "stplayermove",
		"args" => "argPlayerMoves",
		"possibleactions" => array( "movetile", "rentsword" ),
        "updateGameProgression" => true,
        "transitions" => array( "endturn" => 4, "hireexpert" => 5, "exchange" => 7 , "exploresite" => 9, "playermove"=>3  ) 
    ),
	
	 4 => array(
        "name" => "endturn",  //  pay hospital and hire new recruit
        "type" => "game",
        "description" => clienttranslate('${actplayer} is deciding some end of turn actions '),
		"descriptionmyturn" => clienttranslate('${you} can pay the hospital or recruit one worker on the beach or pass'),
		"action" => "stendturn",
		"possibleactions" => array( "payhospital", "recruit", 'pass' ),
        "updateGameProgression" => false,
        "transitions" => array("" => 2) //
    ),
	
	5 => array(
        "name" => "hireexpert",  // 
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is deciding what Specialist he wants to hire '),
		"descriptionmyturn" => clienttranslate('${you} need to decide what Specialist you want to hire '),
		"possibleactions" => array( "pickexpert"),
        "updateGameProgression" => false,
        "transitions" => array( "" => 6  ) //
    ),


    6 => array(
        "name" => "sendexpert",  // 
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is deciding where to send the expert'),
		"descriptionmyturn" => clienttranslate('${you} need to pick the cards that your expert will act on'),
		"possibleactions" => array( "selectcards"),
        "updateGameProgression" => false,
        "transitions" => array( "" => 3 ) //
    ),

    7 => array(
        "name" => "exchange",  // 
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is deciding whether to buy or sell xp '),
		"descriptionmyturn" => clienttranslate('${you} have to select the xp tile that you want to sell or to buy an 2 XP tile for 5 Kara Gold '),
		"possibleactions" => array( "sellxp","buyxp"),
        "updateGameProgression" => false,
        "transitions" => array( "" => 3 ) //
    ),
	
	8 => array(
        "name" => "browsecards",  // 
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is inspecting the cards from the survey of this site'),
		"descriptionmyturn" => clienttranslate('${you} can view some cards of this site, reveal discovered monsters if wanted and press done when finished.'),
		"possibleactions" => array( "revealmonster","viewdone"),
        "updateGameProgression" => false,
        "transitions" => array( "" => 3 ) //
    ),
	
	9 => array(
        "name" => "exploresite",  // 
        "type" => "activeplayer",
        "description" => clienttranslate('${actplayer} is sending an adventurer to an Excavation site'),
		"descriptionmyturn" => clienttranslate('${you} are sending an adventurer to an Excavation site'),
		"possibleactions" => array( "dig","survey"),
        "updateGameProgression" => false,
        "transitions" => array( "dig" => 12 , "fight" => 11 , "browsecards" => 8  ) //
    ),
    
	10 => array(
        "name" => "gettreasure",  // 
        "description" => clienttranslate('${actplayer} obtains a treasure'),
		"descriptionmyturn" => clienttranslate('${you} obtain a treasure'),
        "type" => "game",
        "action" => "sttreasure",
        "updateGameProgression" => false,
        "transitions" => array( "" => 3 )
    ),
	
	11 => array(
	    "name" => "fight",
	    "description" => clienttranslate('${actplayer} is fighting a monster'),
		"descriptionmyturn" => clienttranslate('${you} are fighting a monster'),
        "type" => "game",
        "action" => "stfight",
        "updateGameProgression" => false,
        "transitions" => array( "" => 3 )
    ),
	
	12 => array(
	    "name" => "dig",
	    "description" => clienttranslate('${actplayer} is digging in a Excavation site'),
		"descriptionmyturn" => clienttranslate('${you} are digging in a Excavation site'),
        "type" => "game",
        "action" => "stdig",
        "updateGameProgression" => true,
        "transitions" => array( "playermove" => 3 , "fight" => 11 , "gettreasure" =>10)
    ),
		
    90 => array(
	   "description" => clienttranslate('Final Score'),
        "name" => "gameEndScoring",
        "type" => "game",
        "action" => "stGameEndScoring",
        "updateGameProgression" => false,
        "transitions" => array( "" => 99 )
    ),
    
/*
    Examples:
    
    2 => array(
        "name" => "nextPlayer",
        "description" => '',
        "type" => "game",
        "action" => "stNextPlayer",
        "updateGameProgression" => true,   
        "transitions" => array( "endGame" => 99, "nextPlayer" => 10 )
    ),
    
    10 => array(
        "name" => "playerTurn",
        "description" => clienttranslate('${actplayer} must play a card or pass'),
        "descriptionmyturn" => clienttranslate('${you} must play a card or pass'),
        "type" => "activeplayer",
        "possibleactions" => array( "playCard", "pass" ),
        "transitions" => array( "playCard" => 2, "pass" => 2 )
    ), 

*/    
   
    // Final state.
    // Please do not modify.
    99 => array(
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd"
    )

);


