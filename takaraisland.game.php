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
  * takaraisland.game.php
  *
  * This is the main file for your game logic.
  *
  * In this PHP file, you are going to defines the rules of the game.
  *
  */

require_once( APP_GAMEMODULE_PATH.'module/table/table.game.php' );

class takaraisland extends Table
{
	function takaraisland( )
	{
        	
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();self::initGameStateLabels( array( 
                "stonesfound" => 10,
                "gameOverTrigger" => 11,
				"playermoves"   => 12
				
				
            //    "my_second_global_variable" => 11,
            //      ...
            //    "my_first_game_variant" => 100,
            //    "my_second_game_variant" => 101,
            //      ...
        ) );
        $this->cards = self::getNew( "module.common.deck" );
		$this->cards->init( "cards" );
		
		$this->treasures = self::getNew( "module.common.deck" );
		$this->treasures->init( "treasures" );
		
		$this->tokens = self::getNew( "module.common.deck" );
		$this->tokens->init( "tokens" );
	}
	
    protected function getGameName( )
    {
        return "takaraisland";
    }	

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame( $players, $options = array() )
    {    
        $sql = "DELETE FROM player WHERE 1 ";
        self::DbQuery( $sql ); 

        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown   and now white
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $default_colors = array( "ff0000", "008000", "0000ff", "ffa500" );

 
        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar) VALUES ";
        $values = array();
        foreach( $players as $player_id => $player )
        {
            $color = array_shift( $default_colors );
            $values[] = "('".$player_id."','$color','".$player['player_canal']."','".addslashes( $player['player_name'] )."','".addslashes( $player['player_avatar'] )."')";
        }
	
    	$sql .= implode( $values , "," );
		self::DbQuery( $sql );
		self::reattributeColorsBasedOnPreferences( $players, array(  /* LIST HERE THE AVAILABLE COLORS OF YOUR GAME INSTEAD OF THESE ONES */"ff0000", "008000", "0000ff", "ffa500" ) );
		self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        //self::setGameStateInitialValue( 'my_first_global_variable', 0 );
        
        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        
		self::initStat( 'table', 'cards_digged', 0 );    // Init a table statistics
        self::initStat( 'table', 'stones_found', 0 );
		self::initStat( 'player', 'cards_digged' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'gold' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'experience' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'stones_found' , 0 );  // Init a player statistics (for all players)
		
        // setup the initial game situation here

        self::setGameStateInitialValue( 'stonesfound', 0 ); // Stones of legend found
        self::setGameStateInitialValue( 'playermoves', 0 );

        $cards = array();
        foreach( $this->treasure_types as $cardType)
        {
			$card = array( 'type' => $cardType["type_id"], 'type_arg' => $cardType["isMonster"] , 'nbr' => 1);
			array_push($cards, $card);   
        }        
        $this->treasures->createCards( $cards, 'deck' );
        //shuffle 
        $this->treasures->shuffle( 'deck' );
		
		
		$cards = array();
		foreach( $this->card_types as $cardType)
        {
			$card = array( 'type' => $cardType["type_id"], 'type_arg' => $cardType["deep"] , 'nbr' => $cardType["amount"]);
			if ( $cardType["deep"] == 1 ) 
				{
					array_push($cards, $card);   
				}
        }
        $this->cards->createCards( $cards, 'deep1' );
        //shuffle 
        $this->cards->shuffle( 'deep1' );
		
		$cards = array();
		foreach( $this->card_types as $cardType)
        {
			$card = array( 'type' => $cardType["type_id"], 'type_arg' => $cardType["deep"] , 'nbr' => $cardType["amount"]);
			if ( $cardType["deep"] == 2 ) 
				{
					array_push($cards, $card);   
				}
        }
        $this->cards->createCards( $cards, 'deep2' );
        //shuffle 
        $this->cards->shuffle( 'deep2' );
		
		$cards = array();
		foreach( $this->card_types as $cardType)
        {
			$card = array( 'type' => $cardType["type_id"], 'type_arg' => $cardType["deep"] , 'nbr' => $cardType["amount"]);
			if ( $cardType["deep"] == 3  ) 
				{
					array_push($cards, $card);   
				}
        }
	
