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
	
	public function dig()
    {
		self::setAjaxMode();	
		$this->game->dig();
		self::ajaxResponse();    
	}
	
	public function viewdone()
    {
		self::setAjaxMode();	
		$this->game->viewdone();
		self::ajaxResponse();    
	}
	
	public function revealmonster()
    {
		self::setAjaxMode();	
		$this->game->revealmonster();
		self::ajaxResponse();    
	}
	
	public function survey()
    {
		self::setAjaxMode();	
		$this->game->survey();
		self::ajaxResponse();    
	}
	
	public function buy()
    {
		self::setAjaxMode();	
		$this->game->buy();
		self::ajaxResponse();    
	}
	
	 public function sell()
    {
		self::setAjaxMode();
		$token_id = self::getArg( "token_id", AT_posint, true );
		$this->game->sell($token_id);
		self::ajaxResponse();    
	}	
	
	 public function movetile()
    {
		self::setAjaxMode();
		$tile = self::getArg( "tile", AT_posint, true );
		$destination = self::getArg( "destination", AT_alphanum, true );
		$this->game->movetile($tile,$destination);
		self::ajaxResponse();    
	}	
	
	 public function pickexpert()
    {
		self::setAjaxMode();
		$expertpicked = self::getArg( "expertpicked", AT_alphanum, true );
		$this->game->pickexpert($expertpicked);
		self::ajaxResponse();    
	}
	
	
	 public function recruit()
    {
		self::setAjaxMode();
		$this->game->recruit();
		self::ajaxResponse();    
	}
	
	public function payhospital()
    {
		self::setAjaxMode();
		$this->game->payhospital();
		self::ajaxResponse();    
	}
	
	 public function finish()
    {
		self::setAjaxMode();
		$this->game->finish();
		self::ajaxResponse();    
	}

  }
  

