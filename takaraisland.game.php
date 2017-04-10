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
				"playermoves"   => 12,
				"currentsite"   => 13,
				"monsterpresent" => 14,
				"expertpicked" => 15,
				"mapowner" => 16
				
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
		self::initStat( 'player', 'cards_digged_player' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'gold' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'experience' , 0 );  // Init a player statistics (for all players)
		self::initStat( 'player', 'stones_found' , 0 );  // Init a player statistics (for all players)
		
        // setup the initial game situation here
        // self::DbQuery( "UPDATE player set player_gold = 100" );   // TEST!!!
		
        self::setGameStateInitialValue( 'stonesfound', 0 ); // Stones of legend found
        self::setGameStateInitialValue( 'playermoves', 0 ); // number of movements done by the player (sword can be only the 1st)
		self::setGameStateInitialValue( 'currentsite', 0 ); // Current focused site for dig / survey / fight / expert
		self::setGameStateInitialValue( 'monsterpresent', 0 ); //Monster present in the survey results
		self::setGameStateInitialValue( 'expertpicked', 0 ); //Nr of the expert picked / impersonated
        self::setGameStateInitialValue( 'mapowner', 0 ); // Player_id of the map card owner
		
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
        $sql = "SELECT player_id id, player_gold gold, player_xp xp, player_color color, player_no nbr FROM player ";
		
        $result['players'] = self::getCollectionFromDb( $sql ); //fields of all players are visible 
		
		$sql = "SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens ";
		
        $result['tokens'] = self::getCollectionFromDb( $sql );
		
		$sql = "SELECT card_id id, card_location_arg location_arg from treasures WHERE card_location like 'deck' ";
		
        $result['treasures'] = self::getCollectionFromDb( $sql );
		
		$sql = "SELECT card_id id, card_location_arg location_arg, card_type_arg type_arg , card_location location from cards WHERE card_location like 'deck%' ORDER BY card_location_arg";
		
        $result['cards'] = self::getCollectionFromDb( $sql );
		
		$sql = "SELECT card_id id, card_location_arg location_arg, card_type type, card_type_arg type_arg , card_location location from cards WHERE card_location like 'deck%' AND card_status=1 ORDER BY card_location_arg";
		
        $result['visiblecards'] = self::getCollectionFromDb( $sql );
		
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
        $cardsDrawn = $this->cards->countCardsInLocation( 'removed' );
		$result = $cardsDrawn * 2 ;
        return ($result);
    }

