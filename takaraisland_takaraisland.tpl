{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: (c) Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- takaraisland implementation : (c) Antonio Soler <morgald.es@gmail.com>
-- 
-- This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
-- See http://en.boardgamearena.com/#!doc/Studio for more information.
-------

    takaraisland_takaraisland.tpl
    
    This is the HTML template of your game.
    
    Everything you are writing in this file will be displayed in the HTML page of your game user interface,
    in the "main game zone" of the screen.
    
    You can use in this template:
    _ variables, with the format {MY_VARIABLE_ELEMENT}.
    _ HTML block, with the BEGIN/END format
    
    See your "view" PHP file to check how to set variables and control blocks
    
    Please REMOVE this comment before publishing your game on BGA
-->
<div id="change3d">
 <button id="c3dAngleUp"   class="button3d ">&#x219b;;</button>
 <button id="c3dUp"        class="button3d ">&#8679;</button>
 <button id="c3dAngleDown" class="button3d">&#x219a;</button>
 <br>
 <button id="c3dLeft"  class="button3d ">&#8678;</button>
 <button id="c3dReset" class="button3d">3D</button>
 <button id="c3dRight" class="button3d">&#8680;</button>
 <br>
 <button id="c3dRotateL" class="button3d">&#x21bb;</button>
 <button id="c3dDown"    class="button3d ">&#8681;</button>
 <button id="c3dRotateR" class="button3d ">&#x21ba;</button>
 <br>
 <button id="c3dZoomIn"  class="button3d ">+</button>
 <button id="c3dZoomOut" class="button3d">-</button>
 <button id="c3dClear"   class="button3d">X</button>
</div>
<br>
<div id="playareascaler">
	<div id="playArea">
		<div id="tablecards" class="tablecards"></div>	
		<div id="boardwrapper" >
			<div id="boardPanel" class="boarddiv">
				<div id="swordholder">
					<div id="sword"></div>
				</div>
				
				<div id="expertholder1" class="expertholder">
					<div id="expert1" class="card expert">
						<div id="expert1_front" class="expert1 card-front"></div>
						<div id="expert1_back" class="expert1 card-back"></div>
					</div>
				</div>
				<div id="expertholder2" class="expertholder">
					<div id="expert2" class="card expert">
						<div id="expert2_front" class="expert2 card-front"></div>
						<div id="expert2_back" class="expert2 card-back"></div>
					</div>
				</div>
				<div id="expertholder3" class="expertholder">
					<div id="expert3" class="card expert">
						<div id="expert3_front" class="expert3 card-front"></div>
						<div id="expert3_back" class="expert3 card-back"></div>
					</div>
				</div>
				<div id="expertholder4" class="expertholder">
					<div id="expert4" class="card expert">
						<div id="expert4_front" class="expert4 card-front"></div>
						<div id="expert4_back" class="expert4 card-back"></div>
					</div>
				</div>
				<div id="wrapperdice">
					<div id="diceresult" >

						<div id="dice"  >
						    <!-- <div class="cover x"></div>
							<div class="cover y"></div>
							<div class="cover z"></div> -->
							<div class="side front">
								<div class="dsword"></div>
							</div>
							<div class="side front inner"></div>
							<div class="side top">
								<div class="dsword"></div>
							</div>
							<div class="side top inner"></div>
							<div class="side right">
								<div class="dsword"></div>
							</div>
							<div class="side right inner"></div>
							<div class="side left">
								<div class="dsword"></div>
							</div>
							<div class="side left inner"></div>
							<div class="side bottom">
								<div class="dcross"></div>
							</div>
							<div class="side bottom inner"></div>
							<div class="side back">
								<div class="dcross"></div>
							</div>
							<div class="side back inner"></div>
						</div>
					</div> 						
				</div> 
				<div id="HospitalC"></div>
				<div id="WaitingroomC"></div>
				<div id="deckholder1" class="deckholder"><div id="counterdeck1" class="deckcounter"></div><div id="deck1" class="deck"><div id="button_deck1" class="buttondiv"></div></div></div>
				<div id="deckholder2" class="deckholder"><div id="counterdeck2" class="deckcounter"></div><div id="deck2" class="deck"><div id="button_deck2" class="buttondiv"></div></div></div>
				<div id="deckholder3" class="deckholder"><div id="counterdeck3" class="deckcounter"></div><div id="deck3" class="deck"><div id="button_deck3" class="buttondiv"></div></div></div>
				<div id="deckholder4" class="deckholder"><div id="counterdeck4" class="deckcounter"></div><div id="deck4" class="deck"><div id="button_deck4" class="buttondiv"></div></div></div>
				<div id="deckholder5" class="deckholder"><div id="counterdeck5" class="deckcounter"></div><div id="deck5" class="deck"><div id="button_deck5" class="buttondiv"></div></div></div>
				<div id="deckholder6" class="deckholder"><div id="counterdeck6" class="deckcounter"></div><div id="deck6" class="deck"><div id="button_deck6" class="buttondiv"></div></div></div>

				<div id="workersC"></div>
				
				<div id="treasuredeckholder">
					<div id="treasuredeck"></div>
				</div>
				
				<div id="counter"></div>
				<div id="thedive"></div>
				
				
					<div id="counterC" class="playable" ></div>			
					<div id="diveC"    class="playable" ></div>
					<div id="expertsC" class="playable" ></div>
					<div id="explore1" class="exploreholder playable"></div>
					<div id="explore2" class="exploreholder playable"></div>
					<div id="explore3" class="exploreholder playable"></div>
					<div id="explore4" class="exploreholder playable"></div>
					<div id="explore5" class="exploreholder playable"></div>
					<div id="explore6" class="exploreholder playable"></div>
				
				<div id="reward"   class="treasurereward"></div>
			</div>
		</div>
			<div class="campwrapper">
			<!-- BEGIN camp -->
			
				<div id="playerCamp_{PLAYER_ID}" class="playercamp playercolor_{PLAYER_COLOR}" >
					<div id="playername_{PLAYER_ID}" class="playernameholder" style="color:#{PLAYER_COLOR};"><b>{PLAYER_NAME}</b></div>
					<div id="playerSwordholder_{PLAYER_ID}" class="playerswordholder"></div>
					<div id="playercardstore_{PLAYER_ID}" class="cardstore"></div>
					<div id="xpstore_{PLAYER_ID}" class="xpstore"></div>
					<div id="TH_{PLAYER_ID}" class="playertileholder">
						<div id="tile_{PLAYER_ID}_1" class="playertile tile1 color{PLAYER_COLOR}"></div>
						<div id="tile_{PLAYER_ID}_2" class="playertile tile2 color{PLAYER_COLOR}"></div>
						<div id="tile_{PLAYER_ID}_3" class="playertile tile3 color{PLAYER_COLOR}"></div>
					
					</div>
				</div>
			
			<!-- END camp -->
			</div>
			<div id="removed" class="tablecards"></div>
		</div>
	
</div>


<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${id}" style="position:absolute; top: ${x}px; left: ${y}px;" ></div>';


*/

var jstpl_woundtoken='<div  id="woundtoken_${id}" class="woundtoken" style="left: ${x}px; top: ${y}px;" ></div>';
var jstpl_player_board = '<br>\<div class="cp_board"></b>\<div id="gold_p${id}" class="goldcounter"> <div class="coin"></div> <span id="goldcount_p${id}">0</span>\</div><div id="xp_p${id}" class="goldcounter"> <div class="xpcounter"></div> <span id="xpcount_p${id}">0</span></b>\</div></div>';
var jstpl_stonetoken = '<div id="stonetoken_${id}"  class="card expert"><div class="card stonetoken" style="background-position: ${x}px ${y}px;"></div></div>';

</script>  

{OVERALL_GAME_FOOTER}