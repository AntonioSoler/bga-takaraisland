{OVERALL_GAME_HEADER}

<!-- 
--------
-- BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
-- takaraisland implementation : © Antonio Soler <morgald.es@gmail.com>
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
<table id="playArea">
	<tr id="up">
		<td id="boardcell">
			<div id="boardPanel" class="boarddiv">
				<div id="swordholder">
					<div id="sword" onclick="this.classList.toggle('flipped');">
						<div class="front"></div>
						<div class="back"></div>
					</div>
				</div>
				<div id="expertsC"></div>
				<div id="expert1" class="expertholder"></div>
				<div id="expert2" class="expertholder"></div>
				<div id="expert3" class="expertholder"></div>
				<div id="expert4" class="expertholder"></div>
				<div id="HospitalC"></div>
				<div id="WaitingroomC"></div>
				<div id="deckholder1" class="deckholder"><div id="deck1" class="deck"><div id="button_deck1" class="buttondiv" ></div></div> </div>
				<div id="deckholder2" class="deckholder"><div id="deck2" class="deck"><div id="button_deck2" class="buttondiv" ></div></div> </div>
				<div id="deckholder3" class="deckholder"><div id="deck3" class="deck"><div id="button_deck3" class="buttondiv" ></div></div> </div>
				<div id="deckholder4" class="deckholder"><div id="deck4" class="deck"><div id="button_deck4" class="buttondiv" ></div></div> </div>
				<div id="deckholder5" class="deckholder"><div id="deck5" class="deck"><div id="button_deck5" class="buttondiv" ></div></div> </div>
				<div id="deckholder6" class="deckholder"><div id="deck6" class="deck"><div id="button_deck6" class="buttondiv" ></div></div> </div>

				<div id="workersC"></div>
				<div id="treasuredeck"></div>
				<div id="counterC"></div>
				<div id="diveC"></div>
				<div id="explore1" class="exploreholder"></div>
				<div id="explore2" class="exploreholder"></div>
				<div id="explore3" class="exploreholder"></div>
				<div id="explore4" class="exploreholder"></div>
				<div id="explore5" class="exploreholder"></div>
				<div id="explore6" class="exploreholder"></div>
			</div>
		</td>
		<td>
			<div id="campswrapper">
				<!-- BEGIN camp -->
				<div id="playerCamp_{PLAYER_ID}" class="playercamp playercolor_{PLAYER_COLOR}" >
					<div id="playername_{PLAYER_ID}" class="playernameholder" style="color:#{PLAYER_COLOR};"><b>{PLAYER_NAME}</b></div>
					<div id="playerSwordholder_{PLAYER_ID}" class="playerswordholder"></div>
					<div id="playercardstore_{PLAYER_ID}" class="cardstore"></div>
					<div id="xpstore_{PLAYER_ID}" class="xpstore"></div>
					<div id="TH_{PLAYER_ID}_1" class="playertileholder t1"></div>
					<div id="TH_{PLAYER_ID}_2" class="playertileholder t2"></div>
					<div id="TH_{PLAYER_ID}_3" class="playertileholder t3"></div>
				</div>
				<!-- END camp -->
			</div>
		</td>
	</div>
	<tr id="down">
		<td colspan="2">
			<div id="table_wrap">
				<h2>{TABLE}</h2>
				<div id="tablecards" class="whiteblock tablecards"></div>
			</div>
		</div></td></tr></table>

<script type="text/javascript">

// Javascript HTML templates

/*
// Example:
var jstpl_some_game_item='<div class="my_game_item" id="my_game_item_${id}" style="position:absolute; top: ${x}px; left: ${y}px;" ></div>';


*/

var jstpl_gem='<div  id="gem_${id}" class="gem cardgem" style="left: ${x}px; top: ${y}px;" ></div>';

var jstpl_votecard= '<div id="votecard_${player_id}" class="votecard${action}" ></div>';

var jstpl_artifact='<div class="artifacticon"></div>';

</script>  

{OVERALL_GAME_FOOTER}