//////////////////////////////////////////////////////////////////////////////
//////////// Utility functions
////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */

	function getCardStatus($thiscard_id)
    {
        //Compute and return the game progression
        // there are 5 iterations so each one is a 20% of the game + aproximately 1% for each card drawn in this iteration.

        $result = 0;
        $sql = "SELECT card_status from cards where card_id=". $thiscard_id;
		$result = self::getUniqueValueFromDB( $sql ) ;
        return ($result);
    }
	
	function getGoldBalance($player_id)
    {
        //Compute and return the game progression
        // there are 5 iterations so each one is a 20% of the game + aproximately 1% for each card drawn in this iteration.

        $result = 0;
        $sql = "SELECT player_gold from player where player_id=". $player_id;
		$result = self::getUniqueValueFromDB( $sql ) ;
        return ($result);
    }
	
	function getXPBalance($player_id)
    {
        //Compute and return the game progression
        // there are 5 iterations so each one is a 20% of the game + aproximately 1% for each card drawn in this iteration.

        $result = 0;
        $sql = "SELECT player_xp from player where player_id=". $player_id;
		$result = self::getUniqueValueFromDB( $sql ) ;
        return ($result);
    }
	
	function getStoneBalance($player_id)
    {
        //Compute and return the game progression
        // there are 5 iterations so each one is a 20% of the game + aproximately 1% for each card drawn in this iteration.

        $result = 0;
        $sql = "SELECT Count(*) from tokens where card_type in ('11','12' ) AND card_location='playercardstore_$player_id' ";
		$result = self::getUniqueValueFromDB( $sql ) ;
        return ($result);
    }
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
	switch ($destination) 
		{
			case "explore1":
			case "explore2":
			case "explore3":
			case "explore4":
			case "explore5":
			case "explore6":
			    $sitenr= substr( $destination, 7, 1) ;
				self::setGameStateValue('currentsite', $sitenr );
				$topcard=$this->cards->getCardOnTop( 'deck'.$sitenr );
				
				 //is the card a visible Monster?
				if (( $this->card_types[$topcard['type']]['isMonster'] ==1 ) AND ( $this->getCardStatus($topcard['id']) == 1 ) ) 
				{
					$this->gamestate->nextState( 'fight' );
				}
				
					
				else
				{
					$this->gamestate->nextState( 'exploresite' );
				}
				break;
			case "diveC":
				$sql = "UPDATE player set player_gold = player_gold + 1 WHERE Player_id = $player_id";
				self::DbQuery( $sql );
				self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets 1 <div class="goldlog"></div> for visiting "The Dive"' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'amount' => 1 ,  
					'source' => "diveC"
					) );
				$this->gamestate->nextState( 'playermove' );
				break;
			case "counterC":
				$this->gamestate->nextState( 'exchange' );
				break;
			case "expertsC":
				$this->gamestate->nextState( 'hireexpert' );
				break;
		}
    }
	
	function choosereward($reward)
    {
        $mapowner = self::getGameStateValue( 'mapowner' );
		$player_id = self::getActivePlayerId();
		$gold=5;
		$xp=2;
		if ( $mapowner == $player_id )
		{
			self::setGameStateValue( 'mapowner',0 );
			switch ($reward)
			{
				case 1:
						
						
						self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );
						self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div> for using the Map' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $gold,
								'source' => "playercardstore_".$player_id
								) );
						break;
				case 2:		
						$NOSELL=0;
						
						self::DbQuery( "UPDATE player set player_xp = player_xp + ( $xp ) WHERE Player_id = $player_id" );
						$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
						self::DbQuery( $sql );
						$token_id=self::DbGetLastId();
						self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} gets ${amount} <div class="xplog"></div> points for using the Map' ), array(
							'player_id' => $player_id,
							'player_name' => self::getActivePlayerName(),
							'amount' => $xp,
							'source' => "playercardstore_".$player_id,
							'token_id' => $token_id,
							'NOSELL' => $NOSELL
							) );
			
				
				
						break;
				case 0:
				
			}
		
		}
		
			
        
    }

    function rentsword()
    {
	self::checkAction( 'rentsword' );
	$player_id = self::getActivePlayerId();
	if (( self::getGameStateValue ('playermoves') == 1) AND ( self::getGoldBalance($player_id) >=3 ) ) 
		{
		self::DbQuery( "UPDATE player set player_gold = player_gold - 3 WHERE Player_id = $player_id" );	
		self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 3 <div class="goldlog"></div> to the forge' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => 3 ,  
						'destination' => "swordholder"
						) );	
		self::DbQuery( "UPDATE tokens SET card_location='playerSwordholder_$player_id' WHERE card_type='4'" );
		self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} rents the magic sword.' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'destination' => "playerSwordholder_".$player_id,
					'tile_id' => "sword"
					) );
			
		}	
    }
	
	function revealmonster()
    {
		self::checkAction( 'revealmonster' );
		$player_id = self::getActivePlayerId();
		$sitenr= self::getGameStateValue('currentsite');
		$topcards=$this->cards->getCardsOnTop( 3 , 'deck'.$sitenr );
		$gold=0;
		if ( self::getGameStateValue ('monsterpresent') > 0 )
		foreach($topcards as $thiscard )
		{
			if ( $this->card_types[$thiscard['type']]['isMonster'] ==1 ) 
			{
				$gold+=2;
				$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$thiscard['id'];
				self::DbQuery( $sql );
				self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} detected a monster on the survey of site: ${sitenr}' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'sitenr' => $sitenr ,
						'card' => $thiscard
						) );
			}	
		}
		$sql = "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id";
		self::DbQuery( $sql );
		self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets 2 <div class="goldlog"></div> per monster detected in the survey' ), array(
			'player_id' => $player_id,
			'player_name' => self::getActivePlayerName(),
			'amount' => $gold ,  //deck1_item_card_3
			'source' => $thiscard['location']."_item_card_". $thiscard['id']
			) );
		self::setGameStateValue('monsterpresent' ,0 );
	}
	function dig()
    {
	self::checkAction( 'dig' );
	$player_id = self::getActivePlayerId();
	$sitenr= self::getGameStateValue('currentsite');
	$topcard=$this->cards->getCardOnTop( 'deck'.$sitenr );
	//var_dump( $topcard );
	$card=self::getObjectFromDB( "SELECT * FROM cards WHERE card_id=".$topcard['id'] );
	$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$topcard['id'];
			self::DbQuery( $sql );
	self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} digs a card on the excavation site: ${sitenr}' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'sitenr' => $sitenr ,
					'card' => $topcard
					) );
					
	$this->gamestate->nextState( 'dig' );
	
    }
	
	function survey()
    {
	self::checkAction( 'survey' );
	$player_id = self::getActivePlayerId();
	self::setGameStateValue('monsterpresent' ,0 );
	$sitenr= self::getGameStateValue('currentsite');
	$topcards=$this->cards->getCardsOnTop( 3 , 'deck'.$sitenr );
	$cards=array();
	foreach($topcards as $thiscard )
	{
		if (( $this->card_types[$thiscard['type']]['isMonster'] ==1) AND ( self::getCardStatus($thiscard['id'])==0) ) 
		{
			self::setGameStateValue('monsterpresent' ,1 );;
		}
		array_push ($cards, $thiscard);
		if ( ($thiscard['type'] == '14' ) || ($thiscard['type'] == '2' ) ) 
		{
			if ( self::getCardStatus($thiscard['id'])==0)
			{
				
				self::DbQuery( "UPDATE player set player_gold = player_gold + 2 WHERE Player_id = $player_id" );
				$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$thiscard['id'];
				self::DbQuery( $sql );
				self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} detected a rockfall on the survey of site: ${sitenr}' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'sitenr' => $sitenr ,
						'card' => $thiscard
						) );
				self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets 2 <div class="goldlog"></div> for detecting a rockfall in the survey' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => 2 , 
						'source' => $thiscard['location']."_item_card_". $thiscard['id']
						) );		
			}
			break;
		}
	}
	
	self::notifyPlayer( $player_id, "browsecards", clienttranslate( '${player_name} : These are the cards you can see on the survey of Excavation site: ${sitenr}' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'sitenr' => $sitenr ,
					'cards' => $cards
					) );
			
	$this->gamestate->nextState( 'browsecards' );
    }
	
	function viewdone()
    {
	self::checkAction( 'viewdone' );
	
	$this->gamestate->nextState( );
    }
	
	function recruit()
    {
	self::checkAction( 'recruit' );
	$player_id = self::getActivePlayerId();
	if ( self::getGoldBalance($player_id) >=5 ) 
		{
		self::DbQuery( "UPDATE player set player_gold = player_gold - 5 WHERE Player_id = $player_id" );	
		self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 5 <div class="goldlog"></div> to hire a new adventurer' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => 5 ,  
						'destination' => "workersC"
						) );	
		self::DbQuery( "UPDATE tokens SET card_location='TH_$player_id' WHERE card_type='3' AND card_type_arg=$player_id " );
		self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} gets a new adventurer.' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'destination' => "TH_".$player_id,
					'tile_id' => "tile_".$player_id."_3"
					) );
			
		}
	
    }
	
	function buy()
    {
	self::checkAction( 'buy' );
	$player_id = self::getActivePlayerId();
	if ( self::getGoldBalance($player_id) >=5 ) 
		{
		self::DbQuery( "UPDATE player set player_gold = player_gold - 5 WHERE Player_id = $player_id" );	
		self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 5 <div class="goldlog"></div> to the Counter' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => 5 ,  
						'destination' => "counterC"
						) );	
		$xp=2;
		self::DbQuery( "UPDATE player set player_xp = player_xp + $xp WHERE Player_id = $player_id" );
		$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location,card_location_arg) VALUES (6,$xp,'$player_id',1)";
		self::DbQuery( $sql );
		$token_id=self::DbGetLastId();

		self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} buys 2 <div class="xplog"></div> points at the Counter' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'amount' => $xp,
				'source' => "playercardstore_".$player_id,
				'token_id' => $token_id ,
				'NOSELL' =>  1
				) );		
		}
	$this->gamestate->nextState( );
    }
	
	function sell($token_id)
    {
	self::checkAction( 'sell' );
	
	$token=self::getObjectFromDB ("SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens WHERE card_id=$token_id");
	$player_id = self::getActivePlayerId();
	
	
	if (  ($token['type'] == 6) AND ($token['location'] == $player_id) AND ($token['location_arg']==0 ) ) 
		{
		
		$gold=$token['type_arg']*5;
		$xp=$token['type_arg'];
		
		self::DbQuery('DELETE FROM tokens where card_id='.$token_id);
		self::notifyAllPlayers( "playersellxp", clienttranslate( '${player_name} sells a ${amount} XP token to the Counter' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'amount' => $xp,
				'source' => "xpstore_".$player_id,
				'token_id' => $token_id 
				) );
		self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );	
		self::notifyAllPlayers( "playergetsgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div> from the Counter' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => $gold ,  
						'source' => "counterC"
				) );	
		}
		else 
		{
			self::notifyAllPlayers( 'message', 'GAME ERROR: ${player_name} tried to sell an invalid XP token', array(
							'player_name' => self::getActivePlayerName()
							) );
		}
	$this->gamestate->nextState( );
    }
	
	function selectcards($deckpicked,$token_id)
    {
	self::checkAction( 'selectcards' );
	$player_id = self::getActivePlayerId();
	$expertpicked=self::getGameStateValue( 'expertpicked' );
	switch ($expertpicked) 
		{  
		case 1:
				$topcards=$this->cards->getCardsOnTop( 2, $deckpicked );
				for  ($i=0 ; $i < sizeof($topcards) ; $i++ )
				{
					$card=self::getObjectFromDB( "SELECT * FROM cards WHERE card_id=".$topcards[$i]['id'] );
					$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$topcards[$i]['id'];
							self::DbQuery( $sql );
					self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} uses the Miner to dig a card on the excavation site: ${sitenr}' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'sitenr' => substr( $deckpicked ,-1),
						'card' => $topcards[$i]
						) );
					switch ($topcards[$i]['type'] ) 
					{
						case  "1":  //GALLERIES
						case  "7":
						case "17":
						        self::incStat (1,"cards_digged_player",$player_id);
								self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a gallery card with the Miner.' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'destination' => "playercardstore_".$player_id,
									'tile_id' => "card_". $topcards[$i]['id'],
									'deck' => $topcards[$i]['location']
									) );
								$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcards[$i]['id'];
								self::DbQuery( $sql );	
								break;
						case  "4":     //GO HOSPITAL
						case  "9": 
						case "15":
						case "3":       //GET EXP
						case "8":
						case "16":
								self::incStat (1,"cards_digged_player",$player_id);
								self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs an XP card.' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'destination' => "playercardstore_".$player_id,
									'tile_id' => "card_". $topcards[$i]['id'],
									'deck' => $topcards[$i]['location']
									) );
								$xp=$this->card_types[$topcards[$i]['type']]['xp'];
								self::DbQuery( "UPDATE player set player_xp = player_xp + $xp WHERE Player_id = $player_id" );
								$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcards[$i]['id'];
								self::DbQuery( $sql );
								$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
								self::DbQuery( $sql );
								$token_id=self::DbGetLastId();
								self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} gets ${amount} <div class="xplog"></div> points' ), array(
										'player_id' => $player_id,
										'player_name' => self::getActivePlayerName(),
										'amount' => $xp,
										'source' => "playercardstore_".$player_id,
										'token_id' => $token_id
										) );		
								break;
						case "13":       // TREASURE
						case "18":
						        self::incStat (1,"cards_digged_player",$player_id);
								self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a treasure card on ${deck}.' ), array(
										'player_id' => $player_id,
										'player_name' => self::getActivePlayerName(),
										'destination' => "playercardstore_".$player_id,
										'tile_id' => "card_". $topcards[$i]['id'],
										'deck' => $topcards[$i]['location']
										) );
								$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcards[$i]['id'];
								self::DbQuery( $sql );
								self::sttreasure(true);
								break;
						case "2":       // ROCKFALL
						case "14":
								self::incStat (1,"cards_digged_player",$player_id);
								self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a rockfall card on ${deck}.' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'destination' => "playercardstore_".$player_id,
									'tile_id' => "card_". $topcards[$i]['id'],
									'deck' => $topcards[$i]['location']
									) );
								$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcards[$i]['id'];
								self::DbQuery( $sql );	
								break;
						case "5":        //MONSTER
						case "6":
						case "10":
						case "11":
						case "12":
						case "19":
						case "20":
						case "21":
								self::incStat (1,"cards_digged_player",$player_id);
								$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$topcards[$i]['id'];
									self::DbQuery( $sql );
									self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} digged into a monster in site: ${sitenr} and the Miner runs home' ), array(
											'player_id' => $player_id,
											'player_name' => self::getActivePlayerName(),
											'sitenr' => substr( $deckpicked ,-1),
											'card' => $topcards[$i]
											) );
								break 2;   // BREAK 2 !!!
						case "22":    // STONE OF LEGEND
						case "23":
								self::incStat (1,"cards_digged_player",$player_id);
								self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} FINDS A STONE OF LEGEND!!!' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'destination' => "playercardstore_".$player_id,
									'tile_id' => "card_". $topcards[$i]['id'],
									'deck' => $topcards[$i]['location']
									) );
								$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcards[$i]['id'];
								self::DbQuery( $sql );
								$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (". ( $topcards[$i]['type'] -11) .",0,'playercardstore_$player_id')";
								self::DbQuery( $sql );
								$token_id=self::DbGetLastId();
								$thetoken=self::getObjectFromDB("SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens where card_id=$token_id");
						
								self::notifyAllPlayers( "placestone", clienttranslate( '${player_name} collects a Stone of Legend' ), array(
										'player_id' => $player_id,
										'player_name' => self::getActivePlayerName(),
										'token' => $thetoken
										) );		
								
								self::incGameStateValue('stonesfound',1);
								break;
					}
				}
				$this->gamestate->nextState('playermove');
				break;
			case 3:
				$topcards=$this->cards->getCardsOnTop( 5 , $deckpicked );
				self::setGameStateValue('monsterpresent' ,0 );
				self::notifyPlayer( $player_id, "browsecards", clienttranslate( '${player_name} : These are the cards detected by the Archeologist on the survey of Excavation site: ${sitenr}' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'sitenr' => substr( $deckpicked ,-1),
								'cards' => $topcards
								) );
				$this->gamestate->nextState( 'browsecards' );
			    break;
			case 4:
				$sql="SELECT card_location_arg from cards WHERE card_id=$token_id";
				$result = self::getUniqueValueFromDB( $sql ) ;
				$sql = "SELECT card_id id, card_location_arg location_arg, card_type type, card_type_arg type_arg , card_location location from cards WHERE card_location like '$deckpicked' AND card_location_arg in ( $result , $result +1 , $result -1 )";
				$topcards=self::getObjectListFromDB( $sql );
				self::setGameStateValue('monsterpresent' ,0 );
				self::notifyPlayer( $player_id, "browsecards", clienttranslate( '${player_name} : These are the cards detected by the Soothsayer on the survey of Excavation site: ${sitenr}' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'sitenr' => substr( $deckpicked ,-1),
								'cards' => $topcards
								) );
				$this->gamestate->nextState( 'browsecards' );
			    break;	
		}
	}
	
	function pickexpert($expertpicked)
    {
	self::checkAction( 'pickexpert' );
	$player_id = self::getActivePlayerId();
	self::setGameStateValue( 'expertpicked', 0 );
	$impersonated="";
	
	switch ($expertpicked) 
		{  
		case "expert1":
					$sql = "SELECT card_location from tokens where card_type='7'";
					$result = self::getUniqueValueFromDB( $sql ) ;
					if ( self::getGoldBalance($player_id) >=5 AND $result=="expertholder1" ) 
					{
					self::DbQuery( "UPDATE player set player_gold = player_gold - 5 WHERE Player_id = $player_id" );	
					self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 5 <div class="goldlog"></div> to hire the Miner' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'amount' => 5 ,  
									'destination' => "expertholder1"
									) );
					
					$sql="UPDATE tokens SET card_location='playercardstore_".$player_id."' WHERE card_type='7' LIMIT 1";
					self::DbQuery( $sql );
					
					self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} hires the Miner.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "expert1"
								) );
					}
					self::setGameStateValue( 'expertpicked', 1 );
					$this->gamestate->nextState('sendexpert');
					break;
		case "expert10":$gold=7;
						$impersonated= clienttranslate('The Miner');
						self::setGameStateValue( 'expertpicked', 1 );
			
		case "expert30": if ($impersonated=="")
						{
						$gold=4;
						$impersonated= clienttranslate('The Archeologist');
						self::setGameStateValue( 'expertpicked', 3 );
						}
		case "expert40": if ($impersonated=="")
						{
						$gold=5;
						$impersonated= clienttranslate('The Soothsayer');
						self::setGameStateValue( 'expertpicked', 4 );
						}
						
					$sql = "SELECT card_location from tokens where card_type='8'";
					$result = self::getUniqueValueFromDB( $sql ) ;
					if ( self::getGoldBalance($player_id) >= $gold  AND $result=="expertholder2" ) 
					{
					self::DbQuery( "UPDATE player set player_gold = player_gold - $gold WHERE Player_id = $player_id" );	
					self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays ${amount} <div class="goldlog"></div> to hire the Impersonator acting as ${impersonated}' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'amount' => $gold ,  
									'destination' => "expertholder2",
									'impersonated' => $impersonated
									) );
					
					$sql="UPDATE tokens SET card_location='playercardstore_".$player_id."' WHERE card_type='8' LIMIT 1";
					self::DbQuery( $sql );
					
					self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} hires the Impersonator acting as ${impersonated}' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "expert2",
								'impersonated' => $impersonated
								) );
						
					}
					
					$this->gamestate->nextState('sendexpert');
		
					break;
		case "expert3":
					$sql = "SELECT card_location from tokens where card_type='9'";
					$result = self::getUniqueValueFromDB( $sql ) ;
					if ( self::getGoldBalance($player_id) >=2  AND $result=="expertholder3" ) 
					{
					self::DbQuery( "UPDATE player set player_gold = player_gold - 2 WHERE Player_id = $player_id" );	
					self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 2 <div class="goldlog"></div> to hire the Archeologist' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'amount' => 2 ,  
									'destination' => "expertholder3"
									) );
					
					$sql="UPDATE tokens SET card_location='playercardstore_".$player_id."' WHERE card_type='9' LIMIT 1";
					self::DbQuery( $sql );
					
					self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} hires the Archeologist.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "expert3"
								) );
						
					}
					self::setGameStateValue( 'expertpicked', 3 );
					$this->gamestate->nextState('sendexpert');
					break;
		
			
		case "expert4":
					$sql = "SELECT card_location from tokens where card_type='10'";
					$result = self::getUniqueValueFromDB( $sql ) ;
					if ( self::getGoldBalance($player_id) >=3  AND $result=="expertholder4" ) 
					{
					self::DbQuery( "UPDATE player set player_gold = player_gold - 3 WHERE Player_id = $player_id" );	
					self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 3 <div class="goldlog"></div> to hire the Soothsayer' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'amount' => 3 ,  
									'destination' => "expertholder4"
									) );
					
					$sql="UPDATE tokens SET card_location='playercardstore_".$player_id."' WHERE card_type='10' LIMIT 1";
					self::DbQuery( $sql );
					
					self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} hires the Soothsayer.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "expert4"
								) );
						
					}
					self::setGameStateValue( 'expertpicked', 4 );
					$this->gamestate->nextState('sendexpert');
					break;
		
		}
    }
	
	function payhospital()
    {
	self::checkAction( 'payhospital' );
	
	$player_id = self::getActivePlayerId();
	if ( self::getGoldBalance($player_id) >=2 ) 
		{
		self::DbQuery( "UPDATE player set player_gold = player_gold - 2 WHERE Player_id = $player_id" );	
		self::notifyAllPlayers( "playerpaysgold", clienttranslate( '${player_name} pays 2 <div class="goldlog"></div> to the Hospital' ), array(
						'player_id' => $player_id,
						'player_name' => self::getActivePlayerName(),
						'amount' => 2 ,  
						'destination' => "HospitalC"
						) );
		$token_id=self::getUniqueValueFromDB( "SELECT card_id c from tokens WHERE card_location='HospitalC' AND card_type_arg=$player_id LIMIT 1" );				
		$sql="UPDATE tokens SET card_location='TH_".$player_id."' WHERE card_id=".$token_id." LIMIT 1";
		self::DbQuery( $sql );
		
		$tile_id=self::getUniqueValueFromDB ("SELECT card_type from tokens where card_id=$token_id");
		self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} recovers an adventurer from Hospital.' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'destination' => "TH_".$player_id,
					'tile_id' => "tile_".$player_id."_".$tile_id
					) );
			
		}
	
    }
	
	function finish()
    {
	self::checkAction( 'finish' );
	
	$this->gamestate->nextState( );
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
	function argMonsterpresent()
    {
        return array(
            'monsterpresent' => self::getGameStateValue( 'monsterpresent' )
        );
    }
	
	function argExpertpicked()
    {
        return array(
            'expertpicked' => self::getGameStateValue( 'expertpicked' )
        );
    }
	
	function argMapowner()
    {
        return array(
            'mapowner' => self::getGameStateValue( 'mapowner' )
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
		$this->activeNextPlayer();
		self::setGameStateValue( 'playermoves' , 0 );
		$this->gamestate->nextState( 'playermove' );
		
	}
	////////////////////////////////////////////////////////////////////////////
	
	function stplayermove()
	{
		    self::incGameStateValue( 'playermoves' , 1 );
			$player_id = self::getActivePlayerId();
			self::giveExtraTime($player_id);
			$gold=self::getGoldBalance($player_id);
			if ( self::getGameStateValue('playermoves') == 1 AND $gold > 2 )
			{
				self::notifyPlayer($player_id, "activatesword", clienttranslate( '${player_name} can rent now the sword' ) , array( 'player_name' => self::getActivePlayerName() ) );	
			}
			
			$emptydecks=self::getUniqueValueFromDB("SELECT COUNT(*) FROM (SELECT COUNT(CARD_ID) c FROM cards WHERE card_location like 'deck%' GROUP BY CARD_LOCATION) cardsondecks WHERE c=0");
			if (( $emptydecks >= 5 AND self::getGameStateValue('stonesfound') == 1  ) OR (self::getGameStateValue('stonesfound') == 2))
			{
				$this->gamestate->nextState( 'gameEndScoring' );
			}
			else 
			{
				$sql = "SELECT COUNT(*) from tokens where card_location = 'TH_$player_id'";
				$availableAdventurers = self::getUniqueValueFromDB( $sql );
				if ( $availableAdventurers < 1 )
				{
					$this->gamestate->nextState( 'endturn' );
				}
			}		
	}
	////////////////////////////////////////////////////////////////////////////
	function stendturn()
	{
		$player_id = self::getActivePlayerId();
		self::DbQuery( "UPDATE tokens SET card_location='TH_$player_id' WHERE card_type_arg=$player_id AND card_type in ('1','2','3') and ((card_location like 'explore%') or (card_location in ('diveC','counterC','expertsC','WaitingroomC'))) " );
		self::DbQuery( "UPDATE tokens SET card_location='swordholder' WHERE card_type='4'" );
		$sql = "SELECT COUNT(*) FROM tokens where card_location in ('workersC') and card_type_arg=$player_id";
		$tilesb = self::getUniqueValueFromDB( $sql );   // DOES THE PLAYER HAS TILES TO PAY FOR?
		$sql = "SELECT COUNT(*) FROM tokens where card_location in ('HospitalC') and card_type_arg=$player_id";
		$tilesh = self::getUniqueValueFromDB( $sql );   // DOES THE PLAYER HAS TILES TO PAY FOR?
		$tilest=$tilesh+$tilesb;
		$gold=self::getGoldBalance($player_id);
		if ( $tilest == 0 OR (($gold < 2  AND $tilesh > 0 ) AND ($tilesb>0 AND $gold < 5 )) )
		{
			$this->gamestate->nextState( );
		}
		
	}
	
	////////////////////////////////////////////////////////////////////////////
	
	function stfinish()
	{
		$player_id = self::getActivePlayerId();
		self::DbQuery( "UPDATE tokens SET card_location='WaitingroomC' WHERE card_type_arg=$player_id AND card_type in ('1','2','3') and (card_location like 'HospitalC') " );
		
		self::DbQuery( "UPDATE tokens SET card_location=CONCAT('expertholder',card_type_arg ) , card_location_arg=0 WHERE card_type in ('7','8','9','10') and (card_location like 'playercardstore_$player_id') AND card_location_arg=1 " );
		
		self::DbQuery( "UPDATE tokens SET card_location_arg=1 WHERE card_type in ('7','8','9','10') and (card_location like 'playercardstore_$player_id') " );
		
			$this->gamestate->nextState( );
		
		
	}
	////////////////////////////////////////////////////////////////////////////
	
		
	function stexploresite()
	{

		//$this->gamestate->nextState( );
		
	}
	
	////////////////////////////////////////////////////////////////////////////
	
	function stbrowsecards()
	{

		
	}
	
	////////////////////////////////////////////////////////////////////////////
	function stfight()
	{
		$player_id = self::getActivePlayerId();
		$sitenr= self::getGameStateValue('currentsite');
		$topcard=$this->cards->getCardOnTop( 'deck'.$sitenr );
		$life=$this->card_types[$topcard['type']]['life'];
		$monstername=$this->card_types[$topcard['type']]['name'];
		$wounds=0;
		$sql = "SELECT card_location FROM tokens WHERE card_type like '4' LIMIT 1";
		$swlocation = self::getUniqueValueFromDB( $sql );   // DID THE PLAYER RENT THE SWORD?
		
		if ( $swlocation != 'playerSwordholder_'.$player_id)
		{
			$sql = "SELECT card_type FROM tokens WHERE card_location like 'explore$sitenr' LIMIT 1";
			$tile = self::getUniqueValueFromDB( $sql );
			self::DbQuery( "UPDATE tokens set card_location = 'HospitalC' WHERE card_type = '$tile' AND card_type_arg=$player_id LIMIT 1 " );
			
			self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} does not have the sword. The adventurer is injured by the Monster and has to go to Hospital.' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'destination' => "HospitalC",
				'tile_id' => "tile_".$player_id."_".$tile
				) );	
		}
		else do {
			$result=mt_rand (1,6);
			self::notifyAllPlayers( "rolldice", "" , array(	'result' => $result  ) );
			if ($result<5)   // dice sword  -> place wound
			{ 
				self::DbQuery( "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (5,0,'deck".$sitenr."_item_card_".$topcard['id']."_back')");
				$token_id=self::DbGetLastId();
				$thetoken=self::getObjectFromDB("SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens where card_id=$token_id");
				self::notifyAllPlayers( "placewound", clienttranslate( '${player_name} hits ${monstername} with the sword. The ${monstername} takes a wound.' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'token' => $thetoken,
					'monstername' => $monstername
					
					) );
				
			}
			else     // Player injured -> go to hospital
			{
				$sql = "SELECT card_type FROM tokens WHERE card_location like 'explore$sitenr' LIMIT 1";
				$tile = self::getUniqueValueFromDB( $sql );
				self::DbQuery( "UPDATE tokens set card_location = 'HospitalC' WHERE card_type = '$tile' AND card_type_arg=$player_id LIMIT 1 " );
				
				self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} adventurer is injured by the ${monstername} and has to go to Hospital.' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'destination' => "HospitalC",
					'monstername' => $monstername,
					'tile_id' => "tile_".$player_id."_".$tile
					) );
				;
				break;
			} 
			$wounds=self::getUniqueValueFromDB( "SELECT COUNT(*) c FROM tokens where card_location='deck".$sitenr."_item_card_".$topcard['id']."_back'" );		
		} while ($life > $wounds);
		
		if ( $wounds >= $life )
		{   
			self::incStat (1,"cards_digged_player",$player_id);
			self::DbQuery( "DELETE FROM tokens WHERE card_location ='deck".$sitenr."_item_card_".$topcard['id']."_back'");
			self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} beats the ${monstername}.' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'destination' => "playercardstore_".$player_id,
				'tile_id' => "card_". $topcard['id'],
				'monstername' => $monstername,
				'deck' => $topcard['location']
				) );
			$xp=$this->card_types[$topcard['type']]['xp'];
			$gold=$this->card_types[$topcard['type']]['gold'];
			
			if ($gold>0)
			{
				self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );
				$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
				self::DbQuery( $sql );
	
				self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div>' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'amount' => $gold,
					'source' => "playercardstore_".$player_id
					) );
			}
			if ($xp>0)
			{
				self::DbQuery( "UPDATE player set player_xp = player_xp + $xp WHERE Player_id = $player_id" );
				$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
				self::DbQuery( $sql );
				$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
				self::DbQuery( $sql );
				$token_id=self::DbGetLastId();
		
				self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} gets ${amount} <div class="xplog"></div> points' ), array(
					'player_id' => $player_id,
					'player_name' => self::getActivePlayerName(),
					'amount' => $xp,
					'source' => "playercardstore_".$player_id,
					'token_id' => $token_id
					) );
			}
		}
		
		
		
		$this->gamestate->nextState();
	}
	
	////////////////////////////////////////////////////////////////////////////
	
		
	function stdig()
	{
        
		$player_id = self::getActivePlayerId();
		$sitenr= self::getGameStateValue('currentsite');
		$topcard=$this->cards->getCardOnTop( 'deck'.$sitenr );
		$gohospital=false;yep
		//var_dump ($this->card_types[$topcard['type']]['name'] ) ;
		switch ($topcard['type'] ) 
		{
			case  "1":  //GALLERIES
			case  "7":
			case "17":
						self::incStat (1,"cards_digged_player",$player_id);
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a gallery card.' ), array(
							'player_id' => $player_id,
							'player_name' => self::getActivePlayerName(),
							'destination' => "playercardstore_".$player_id,
							'tile_id' => "card_". $topcard['id'],
							'deck' => $topcard['location']
							) );
						$gold=$this->card_types[$topcard['type']]['gold'];
						self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );
				        $sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
				
						self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div> for digging a gallerie card' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $gold,
								'source' => "playercardstore_".$player_id
								) );		
						break;
			case  "4":     //GO HOSPITAL
			case  "9": 
			case "15":
			            $gohospital=true; 
			
			case "3":       //GET EXP
			case "8":
			case "16":
						self::incStat (1,"cards_digged_player",$player_id);
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs an XP card.' ), array(
							'player_id' => $player_id,
							'player_name' => self::getActivePlayerName(),
							'destination' => "playercardstore_".$player_id,
							'tile_id' => "card_". $topcard['id'],
							'deck' => $topcard['location']
							) );
						$xp=$this->card_types[$topcard['type']]['xp'];
						self::DbQuery( "UPDATE player set player_xp = player_xp + $xp WHERE Player_id = $player_id" );
				        $sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
						$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
						self::DbQuery( $sql );
						$token_id=self::DbGetLastId();
				
						self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} gets ${amount} <div class="xplog"></div> points' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $xp,
								'source' => "playercardstore_".$player_id,
								'token_id' => $token_id
								) );		
						if ($gohospital==true)
						{
							
							$sql = "SELECT card_type FROM tokens WHERE card_location like 'explore$sitenr' LIMIT 1";
							$tile = self::getUniqueValueFromDB( $sql );
							self::DbQuery( "UPDATE tokens set card_location = 'HospitalC' WHERE card_type = '$tile' AND card_type_arg=$player_id LIMIT 1 " );
							
							self::notifyAllPlayers( "movetoken", clienttranslate( 'The adventurer from ${player_name} is injured and has to go to Hospital.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "HospitalC",
								'tile_id' => "tile_".$player_id."_".$tile
								) );	
						}
						
						break;
			case "13":       // TREASURE
			case "18":
						self::incStat (1,"cards_digged_player",$player_id);
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a treasure card on ${deck}.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "card_". $topcard['id'],
								'deck' => $topcard['location']
								) );
						$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
						$this->gamestate->nextState("gettreasure");
						
						break;
			case "2":       // ROCKFALL
			case "14":
						$advcount=self::getUniqueValueFromDB( "SELECT COUNT(*) FROM tokens where card_location='explore$sitenr'" );
						if ($advcount >= 2) 
						{
							self::incStat (1,"cards_digged_player",$player_id);
							self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} digs a rockfall card on ${deck}.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "card_". $topcard['id'],
								'deck' => $topcard['location']
								) );
							$gold=self::getUniqueValueFromDB( "SELECT COUNT(*) FROM cards where card_location like 'deck%' and card_status=1 AND card_type in ('2','14') "  );
							$gold=$gold * 2 ;  // visible rockfalls x 2
							self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );
							$sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
							self::DbQuery( $sql );
					
							self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div> for digging a rockfall ( 2 x visible rockfalls)' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'amount' => $gold,
									'source' => "playercardstore_".$player_id
									) );
						}
						else
						{
							self::notifyAllPlayers( 'message', clienttranslate( '${player_name} sent a first adventurer to dig a rockfall on site ${sitenr} (2 adventurers required)'), array(
							'player_name' => self::getActivePlayerName(),
								'sitenr' => $sitenr
						
							) );
						}
						break;
			case "5":        //MONSTER
			case "6":
			case "10":
			case "11":
			case "12":
			case "19":
			case "20":
			case "21":
						// DID THE PLAYER RENT THE SWORD?
						$sql = "SELECT card_location FROM tokens WHERE card_type like '4' LIMIT 1";
						$swlocation = self::getUniqueValueFromDB( $sql );
						
						$sql = "UPDATE cards set card_status = 1 WHERE card_id = ".$topcard['id'];
							self::DbQuery( $sql );
							self::notifyAllPlayers( "revealcard", clienttranslate( '${player_name} digs into a monster in site: ${sitenr}' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'sitenr' => $sitenr ,
									'card' => $topcard
									) );
						
						
						if ( $swlocation != 'playerSwordholder_'.$player_id)
						{
							$sql = "SELECT card_type FROM tokens WHERE card_location like 'explore$sitenr' LIMIT 1";
							$tile = self::getUniqueValueFromDB( $sql );
							self::DbQuery( "UPDATE tokens set card_location = 'HospitalC' WHERE card_type = '$tile' AND card_type_arg=$player_id LIMIT 1 " );
							
							self::notifyAllPlayers( "movetoken", clienttranslate( '${player_name} does not have the sword. The adventurer is injured by the Monster and has to go to Hospital.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "HospitalC",
								'tile_id' => "tile_".$player_id."_".$tile
								) );	
						}
						
						break;
			case "22":    // STONE OF LEGEND
			case "23":
						self::incStat (1,"cards_digged_player",$player_id);
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} FINDS A STONE OF LEGEND!!!' ), array(
							'player_id' => $player_id,
							'player_name' => self::getActivePlayerName(),
							'destination' => "playercardstore_".$player_id,
							'tile_id' => "card_". $topcard['id'],
							'deck' => $topcard['location']
							) );
						
				        $sql = "UPDATE cards set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
						$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (". ( $topcard['type'] -11) .",0,'playercardstore_$player_id')";
						self::DbQuery( $sql );
						$token_id=self::DbGetLastId();
						$thetoken=self::getObjectFromDB("SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens where card_id=$token_id");
				
						self::notifyAllPlayers( "placestone", clienttranslate( '${player_name} collects a Stone of Legend' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'token' => $thetoken
								) );		
						
						self::incGameStateValue('stonesfound',1);
						
						break;
		}					
		
		$this->gamestate->nextState("playermove");
		
	}