        $this->cards->createCards( $cards, 'deep3' );
        //shuffle 
        $this->cards->shuffle( 'deep3' );
		
		
		$decks=array('deck1','deck2','deck3','deck4','deck5','deck6');
		shuffle($decks);
		$location=array_pop($decks);
		$sql = "UPDATE cards SET card_location='$location' WHERE card_type=22";
        self::DbQuery( $sql ); 
		$sql = "UPDATE cards SET card_location='$location' WHERE card_location='deep3' LIMIT 2";
        self::DbQuery( $sql ); 
		
		
		$location=array_pop($decks);
		$sql = "UPDATE cards SET card_location='$location' WHERE card_type=23";
		self::DbQuery( $sql );
		$sql = "UPDATE cards SET card_location='$location' WHERE card_location='deep3' LIMIT 2";
        self::DbQuery( $sql );
		
		
		for ($i=1 ; $i<=4 ; $i++ )
		{
			$location=array_pop($decks);
			$sql = "UPDATE cards SET card_location='$location' WHERE card_location='deep3' LIMIT 3";
			self::DbQuery( $sql );		
		}
		for ($i=1 ; $i<=6 ; $i++ )
		{
			$location='deck'.$i;
			$this->cards->shuffle( $location );
		}
				
		$deeps=array('deep2','deep1');
		$decks=array('deck1','deck2','deck3','deck4','deck5','deck6');
		foreach ( $deeps as $thisdeep )
		{
			$this->cards->shuffle( $thisdeep );
			foreach ( $decks as $thisdeck)
			{
				for ($i=1 ; $i<=3 ; $i++ ) 
				{
					 //$thiscard = $this->cards->getCardOnTop( $thisdeep );
					 $sql = "SELECT card_id FROM cards WHERE card_location='$thisdeep' LIMIT 1";
                     $thiscard = self::getUniqueValueFromDB( $sql );
					 $this->cards->insertCardOnExtremePosition( $thiscard , $thisdeck, true ); 
				}
			}
		}
		
		$cards=array();
		foreach( $players as $player_id => $player )
        {			
            $sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (1,$player_id,'TH_".$player_id."'), (2,$player_id,'TH_".$player_id."'),(3,$player_id,'workersC' )";
			self::DbQuery( $sql );
        }
		$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (4,0,'swordholder'),(7,1,'expertholder1'),(8,2,'expertholder2'),(9,3,'expertholder3'),(10,4,'expertholder4')";
			
		self::DbQuery( $sql );

		
        $players = self::loadPlayersBasicInfos();

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas()
    {
        $result = array( 'players' => array() );
    
        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!
        $result['current_player_id'] = $current_player_id;
		$players = self::loadPlayersBasicInfos();
    
        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_gold gold, player_color color, player_no nbr FROM player ";
		
        $result['players'] = self::getCollectionFromDb( $sql ); //fields of all players are visible 
		
		$sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens ";
		
        $result['tokens'] = self::getCollectionFromDb( $sql );
		
		$sql = "SELECT card_id id, card_location_arg location_arg from treasures ";
		
        $result['treasures'] = self::getCollectionFromDb( $sql );
		
		$sql = "SELECT card_id id, card_location_arg location_arg, card_type_arg type_arg , card_location location from cards ";
		
        $result['cards'] = self::getCollectionFromDb( $sql );
		
		/*
		$sql = "SELECT player_tent FROM player WHERE player_id='$current_player_id'";
        $result['tent'] = self::getUniqueValueFromDB( $sql );  //only you can see your tent
        
        //show number of cards in deck too.
        $result['cardsRemaining'] = $this->cards->countCardsInLocation('deck');
        $result['iterations'] = $this->getGameStateValue('iterations');
        $result['exploringPlayers'] = $this->getExploringPlayers();
		$sql = "SELECT COUNT(*) FROM cards WHERE card_location ='temple' AND card_type in (12,13,14,15,16)"; 
		$result['templeartifacts'] = self::getUniqueValueFromDB( $sql );
		$result['table'] = $this->cards->getCardsInLocation( 'table' );
         */     
        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression()
    {
        //Compute and return the game progression
        // there are 5 iterations so each one is a 20% of the game + aproximately 1% for each card drawn in this iteration.

        $result = 0;
        $cardsDrawn = $this->cards->countCardsInLocation( 'discard' );
		$result = $cardsDrawn * 2 ;
        return ($result);
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

	
	
	
//////////////////////////////////////////////////////////////////////////////
//////////// Player actions
//////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in takaraisland.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} played ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
*/    

    function movetile($tile,$destination)
    {
	self::checkAction( 'movetile' );
	$player_id = self::getActivePlayerId();
	self::DbQuery( "UPDATE tokens SET card_location='$destination' WHERE card_type_arg=$player_id AND card_type='$tile'" );
    self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} moved an adventurer.' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'destination' => $destination,
				'tile_id' => "tile_".$player_id."_".$tile
				) );
	
	$this->gamestate->nextState( 'dig' );
	
    }

    function rentsword()
    {
	self::checkAction( 'rentsword' );
	$player_id = self::getActivePlayerId();
	
	self::DbQuery( "UPDATE tokens SET card_location='playerSwordholder_$player_id' WHERE card_type='4'" );
    self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} rents the magic sword.' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'destination' => "playerSwordholder_".$player_id,
				'tile_id' => "sword"
				) );
		
	;	
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */
	function argPlayerMoves()
    {
        return array(
            'playermoves' => self::getGameStateValue( 'playermoves' )
        );
    }
	
	

