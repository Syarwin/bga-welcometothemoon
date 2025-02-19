define(['dojo', 'dojo/_base/declare', g_gamethemeurl + 'modules/js/data.js'], (dojo, declare) => {
  // const PLAYER_COUNTERS = ['immediateCiv', 'endgameCiv'];

  // const SCORE_CATEGORIES = ['planet', 'tracks', 'lifepods', 'meteors', 'civ', 'objectives', 'total'];
  // const SCORE_MULTIPLE_ENTRIES = ['civ', 'objectives'];

  return declare('welcometothemoon.players', null, {
    getPlayers() {
      return Object.values(this.gamedatas.players);
    },

    getColoredName(pId) {
      let name = this.gamedatas.players[pId].name;
      return this.coloredPlayerName(name);
    },

    getPlayerColor(pId) {
      return this.gamedatas.players[pId].color;
    },

    isSolo() {
      return this.getPlayers().length == 1;
    },

    setupPlayers() {
      this.setupChangeScoreSheetArrows();

      // Change No so that it fits the current player order view
      let currentNo = this.getPlayers().reduce((carry, player) => (player.id == this.player_id ? player.no : carry), 1);
      let nPlayers = Object.keys(this.gamedatas.players).length;
      this.forEachPlayer((player) => (player.order = (player.no + nPlayers - currentNo) % nPlayers));
      this.orderedPlayers = Object.values(this.gamedatas.players).sort((a, b) => a.order - b.order);

      // Add player board and player panel
      this.orderedPlayers.forEach((player, i) => {
        let pId = player.id;

        // Panels
        this.place('tplPlayerPanel', player, `overall_player_board_${player.id}`);
        $(`overall_player_board_${pId}`).addEventListener('click', () => this.goToPlayerBoard(player.id));

        // Tooltips
        this.addCustomTooltip(`plan-status-1-${pId}`, _('Status of Mission A'));
        this.addCustomTooltip(`plan-status-2-${pId}`, _('Status of Mission B'));
        this.addCustomTooltip(`plan-status-3-${pId}`, _('Status of Mission C'));
        this.addCustomTooltip(`numbers-status-container-${pId}`, _('Number of filled up number slots'));
        this.addCustomTooltip(`system-errors-status-container-${pId}`, _('Number of System errors'));
      });

      //      this.setupPlayersCounters();
      if (this.isSolo()) {
        $('ebd-body').dataset.solo = 1;
        this.setupAstra();
      }
    },

    onChangePlayerBoardsLayoutSetting(v) {
      if (v == 0) {
        this.goToPlayerBoard(this.orderedPlayers[0].id);
      } else {
        this._focusedPlayer = null;
      }
    },

    goToPlayerBoard(pId, evt = null) {
      if (evt) evt.stopPropagation();

      let v = 0; // TODO this.settings.playerBoardsLayout;
      if (v == 0) {
        // Tabbed view
        this._focusedPlayer = pId;
        [...$('score-sheet-holder').querySelectorAll('.score-sheet')].forEach((board) =>
          board.classList.toggle('active', board.id == `score-sheet-${pId}`)
        );
      } else if (v == 1) {
        // Multiple view
        this._focusedPlayer = null;
        window.scrollTo(0, $(`score-sheet-${pId}`).getBoundingClientRect()['top'] - 30);
      }
    },

    setupChangeScoreSheetArrows() {
      let leftArrow = $(`score-sheet-wrapper`).querySelector('.slideshow-left');
      if (leftArrow) leftArrow.addEventListener('click', () => this.switchPlayerBoard(-1));

      let rightArrow = $(`score-sheet-wrapper`).querySelector('.slideshow-right');
      if (rightArrow) rightArrow.addEventListener('click', () => this.switchPlayerBoard(1));
    },

    getDeltaPlayer(pId, delta) {
      let playerOrder = this.orderedPlayers;
      let index = playerOrder.findIndex((elem) => elem.id == pId);
      if (index == -1) return -1;

      let n = playerOrder.length;
      return playerOrder[(((index + delta) % n) + n) % n].id;
    },

    switchPlayerBoard(delta) {
      let pId = this.getDeltaPlayer(this._focusedPlayer, delta);
      if (pId == -1) return;
      this.goToPlayerBoard(pId);
    },

    tplPlayerPanel(player) {
      let pId = player.id;
      return `<div class="wttm-player-panel">
        <div class="plans-status">
          <div id="plan-status-1-${pId}" class="plan-status-1"></div>
          <div id="plan-status-2-${pId}" class="plan-status-2"></div>
          <div id="plan-status-3-${pId}" class="plan-status-3"></div>
        </div>

      <div id="numbers-status-container-${pId}" class="numbers-status">
        <div id="numbers-status-${pId}"></div>
        <div></div>
        <div class="numbers-scenario-amount"></div>
      </div>

      <div id="system-errors-status-container-${pId}" class="system-errors-status">
        <div id="errors-status-${pId}"></div>
        <div></div>
        <div class="errors-scenario-amount"></div>
      </div>
    </div>`;
    },

    /////////////////////////////////////////////////////////////
    //  ____                     ____  _               _
    // / ___|  ___ ___  _ __ ___/ ___|| |__   ___  ___| |_
    // \___ \ / __/ _ \| '__/ _ \___ \| '_ \ / _ \/ _ \ __|
    //  ___) | (_| (_) | | |  __/___) | | | |  __/  __/ |_
    // |____/ \___\___/|_|  \___|____/|_| |_|\___|\___|\__|
    /////////////////////////////////////////////////////////////

    setupScoreSheets() {
      const scenarioId = this.gamedatas.scenario;
      const data = SCENARIOS_DATA[scenarioId];

      this.forEachPlayer((player) => {
        let pId = player.id;
        // Global wrapper
        $('score-sheet-holder').insertAdjacentHTML(
          'beforeend',
          `<div id="score-sheet-${pId}" class="score-sheet" data-board="${scenarioId}" style="border-color:#${player.color}">
            <div class='player-name' style="color:#${player.color}; border-color:#${player.color}">${player.name}</class>
            <div class="scoresheet-overlay"></div>
          </div>`
        );

        // Slots
        let nNumbers = 0,
          nErrors = 0;
        data.sections.forEach((section) => {
          if (section.id == 'numbers') nNumbers = section.elts.length;
          if (section.id == 'errors') nErrors = section.elts.length;

          let className = section.eltClass;
          section.elts.forEach((elt) => {
            $(`score-sheet-${pId}`).insertAdjacentHTML(
              'beforeend',
              `<div class='wttm-slot ${className}' id='slot-${pId}-${elt.id}' data-id='${elt.id}' style="left:${elt.x}px; top:${elt.y}px"></div>`
            );

            if (elt.r) {
              $(`slot-${pId}-${elt.id}`).style.transform = `rotate(${elt.r}deg)`;
            }
          });
        });

        // Scoresheet dynamic data
        this.updateComputedScoresheetData(pId);

        // Player panels
        $(`numbers-status-container-${pId}`).querySelector('.numbers-scenario-amount').innerHTML = nNumbers;
        $(`system-errors-status-container-${pId}`).querySelector('.errors-scenario-amount').innerHTML = nErrors;
      });

      this.goToPlayerBoard(this.orderedPlayers[0].id);
    },

    updateComputedScoresheetData(pId) {
      this.gamedatas.players[pId].scoresheet.forEach((entry) => {
        // Dynamic slot on scoresheet
        if (entry.slot) {
          $(`slot-${pId}-${entry.slot}`).innerHTML = entry.v;
        }
        // Dynamic data on player panel
        else if (entry.panel) {
          $(`${entry.panel}-status-${pId}`).innerHTML = entry.v;
        }
      });
    },

    ////////////////////////////////////
    //      _        _
    //     / \   ___| |_ _ __ __ _
    //    / _ \ / __| __| '__/ _` |
    //   / ___ \\__ \ |_| | | (_| |
    //  /_/   \_\___/\__|_|  \__,_|
    ////////////////////////////////////
    setupAstra() {
      const level = this.gamedatas.astraLevel;
      const opponent = this.getAstraOpponentsData()[level];
      const scenario = this.gamedatas.scenario;
      const adventureDatas = this.getAstraAdventuresData()[scenario];

      let scores = '';
      let minMult = Math.min(...opponent.mult);
      let maxMult = Math.max(...opponent.mult);
      ['robot', 'energy', 'plant', 'water', 'astronaut', 'planning'].forEach((icon, i) => {
        const mult = opponent.mult[i];
        let highlight = '';
        if (mult == minMult) highlight = 'min-mult';
        if (mult == maxMult && mult > minMult + 1) highlight = 'max-mult';

        scores += `<div class='astra-score-category'>
          <div class='category-icon' data-icon='${icon}'></div>
          <div class='category-multiplier ${highlight}'>${mult}</div>
          <div class='category-cross'>x</div>
          <div class='category-count' id='astra-${icon}-count'></div>
          <div class='category-score' id='astra-${icon}-score'></div>
        </div>`;
      });

      // Fixed score
      scores += `<div class='astra-score-category fixed-score'>
        <div class='category-multiplier'></div>
        <div class='category-cross'></div>
        <div class='category-score' id='astra-fixed-score'>${adventureDatas.fixedScore}</div>
      </div>`;

      // Difficulty score
      scores += `<div class='astra-score-category level-score'>
        <div class='category-icon' data-icon='level'></div>
        <div class='category-cross'>x</div>
        <div class='category-multiplier'>${adventureDatas.levelMultiplier}</div>
        <div class='category-score' id='astra-level-score'></div>
      </div>`;

      scores += `<div class='astra-score-category total-score'>
        <div class='category-cross'></div>
        <div class='category-multiplier'></div>
        <div class='category-score' id='astra-total-score'></div>
      </div>`;

      let bonusSlots = '';
      for (let i = 0; i < adventureDatas.nBonuses; i++) {
        bonusSlots += `<div class="astra-bonus-slot-wrapper"><div class="astra-bonus-slot" id="astra-bonus-${i}"></div></div>`;
      }

      let astraMisc = '';
      if (adventureDatas.descMisc) {
        if (scenario != 1) {
          astraMisc = `<div id="astra-misc" class="astra-misc" data-scenario="${scenario}"></div>`;
        }
        // Scenario 1 : grid for crossing off rockets
        else {
          let grid = '';
          for (let slot = 0; slot <= 80; slot++) {
            grid += `<div id='astra-rocket-slot-${slot}' class='astra-rocket-slot'></div>`;
          }
          astraMisc = `<div id="astra-misc" class="astra-misc" data-scenario="${scenario}">
            <div class='astra-rocket-grid'>
              ${grid}
            </div>
          </div>`;
        }
      }

      $('astra-container').insertAdjacentHTML(
        'beforeend',
        `<div class="astra-wrapper" data-scenario="${scenario}">
        <div class="astra-opponent" data-level="${level}">
          <div class="astra-level" data-level="${level}"></div>
          <div class="astra-name">${opponent.name}</div>
        </div>
        <div class="astra-scores">
          ${scores}
        </div>
        <div class="astra-effects-bonus-wrapper" data-scenario="${scenario}">
          <div id="astra-effects" class="astra-effects"></div>
          <div id="astra-bonus" class="astra-bonus"></div>
          <div class="astra-bonus-slots">
            ${bonusSlots}            
          </div>
        </div>
        ${astraMisc}
      </div>`
      );

      this.addCustomTooltip('astra-effects', `<h2>${_('ASTRA EFFECT:')}</h2> ${adventureDatas.descEffects}`);
      this.addCustomTooltip('astra-bonus', `<h2>${_('SOLO BONUS:')}</h2> ${adventureDatas.descSoloBonus}`);
      if (adventureDatas.descMisc) {
        this.addCustomTooltip('astra-misc', `${adventureDatas.descMisc}`);
      }

      this.updateAstra();
    },

    updateAstra() {
      Object.entries(this.gamedatas.astra).forEach(([slot, value]) => {
        $(slot).innerHTML = value;
      });
    },

    getAstraOpponentsData() {
      return {
        1: {
          name: 'Katherine',
          mult: [2, 3, 2, 3, 1, 4],
        },
        2: {
          name: 'Alexei',
          mult: [2, 3, 3, 2, 4, 3],
        },
        3: {
          name: 'Margaret',
          mult: [5, 4, 2, 2, 2, 3],
        },
        4: {
          name: 'Franklin',
          mult: [2, 6, 4, 3, 3, 2],
        },
        5: {
          name: 'Sergei',
          mult: [4, 4, 4, 4, 4, 3],
        },
        6: {
          name: 'Stephanie',
          mult: [6, 2, 4, 5, 4, 4],
        },
        7: {
          name: 'Thomas',
          mult: [5, 4, 3, 6, 5, 4],
        },
        8: {
          name: 'Peggy',
          mult: [5, 3, 6, 3, 6, 6],
        },
      };
    },

    getAstraAdventuresData() {
      return {
        1: {
          fixedScore: 0,
          levelMultiplier: 0,
          nBonuses: 9,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A, B or C, select one Sabotage effect still available in your marking area and cross it off immediately. From now on, this bonus is no longer available for you. In addition, circle 1 System Error immediately.'
          ),
          descSoloBonus: _('Each time you trigger a Sabotage effect, circle 1 Solo bonus on the ASTRA Adventure card.'),
          descMisc: _(
            'You will not tally up ASTRA’s score at the end of the game, because during the whole game you will use the scoring track on the ASTRA Adventure card. Each time you give a Starship card to ASTRA, immediately cross off the number of boxes corresponding to the action type, as specified on the ASTRA Opponent card.'
          ),
        },
        2: {
          fixedScore: 5,
          levelMultiplier: 1,
          nBonuses: 10,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A or B, choose a space station with the highest multiplier still available on your sheet and cross it off immediately. From now on, you will only be able to get the lowest multiplier of that space station. The ASTRA Effect C card does not trigger the ASTRA Effect, but when you draw it on the 2nd draw, you must nevertheless flip the mission C card.'
          ),
          descSoloBonus: _(
            'Whenever you get the highest multiplier from a space station, circle 2 Solo bonuses on the ASTRA Adventure card.'
          ),
          descMisc: _(
            'At the end of the game, on your course, if the number of complete zones is higher than or equal to the number of sets of two Energy cards given to ASTRA, then you are in the lead, and you earn 20 points for your complete zones. Otherwise, you are second and you earn only 10 points. You do not earn any points if you do not have at least one complete zone. ASTRA does not receive any additional points for that.'
          ),
        },
        3: {
          fixedScore: 10,
          levelMultiplier: 3,
          nBonuses: 8,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A or B, choose a quarter with the highest 15 points bonus still available on your sheet, and immediately cross it off. From now on, you will only be able to earn the 5 points bonus by numbering this quarter. The ASTRA Effect C card does not trigger the ASTRA Effect, but when you draw it on the 2nd draw, you must nevertheless flip the mission C card.'
          ),
          descSoloBonus: _(
            'Whenever you get the highest bonus of 15 points by numbering all the buildings of a quarter, circle 2 Solo bonuses on the ASTRA Adventure card.'
          ),
          descMisc: _(
            'At the end of the game, if your number of crossed off astronauts is higher than or equal to the number of Astronaut cards given to ASTRA, then you are in the lead and you earn 20 points. Otherwise, you are second and you earn only 10 points. You do not earn any points if you have not crossed off at least one astronaut. ASTRA does not receive any additional points for that.'
          ),
        },
        4: {
          fixedScore: 5,
          levelMultiplier: 2,
          nBonuses: 8,
          descEffects: _(
            'As soon as you draw an ASTRA Effect, either A or B, choose a filling bonus of a main factory still available on your sheet and immediately cross it off. From now on, this bonus is no longer available for you. The ASTRA Effect C card does not trigger the ASTRA Effect, but when you draw it on the 2nd draw, you must nevertheless flip the mission C card.'
          ),
          descSoloBonus: _(
            'Whenever you earn a filling bonus of a main factory, circle 2 Solo bonus on the ASTRA Adventure card.'
          ),
        },
        5: {
          fixedScore: 10,
          levelMultiplier: 2,
          nBonuses: 8,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A, B or C, choose one highest bonus on top or at the bottom of a skyscraper that is still available on your sheet and immediately cross it off. From now on, you will only be able to earn the lowest bonus there.'
          ),
          descSoloBonus: _(
            'Whenever you get the highest bonus at the top or at the bottom of a skyscraper, circle one Solo bonus on the ASTRA Adventure card.'
          ),
          descMisc: _(
            'At the end of the game, if your number of crossed off astronauts is higher than or equal to the number of Astronaut cards given to ASTRA, then you are in the lead and you earn 20 points. Otherwise, you are second and you earn only 10 points. You do not earn any points if you have not crossed off at least one astronaut. ASTRA does not receive any additional points for that.'
          ),
        },
        6: {
          fixedScore: 5,
          levelMultiplier: 4,
          nBonuses: 9,
          descEffects: _(
            'The ASTRA Effect is different on the 1st and the 2nd draw of the ASTRA Effect cards. On the 1st draw: Whenever you draw an ASTRA Effect card, either A, B or C, ASTRA immediately activates the green or blue virus, or triggers a propagation. In the scoring area, choose a virus activation symbol still available between green and blue, cross it off and activate the corresponding virus. If both green and blue viruses are already activated, select instead a Propagation symbol still available and cross it off. You must immediately (and not on phase 5) trigger a propagation of all active viruses. On the 2nd draw: Whenever you draw an ASTRA Effect card, either A, B or C, immediately activate the virus shown on the corresponding Mission card, either A, B or C, and immediately (and not on phase 5) trigger a propagation of all active viruses. Then flip over the Mission card on its accomplished mission side.'
          ),
          descSoloBonus: _(
            'Whenever you activate a virus yourself (with a mission or a Plant/Water action), or you trigger a Propagation symbol (with a Plant/Water action), circle 1 Solo bonus on the ASTRA Adventure card.'
          ),
        },
        7: {
          fixedScore: 15,
          levelMultiplier: 1,
          nBonuses: 6,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A, B or C, choose a greenhouse X2 bonus still available on your sheet, and immediately cross it off. From now on, this bonus is no longer available for you.'
          ),
          descSoloBonus: _('Whenever you get a x2 bonus, circle 1 Solo bonus on the ASTRA Adventure card.'),
        },
        8: {
          fixedScore: 20,
          levelMultiplier: 3,
          nBonuses: 6,
          descEffects: _(
            'As soon as you draw an ASTRA Effect card, either A, B or C, immediately perform 2 Planning actions for ASTRA. On the active sheet of the current turn, draw 2 ASTRA insignia on a moon. You must choose a moon whose planet is still available, and as a priority the moon of the planet with the most of your insignia (planet + moon). If several planets are tied, you can choose whichever. If the moon is already occupied by an insignia, add an ASTRA insignia next to it, then draw the 2nd insignia on the moon of the next planet in the priority order.'
          ),
          descSoloBonus: _(
            'Whenever you draw your insignia on the flags of 2 planets, circle 1 Solo bonus on the ASTRA Adventure card.'
          ),
          descMisc: 'TODO',
        },
      };
    },

    ////////////////////////////////////////////////////
    //   ____                  _
    //  / ___|___  _   _ _ __ | |_ ___ _ __ ___
    // | |   / _ \| | | | '_ \| __/ _ \ '__/ __|
    // | |__| (_) | |_| | | | | ||  __/ |  \__ \
    //  \____\___/ \__,_|_| |_|\__\___|_|  |___/
    //
    ////////////////////////////////////////////////////

    // tplPlayerPanel(player) {
    //   return `<div class="welcometothemoon-first-player-holder" id="firstPlayer-${player.id}"></div>
    //   <div class='player-info'>
    //     <div class='civ-hand' id='civ-cards-indicator-${player.id}'>
    //       <span id='counter-${player.id}-immediateCiv'>0</span>!
    //       +
    //       <span id='counter-${player.id}-endgameCiv'>0</span>
    //       •
    //       ${this.formatIcon('civ')}
    //     </div>
    //     ${this.isSolo() ? '' : `<div class="private-objectives" id="private-objectives-${player.id}"></div>`}
    //   </div>`;
    // },

    // /**
    //  * Create all the counters for player panels
    //  */
    // setupPlayersCounters() {
    //   this._playerCounters = {};
    //   this.forEachPlayer((player) => {
    //     this._playerCounters[player.id] = {};
    //     PLAYER_COUNTERS.forEach((res) => {
    //       let v = player[res] || 0;
    //       this._playerCounters[player.id][res] = this.createCounter(`counter-${player.id}-${res}`, v);
    //     });
    //   });
    // },

    // /**
    //  * Update all the counters in player panels according to gamedatas, useful for reloading
    //  */
    // updatePlayersCounters(anim = true) {
    //   this.forEachPlayer((player) => {
    //     // PLAYER_COUNTERS.forEach((res) => {
    //     //   let value = player[res] || 0;
    //     //   this._playerCounters[player.id][res].goTo(value, anim);
    //     // });

    //     // CIV counters
    //     let immediateCiv = 0,
    //       endgameCiv = 0;
    //     Object.values(player.playedCiv).forEach((card) => {
    //       if (card.effectType == 'immediate') immediateCiv++;
    //       else endgameCiv++;
    //     });

    //     Object.values(player.handCiv).forEach((card) => {
    //       if (card.effectType == 'immediate') immediateCiv++;
    //       else endgameCiv++;
    //     });

    //     this._playerCounters[player.id]['immediateCiv'].toValue(immediateCiv);
    //     this._playerCounters[player.id]['endgameCiv'].toValue(endgameCiv);
    //   });
    // },

    //////////////////////////////////////
    //  ____
    // / ___|  ___ ___  _ __ ___  ___
    // \___ \ / __/ _ \| '__/ _ \/ __|
    //  ___) | (_| (_) | | |  __/\__ \
    // |____/ \___\___/|_|  \___||___/
    //////////////////////////////////////

    // tplScoreModal() {
    //   return `
    // <table id='players-scores'>
    //   <thead>
    //     <tr id="scores-names">
    //       <th>${_('NAME')}</th>
    //     </tr>
    //     <tr id="scores-planets">
    //       <th>${_('Planet Name')}</th>
    //     </tr>
    //     <tr id="scores-corporations">
    //       <th>${_('Corporation Name')}</th>
    //     </tr>
    //   </thead>
    //   <tbody id="scores-body">
    //     <tr id="scores-row-planet">
    //       <td class="row-header">${_('Planet')}</td>
    //     </tr>
    //     <tr id="scores-row-tracks">
    //       <td class="row-header">${_('Resource Tracks')}</td>
    //     </tr>
    //     <tr id="scores-row-lifepods">
    //       <td class="row-header">${_('Lifepods')}</td>
    //     </tr>
    //     <tr id="scores-row-meteors">
    //       <td class="row-header">${_('Meteorites')}</td>
    //     </tr>
    //     <tr id="scores-row-civ">
    //       <td class="row-header">${_('CIV cards')}</td>
    //     </tr>
    //     <tr id="scores-row-objectives">
    //       <td class="row-header">${_('Objectives')}</td>
    //     </tr>

    //     <tr id="scores-row-total">
    //       <td class="row-header">${_('TOTAL')}</td>
    //     </tr>
    //   </tbody>
    // </table>
    // `;
    // },

    // /*
    //  * Display a table with a nice overview of current situation for everyone
    //  */
    // setupScoresModal() {
    //   this._scoresModal = new customgame.modal('showScores', {
    //     class: 'welcometothemoon_popin',
    //     closeIcon: 'fa-times',
    //     contents: this.tplScoreModal(),
    //     closeAction: 'hide',
    //     scale: 0.8,
    //     breakpoint: 800,
    //     verticalAlign: 'flex-start',
    //   });

    //   // Create columns
    //   this.forEachPlayer((player) => {
    //     let planetName = player.planetId ? _(PLANETS_DATA[player.planetId].name) : '';
    //     let corporationName = player.corporationId ? _(CORPOS_DATA[player.corporationId].name) : '';

    //     $('scores-names').insertAdjacentHTML('beforeend', `<th style='color:#${player.color}'>${player.name}</th>`);
    //     $('scores-planets').insertAdjacentHTML('beforeend', `<th>${_(planetName)}</th>`);
    //     $('scores-corporations').insertAdjacentHTML('beforeend', `<th>${_(corporationName)}</th>`);

    //     SCORE_CATEGORIES.forEach((row) => {
    //       let scoreElt = '<div><span id="score-' + player.id + '-' + row + '"></span><i class="fa fa-circle"></i></div>';
    //       let addClass = '';

    //       // Wrap that into a scoring entry
    //       scoreElt = `<div class="scoring-entry ${addClass}">${scoreElt}</div>`;

    //       if (SCORE_MULTIPLE_ENTRIES.includes(row)) {
    //         scoreElt += `<div class="scoring-subentries" id="score-subentries-${player.id}-${row}"></div>`;
    //       }

    //       $(`scores-row-${row}`).insertAdjacentHTML('beforeend', `<td>${scoreElt}</td>`);
    //     });
    //   });

    //   $('show-scores').addEventListener('click', () => this.showScoresModal());
    //   this.addTooltip('show-scores', '', _('Show scoring details.'));
    //   if (this.gamedatas.scores === null) {
    //     dojo.style('show-scores', 'display', 'none');
    //   }
    // },

    // showScoresModal() {
    //   this._scoresModal.show();
    // },

    // onEnteringStateGameEnd() {
    //   this.showScoresModal();
    //   dojo.style('show-scores', 'display', 'block');
    // },

    // /**
    //  * Create score counters
    //  */
    // setupPlayersScores() {
    //   this._scoresCounters = {};

    //   this.forEachPlayer((player) => {
    //     this._scoresCounters[player.id] = {};

    //     SCORE_CATEGORIES.forEach((category) => {
    //       this._scoresCounters[player.id][category] = this.createCounter('score-' + player.id + '-' + category);
    //     });
    //   });

    //   this.updatePlayersScores(false);
    // },

    // /**
    //  * Update score counters
    //  */
    // updatePlayersScores(anim = true) {
    //   if (this.gamedatas.scores !== null) {
    //     this.forEachPlayer((player) => {
    //       this.updatePlayerScores(player.id, anim);
    //     });
    //   }
    // },

    // updatePlayerScores(pId, anim = true) {
    //   SCORE_CATEGORIES.forEach((category) => {
    //     if (this.gamedatas.scores[pId][category] === undefined) return;

    //     let value = category == 'total' ? this.gamedatas.scores[pId]['total'] : this.gamedatas.scores[pId][category]['total'];
    //     this._scoresCounters[pId][category][anim ? 'toValue' : 'setValue'](value);

    //     let entries = this.gamedatas.scores[pId][category].entries;
    //     // if (SCORE_MULTIPLE_ENTRIES.includes(category)) {
    //     //   let container = $(`score-subentries-${player.id}-${category}`);
    //     //   dojo.empty(container);
    //     //   this.gamedatas.scores[player.id][category]['entries'].forEach((entry) => {
    //     //     dojo.place(
    //     //       `<div class="scoring-subentry">
    //     //       <div>${_(entry.source)}</div>
    //     //       <div>
    //     //         ${entry.score}
    //     //         <i class="fa fa-star"></i>
    //     //       </div>
    //     //     </div>`,
    //     //       container
    //     //     );
    //     //   });
    //     // }

    //     // Planet => show each row/column status
    //     if (category == 'planet') {
    //       Object.keys(entries).forEach((id) => {
    //         if (['Cerberus1', 'Cerberus2', 'Cerberus3'].includes(id)) return;

    //         let t = id.split('_');
    //         if (t[0] == 'city') return; // Gaia
    //         let cell = t[0] == 'column' ? this.getPlanetCell(pId, t[1], -1) : this.getPlanetCell(pId, -1, t[1]);
    //         cell.classList.toggle('ok', entries[id] > 0);
    //         cell.classList.toggle('nok', entries[id] == 0);
    //       });
    //     }
    //     // Objectives
    //     else if (category == 'objectives') {
    //       Object.keys(entries).forEach((cardId) => {
    //         let t = cardId.split('_');

    //         if (t[0] == 'NOCard') {
    //           $(`card-${t[1]}-${pId}-value`).innerHTML = Math.abs(entries[cardId][1]);
    //           $(`card-${t[1]}-${pId}-medal`).innerHTML = entries[cardId][0];
    //           if ($(`card-${t[1]}d-${pId}-value`)) {
    //             $(`card-${t[1]}d-${pId}-value`).innerHTML = Math.abs(entries[cardId][1]);
    //             $(`card-${t[1]}d-${pId}-medal`).innerHTML = entries[cardId][0];
    //           }
    //         } else if (t[0] == 'POCard') {
    //           console.log(cardId);
    //           let v = entries[cardId];
    //           $(`card-${t[1]}-${pId}-medal`).innerHTML = v;
    //           $(`card-${t[1]}-${pId}-value`).classList.toggle('ok', v > 0);
    //         }
    //       });
    //     }
    //   });
    //   if (this.scoreCtrl && this.scoreCtrl[pId] !== undefined) {
    //     this.scoreCtrl[pId].toValue(this.gamedatas.scores[pId].total);
    //   }
    // },
  });
});