////////////////////////////////////////////////////////////////////////////
	
	function sttreasure($callback = false)
	{
        
		$player_id = self::getActivePlayerId();
		$topcard=$this->treasures->getCardOnTop( 'deck' );
		self::notifyAllPlayers( "fliptreasure", clienttranslate( '${player_name} reveals a treasure card ' ), array(
				'player_id' => $player_id,
				'player_name' => self::getActivePlayerName(),
				'card' => $topcard
					) );
		$cardname=  $this->treasure_types[$topcard['type']]['name'];
		
		
		switch ($topcard['type'] ) 
		{
			case  "1":  
			case  "4":
			case  "5":
			case  "6":
			case  "7":
			case  "8":
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} treasure card is a ${cardname} !' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "treasure_". $topcard['id'],
								'deck' => 'treasuredeck',
								'cardname' => $cardname
									) );
						$sql = "UPDATE treasures set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
							
						$xp=$this->treasure_types[$topcard['type']]['xp'];
						$gold=$this->treasure_types[$topcard['type']]['gold'];
						
						if ($gold>0)
						{
							self::DbQuery( "UPDATE player set player_gold = player_gold + $gold WHERE Player_id = $player_id" );
							self::notifyAllPlayers( "playergetgold", clienttranslate( '${player_name} gets ${amount} <div class="goldlog"></div>' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $gold,
								'source' => "playercardstore_".$player_id
								) );
						}
						if ($xp!=0)
						{
							$NOSELL=0;
							if ($xp < 0 )
							{
								$NOSELL=1;
							}
							self::DbQuery( "UPDATE player set player_xp = player_xp + ( $xp ) WHERE Player_id = $player_id" );
							$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
							self::DbQuery( $sql );
							$token_id=self::DbGetLastId();
							self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} gets ${amount} <div class="xplog"></div> points' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $xp,
								'source' => "playercardstore_".$player_id,
								'token_id' => $token_id,
								'NOSELL' => $NOSELL
								) );
						}
						
						break;
			case  "3":     
						$life=2;
						$monstername=$cardname;
						$wounds=0;
						do {
							$result=mt_rand (1,6);
							self::notifyAllPlayers( "rolldice", "" , array(	'result' => $result  ) );
							if ($result<5)   // dice sword  -> place wound
							{ 
								self::DbQuery( "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (5,0,'treasuredeck_item_treasure_".$topcard['id']."_back')");
								$token_id=self::DbGetLastId();
								$thetoken=self::getObjectFromDB("SELECT card_id id, card_type type, card_type_arg type_arg, card_location location, card_location_arg location_arg from tokens where card_id=$token_id");
								self::notifyAllPlayers( "placewound", clienttranslate( '${player_name} hits ${monstername} with the sword. The ${monstername} takes a wound.' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'token' => $thetoken,
									'monstername' => $monstername
									) );	
							}
							else     //  Mimic chest escapes
							{
								self::notifyAllPlayers( "message", clienttranslate( '${player_name} fails to defeat the ${monstername}' ), array(
									'player_id' => $player_id,
									'player_name' => self::getActivePlayerName(),
									'monstername' => $monstername
									) );
								;
								break;
							} 
							$wounds=self::getUniqueValueFromDB( "SELECT COUNT(*) c FROM tokens where card_location='treasuredeck_item_treasure_".$topcard['id']."_back'" );		
						} while ($life > $wounds);
						self::DbQuery( "DELETE FROM tokens WHERE card_location ='treasuredeck_item_treasure_".$topcard['id']."_back'");
							self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} fight with the ${cardname} ends.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "treasure_". $topcard['id'],
								'deck' => 'treasuredeck',
								'cardname' => $cardname
									) );
								
							$sql = "UPDATE treasures set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						    self::DbQuery( $sql );
							
						if ( $wounds >= $life )
						{   
							
							$xp=2;
							self::DbQuery( "UPDATE player set player_xp = player_xp + $xp WHERE Player_id = $player_id" );
								
							$sql = "INSERT INTO tokens ( card_type, card_type_arg, card_location) VALUES (6,$xp,'$player_id')";
							self::DbQuery( $sql );
							$token_id=self::DbGetLastId();
					
							self::notifyAllPlayers( "playergetxp", clienttranslate( '${player_name} wins and gets ${amount} <div class="xplog"></div> points and another treasure card.' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'amount' => $xp,
								'source' => "playercardstore_".$player_id,
								'token_id' => $token_id
								) );
							
							self::sttreasure(true);	
						}
						break;
			case  "2":   
						self::notifyAllPlayers( "removecard", clienttranslate( '${player_name} treasure card is a ${cardname}! ' ), array(
								'player_id' => $player_id,
								'player_name' => self::getActivePlayerName(),
								'destination' => "playercardstore_".$player_id,
								'tile_id' => "treasure_". $topcard['id'],
								'deck' => 'treasuredeck',
								'cardname' => $cardname
									) );
						$sql = "UPDATE treasures set card_location = 'removed' WHERE card_id = ".$topcard['id'];
						self::DbQuery( $sql );
						self::setGameStateValue ('mapowner',$player_id);
									
						break;
		}					
		if ($callback!=true)
		{
			$this->gamestate->nextState();
		}	
		
	}
	
