<?php
/**
 *------
 * BGA framework: � Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * takaraisland implementation : � Antonio Soler <morgald.es@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 * 
 * takaraisland.action.php
 *
 * takaraisland main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/takaraisland/takaraisland/myAction.html", ...)
 *
 */
  
  
  class action_takaraisland extends APP_GameAction
  { 
    // Constructor: please do not modify
   	public function __default()
  	{
  	    if( self::isArg( 'notifwindow') )
  	    {
            $this->view = "common_notifwindow";
  	        $this->viewArgs['table'] = self::getArg( "table", AT_posint, true );
  	    }
  	    else
  	    {
            $this->view = "takaraisland_takaraisland";
            self::trace( "Complete reinitialization of board game" );
      }
  	} 
  	
  	// defines your action entry points there


    /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */

    public function rentsword()
    {
		self::setAjaxMode();	
		$this->game->rentsword();
		self::ajaxResponse();    
	}
	
	 public function playermovetile()
    {
		self::setAjaxMode();
		$tile = self::getArg( "tile", AT_posint, true );
		$destination = self::getArg( "destination", AT_alphanum, true );
		$this->game->playermovetile($tile,$destination);
		self::ajaxResponse();    
	}	
	
	 public function pass()
    {
		self::setAjaxMode();
		$this->game->pass();
		self::ajaxResponse();    
	}

  }
  

