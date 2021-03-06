<?php
/**
 *------
 * BGA framework: (c) Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * takaraisland implementation : (c) Antonio Soler <morgald.es@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * takaraisland.view.php
 *
 * This is your "view" file.
 *
 * The method "build_page" below is called each time the game interface is displayed to a player, ie:
 * _ when the game starts
 * _ when a player refreshes the game page (F5)
 *
 * "build_page" method allows you to dynamically modify the HTML generated for the game interface. In
 * particular, you can set here the values of variables elements defined in takaraisland_takaraisland.tpl (elements
 * like {MY_VARIABLE_ELEMENT}), and insert HTML block elements (also defined in your HTML template file)
 *
 * Note: if the HTML of your game interface is always the same, you don't have to place anything here.
 *
 */
  
  require_once( APP_BASE_PATH."view/common/game.view.php" );
  
  class view_takaraisland_takaraisland extends game_view
  {
    function getGameName() {
        return "takaraisland";
    }    
  	function build_page( $viewArgs )
  	{		
  	    // Get players & players number
        $players = $this->game->loadPlayersBasicInfos();
        $players_nbr = count( $players );

        /*********** Place your code below:  ************/


        /*
        
        // Examples: set the value of some element defined in your tpl file like this: {MY_VARIABLE_ELEMENT}

        // Display a specific number / string
        $this->tpl['MY_VARIABLE_ELEMENT'] = $number_to_display;

        // Display a string to be translated in all languages: 
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::_("A string to be translated");

        // Display some HTML content of your own:
        $this->tpl['MY_VARIABLE_ELEMENT'] = self::raw( $some_html_code );
        
        */
        
        /*
        
        // Example: display a specific HTML block for each player in this game.
        // (note: the block is defined in your .tpl file like this:
        //      <!-- BEGIN myblock --> 
        //          ... my HTML code ...
        //      <!-- END myblock --> 
        

        $this->page->begin_block( "takaraisland_takaraisland", "myblock" );
        foreach( $players as $player )
        {
            $this->page->insert_block( "myblock", array( 
                                                    "PLAYER_NAME" => $player['player_name'],
                                                    "SOME_VARIABLE" => $some_value
                                                    ...
                                                     ) );
        }
        
        */
		
		$this->tpl['TABLE'] = self::_("Table");
		$this->tpl['DECKSIZE'] = self::_("Deck Size: ");
		
		$this->page->begin_block( "takaraisland_takaraisland", "camp" );
		
		$playercount=0;
		
		global $g_user;
        
		$current_player_id = $g_user->get_id();
     	
		foreach( $players as $player )	
		{
            if  ( $current_player_id  == $player['player_id'] )
			{
				$this->page->insert_block( "camp", array( "PLAYER_ID" => $player['player_id'],
			                                            "PLAYER_NAME" => $player['player_name'],
														"PLAYER_COLOR" => $player['player_color']
                                                      ));
			}
		}
		foreach( $players as $player )	
		{
            if  ( $current_player_id  != $player['player_id'] )
			{
				$this->page->insert_block( "camp", array( "PLAYER_ID" => $player['player_id'],
			                                            "PLAYER_NAME" => $player['player_name'],
														"PLAYER_COLOR" => $player['player_color']
                                                      ));
			}
		}
        /*********** Do not change anything below this line  ************/
  	}
  }
  