////////////////////////////////////////////////////////////////////////////

    function displayScores()
    {
        $players = self::loadPlayersBasicInfos();

        $table[] = array();
        
        //left hand col
        $table[0][] = array( 'str' => ' ', 'args' => array(), 'type' => 'header');
        $table[1][] = $this->resources["gold"    ];
        $table[2][] = $this->resources["experience"  ];
        $table[3][] = $this->resources["stone of legend"];
		$table[4][] = clienttranslate(' Score =  XP + ( Gold / 5 ) + Stones * 10 ');

        foreach( $players as $player_id => $player )
        {
            $table[0][] = array( 'str' => '${player_name}',
                                 'args' => array( 'player_name' => $player['player_name'] ),
                                 'type' => 'header'
                               );
            

            $gold = $this->getGoldBalance (  $player_id );
			$XP = $this->getXPBalance (  $player_id );
			$stones = $this->getStoneBalance (  $player_id );
			
			self::setStat( $gold , "gold", $player_id );
			self::setStat( $XP , "experience", $player_id );
			self::setStat( $stones , "stones_found", $player_id );
			
			if ( self::getGameStateValue('stonesfound') < 2 )
			{
				$stones=0;				
			}
			
			$table[1][] = $gold;
            $table[2][] = $XP;
			$table[3][] = $stones;
						
			$score = floor( $gold / 5  ) + $XP + $stones * 10  ;
			$score_aux=$score;
			
			$table[4][] = $score_aux ;
			
			if ($stones==2)
			{
			 	$score = 1000 ;
			}
			
			
			$sql = "UPDATE player SET player_score = ".$score." WHERE player_id=".$player['player_id'];
            self::DbQuery( $sql );
			
			$sql = "UPDATE player SET player_score_aux = ".$XP." WHERE player_id=".$player['player_id'];
            self::DbQuery( $sql );
				
            
        }
		

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
		
		$sql = "SELECT COUNT(*) FROM cards WHERE card_location like 'removed' ";
        $cardsplayed = self::getUniqueValueFromDB( $sql );
		self::setStat( self::getGameStateValue('stonesfound') , "stones_found" );
		self::setStat( $cardsplayed , "cards_digged" );
    
        $this->gamestate->nextState();
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
