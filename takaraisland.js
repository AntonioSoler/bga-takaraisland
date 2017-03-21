/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * takaraisland implementation : © <Your name here> <Your email address here>
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
			
            // Setting up player boards
			
			for( var player_id in gamedatas.players )
            {
                var player = gamedatas.players[player_id];
                         
                // Setting up players boards if needed
                var player_board_div = $('player_board_'+player_id);
                dojo.place( this.format_block('jstpl_player_board', player ), player_board_div );
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
				this[decks[i]].jstpl_stock_item="<div id=\"${id}\" class=\"stockitem card\" style=\"top:${top}px;left:${left}px;z-index:${position};\"> <div id=\"${id}_front\" class=\"card-front\"></div><div id=\"${id}_back\" class=\"card-back\"></div>";
			}
			
			this.treasures = new ebg.stock();
			this.treasures.create( this, $("treasuredeck"), this.cardwidth, this.cardheight );
			this.treasures.image_items_per_row = 3;
			this.treasures.setSelectionMode( 0 );
			this.treasures.item_margin = 0;
			this.treasures.setOverlap( 0.05 , 0 );
			this.treasures.jstpl_stock_item="<div id=\"${id}\" class=\"stockitem card treasure\" style=\"top:${top}px;left:${left}px;z-index:${position};\"> <div id=\"${id}_front\" class=\"card-front\" ></div><div id=\"${id}_back\" class=\"card-back\"></div>";

			
			for (  i in gamedatas.players ) 
            {
			     // Create decks:
                thisplayerid=this.gamedatas.players[i].id;
				thisstore="xpstore_"+thisplayerid;
				this[thisstore] = new ebg.stock();
				this[thisstore].create( this, $(thisstore), 45 , 50);
				this[thisstore].image_items_per_row = 2;
				this[thisstore].setSelectionMode( 0 );
				this[thisstore].item_margin = 1;
				this[thisstore].setOverlap( 50 , 0 );
				tarray=[1,2,4,5,8,10,-2,3]   //xp values
				for ( var c = 0; c < 8; c++) 
				{
					this[thisstore].addItemType( tarray[c] , 0 , g_gamethemeurl+'img/xp.png', c );
				}
				tileholder="TH_"+thisplayerid;
				this[tileholder] = new ebg.stock();
				this[tileholder].create( this, $(tileholder), 60 , 60);
				this[tileholder].image_items_per_row = 4;
				this[tileholder].setSelectionMode( 0 );
				
                this[tileholder].jstpl_stock_item= "<div id=\"${id}\" class=\"stockitem playertile\" style=\"width:${width}px;height:${height}px;z-index:${position};background-image:url('${image}');border-radius:50%;\"></div>";
				
				colortiles = { hff0000:2, h008000:3, h0000ff:0, hffa500:1 };	
				
				for ( var c = 0; c <= 2; c++) 
				{
					this[tileholder].addItemType( c+1 , 0 , g_gamethemeurl+'img/playertiles.png', c * 4 +colortiles["h"+this.gamedatas.players[i].color] );
					this[tileholder].addToStockWithId( c+1 , c+1  )
				}				
			}
			
			
            for( var i in this.gamedatas.cards )
            {
				var thisdeck = this.gamedatas.cards[i].location;
				var card = this.gamedatas.cards[i];
				this[thisdeck].addItemType( card.id, card.location_arg, g_gamethemeurl+'img/cards.jpg', card.type_arg-1 );
				this[thisdeck].addToStockWithId( card.id , "card_"+card.id  );
				
			    // custom backgound positioning, to apply the backgound pos to the front instead of the father element
				xpos= -150*((card.type_arg - 1 )% this[thisdeck].image_items_per_row );
				ypos= -200*(Math.floor( (card.type_arg -1 ) / this[thisdeck].image_items_per_row ));
				position= xpos+"px "+ ypos+"px ";
				
				dojo.style(this.gamedatas.cards[i].location+'_item_card_'+card.id+"_front" , "background-position", position);
				
				
            }
			
			for( var i in this.gamedatas.treasures )
            {		
				var card = this.gamedatas.treasures[i];
				this.treasures.addItemType( card.id, card.location_arg, g_gamethemeurl+'img/treasure.jpg', 0 );
				this.treasures.addToStockWithId( card.id , "treasure_"+card.id  )
            }
			
			dojo.connect( $('button_deck1'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck2'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck3'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck4'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck5'), 'onclick', this, 'browseGatherDeck' );
			dojo.connect( $('button_deck6'), 'onclick', this, 'browseGatherDeck' );
			
			this.addEventToClass ("treasure ", 'onclick' , 'fliptreasure' );
			
			dojo.connect( $('dice'), 'onclick', this, 'rolldice' ); // FOR TEST!!!!
			
			this.addTooltipHtml("dice",  "<div class='tooltipimage'><img src='"+ g_gamethemeurl +"img/dice.png' ></div><div  class='tooltipmessage'> " + 	
			 _(" <p><h3> &#10010; </h3> The adventurer is injured by the monster and has go to hospital. The fighting ends <p><p>  <h3>&#128481; </h3> The player has injured the monster and it takes a wound. " ) +"</div>", "" );
				
			this.addTooltipHtml("expert1",  "<div class='tooltipimage'><div class='card expertcardfront expert1' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE MINER:  <hr>  Permits to digg 2 tiles in of a excavation site deck. <p>XP tiles, Stones of Legend are kept by the player <p> The miner is not affected by the <h3> &#10010 </h3> go to hospital symbol.<p>Kara gold cards and Rockfalls are destroyed and give no reward.<p>If a monster appears there is no fight but the miner digging ends." )+"</div>", "" );
			
			this.addTooltipHtml("expert2",  "<div class='tooltipimage'><div class='card expertcardfront expert2' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE IMPERSONATOR:  <hr>  Copies the effect of another specialist who is not available at the time. <p> For hiring this specialist you have to pay the original price ot the selected speciallist +2 Kara gold." )+"</div>", "" );
			
			this.addTooltipHtml("expert3",  "<div class='tooltipimage'><div class='card expertcardfront expert3' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE ARCHEOLOGIST:  <hr>  Reveals the first 5 tiles on top of a excavation site deck. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>", "" );
			
			this.addTooltipHtml("expert4",  "<div class='tooltipimage'><div class='card expertcardfront expert4'</div> </div> <div  class='tooltipmessage'> "  +
			_( " THE SOOTHSAYER:  <hr>  Reveals 3 consecutive tiles of a excavation site deck at any level. <p> The rockfalls and monsters do not stop the survey. <p> Tiles already faced up still count as part of the survey." )+"</div>", "" );
			
			this.addTooltipHtml("sword",  "<div class='tooltipimage'><div class='swfront' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE MAGIC SWORD:  <hr>  <b> At the beggining of the turn </b>a player can rent the magic sword for  3 Kara gold. <p> This allows to fight a monster revealed on top of a deck. <p> Also if digging a monster appears there is no fight but the adventurer does not go to the hospital." )+"</div>", "" );
			
			this.addTooltipHtml("HospitalC",  "<div class='tooltipimage' ><div class='hospitalthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE HOSPITAL:  <hr>  <b> Adventurers injured during exploration or in combat come here <p> At the end of the turn a player can pay 2 Kara gold to accelerate the rcovery of their injured adventurers. <p> If a player chooses not to pay the adventurers will spend another turn on the waitingroom." )+"</div>", "" );
			
			this.addTooltipHtml("WaitingroomC",  "<div class='tooltipimage' ><div class='hospitalthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE HOSPITAL:  <hr>  <b> Adventurers injured during exploration or in combat come here <p> At the end of the turn a player can pay 2 Kara gold to accelerate the rcovery of their injured adventurers. <p> If a player chooses not to pay the adventurers will spend another turn on the waitingroom." )+"</div>", "" );
			
			this.addTooltipHtml("thedive",  "<div class='tooltipimage' ><div class='divethumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE DIVE:  <hr>  <b> The local pub of the ilsand. <p>A player can send here up to 3 of his adventurers to make money gambling and will get 1 Kara gold for each one of them." )+"</div>", "" );
			
			this.addTooltipHtml("counter",  "<div class='tooltipimage' ><div class='counterthumb' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE COUNTER:  <hr>  <b> A player can send here 1 Adventurer per turn <p> The operations here are:  <p> BUY XP :  the player can buy an 2 XP token for 5 Kara gold. <p> SELL XP : A player can sell an XP token and will receive 5 Kara gold per XP point." )+"</div>", "" );
			
			this.addTooltipHtml("expertsC",  "<div class='tooltipimage' ><img src='"+ g_gamethemeurl +"img/expert_front.jpg'  height='100' width='75' ></div> </div> <div  class='tooltipmessage'> "  +
			_( " THE DOCKS:  <hr>  <b> A player can send here 1 Adventurer per turn to hire an Specialist.<p> The Specialists can perform special operations depending on the type. <p> The Specialist card is moved to the player's board <p> A the end of the turn  the Specialist has to rest for another turn: this card fliped faced down. <p> Faced down Specialists are returned to the docks at the end of the next turn." )+"</div>", "" );
			
			this.addTooltipHtml("workersC",  "<div class='tooltipimage  beachthumb'></div> <div  class='tooltipmessage'> "  +
			_( " THE BEACH:  <hr>  <b> At the end of the turn a player can hire an extra Adventurer for 5 Kara gold.<p> The new Adventurer  tile is then moved to the player's board. <p> This can only be done once in the game." )+"</div>", "" );
			
			this.addTooltip("explore1", "Excavation site 1","");
			this.addTooltip("explore2", "Excavation site 2","");
			this.addTooltip("explore3", "Excavation site 3","");
			this.addTooltip("explore4", "Excavation site 4","");
			this.addTooltip("explore5", "Excavation site 5","");
			this.addTooltip("explore6", "Excavation site 6","");
			this.addTooltipToClass("coin", "Kara Gold","");
			
			for( var i in this.gamedatas.tokens )
            {
				
				var thistoken = this.gamedatas.tokens[i];
				this.placetoken(thistoken);
            }

			/*
			for ( var i=1;i<=gamedatas.iterations;i++ )
			{
					dojo.addClass( "templecard"+i ,"on");
			}
            
			this.addTooltipToClass( "templeclass", _( "The number of expeditions remaining" ), "" );
			
			this.addTooltipToClass( "tent", _( "Gems are stored here after each expedition. Once in your tent, the gems are safe.<br><b> ONLY the player owner of the tent knows how many gems does it contain</b>" ), "" );
			
			this.addTooltipToClass( "gemfield", _( "These are your share of Gems obtained on the current expedition, you need to return to the camp to safely store them" ), "" );
			
			this.addTooltipToClass( "cardback", _( "Each round players vote to return to camp or to keep exploring. Players who leave can pick up the gems left on the cards " ), "" );
			
			this.addTooltipToClass( "votecardLeave", _( "This player has voted to return to camp and has stored his gems in the tent" ), "" );
			
			this.addTooltipToClass( "votecardExplore", _( "This player has voted to continue exploring" ), "" );
			
			this.addTooltipToClass( "stockitem", _( "This represents the events ocurred during exploration" ), "" );
			
            // Setup game notifications to handle (see "setupNotifications" method below) */
			
			//debugger;
            this.setupNotifications();

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
            
            /* Example:
            
            case 'myGameState':
            
                // Show some HTML block at this game state
                dojo.style( 'my_html_block_id', 'display', 'block' );
                
                break;
           */
           
		    case 'reshuffle':

            
                // Show some HTML block at this game state
                dojo.query('.votecardExplore').removeClass('votecardExplore') ;
                
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
            
            switch( stateName )
            {
            
            /* Example:
            
            case 'myGameState':
            
                // Hide the HTML block we are displaying only during this game state
                dojo.style( 'my_html_block_id', 'display', 'none' );
                
                break;
           */
           
           
            case 'dummmy':
                break;
            }               
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
			    case 'vote':
                    this.addActionButton( 'explore_button', _('Explore the temple'), 'voteExplore' );
					this.addActionButton( 'leave_button', _('Return to camp'), 'voteLeave' ); 
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
        /* fsno and fstype controls the css style to load, boardloc controls on which predefine div should the tile slides to. */
        
		flipcard: function ( card, visible )
		{
			image_items_per_row=this[card.location].image_items_per_row;
			
			xpos= -150*((card.id - 1 )% image_items_per_row );
			ypos= -200*(Math.floor( (card.id -1 ) / image_items_per_row ));
			position= xpos+"px "+ ypos+"px ";
			
			dojo.style('card_back_'+card.id , "background-position", position);

			if (visible) 
				{
				dojo.toggleClass('stile_'+location_arg , "visible", true);
				}		
			else
				{
				dojo.toggleClass('stile_'+location_arg , "flipped", true);
				}
		},
		
		fliptreasure: function ( sourceclick,card, visible )
		{
			image_items_per_row=3;
			//debugger;
			var target = sourceclick.target || sourceclick.srcElement;
			target_id=target.id;
			card_id= target_id.replace(/\D+/g, "");  //Regex to all chars but numbers
			
			xpos= -150*((card_id - 1 )% image_items_per_row );
			ypos= -200*(Math.floor( (card_id -1 ) / image_items_per_row ));
			position= xpos+"px "+ ypos+"px ";
			
			dojo.style('treasuredeck_item_treasure_'+card_id +'_back', "background-position", position);
            this.slideToObjectRelative ('treasuredeck_item_treasure_'+card_id , "reward",1000,1000);
			if (visible) 
				{
				dojo.toggleClass('treasuredeck_item_treasure_'+card_id , "visible", true);
				}		
			else
				{
				dojo.toggleClass('treasuredeck_item_treasure_'+card_id, "flipped", true);
				}
		},
		
		rolldice : function(thetoken) {
			dojo.toggleClass("dice",'rolled');
			if(dojo.hasClass("dice", "rolled"))
				{var audio = new Audio(g_gamethemeurl+'sound/roll.mp3');
				audio.play();
				r=Math.floor((Math.random() * 6 ) + 1);
				diceresult ="num"+r;	
				dojo.replaceClass("diceresult",diceresult);
			}
			else
			{
				dojo.replaceClass("diceresult","")
			}
		},
		
		placetoken : function(thetoken) {
			
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
			}
        },
		
		
		placewound: function(thetoken) {
		x = Math.floor(Math.random() * 50) + 50;  	
		y = Math.floor(Math.random() * 120) + 50;
		dojo.place(
                this.format_block('jstpl_woundtoken', {
                    id: thetoken.id ,
					x : x,
					y : y
                }), thetoken.location);
		this.addTooltipToClass( "woundtoken", _( "This monster has received a wound and now it has one life point less" ), "" );
    
		},
		
		placexptoken: function(thetoken) {
			this["xpstore_"+thetoken.location].addToStockWithId(thetoken.type_arg,thetoken.id );
		},
		
		moveexpert: function(thetoken) {
			this.slideToObjectRelative ("expert"+thetoken.type_arg, thetoken.location);
			if (thetoken.location_arg==1) 
			{
				dojo.toggleClass("expert"+thetoken.type_arg , "flipped", true);
			}
		},
		
		moveplayertile: function(thetoken) {
			this.slideToObjectRelative ("TH_"+thetoken.type_arg+"_item_"+thetoken.type, thetoken.location);			
		},
		
		movesword: function(thetoken) {
			this.slideToObjectRelative ("sword", thetoken.location);			
		},

		browseGatherDeck : function(sourceclick) {
            var browseddeck = "";
			var target = sourceclick.target || sourceclick.srcElement;
			if (dojo.byId("tablecards").children.length > 0 )
			{
				browseddeck=dojo.byId("tablecards").children[0].id;
			}
			thisdeck="deck"+target.id.charAt(11);
			returndeck="deckholder"+target.id.charAt(11);
			
			if ( thisdeck==browseddeck) 
			{
				this[thisdeck].item_margin = 0;			
				this[thisdeck].setOverlap( 0.5 , 0 );
				
				this.slideToObjectRelative (thisdeck, returndeck);			
			}
			else
			{   
				if ( browseddeck != "" )
				{
		            this[browseddeck].item_margin = 0;	
					this[browseddeck].setOverlap( 0.5 , 0 );
					returndeck="deckholder"+dojo.byId("tablecards").children[0].id.charAt(4);
				    
            		
					this.slideToObjectRelative (browseddeck, returndeck);
				}
				this[thisdeck].item_margin = 5;
				this.slideToObjectRelative (thisdeck, "tablecards" );
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
            this.stripPosition(token);

            var box = this.attachToNewParentNoDestroy(token, finalPlace);
            var anim = this.slideToObjectPos(token, finalPlace, box.l, box.t, tlen, tdelay);

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
		
		////////////////////////////////////////////////
        placeGem: function ( gem_id, destination) 
		{
			
		x = Math.floor(Math.random() * 80) + 40;  	
		y = Math.floor(Math.random() * 150) + 60;
		dojo.place(
                this.format_block('jstpl_gem', {
                    id: gem_id ,
					x : x,
					y : y
                }), destination);
		this.addTooltipToClass( "cardgem", _( "Some gems were left on the floor because they could not be didvidied evenly among the explorers. The players could pick this when returning on the way back to camp" ), "" );
        },
		
		placeVotecard: function ( player_id, action) 
		{	
			
            dojo.place( this.format_block ( 'jstpl_votecard', {
                player_id: player_id,
                action: action
            } ) , 'cardholder_'+player_id,"only" );
            
            this.placeOnObject( 'votecard_'+player_id, 'overall_player_board_'+player_id );
            this.slideToObject( 'votecard_'+player_id, 'cardholder_'+player_id ).play();
        },
		
		moveGem: function ( source, destination ,amount) 
		{
			var animspeed=300;
			for (var i = 1 ; i<= amount ; i++)
			{
				this.slideTemporaryObjectAndIncCounter( '<div class="gem spining"></div>', 'page-content', source, destination, 500 , animspeed );
				animspeed += 300;
			}
			//for (var i = 1 ; i<= amount ; i++)
			//{
			//	$(destination).innerHTML++ ;
			//}
        },
		
		moveCard: function ( id , destination , isartifact ) 
		{
			dojo.addClass( "tablecards_item_tablecard_"+id ,"animatedcard") ;
			this.tablecards.removeFromStockById( "tablecard_"+id , destination  );	
			if ( isartifact == 1 ) 
				{
					dojo.place( "<div class='artifacticon'></div>" , destination  , "last");
					this.addTooltipToClass( "artifacticon", _( "Each artifact worths 5 gems, the 4th and 5th drawn give 5 extra gems" ), "" );
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

		voteExplore: function( evt )
        {
            console.log( 'voteExplore' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'voteExplore' ) )
            {   return; }

            this.ajaxcall( "/takaraisland/takaraisland/voteExplore.html", {  }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
        },
        
		voteLeave: function( evt )
        {
            console.log( 'voteLeave' );
            
            // Preventing default browser reaction
            dojo.stopEvent( evt );

            // Check that this action is possible (see "possibleactions" in states.inc.php)
            if( ! this.checkAction( 'voteLeave' ) )
            {   return; }

            this.ajaxcall( "/takaraisland/takaraisland/voteLeave.html", {  }, 
                         this, function( result ) {
                            
                            // What to do after the server call if it succeeded
                            // (most of the time: nothing)
                            
                         }, function( is_error) {

                            // What to do after the server call in anyway (success or failure)
                            // (most of the time: nothing)

                         } );        
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
            dojo.subscribe( 'playCard', this, "notif_cardPlayed" );
			this.notifqueue.setSynchronous( 'playCard', 2000 );
			dojo.subscribe( 'ObtainGems', this, "notif_ObtainGems" );
            this.notifqueue.setSynchronous( 'ObtainGems', 2000 );
			dojo.subscribe('tableWindow', this, "notif_finalScore");
            this.notifqueue.setSynchronous('tableWindow', 8000);
			dojo.subscribe('reshuffle', this, "notif_reshuffle");
            this.notifqueue.setSynchronous('reshuffle', 4000);
			dojo.subscribe('playerleaving', this, "notif_playerleaving");
            this.notifqueue.setSynchronous('playerleaving', 3000);
			dojo.subscribe('artifactspicked', this, "notif_artifactspicked");
            this.notifqueue.setSynchronous('artifactspicked', 2000);
			dojo.subscribe('playerexploring', this, "notif_playerexploring");
            this.notifqueue.setSynchronous('playerexploring', 1000);
			dojo.subscribe('stcleanpockets', this, "notif_stcleanpockets");
            this.notifqueue.setSynchronous('stcleanpockets', 4000);
			
            // 
        },  
        
        // TODO: from this point and below, you can write your game notifications handling methods
        
        /*
        Example:
        */
        notif_cardPlayed: function( notif )
        {
            console.log( 'notif_cardPlayed' );
            console.log( notif );
			var card = notif.args.card_played;
            this.tablecards.addToStockWithId( card.type , "tablecard_"+card.id ,  'templecard'+this.gamedatas.iterations  );
			if ( card.type >=12 && card.type <=16 ) 
			   {
					dojo.addClass( "tablecards_item_tablecard_"+card.id , "isartifact" )
					//dojo.attr("tablecards_item_tablecard_"+card.id, "title", card.id)
				}
			for ( var g=card.location_arg ; g>0 ; g-- )
				{
					this.placeGem( card.id+"_"+g, "tablecards_item_tablecard_"+card.id   ) ;					
				}
			this.addTooltipToClass( "stockitem", _( "This represents the events ocurred during exploration" ), "" );
			dojo.byId("decksize").innerHTML=eval(dojo.byId("decksize").innerHTML)-1;
        },
		
		notif_playerleaving: function( notif )
        {
            console.log( 'notif_playerleaving' );
			notif.args=this.notifqueue.playerNameFilterGame(notif.args);
            console.log( notif );
			this.placeVotecard ( notif.args.thisid , "Leave" );
			this.addTooltipToClass( "votecardLeave", _( "This player has voted to return to camp and has stored his gems in the tent" ), "" );
            var animspeed=0;
			gems = dojo.byId("gem_field_"+notif.args.thisid).innerHTML
			if ( gems >=1 )
			{
				for ( var g=1 ; g<=gems  ; g++ )
				{
					if (this.gamedatas.current_player_id == notif.args.thisid) 
						{
						this.slideTemporaryObjectAndIncCounter ( '<div class="gem spining"></div>', 'page-content', "gem_field_"+notif.args.thisid , "tent_"+notif.args.thisid , 500 , animspeed );
						}
					else 
						{
						this.slideTemporaryObject( '<div class="gem spining"></div>', 'page-content', "gem_field_"+notif.args.thisid , "tent_"+notif.args.thisid, 500 , animspeed );
						}
					animspeed += 300;
				}
			}
			if ( notif.args.gems >=1 )
			{	
				animspeed=0;
				gemarray=dojo.query('[id^=tablecards_] > *');
				for ( var g=0 ; g<= notif.args.gems-1  ; g++ )
				{   
			        try {    //Surrounding this with a TRY-CATCH because there is a race condition, if the user presses F5 during this process the gamedatas would give us 0 gems on the cards to pick.
					    pickedgem=gemarray[g];
						source=pickedgem.parentElement.id
					    dojo.destroy( pickedgem.id );
						
						if (this.gamedatas.current_player_id == notif.args.thisid) 
							{
							this.slideTemporaryObjectAndIncCounter ( '<div class="gem spining"></div>', 'page-content', source , "tent_"+notif.args.thisid , 500 , animspeed );
							}
						else 
							{
							this.slideTemporaryObject( '<div class="gem spining"></div>', 'page-content', source , "tent_"+notif.args.thisid , 500 , animspeed );
							}
						animspeed += 300;
					}
					catch(err) {}
					
				}
			}
			dojo.byId("gem_field_"+notif.args.thisid).innerHTML = 0 ;
        },
		
		notif_playerexploring: function( notif )
        {
            console.log( 'notif_playerexploring' );
			notif.args=this.notifqueue.playerNameFilterGame(notif.args);
            console.log( notif );
			this.placeVotecard ( notif.args.thisid , "Explore" );			
			
			this.addTooltipToClass( "votecardExplore", _( "This player has voted to continue exploring" ), "" );
        },
		
		notif_artifactspicked: function( notif )
        {
            console.log( 'notif_artifactspicked' );
			notif.args=this.notifqueue.playerNameFilterGame(notif.args);
            console.log( notif );
			if ( notif.args.extra >0 )
			{
				animspeed = 0;
				if (this.gamedatas.current_player_id == notif.args.thisid) 
						{
						this.slideTemporaryObjectAndIncCounter ( '<div class="gem spining"></div>', 'page-content', "field_"+notif.args.thisid , "tent_"+notif.args.thisid , 500 , animspeed );
						}
					else 
						{
						this.slideTemporaryObject( '<div class="gem spining"></div>', 'page-content', "field_"+notif.args.thisid , "tent_"+notif.args.thisid, 500 , animspeed );
						}
				animspeed += 300;
			}	
			for ( card_id in notif.args.cards )				
			{    
				this.moveCard ( notif.args.cards[card_id].id ,'field_'+notif.args.thisid , 1 );
			}
        },
		
		notif_stcleanpockets: function( notif )
        {
            console.log( 'notif_stcleanpockets' );
            console.log( notif );
			var card = notif.args.card_played;
            
			this.moveCard ( card.id ,'templePanel', 0);
			dojo.query(".isartifact").addClass("animatedcard");
			artifacts=document.getElementsByClassName("isartifact");
			for (i=0 ; i< artifacts.length ; i++ )
			    {
					this.slideToObjectAndDestroy ( artifacts[i].id,'templePanel', 400 ,0);
					dojo.place( "<div class='artifacticon removed'></div>" , 'templecard5' , "last");
					this.addTooltipToClass( "removed", _( "This artifact was not picked by the explorers and now is lost forever in the temple" ), "" );
				}	
        },
		
        notif_ObtainGems: function( notif )
        {
            console.log( 'notif_ObtainGems' );
            console.log( notif );
			var card = notif.args.card_played;
			for (i in notif.args.players)
			{			
				 this.moveGem ( "tablecards_item_tablecard_"+card.id , "gem_field_"+this.gamedatas.players[i].id , notif.args.gems )
			}
		    
        },
		notif_reshuffle: function( notif )
        {
            console.log( 'notif_reshuffle' );
            console.log( notif );
			dojo.query(".isartifact").addClass("animatedcard");
			artifacts=document.getElementsByClassName("isartifact");
			for (i=0 ; i< artifacts.length ; i++ )
			    {
					this.slideToObjectAndDestroy ( artifacts[i].id,'templePanel', 400 ,0);
					dojo.place( "<div class='artifacticon removed'></div>" , 'templecard5' , "last");
					this.addTooltipToClass( "removed", _( "This artifact was not picked by the explorers and now is lost forever in the temple" ), "" );
				}
			if (notif.args.iterations <=5 )
			{
				for (i in this.gamedatas.players )
				{			
					dojo.byId( "gem_field_"+this.gamedatas.players[i].id ).innerHTML=0;
					this.placeVotecard ( this.gamedatas.players[i].id , "Back" )  
				}
				this.tablecards.removeAll();
				
				this.slideTemporaryObject( "<div  class='templecard t"+ notif.args.iterations +" on spining'></div>" , 'templePanel', 'templePanel', "templecard"+notif.args.iterations, 400, 0);  
				dojo.addClass( "templecard"+notif.args.iterations ,"on")
				dojo.byId("decksize").innerHTML=notif.args.cardsRemaining;
			}
			
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