//////////////////////////////////////////////////////////////////////////////
//////////// Game state actions
////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */
    
    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

	////////////////////////////////////////////////////////////////////////////

    function ststartturn()
	{
		self::setGameStateValue( 'playermoves' , 0 );
		$this->gamestate->nextState( 'playermove' );
		
	}
	////////////////////////////////////////////////////////////////////////////
	
	function stplayermove()
	{
		    self::incGameStateValue( 'playermoves' , 1 );
			$player_id=self::getActivePlayerId();
			$sql = "SELECT COUNT(*) from tokens where card_location = 'TH_$player_id'";
			$availableAdventurers = self::getUniqueValueFromDB( $sql );
			if ( $availableAdventurers == 0 )
			{
				$this->gamestate->nextState( 'endturn' );
			}
			
	}
	////////////////////////////////////////////////////////////////////////////
	function stendturn()
	{
		$player_id = self::getActivePlayerId();
		self::DbQuery( "UPDATE tokens SET card_location='TH_$player_id' WHERE card_type_arg=$player_id AND card_type in ('1','2','3') and ((card_location like 'explore%') or (card_location in ('diveC','counterC','expertsC'))) " );
		self::DbQuery( "UPDATE tokens SET card_location='swordholder' WHERE card_type='4'" );
		$this->activeNextPlayer();
		$this->gamestate->nextState( );
		
	}
	
	////////////////////////////////////////////////////////////////////////////
	
		
	function stexploresite()
	{

		$this->gamestate->nextState( );
		
	}
	
	////////////////////////////////////////////////////////////////////////////
	
		
	function stdig()
	{

		$this->gamestate->nextState("playermove");
		
	}


////////////////////////////////////////////////////////////////////////////

    function displayScores()
    {
        $players = self::loadPlayersBasicInfos();

        $table[] = array();
        
        //left hand col
        $table[0][] = array( 'str' => ' ', 'args' => array(), 'type' => 'header');
        $table[1][] = $this->resources["gems"    ];
        $table[2][] = $this->resources["artifacts"  ];
        
		$table[3][] = array( 'str' => '<span class=\'score\'>Score</span>', 'args' => array(), 'type' => '');

        foreach( $players as $player_id => $player )
        {
            $table[0][] = array( 'str' => '${player_name}',
                                 'args' => array( 'player_name' => $player['player_name'] ),
                                 'type' => 'header'
                               );
            $table[1][] = $this->getGemsPlayer( $player_id, 'tent' );
            $table[2][] = $this->cards->countCardsInLocation( $player['player_id']);

            $gems = $this->getGemsPlayer( $player_id, 'tent' ) ;
			$artifacts = $this->cards->countCardsInLocation( $player_id );
			
			$score = $gems + 5 * $artifacts ;
			
			self::setStat( $gems , "gems_number", $player_id );
			self::setStat( $artifacts , "artifacts_number", $player_id );
			
			$sql = "UPDATE player SET player_score = ".$score." WHERE player_id=".$player['player_id'];
            self::DbQuery( $sql );
			
			$sql = "UPDATE player SET player_score_aux = ".$this->cards->countCardsInLocation( $player['player_id'])." WHERE player_id=".$player['player_id'];
            self::DbQuery( $sql );
				
            $table[3][] = array( 'str' => '<span class=\'score\'>${player_score}</span>',
                                 'args' => array( 'player_score' => $score ),
                                 'type' => ''
                               );
        }
		self::setStat( self::getGameStateValue( 'artifactspicked') , "artifacts_drawn" );

        $this->notifyAllPlayers( "tableWindow", '', array(
            "id" => 'finalScoring',
            "title" => $this->resources["score_window_title"],
            "table" => $table,
            "header" => '<div>'.$this->resources["win_condition"].'</div>',
			"closing" => clienttranslate( "OK" )
            //"closelabel" => clienttranslate( "Closing button label" )
        ) ); 
    }

////////////////////////////////////////////////////////////////////////////

    function stGameEndScoring()
    {
        //stats for each player, we want to reveal how many gems they have in tent
        //In the case of a tie, check amounts of artifacts. Set auxillery score for this

        //stats first

        $this->displayScores();
    
        $this->gamestate->nextState('');
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Zombie
////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
    */

    function zombieTurn( $state, $active_player )
    {
    	$statename = $state['name'];
    	
        $this->gamestate->setPlayerNonMultiactive( $active_player, '' );

        throw new feException( "Zombie mode not supported at this game state: ".$statename );
    }
}
