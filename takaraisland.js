/**
 *------
 * BGA framework: (c) Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * takaraisland implementation : (c) Antonio Soler Morgalad.es@gmail.com
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * takaraisland.js
 *
 * takaraisland user interface script
 * 
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

define([
    "dojo","dojo/_base/declare",
    "ebg/core/gamegui",
    "ebg/counter",
    "ebg/stock",
	"ebg/zone"
],
function (dojo, declare) {
    return declare("bgagame.takaraisland", ebg.core.gamegui, {
        constructor: function(){
            console.log('takaraisland constructor');
              
            // Here, you can init the global variables of your user interface
            // Example:
            // this.myGlobalValue = 0;
			this.cardwidth = 150;
            this.cardheight = 200;
			this.interface_min_width = 1020;
			
			this.control3dxaxis=50;
			this.control3dzaxis=-10;
			this.control3dxpos=0;
			this.control3dypos=0;
			this.control3dpers=4000;
			this.control3dmode3d=false;
			
        },
        
        /*
            setup:
            
            This method must set up the game user interface according to current game situation specified
            in parameters.
            
            The method is called each time the game interface is displayed to a player, ie:
            _ when the game starts
            _ when a player refreshes the game page (F5)
            
            "gamedatas" argument contains all datas retrieved by your "getAllDatas" PHP method.
        */
        
        setup: function( gamedatas )
        {
            console.log( "Starting game setup" );
            			
			this.param=new Array();
			this.gameconnections=new Array();			
			this.swordconnection=null;
										//change3d: function ( xaxis , xpos , ypos , zaxis , pers, enable3d )
			
			dojo.connect($('c3dZoomin'),  "onclick", dojo.hitch(this, this.change3d,  10 , 0 , 0 , 0 , 0 , true ));
			dojo.connect($('c3dZoomout'), "onclick", dojo.hitch(this, this.change3d,  -10 , 0 , 0 , 0 , 0 , true ));
			dojo.connect($('c3dUp'),      "onclick", dojo.hitch(this, this.change3d,  0 , 100 , 0 , 0 , 0 , true ));
			dojo.connect($('c3dDown'),    "onclick", dojo.hitch(this, this.change3d,  0 , -100 , 0 , 0 , 0 , true ));
			dojo.connect($('c3dLeft'),    "onclick", dojo.hitch(this, this.change3d,  0 , 0 , -100 , 0 , 0 , true ));
			dojo.connect($('c3dRight'),   "onclick", dojo.hitch(this, this.change3d,  0 , 0 , 100 , 0 , 0 , true ));
			dojo.connect($('c3dRotateL'), "onclick", dojo.hitch(this, this.change3d,  0 , 0 , 0 , 10 , 0 , true ));
			dojo.connect($('c3dRotateR'), "onclick", dojo.hitch(this, this.change3d,  0 , 0 , 0 , -10 , 0 , true ));
			dojo.connect($('c3dReset'),   "onclick", dojo.hitch(this, this.change3d,  0 , 0 , 0 , 0 , 0 , false ));
			
            // Setting up player boards
			
			for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // Setting up players boards if needed
                var player_board_div = $('player_board_'+player_id);
                dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );
				dojo.byId("goldcount_p"+player_id).innerHTML=player['gold'];
				dojo.byId("xpcount_p"+player_id).innerHTML=player['xp'];
            }

			decks= ["deck1","deck2","deck3","deck4","deck5","deck6"];
			for ( var i = 0; i < decks.length; i++) 
            {
			     // Create decks:	
				this[decks[i]] = new ebg.stock();
				this[decks[i]].create( this, $(decks[i]), this.cardwidth, this.cardheight );
				this[decks[i]].image_items_per_row = 7;
				this[decks[i]].setSelectionMode( 0 );
				this[decks[i]].item_margin = 0;
				this[decks[i]].setOverlap( 0.5 , 0 );
				this[decks[i]].jstpl_stock_item="<div id=\"${id}\" class=\"stockitem card\" style=\"top:${top}px;left:${left}px;z-index:${position}; transform: translatez(0.${position}em);\"> <div id=\"${id}_front\" class=\"card-front\"></div><div id=\"${id}_back\" class=\"card-back\"></div>";
                this[decks[i]].setSelectionAppearance( 'class' );				
			}
			
			this.treasuredeck = new ebg.stock();
			this.treasuredeck.create( this, $("treasuredeck"), this.cardwidth, this.cardheight );
			this.treasuredeck.image_items_per_row = 3;
			this.treasuredeck.setSelectionMode( 0 );
			this.treasuredeck.item_margin = 0;
			this.treasuredeck.setOverlap( 0.05 , 0 );
			this.treasuredeck.jstpl_stock_item="<div id=\"${id}\" class=\"stockitem card treasure\" style=\"top:${top}px;left:${left}px;z-index:${position}; transform: translatez(0.${position}em);\"> <div id=\"${id}_front\" class=\"card-front\" ></div><div id=\"${id}_back\" class=\"card-back\"></div>";

			
			for (  i in gamedatas.players ) 
            {
			     // Create decks:
                thisplayerid=this.gamedatas.players[i].id;
				thisstore="xpstore_"+thisplayerid;
				this[thisstore] = new ebg.stock();
				this[thisstore].create( this, $(thisstore), 45 , 48);
				this[thisstore].image_items_per_row = 2;
				this[thisstore].setSelectionMode( 0 );
				this[thisstore].item_margin = 1;
				this[thisstore].setOverlap( 50 , 0 );
				tarray=[1,2,4,5,8,10,-2,3]   //xp values
				for ( var c = 0; c < 8; c++) 
				{
					this[thisstore].addItemType( tarray[c] , 0 , g_gamethemeurl+'img/xp.png', c );
				}		
			}
			
            
            for( var i in this.gamedatas.cards )
            { 
				var thisdeck = this.gamedatas.cards[i].location;
				var card = this.gamedatas.cards[i];
				
				this[thisdeck].addItemType( card.id, card.location_arg, g_gamethemeurl+'img/cards.jpg', card.type_arg-1 );
				this[thisdeck].addToStockWithId( card.id , "card_"+card.id  );
				
			    // custom background positioning, to apply the backgound pos to the front instead of the father element
				xpos= -150*((card.type_arg - 1 )% this[thisdeck].image_items_per_row );
				ypos= -200*(Math.floor( (card.type_arg -1 ) / this[thisdeck].image_items_per_row ));
				position= xpos+"px "+ ypos+"px ";
				
				dojo.style(this.gamedatas.cards[i].location+'_item_card_'+card.id+"_front" , "background-position", position);
				myvalue="translateZ("+(card.location_arg*3 )+"px)";
				$(this.gamedatas.cards[i].location+'_item_card_'+card.id).style.transform = myvalue ;
				
            }
			
			for( var i in this.gamedatas.treasures )
            {		
				var card = this.gamedatas.treasures[i];
				this.treasuredeck.addItemType( card.id, card.location_arg, g_gamethemeurl+'img/treasure.jpg', 0 );
				this.treasuredeck.addToStockWithId( card.id , "treasure_"+card.id  );
				myvalue="translateZ("+(card.location_arg*3 )+"px)";
				$('treasuredeck_item_treasure_'+card.id).style.transform = myvalue ;
            }
			
			dojo.connect( $('button_deck1'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck2'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck3'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck4'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck5'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck6'), 'onclick', this, 'browseGatherDeck' );
			
			for( var i in this.gamedatas.tokens )
            {
				var thistoken = this.gamedatas.tokens[i];
				this.placetokens(thistoken);
            }
			if (this.gamedatas.activatesword)
			{ 
				dojo.addClass( 'swordholder','borderpulse' ) ;
				this.swordconnection= dojo.connect ( $('sword'),'onclick',this,'rentsword');
			}
			
			
			this.addTooltipHtml("dice",  "<div class='tooltipimage'><img src='"+ g_gamethemeurl +"img/dice.png' ></div><div  class='tooltipmessage'> " + 	
			 _(" <p><h3> &#10010; </h3> The adventurer is injured by the monster and has go to hospital. The fighting ends <p><p>  <h3> &dagger; </h3> The player has injured the monster and it takes a wound. " ) +"</div>", "" );
				
			this.addTooltipHtml("expert1",  "<div class='tooltipimage'><div class='card expertcardfront expert1' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE MINER </b>  <hr>  Permits to dig 2 tiles in of a excavation site deck. <p>XP tiles, Stones of Legend are kept by the player <p> The miner is not affected by the <b>&#10010;</b> go to hospital symbol.<p>Kara gold cards and Rockfalls are destroyed and give no reward.<p>If a monster appears there is no fight but the miner digging ends." )+"</div>", "" );
			
			this.addTooltipHtml("expert2",  "<div class='tooltipimage'><div class='card expertcardfront expert2' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE IMPERSONATOR</b> <hr>  Copies the effect of another specialist who is not available at the time. <p> For hiring this specialist you have to pay the price ot the selected specialist +2 Kara gold." )+"</div>", "" );
			
			this.addTooltipHtml("expert3",  "<div class='tooltipimage'><div class='card expertcardfront expert3' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE ARCHEOLOGIST</b> <hr> Allows the player to see the first 5 tiles on top of a excavation site deck. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>", "" );
			
			this.addTooltipHtml("expert4",  "<div class='tooltipimage'><div class='card expertcardfront expert4'></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE SOOTHSAYER </b><hr>  Allows the player to see 3 consecutive tiles of a excavation site deck at any level. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>", "" );
			
			this.addTooltipHtml("sword", "<div class='tooltipimage'><div class='sword' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE MAGIC SWORD </b> <hr>  <b> At the beggining of the turn </b>a player can rent the magic sword for  3 Kara gold. <p> This allows to fight a monster revealed on top of a deck. <p> Also if digging a monster appears there is no fight but the adventurer does not go to the hospital." )+"</div>", "" );
			
			this.addTooltipHtml("HospitalC", "<div class='tooltipimage' ><div class='hospitalthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE HOSPITAL </b> <hr> Adventurers injured during exploration or in combat come here <p> At the end of the turn a player can pay 2 Kara gold to accelerate the recovery of their injured adventurers. <p> If a player chooses not to pay the adventurers will spend another turn on the waitingroom." )+"</div>", "" );
			
			this.addTooltipHtml("WaitingroomC", "<div class='tooltipimage' ><div class='hospitalthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE HOSPITAL </b> <hr> Adventurers injured during exploration or in combat come here <p> At the end of the turn a player can pay 2 Kara gold to accelerate the rcovery of their injured adventurers. <p> If a player chooses not to pay the adventurers will spend another turn on the waitingroom." )+"</div>", "" );
			
			this.addTooltipHtml("thedive", "<div class='tooltipimage' ><div class='divethumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE DIVE </b> <hr>  The local pub of the island. <p>A player can send here up to 3 of his adventurers to make money gambling and will get 1 Kara gold for each one of them." )+"</div>", "" );
			
			this.addTooltipHtml("counter", "<div class='tooltipimage' ><div class='counterthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE COUNTER </b> <hr> A player can send here 1 Adventurer per turn <p> The operations here are:  <p> BUY XP :  the player can buy an 2 XP token for 5 Kara gold. <p> SELL XP : A player can sell an XP token and will receive 5 Kara gold per XP point. <p><b> XP tokens adquired on the Counter cannot be sold back </b>" )+"</div>", "" );
			
			this.addTooltipHtml("expertsC", "<div class='tooltipimage' ><img src='"+ g_gamethemeurl +"img/expert_front.jpg'  height='100' width='75' ></div> </div> <div  class='tooltipmessage'> "  +
			_( "<b> THE DOCKS </b> <hr> A player can send here 1 Adventurer per turn to hire an Specialist.<p> The Specialists can perform special operations depending on the type. <p> The Specialist card is moved to the player's board <p> A the end of the turn  the Specialist has to rest for another turn: this card is flipped faced down. <p> Faced down Specialists are returned to the docks at the end of the next turn." )+"</div>", "" );
			
			this.addTooltipHtml("workersC", "<div class='tooltipimage  beachthumb'></div> <div  class='tooltipmessage'> "  +
			_( "<b> THE BEACH </b><hr> <b>At the end of the turn</b> a player can hire an extra Adventurer for 5 Kara gold.<p> The new Adventurer tile is then moved to the player's board. <p> This can only be done once in the game." )+"</div>", "" );
			
			this.addTooltip("explore1", _("Excavation site 1"),"");
			this.addTooltip("explore2", _("Excavation site 2"),"");
			this.addTooltip("explore3", _("Excavation site 3"),"");
			this.addTooltip("explore4", _("Excavation site 4"),"");
			this.addTooltip("explore5", _("Excavation site 5"),"");
			this.addTooltip("explore6", _("Excavation site 6"),"");
			
			this.addTooltip("button_deck1", _("Browse/Gather Deck 1"),"");
			this.addTooltip("button_deck2", _("Browse/Gather Deck 2"),"");
			this.addTooltip("button_deck3", _("Browse/Gather Deck 3"),"");
			this.addTooltip("button_deck4", _("Browse/Gather Deck 4"),"");
			this.addTooltip("button_deck5", _("Browse/Gather Deck 5"),"");
			this.addTooltip("button_deck6", _("Browse/Gather Deck 6"),"");
			
			
			this.addTooltipToClass("coin", _('Kara Gold'),"");
			this.addTooltipToClass("xpcounter", _('XP points'),"");
			
			
            this.setupNotifications();
			
			for( var i in this.gamedatas.visiblecards )
            {
				var thiscard = this.gamedatas.visiblecards[i];
				this.flipcard(thiscard, true);
            }

            console.log( "Ending game setup" );
        },
       

        ///////////////////////////////////////////////////
        //// Game & client states
        
        // onEnteringState: this method is called each time we are entering into a new game state.
        //                  You can use this method to perform some user interface changes at this moment.
        //
        onEnteringState: function( stateName, args )
        {
            console.log( 'Entering state: '+stateName );
			
			switch( stateName )
            {
            case 'startturn':
			    //debugger;
			    for( var player_id in args.args.argScores['players'] )
				{
					var player = args.args.argScores['players'][player_id];
					
					dojo.byId("goldcount_p"+player_id).innerHTML=player['gold'];
					dojo.byId("xpcount_p"+player_id).innerHTML=player['xp'];
					var newScore = player['score'];
					this.scoreCtrl[ player_id ].toValue( newScore );
				}
			    
		    break;
            
            case 'playermove':
			    
			    if (this.isCurrentPlayerActive() )
				{
					list =dojo.query( '#TH_'+this.getActivePlayerId() +' .playertile' );
					if (typeof list[0] !== 'undefined') 
					{ 
						this.selectadventurer(null,list[0]);
					}
					/* +++++++++++++ AUTOSELECT
					
					list =dojo.query( '#TH_'+this.getActivePlayerId() +' .playertile' ).addClass( 'borderpulse' ) ;
					
					for (var i = 0; i < list.length; i++)
					{
						var thiselement = list[i];
						this.gameconnections.push( dojo.connect(thiselement, 'onclick' , this, 'selectadventurer'))
					}*/
				}
				break;
				
			case 'hireexpert':
			    dojo.forEach(this.gameconnections, dojo.disconnect);
				dojo.query(".borderpulse").removeClass("borderpulse");
				this.gameconnections=[];
				if (this.myDlg)	
					{ 
					this.myDlg.hide() ;
					this.myDlg.destroyRecursive() ;
					}
			    if (this.isCurrentPlayerActive() )
				{
					list =dojo.query( '.expertholder .card' ).addClass( 'borderpulse' ) ;
					for (var i = 0; i < list.length; i++)
					{
						var thiselement = list[i];
						this.gameconnections.push( dojo.connect(thiselement, 'onclick' , this, 'pickexpert'))
					}
				}
				break;
            case 'sendexpert':
			    dojo.forEach(this.gameconnections, dojo.disconnect);
				dojo.query(".borderpulse").removeClass("borderpulse");
				this.gameconnections=[];
				
			    if ((this.isCurrentPlayerActive()) && (dojo.byId("tablecards").children.length > 0) && (this.expertpicked == 4 ) )
				{
					browseddeck=dojo.byId("tablecards").children[0].id;
					this[browseddeck].setSelectionMode (1);
				}
				break;			
			
			case 'exchange':
			    
			    if (this.isCurrentPlayerActive() )
				{
					thisplayerid=this.getActivePlayerId();
					thisstore="xpstore_"+thisplayerid;
					this[thisstore].setSelectionMode( 1 );
					
					dojo.addClass( thisstore, 'borderpulse' ) ;
				}
				break;
			
            case 'endturn':
			    if (this.isCurrentPlayerActive() )
				{
					list=dojo.query( '#workersC #tile_'+this.getActivePlayerId() +'_3' ).addClass( 'borderpulse' ) ;
					for (var i = 0; i < list.length; i++)
					{
						var thiselement = list[i];
						this.recruitcon= dojo.connect(thiselement, 'onclick' , this, 'recruit');
					}
					list=dojo.query( '#HospitalC > div[id ^= "tile_'+this.getActivePlayerId()+'"]') ;
					if ( list.length > 0 )
					{
						dojo.addClass("HospitalC",'borderpulse' ) ;
						this.gameconnections.push( dojo.connect($("HospitalC"), 'onclick' , this, 'payhospital'))
					}
				}
				break;
				
		    case 'finish':
			    
			    
				list=dojo.query( '#WaitingroomC div[id^="tile_'+this.getActivePlayerId()+'"]') ;
				for (var i = 0; i < list.length; i++)
				{
					var thiselement = list[i].id;
					this.slideToObjectRelative ( thiselement , "TH_"+thiselement.split('_')[1] , 1000 ) 
				}
				list=dojo.query( '#HospitalC div[id^="tile_'+this.getActivePlayerId()+'"]') ;
				for (var i = 0; i < list.length; i++)
				{
					var thiselement = list[i].id;
					this.slideToObjectRelative ( thiselement , "WaitingroomC" , 1000 ) ;
				}
				list=dojo.query( '#playercardstore_'+this.getActivePlayerId()+' .visible') ;
				for (var i = 0; i < list.length; i++)
				{
					var thiselement = list[i].id; 
					dojo.toggleClass(thiselement, 'traveller');
					dojo.toggleClass(thiselement, 'visible');
					console.log("*** returning expert"+thiselement);
					this.slideToObjectRelative ( thiselement , "expertholder" + thiselement.substr(-1) ,1000 ) ;
				}
				
				dojo.forEach(this.gameconnections, dojo.disconnect);
			    dojo.query(".borderpulse").removeClass("borderpulse");
			    this.gameconnections=[];
			    dojo.query( '.flipped' ).removeClass( 'flipped' )   ;
				
				list=dojo.query( '#playercardstore_'+this.getActivePlayerId()+' > div[id^="expert"]') ;
				for (var i = 0; i < list.length; i++)
				{
					var thiselement = list[i].id;  //expert1
					dojo.toggleClass(thiselement, 'visible');
				}
				
				
                break; 		
           
            case 'dummmy':
                break;
            }
        },

        // onLeavingState: this method is called each time we are leaving a game state.
        //                 You can use this method to perform some user interface changes at this moment.
        //
        onLeavingState: function( stateName )
        {
            console.log( 'Leaving state: '+stateName );
            dojo.query(".traveller").removeClass("traveller");
            switch( stateName )
            {

		    case 'playermove':
			    dojo.query( '.borderpulse' ).removeClass( 'borderpulse' )   ;
                break;	
				
			case 'endturn':
			    this.slideToObjectRelative ("sword","swordholder",1000);
				list=dojo.query( '.playable .playertile' );
				for (var i = 0; i < list.length; i++)
				{
					var thiselement = list[i].id;
					this.slideToObjectRelative ( thiselement , "TH_"+thiselement.split('_')[1],1000  ) 
				}
				dojo.forEach(this.gameconnections, dojo.disconnect);
				dojo.disconnect(this.recruitcon);
			    dojo.query(".borderpulse").removeClass("borderpulse");
			    this.gameconnections=[];
				this.recruitcon=null;
				dojo.query( '.flipped' ).addClass( 'traveller' );
			    dojo.query( '.flipped' ).removeClass( 'flipped' ) ;
                break;
			
			case 'exchange':
			    
			    if (this.isCurrentPlayerActive() )
				{
					thisplayerid=this.getActivePlayerId();
					thisstore="xpstore_"+thisplayerid;
					this[thisstore].setSelectionMode( 0 );
					
					dojo.removeClass( thisstore,'borderpulse' ) ;
				}
				break;
			case 'hireexpert': 	
				dojo.forEach(this.gameconnections, dojo.disconnect);
				dojo.query(".borderpulse").removeClass("borderpulse");
				this.gameconnections=[];
				if (this.myDlg)	
					{ 
					this.myDlg.hide() ;
					this.myDlg.destroyRecursive() ;
					}
				break;
            case 'fight':
				dojo.replaceClass('diceresult','no');
			    dojo.replaceClass('dice','no');
                break;
			
			case 'gettreasure':
				dojo.replaceClass('diceresult','no');
			    dojo.replaceClass('dice','no');
                break;			
				
			case 'sendexpert':
			    this.expertpicked=0;
				break;
            }
		dojo.query(".traveller").removeClass("traveller");
        }, 

        // onUpdateActionButtons: in this method you can manage "action buttons" that are displayed in the
        //                        action status bar (ie: the HTML links in the status bar).
        //        
        onUpdateActionButtons: function( stateName, args )
        {
            console.log( 'onUpdateActionButtons: '+stateName );
                      
            if( this.isCurrentPlayerActive() )
            {            
                switch( stateName )
                {
			    case 'exploresite':
					if ( args.argRocfallVisible == 1 ) 
					{
						this.addActionButton( 'dig_button', _('Destroy a Rockfall on this site (2 adventurers required)'), 'dig' );
					}
					else 
					{
						this.addActionButton( 'dig_button', _('Dig 1 card on this site'), 'dig' );
						this.addActionButton( 'survey_button', _('Survey the first 3 cards of this site'), 'survey' ); 
					}
					this.addActionButton( 'viewdone_button', _("Cancel"), 'cancel' );
                    break;
				case 'exchange':
                    this.addActionButton( 'buy_button', _('Buy 2XP token for 5 Kara gold  '), 'buy' );
					this.addActionButton( 'sell_button', _('Sell selected XP token'), 'sell' );
					this.addActionButton( 'viewdone_button', _("Cancel"), 'cancel' );					
                    break;
                case 'browsecards':
				    if ( args.monsterpresent == 1 )
					{
						this.addActionButton( 'revealmonster_button', _('Reveal monsters for 2 Kara Gold'), 'revealmonster' );
					}	
                   this.addActionButton( 'viewdone_button', _("Done"), 'viewdone' ); 
                    break;
					
				 case 'endturn':
					this.addActionButton( 'viewdone_button', _("END TURN"), 'viewdone' );
					break;
				 
				 case 'hireexpert':
					this.addActionButton( 'viewdone_button', _("Cancel"), 'cancel' );					
                    break;
					
				 case 'sendexpert':
				 
				    this.expertpicked=args.expertpicked;
				    if ( this.expertpicked == 4 )
					{
						this.addActionButton( 'selectcards_button', _('Show me selected cards'), 'selectcards' );
					}	
                    else						
					{
						this.addActionButton( 'selectcards_button', _('Select this deck'), 'selectcards' ); 
					}
                    break;
				
				case 'playermove':
				   				   
				    if ( this.getActivePlayerId() == args.mapowner )
					{
						this.addActionButton( 'reward_1', _('get 5 Kara Gold for the Map'), 'choosereward' );
					
						this.addActionButton( 'reward_2', _('get 2 XP for the Map'), 'choosereward' ); 
					}
                    break;	
				/*              
                 Example:
 
                 case 'myGameState':
                    
                    // Add 3 action buttons in the action status bar:
                    
                    this.addActionButton( 'button_1_id', _('Button 1 label'), 'onMyMethodToCall1' ); 
                    this.addActionButton( 'button_2_id', _('Button 2 label'), 'onMyMethodToCall2' ); 
                    this.addActionButton( 'button_3_id', _('Button 3 label'), 'onMyMethodToCall3' ); 
                    break;
*/
                }
            }
        },        

        ///////////////////////////////////////////////////
        //// Utility methods
        
        /*
        
            Here, you can defines some utility methods that you can use everywhere in your javascript
            script.
        
        */
        change3d: function ( xaxis , xpos , ypos , zaxis , pers, enable3d )
		{
			//debugger;
			if ( enable3d == false ){
			this.control3dmode3d= !this.control3dmode3d ;
			}
			
			if ( this.control3dmode3d == false )
			{			
		    
			// $('#playArea').style.transform = "rotatex("+50+"deg) translate("+0+"px,"+0+"px) rotateZ("+-10+"deg)" ; 		
			$('playArea').style.transform = "rotatex("+0+"deg) translate("+0+"px,"+0+"px) rotateZ("+0+"deg)" ; 		
			}
			else
			{
			this.control3dxaxis+= xaxis;
			if (this.control3dxaxis >= 90 ) { this.control3dxaxis = 90 ; }
			if (this.control3dxaxis <= 0 ) { this.control3dxaxis = 0 ;}
			this.control3dzaxis+= zaxis;
			this.control3dxpos+= xpos;
			this.control3dypos+= ypos;
			this.control3dpers+= pers;
			 $('playArea').style.transform = "rotatex("+this.control3dxaxis+"deg) translate("+this.control3dypos+"px,"+this.control3dxpos+"px) rotateZ("+this.control3dzaxis+"deg)" ;
			 //$('playareascaler').style.perspective = this.control3dpers+"px" ;
			}
		},

		
		flipcard: function ( card, visible )
		{
			image_items_per_row=7;
			card_art=eval(card.type) + 2; // I have the 3 card backgrounds at the beggining of the file
			xpos= -150*((card_art )% image_items_per_row );
			ypos= -200*(Math.floor( (card_art ) / image_items_per_row ));
			position= xpos+"px "+ ypos+"px ";
			dojo.style(card.location+'_item_card_'+card.id+"_back" , "background-position", position);
			cardtooltips =["",
			_("Gallery: this card gives Kara Gold when dug"),
			_("Rockfall: gives 2 kara gold when detected in a Survey.<p> Requires 2 adventures to dig and gives Gold 2 x nr of visible rockfalls on other sites"),
			_("Experience: this card gives XP points when dug" ),
			_("Experience: this card gives XP points when dug but the adventurer will be injured and will go to Hospital" ),
			_("Bat: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Bat: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Gallery: this card gives Kara Gold when dug"),
			_("Experience: this card gives XP points when dug" ),
			_("Experience: this card gives XP points when dug but the adventurer will be injured and will go to Hospital" ),
			_("Goblin: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Goblin: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Goblin: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Treasure: this card gives a treasure from the treasure deck when dug" ),
			_("Rockfall: gives 2 kara gold when detected in a Survey.<p> Requires 2 adventures to dig and gives Gold 2 x nr of visible rockfalls on other sites"),
			_("Experience: this card gives XP points when dug but the adventurer will be injured and will go to Hospital" ),
			_("Experience: this card gives XP points when dug" ),
			_("Gallery: this card gives Kara Gold when dug"),
			_("Treasure: this card gives a treasure from the treasure deck when dug" ),
			_("Skeleton: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Skeleton: a monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Drake: a POWERFUL monster that needs to be defeated to continue the exploration,<p> you need the Magic Sword to fight it" ),
			_("Stone of legend: if a player diggs both Stones he would automatically win the game.<p> If 2 players have one part they tally the XP points"),
			_("Stone of legend: if a player diggs both Stones he would automatically win the game.<p> If 2 players have one part they tally the XP points")]
			
			this.addTooltip(card.location+'_item_card_'+card.id+"_back", cardtooltips[card.type] ,"");
			
			if (visible) 
				{
				dojo.toggleClass( card.location+'_item_card_'+card.id , "flipped", true);
				dojo.toggleClass( card.location+'_item_card_'+card.id, "visible", true);
				}		
			else
				{
				dojo.toggleClass( card.location+'_item_card_'+card.id , "flipped", true);
				}
		},
		
		fliptreasure: function ( card, visible )
		{
			image_items_per_row=3;
			//card_id= target_id.replace(/\D+/g, "");  //Regex to remove all chars but numbers
			
			xpos= -150*((card.type )% image_items_per_row );
			ypos= -200*(Math.floor( (card.type  ) / image_items_per_row ));
			position= xpos+"px "+ ypos+"px ";
			
			dojo.style('treasuredeck_item_treasure_'+card.id +'_back', "background-position", position);
            this.slideToObjectRelative ('treasuredeck_item_treasure_'+card.id , "reward",1000,1000);
			if (visible) 
				{
				dojo.toggleClass('treasuredeck_item_treasure_'+card.id , "visible", true);
				}		
			else
				{
				dojo.toggleClass('treasuredeck_item_treasure_'+card.id, "flipped", true);
				}
		},
		
		rolldice : function(r) {
			    dojo.toggleClass("dice",'rolled');
			    if ( soundManager.bMuteSound == false)
				{
					var audio = new Audio(g_gamethemeurl+'img/roll.mp3');
					audio.play();
				}
				/*playSound( 'die-roll' );*/
				
				diceresult ="num"+r;	
				dojo.replaceClass("diceresult",diceresult);
			
		},
		
		selectadventurer : function(sourceclick, firstaventurer) {
			if (typeof firstaventurer !== 'undefined') 
			{
				this.adventurer = firstaventurer.id  ; // the variable is defined
				target=firstaventurer ;
			}
			else
			{
				dojo.stopEvent( sourceclick );
				var target = sourceclick.target || sourceclick.srcElement;
				this.adventurer=target.id;
			}	
			dojo.toggleClass(this.adventurer,"tileselected");
			//debugger; //
			dojo.style("playArea", "cursor", "url('"+g_gamethemeurl+"img/"+target.parentElement.parentElement.classList[1] +"_"+ target.id.slice(-1)+".png') ,auto");
			dojo.forEach(this.gameconnections, dojo.disconnect);
			//dojo.query(".borderpulse").removeClass("borderpulse");
			this.gameconnections=[];
			
			if (dojo.byId("expertsC").children.length == 0) 
			{
				dojo.toggleClass("expertsC","borderpulse")
				this.gameconnections.push ( dojo.connect( $("expertsC") ,'onclick',this,'playermovetile'));
			}
			if (dojo.byId("counterC").children.length == 0) 
			{
				dojo.toggleClass("counterC","borderpulse");
				this.gameconnections.push ( dojo.connect( $("counterC"),'onclick',this,'playermovetile'));
			}
			dojo.toggleClass( 'diveC' ,"borderpulse");
			this.gameconnections.push ( dojo.connect($('diveC') ,'onclick',this,'playermovetile'));
			
			for ( i=1 ; i<=6 ; i++ )
			{	
				if ($('deck'+i).children.length > 1 )
				{
					dojo.toggleClass( 'explore'+i ,"borderpulse");
					this.gameconnections.push ( dojo.connect($('explore'+i) ,'onclick',this,'playermovetile'));
				}
			}
			},
		
		placetokens : function(thetoken) {
            switch (thetoken.type)
			{
				case "1":
				case "2":
				case "3": 
						this.moveplayertile(thetoken);
						break; 
				case "4" :
						this.movesword(thetoken);
						break; 
				case "5" :
						this.placewound(thetoken);
						break; 
				case "6" :
						this.placexptoken(thetoken);
						break; 
				case "7" :
				case "8":
				case "9":
				case "10":
						this.moveexpert(thetoken);
						break;
				case "11":
				case "12":
						this.placestone(thetoken);
						break; 
			}
        },
		
		
		placestone: function(thetoken) {
		x =  -150 * ( thetoken.type - 8 ) ;  	
		y = -600;
		dojo.place(
                this.format_block('jstpl_stonetoken', {
                    id: thetoken.id ,
					x : x,
					y : y				
                }), thetoken.location);
		this.addTooltipToClass( "stonetoken", _( "Stone of legend: if a player diggs both Stones he would automatically win the game.<p> If 2 players have one part they tally the XP points" ), "" );
    
		},
		
		placewound: function(thetoken) {
		x = Math.floor(Math.random() * 50) + 10;  	
		y = Math.floor(Math.random() * 50) + 50;
		dojo.place(
                this.format_block('jstpl_woundtoken', {
                    id: thetoken.id ,
					x : x,
					y : y
                }), thetoken.location);
		this.addTooltipToClass( "woundtoken", _( "This monster has received a wound and now it has one less life point" ), "" );
    
		},
		
		placexptoken: function(thetoken) {
			
			this["xpstore_"+thetoken.location].addToStockWithId(thetoken.type_arg,thetoken.id );
			if (thetoken.location_arg == 1)
			{
				dojo.addClass("xpstore_"+thetoken.location+"_item_"+thetoken.id, "NOSELL")
			}
		},
		
		moveexpert: function(thetoken) {
			this.slideToObjectRelative ("expert"+thetoken.type_arg, thetoken.location);
			if (thetoken.location_arg==1) 
			{
				dojo.toggleClass("expert"+thetoken.type_arg , "visible", true);
			}
		},
		
		moveplayertile: function(thetoken) {		
			this.slideToObjectRelative ("tile_"+thetoken.type_arg+"_"+thetoken.type, thetoken.location , 1000);			
		},
		
		movesword: function(thetoken) {
			this.slideToObjectRelative ("sword", thetoken.location, 1000);			
		},

		browseGatherDeck : function(sourceclick,deck) {
    		var browseddeck = "";
			//dojo.stopEvent( sourceclick );

			if ( typeof deck == 'undefined')
			{
				var target = sourceclick.target || sourceclick.srcElement;
				deck = target.id.charAt(11);
			}
			
			if (dojo.byId("tablecards").children.length > 0 )
			{
				browseddeck=dojo.byId("tablecards").children[0].id;
			}
			thisdeck="deck"+deck;
			returndeck="deckholder"+deck;
			
			if ( thisdeck==browseddeck) 
			{
				this[thisdeck].item_margin = 0;			
				this[thisdeck].setOverlap( 0.5 , 0 );				
				this.slideToObjectRelative (thisdeck, returndeck);
				dojo.destroy("marker");// destroy marker
				if (this.expertpicked == 4)
				{
					this[thisdeck].setSelectionMode (0);
				}	
			}
			else
			{   
				if ( browseddeck != "" )
				{
		            this[browseddeck].item_margin = 0;	
					this[browseddeck].setOverlap( 0.5 , 0 );
					returndeck="deckholder"+dojo.byId("tablecards").children[0].id.charAt(4);
					this.slideToObjectRelative (browseddeck, returndeck); 
					dojo.destroy("marker");// destroy marker
					if (this.expertpicked == 4)
					{
						this[browseddeck].setSelectionMode (0);
					}	
				}
				this[thisdeck].item_margin = 5;
				this.slideToObjectRelative (thisdeck, "tablecards" );
				dojo.place("<div id='marker' class='marker' style='cursor: zoom-out;'></div>", "deckholder"+deck ) ;
				targetdeck=$('button_deck'+deck);
				
				//$('marker').onclick = this.browseGatherDeck( null ,deck );
				
				dojo.connect($('marker'), "onclick", dojo.hitch(this, this.browseGatherDeck,  null , deck));
				
				this.addTooltip("marker", _("This is the currently selected deck, see the cards expanded above"),"");
				if (this.expertpicked == 4)
					{
						this[thisdeck].apparenceBorderWidth="2px";
						this[thisdeck].setSelectionMode (1);
					}
				this[thisdeck].setOverlap( 100 , 0 );
			}
        },
		
		attachToNewParentNoDestroy : function(mobile, new_parent) {
            if (mobile === null) {
                console.error("attachToNewParent: mobile obj is null");
                return;
            }
            if (new_parent === null) {
                console.error("attachToNewParent: new_parent is null");
                return;
            }
            if (typeof mobile == "string") {
                mobile = $(mobile);
            }
            if (typeof new_parent == "string") {
                new_parent = $(new_parent);
            }

            var src = dojo.position(mobile);
            dojo.style(mobile, "position", "absolute");
            dojo.place(mobile, new_parent, "last");
            var tgt = dojo.position(mobile);
            var box = dojo.marginBox(mobile);

            var left = box.l + src.x - tgt.x;
            var top = box.t + src.y - tgt.y;
            dojo.style(mobile, "top", top + "px");
            dojo.style(mobile, "left", left + "px");
            return box;
        },

        /**
         * This method is similar to slideToObject but works on object which do not use inline style positioning. It also attaches object to
         * new parent immediately, so parent is correct during animation
         */
        slideToObjectRelative : function(token, finalPlace, tlen, tdelay, onEnd) {
            this.resetPosition(token);
             
            dojo.toggleClass( token , "traveller", true);
			
            var box = this.attachToNewParentNoDestroy(token, finalPlace);
			var anim = this.slideToObjectPos(token, finalPlace, box.l, box.t, tlen, tdelay);
			
			
			dojo.toggleClass( token , "traveller", true);

            dojo.connect(anim, "onEnd", dojo.hitch(this, function(token) {
                this.stripPosition(token);
                if (onEnd) onEnd(token);
            }));

            anim.play();
        },
		
		stripPosition : function(token) {
            // console.log(token + " STRIPPING");
            // remove any added positioning style
            dojo.style(token, "display", null);
            dojo.style(token, "top", null);
            dojo.style(token, "left", null);
            dojo.style(token, "position", null);
        },
		
		resetPosition : function(token) {
            // console.log(token + " RESETING");
            // remove any added positioning style
            dojo.style(token, "display", null);
            dojo.style(token, "top", "0px");
            dojo.style(token, "left", "0px");
            dojo.style(token, "position", null);
        },
		////////////////////////////////////////////////
		
		giveXp: function ( source, player ,amount , thetoken_id, NOSELL) 
		{
				dojo.byId("xpcount_p"+player).innerHTML=eval(dojo.byId("xpcount_p"+player).innerHTML) + amount;
				this["xpstore_"+player].addToStockWithId( amount ,thetoken_id, source );
				if (NOSELL == 1)
				{
					dojo.addClass("xpstore_"+player+"_item_"+thetoken_id, "NOSELL")
				}
		},

		giveGold: function ( source, destination ,amount) 
		{
			var animspeed=200;
			for (var i = 1 ; i<= amount ; i++)
			{
				this.slideTemporaryObjectAndIncCounter( '<div class="bigcoin spining"></div>', 'page-content', source, destination, 1000 , animspeed );
				animspeed += 200;
			}
        },
		
		payGold: function ( source, destination ,amount) 
		{
			var animspeed=200;
			for (var i = 1 ; i<= amount ; i++)
			{
				dojo.byId(source).innerHTML=eval(dojo.byId(source).innerHTML) - 1;
				this.slideTemporaryObject( '<div class="bigcoin spining"></div>', 'page-content', source, destination, 1000 , animspeed );
				animspeed += 200;
			}
        },
		
		slideToObjectAndDestroyAndIncCounter: function( mobile_obj , to, duration, delay ) 
		{
			var obj = dojo.byId(mobile_obj );
			dojo.style(obj, "position", "absolute");
			dojo.style(obj, "left", "0px");
			dojo.style(obj, "top", "0px");
			var anim = this.slideToObject(obj, to, duration, delay );
			
			this.param.push(to);
            
			dojo.connect(anim, "onEnd", this, 'incAndDestroy' );
			anim.play();
			return anim;
		},
		
		slideTemporaryObjectAndIncCounter: function( mobile_obj_html , mobile_obj_parent, from, to, duration, delay ) 
		{
			var obj = dojo.place(mobile_obj_html, mobile_obj_parent );
			dojo.style(obj, "position", "absolute");
			dojo.style(obj, "left", "0px");
			dojo.style(obj, "top", "0px");
			this.placeOnObject(obj, from);
			
			var anim = this.slideToObject(obj, to, duration, delay );
			
			this.param.push(to);
            
			dojo.connect(anim, "onEnd", this, 'incAndDestroy' );
			anim.play();
			return anim;
			},
		 
		incAndDestroy : function(node) 
		{				
				dojo.destroy(node);
				target=this.param.shift();
				dojo.byId(target).innerHTML=eval(dojo.byId(target).innerHTML) + 1;
		}, 
		
        ///////////////////////////////////////////////////
        //// Player's action
        
        /*
        
            Here, you are defining methods to handle player's action (ex: results of mouse click on 
            game objects).
            
            Most of the time, these methods:
            _ check the action is possible at this game state.
            _ make a call to the game server
        
        */
        
        /* Example:
        
        onMyMethodToCall1: function( evt )
        {
            console.log( 'onMyMethodToCall1' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );
            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'myAction' ) )
            {   return; }
            this.ajaxcall( "/takaraisland/takaraisland/myAction.html", { 
                                                                    lock: true, 
                                                                    myArgument1: arg1, 
                                                                    myArgument2: arg2,
                                                                    ...
                                                                 }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {
                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)
                         } );        
        },        
        
        */
		choosereward: function( evt )
        {
            // Stop this event propagation
			
            dojo.stopEvent( evt );
			if( ! this.checkAction( 'choosereward' ) )
            {   return; }

            // Get the cliqued pos and Player field ID
            var reward = evt.currentTarget.id.split('_')[1];
			dojo.destroy("reward_1");
			dojo.destroy("reward_2");
            if( this.checkAction( 'choosereward' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/choosereward.html", {
                    reward:reward
                }, this, function( result ) {} );
            }            
        },    

		playermovetile: function( evt )
        {
            // Stop this event propagation
			
            dojo.stopEvent( evt );
			if( ! this.checkAction( 'movetile' ) )
            {   return; }

            // Get the cliqued pos and Player field ID
            var destination = evt.currentTarget.id;
			var tile_id = this.adventurer.split('_');
			var tile = tile_id[2];
			
		/*	this.confirmationDialog( _('Are you sure you want to make this?'), dojo.hitch( this, function() {
            this.ajaxcall( '/mygame/mygame/makeThis.html', { lock:true }, this, function( result ) {} );
			} ) ); */
			
			
			dojo.toggleClass(this.adventurer,"tileselected");
			dojo.style("playArea", "cursor", "");
			
			dojo.forEach(this.gameconnections, dojo.disconnect);
			if (this.swordconnection)
			{
				dojo.disconnect (this.swordconnection);
				this.swordconnection=null;
			}
			dojo.query(".borderpulse").removeClass("borderpulse");
		
            if( this.checkAction( 'movetile' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/movetile.html", {
                    tile:tile,
                    destination:destination
                }, this, function( result ) {} );
            }            
        },    

		rentsword: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'rentsword' ) )
            {   return; }
			dojo.removeClass( 'swordholder','borderpulse' ) ;
			dojo.disconnect(this.swordconnection);
			this.swordconnection=null;
			if( this.checkAction( 'rentsword' ) && (this.gamedatas.players[this.getActivePlayerId()]['gold']>=3 )  )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/rentsword.html", {
                }, this, function( result ) {} );
            }
			else
			{
				this.showMessage  ( _("You cannot afford to rent the sword..."), "info")
			}
			
        },
		
		pickexpert : function(sourceclick) {
			dojo.stopEvent( sourceclick );
			if( ! this.checkAction( 'pickexpert' ) )
            {   return; }
		
			var target = sourceclick.target || sourceclick.srcElement;
			expertpicked=sourceclick.currentTarget.id;
			
			switch (expertpicked)
			{
				case 'expert1':
							if( this.gamedatas.players[this.getActivePlayerId()]['gold']>=5   )    // Miner
							{            
								this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
									 expertpicked:expertpicked								 
								}, this, function( result ) {} );
							}
							else
							{
								this.showMessage  ( _("You cannot afford to hire this Specialist..."), "info")
							}
							break;
				case 'expert2':
							// Create the new dialog. You should store the handler in a member variable to access it later
							this.myDlg = new dijit.Dialog({ title: _("What specialist do you want to impersonate for +2 extra Kara gold?"), style: "width: 1000px" , closable:false });
							this.myDlg.autofocus = false;
							this.myDlg.refocus = false;
							// Create the HTML of my dialog. 

							var html = "<div id='im_miner' class='tooltipimage'><div class='card expertcardfront expert1' ></div>"+
									_( "<b> THE MINER </b> <hr> Permits to digg 2 tiles in of a excavation site deck. <p>XP tiles, Stones of Legend are kept by the player <p> The miner is not affected by the <b>&#10010;</b> go to hospital symbol.<p>Kara gold cards and Rockfalls are destroyed and give no reward.<p>If a monster appears there is no fight but the miner digging ends." )+"</div>"+
									"&nbsp;<div id='im_arch' class='tooltipimage'><div class='card expertcardfront expert3' ></div>"+
									_( "<b> THE ARCHEOLOGIST </b> <hr> Allows the player to see the first 5 tiles on top of a excavation site deck. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>"+
									"&nbsp;<div id='im_sooth' class='tooltipimage'><div class='card expertcardfront expert4' ></div>"+
									_( "<b> THE SOOTHSAYER </b> <hr>  Allows the player to see 3 consecutive tiles of a excavation site deck at any level. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>";
									
							// Show the dialog
							
							if( this.gamedatas.players[this.getActivePlayerId()]['gold']<4   )    // Miner
							{            
								this.showMessage  ( _("You cannot afford to hire this Specialist..."), "info")
								break;
							}
							
							this.myDlg.attr("content", html );
							this.myDlg.show(); 
							
							
							
							dojo.connect( $('im_miner'), 'onclick', this, function(evt){
							   evt.preventDefault();
							   this.myDlg.hide();
							   this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
									 expertpicked:'expert10'								 
								}, this, function( result ) {} );
							} );
							dojo.connect( $('im_arch'), 'onclick', this, function(evt){
								   evt.preventDefault();
								   this.myDlg.hide();
								   this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
										 expertpicked:'expert30'								 
									}, this, function( result ) {} );
								} );
							dojo.connect( $('im_sooth'), 'onclick', this, function(evt){
								   evt.preventDefault();
								   this.myDlg.hide();
								   this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
										 expertpicked:'expert40'								 
									}, this, function( result ) {} );
							   } );
							break;
				
				case 'expert3':
							if( this.gamedatas.players[this.getActivePlayerId()]['gold']>=2   )    // Archeologist
							{            
								this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
									 expertpicked:expertpicked
								}, this, function( result ) {} );
							}
							else
							{
								this.showMessage  ( _("You cannot afford to hire this Specialist..."), "info")
							}
								
							break;
				case 'expert4':
							if( this.gamedatas.players[this.getActivePlayerId()]['gold']>=3   )    // Sothsayer
							{            
								this.ajaxcall( "/takaraisland/takaraisland/pickexpert.html", {
									 expertpicked:expertpicked
								}, this, function( result ) {} );
							}
							else
							{
								this.showMessage  ( _("You cannot afford to hire this Specialist..."), "info")
							}
							break;	
			}
		},
		
		recruit: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'recruit' ) )
            {   return; }
			dojo.disconnect(this.recruitcon);	
			dojo.query(".playertile").removeClass("borderpulse");
			
			if( this.checkAction( 'recruit' ) && (this.gamedatas.players[this.getActivePlayerId()]['gold']>=5 )  )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/recruit.html", {
                }, this, function( result ) {} );
            }
			else
			{
				this.showMessage  ( _("You cannot afford to recruit a new adventurer..."), "info")
			}
			
        },
		
		payhospital: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'payhospital' ) )
            {   return; }
			list=dojo.query( '#HospitalC > div[id ^= "tile_'+this.getActivePlayerId()+'"]') ;
			if ( list.length == 0 )
			
			{
				dojo.removeClass("HospitalC",'borderpulse' ) ;
				dojo.forEach(this.gameconnections, dojo.disconnect);
			    this.gameconnections=[];
				this.showMessage  ( _("You have no more adventurers in the Hospital..."), "info")
				
			}
			else if( this.checkAction( 'payhospital' ) && (this.gamedatas.players[this.getActivePlayerId()]['gold']>=2 )  )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/payhospital.html", {
                }, this, function( result ) {} );
            }
			else
			{
				this.showMessage  ( _("You cannot afford to pay Hospital fee..."), "info")
			}
			
        },
		
		dig: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'dig' ) )
            {  return; }
			
			if( this.checkAction( 'dig' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/dig.html", {
                }, this, function( result ) {} );
            }	
        },
		
		survey: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'survey' ) )
            {  return; }
			
			if( this.checkAction( 'survey' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/survey.html", {
                }, this, function( result ) {} );
            }	
        },
		
		revealmonster: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'revealmonster' ) )
            {  return; }
			
			if( this.checkAction( 'revealmonster' ) )    // Check that this action is possible at this moment
            {           
				dojo.destroy("revealmonster_button");
                this.ajaxcall( "/takaraisland/takaraisland/revealmonster.html", {
                }, this, function( result ) {} );
            }	
        },
		
		viewdone: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'viewdone' ) )
            {  return; }
			dojo.query(".borderpulse").removeClass("borderpulse");
			dojo.query(".flipped").addClass("traveller");
			dojo.query(".flipped").removeClass("flipped");
			if( this.checkAction( 'viewdone' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/viewdone.html", {
                }, this, function( result ) {} );
            }	
        },
		
		cancel: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'cancel' ) )
            {  return; }
			
			if( this.checkAction( 'cancel' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/cancel.html", {
                }, this, function( result ) {} );
            }	
        },
		
		buy: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'buy' ) )
            {  return; }
			
			if( this.checkAction( 'buy' ) && (this.gamedatas.players[this.getActivePlayerId()]['gold']>=5 )  )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/buy.html", {
                }, this, function( result ) {} );
            }
			else
			{
				this.showMessage  ( _("You cannot afford to buy XP..."), "info")
			}	
        },
		
		sell: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'sell' ) )
            {  return; }
		    
			token=this['xpstore_'+this.getActivePlayerId()].getSelectedItems();
			if (token.length < 1) 
			{
				this.showMessage  ( _("You have to select one of your XP tokens to sell"), "info");
				return;
			}
			if ( dojo.hasClass('xpstore_'+this.getActivePlayerId()+"_item_"+token[0].id,'NOSELL'))
			{
				this.showMessage  ( _("You cannot sell this XP token at the Counter"), "info");
				return;
			}	
			if( this.checkAction( 'sell' ) )    // Check that this action is possible at this moment
            {            
                this.ajaxcall( "/takaraisland/takaraisland/sell.html", {
					token_id:token[0].id
                }, this, function( result ) {} );
            }	
        },
		
		selectcards: function( evt )
        {
			dojo.stopEvent( evt );
			if( ! this.checkAction( 'selectcards' ) )
            {  return; }
		    if ( $(tablecards).children.length > 0 )
			{	
		        
				selecteddeck=$(tablecards).children[0].id;
				token=this[selecteddeck].getSelectedItems();
				thetoken=0;
				if (token.length >= 1)
				{
					thetoken=token[0].id.split('_')[1] ;
					this[selecteddeck].setSelectionMode(0);
				}
				if ((token.length < 1) && (this.expertpicked == 4) ) 
				{
					this.showMessage  ( _("You have to select the cards for the Soothsayer"), "info");
					return;
				}
				
				if( this.checkAction( 'selectcards' ) )    // Check that this action is possible at this moment
				{            
					this.ajaxcall( "/takaraisland/takaraisland/selectcards.html", {
						token_id:thetoken,
						deckpicked:selecteddeck
					}, this, function( result ) {} );
				}
            }	
			else
			{
				this.showMessage  ( _("You have to spread one deck to select it first"), "info");
					return;
			}
        },
        ///////////////////////////////////////////////////
        //// Reaction to cometD notifications

        /*
            setupNotifications:
            
            In this method, you associate each of your game notifications with your local method to handle it.
            
            Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" calls in
                  your takaraisland.game.php file.
        
        */
        setupNotifications: function()
        {
            console.log( 'notifications subscriptions setup' );
            
            // TODO: here, associate your game notifications with local methods
            
            // Example 1: standard notification handling
            //dojo.subscribe( 'cardPlayed', this, "notif_cardPlayed" );
            
            // Example 2: standard notification handling + tell the user interface to wait
            //            during 3 seconds after calling the method in order to let the players
            //            see what is happening in the game.
            dojo.subscribe( 'movetoken', this, "notif_movetoken" );
			this.notifqueue.setSynchronous( 'movetoken', 2000 );
			
			dojo.subscribe( 'placestone', this, "notif_placestone" );
			this.notifqueue.setSynchronous( 'placestone', 1000 );
			
			dojo.subscribe( 'activatesword', this, "notif_activatesword" );
            this.notifqueue.setSynchronous( 'activatesword', 500 );
			
			dojo.subscribe('revealcard', this, "notif_revealcard");
            this.notifqueue.setSynchronous('revealcard', 3000);
			
			dojo.subscribe('browsecards', this, "notif_browsecards");
            this.notifqueue.setSynchronous('browsecards', 3000);
			
			dojo.subscribe('playerpaysgold', this, "notif_playerpaysgold");
            this.notifqueue.setSynchronous('playerpaysgold', 2000);
			
			dojo.subscribe('playergetgold', this, "notif_playergetgold");
            this.notifqueue.setSynchronous('playergetgold', 2000);
			
			dojo.subscribe( 'removecard', this, "notif_removecard" );
			this.notifqueue.setSynchronous( 'removecard', 2000 );
			
			dojo.subscribe('playergetxp', this, "notif_playergetxp");
            this.notifqueue.setSynchronous('playergetxp', 2000);
			
			dojo.subscribe('playersellxp', this, "notif_playersellxp");
            this.notifqueue.setSynchronous('playersellxp', 2000);
			
			dojo.subscribe('rolldice', this, "notif_rolldice");
            this.notifqueue.setSynchronous('rolldice', 5000);
			
			dojo.subscribe('placewound', this, "notif_placewound");
            this.notifqueue.setSynchronous('placewound', 2000);
			
			dojo.subscribe('fliptreasure', this, "notif_fliptreasure");
            this.notifqueue.setSynchronous('fliptreasure', 3000);
			
			dojo.subscribe('tableWindow', this, "notif_finalScore");
            this.notifqueue.setSynchronous('tableWindow', 5000);
            // 
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        */
        notif_movetoken: function( notif )
        {
            console.log( 'notif_movetoken' );
            console.log( notif );
            this.slideToObjectRelative (notif.args.tile_id, notif.args.destination,1000)
        },
		
		notif_placestone: function( notif )
        {
            console.log( 'notif_placestone' );
            console.log( notif );
            this.placestone (notif.args.token)
        },
		notif_activatesword: function( notif )
        {
            console.log( 'notif_activatesword' );
            console.log( notif );
            dojo.addClass( 'swordholder','borderpulse' ) ;
			this.swordconnection= dojo.connect ( $('sword'),'onclick',this,'rentsword');
        },
			
		notif_revealcard: function( notif )
        {
            console.log( 'notif_revealcard' );
            console.log( notif );
			
			if ( !notif.args.istopcard )
			{	if (($(tablecards).children.length < 1  ) || ($(tablecards).children["0"].id != "deck"+notif.args.sitenr))
				{
					this.browseGatherDeck ( null , notif.args.sitenr );
				}
			}
			this.flipcard ( notif.args.card, true );
        },			
		
		notif_browsecards: function( notif )
        {
            console.log( 'notif_browsecards' );
            console.log( notif );         
			if (($(tablecards).children.length < 1 ) || ($(tablecards).children["0"].id != "deck"+notif.args.sitenr))
			{
				this.browseGatherDeck ( null , notif.args.sitenr );
			}
			for (i=0; i<notif.args.cards.length ; i++)
			{	
				this.flipcard ( notif.args.cards[i], false );
			}
		},
		
		notif_playergetgold: function( notif )
        {
            console.log( 'notif_playergetgold' );
            console.log( notif );
            this.gamedatas.players[notif.args.player_id]['gold']+=notif.args.amount;
			this.giveGold ( notif.args.source , "goldcount_p"+notif.args.player_id, notif.args.amount );
        },			
		
		notif_playerpaysgold: function( notif )
        {
            console.log( 'notif_playerpaysgold' );
            console.log( notif );
            this.gamedatas.players[notif.args.player_id]['gold']+=notif.args.amount;
			this.payGold ( "goldcount_p"+notif.args.player_id, notif.args.destination , notif.args.amount );
        },	
		
		notif_removecard: function( notif )
        {
            console.log( 'notif_removecard' );
            console.log( notif );
			
			dojo.toggleClass( notif.args.deck+"_item_"+notif.args.tile_id , "traveller", true);
		    this[notif.args.deck].removeFromStockById (notif.args.tile_id, notif.args.destination)
        },
		
		notif_playergetxp: function( notif )
        {
            console.log( 'notif_playergetxp' );
            console.log( notif );
            this.gamedatas.players[notif.args.player_id]['xp']+=notif.args.amount;	
			this.giveXp ( notif.args.source, notif.args.player_id , notif.args.amount , notif.args.token_id, notif.args.NOSELL);
        },
		
		notif_playersellxp: function( notif )
        {
            console.log( 'notif_playersellxp' );
            console.log( notif );
            this.gamedatas.players[notif.args.player_id]['xp']-=notif.args.amount;
			this[notif.args.source].removeFromStockById (notif.args.token_id, 'counterC')
		},
		
		notif_rolldice: function( notif )
        {
            console.log( 'notif_rolldice' );
            console.log( notif );
			this.rolldice(notif.args.result);
        },
		
		notif_placewound: function( notif )
        {
			console.log( 'notif_placewound' );
			dojo.replaceClass('dice','no');
			dojo.replaceClass('diceresult','no');			
            console.log( notif );
			this.placewound(notif.args.token);
		},
		
		notif_fliptreasure: function( notif )
        {
            console.log( 'notif_fliptreasure' );
            console.log( notif );
			this.fliptreasure ( notif.args.card, true );
        },	
		
		notif_finalScore: function (notif) 
		{
            console.log('**** Notification : finalScore');
            console.log(notif);

            // Update score
            //this.scoreCtrl[notif.args.player_id].incValue(notif.args.score_delta);
        },
        
   });             
 });  