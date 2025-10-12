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

    isScenario8() {
      return this.gamedatas.scenario == 8;
    },

    setupPlayers() {
      // Add player board and player panel
      this.forEachOrderedPlayer((player) => {
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

      let v = this.settings ? this.settings.playerBoardsLayout : 0;
      if (v == 0) {
        // Tabbed view
        this._focusedPlayer = pId;
        let oScoresheets = [...$('score-sheet-holder').querySelectorAll('.score-sheet')];
        let nextIndex = null;
        oScoresheets.forEach((board, i) => {
          board.classList.toggle('active', board.id == `score-sheet-${pId}`);
          board.classList.remove('second-active');

          if (board.id == `score-sheet-${pId}` && this.isScenario8()) {
            nextIndex = (i + 1) % oScoresheets.length;
          }
        });

        if (nextIndex !== null) {
          oScoresheets[nextIndex].classList.add('second-active');
        }
      } else if (v == 1) {
        // Multiple view
        this._focusedPlayer = null;
        window.scrollTo(0, $(`score-sheet-${pId}`).getBoundingClientRect()['top'] - 30);
      }
    },

    setupChangeScoreSheetArrows() {
      $(`score-sheet-wrapper`)
        .querySelectorAll('.slideshow-left')
        .forEach((leftArrow) => leftArrow.addEventListener('click', () => this.switchPlayerBoard(-1)));

      $(`score-sheet-wrapper`)
        .querySelectorAll('.slideshow-right')
        .forEach((rightArrow) => rightArrow.addEventListener('click', () => this.switchPlayerBoard(1)));
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
      this.setupOverview();
      this.ensureSpecificGameImageLoading([`scenario-${scenarioId}.jpg`]);
      $('scenario-name').innerHTML = `#${scenarioId}: ${data.name}`;

      // Specific setup for scenario8
      if (scenarioId == 8) {
        this.setupScoreSheetsScenario8();
        return;
      }

      if (this.isSolo()) {
        $('ebd-body').dataset.solo = 1;
        this.setupAstra();
      }

      this.forEachOrderedPlayer((player) => {
        let pId = player.id;
        // Global wrapper
        $('score-sheet-holder').insertAdjacentHTML(
          'beforeend',
          `<div id="score-sheet-${pId}" class="score-sheet" data-board="${scenarioId}" style="border-color:#${player.color}">
            <div class='player-name' style="color:#${player.color}; border-color:#${player.color}">
              <div class='slideshow-left'><</div>
              ${player.name}
              <div class='slideshow-right'>></div>
            </div>
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

        // Player panels
        $(`numbers-status-container-${pId}`).querySelector('.numbers-scenario-amount').innerHTML = nNumbers;
        $(`system-errors-status-container-${pId}`).querySelector('.errors-scenario-amount').innerHTML = nErrors;
      });

      this.setupChangeScoreSheetArrows();
      this.goToPlayerBoard(this.orderedPlayers[0].id);
    },

    setupScoreSheetsScenario8() {
      const scenarioId = 8;
      const data = SCENARIOS_DATA[scenarioId];
      $('score-sheet-holder').dataset.board = scenarioId;

      let players = this.orderedPlayers;

      if (this.isSolo()) {
        players.push({
          id: 0,
          name: 'Astra',
          color: 'black',
        });
      }

      let n = players.length;
      for (let i = 0; i < n; i++) {
        let player1 = players[i];
        let player2 = players[(i - 1 + n) % n];
        let pId = player1.id;

        // Global wrapper
        $('score-sheet-holder').insertAdjacentHTML(
          'beforeend',
          `<div id="score-sheet-${pId}" class="score-sheet" data-board="${scenarioId}" style="border-color:#${player1.color}">
            <div class="gradient-border" style="background: linear-gradient(to bottom, #${player2.color}, #${player1.color});"></div>
            <div class="background-holder"></div>
            <div class="scoresheet-rotate">${this.formatIcon('reshuffle')}</div>
            <div class="scoresheet-rotate bottom-right">${this.formatIcon('reshuffle')}</div>
            <div class='player-name2' style="color:#${player1.color}; border-color:#${player1.color}">
              <div class='slideshow-left'><</div>
              ${player1.name}
              <div class='slideshow-right'>></div>
            </div>
            <div class='player-name' style="color:#${player2.color}; border-color:#${player2.color}">
              <div class='slideshow-left'><</div>
              ${player2.name}
              <div class='slideshow-right'>></div>
            </div>
            <div class="scoresheet-overlay"></div>
          </div>`
        );

        // Slots
        let nNumbers = 0,
          nErrors = 0;
        data.sections.forEach((section) => {
          if (section.id == 'numbers') nNumbers = section.elts.length;
          if (section.id == 'errors1') nErrors = section.elts.length;

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

        // Player panels
        if (pId != 0) {
          let numberDiv = $(`numbers-status-container-${pId}`).querySelector('.numbers-scenario-amount');
          numberDiv.innerHTML = nNumbers;
          numberDiv.parentNode.classList.add('S8');
          let errorDiv = $(`system-errors-status-container-${pId}`).querySelector('.errors-scenario-amount');
          errorDiv.innerHTML = nErrors;
          errorDiv.parentNode.classList.add('S8');
        }
      }

      // Scoresheet dynamic data
      for (let i = 0; i < n; i++) {
        this.updateComputedScoresheetData(players[i].id);
      }
      if (this.isSolo()) {
        $('ebd-body').dataset.solo = 1;
        this.setupAstra();
      }

      this.reversedOrderedPlayers = [];
      for (let i = 2; i < n; i++) {
        this.reversedOrderedPlayers.push(players[i]);
      }
      this.reversedOrderedPlayers.push(players[0]);
      this.reversedOrderedPlayers.push(players[1]);

      this.setupChangeScoreSheetArrows();
      this.goToPlayerBoard(this.orderedPlayers[0].id);

      $(`score-sheet-wrapper`)
        .querySelectorAll('.scoresheet-rotate')
        .forEach((rotate) => rotate.addEventListener('click', () => this.updateScoresheetRotation()));
    },

    updateScoresheetRotation(newValue) {
      if (!this.isScenario8()) return;

      if (newValue !== undefined) {
        $('score-sheet-holder').classList.toggle('rotated', newValue);
      } else {
        $('score-sheet-holder').classList.toggle('rotated');
      }

      // Update order => useful for multiple view !
      if ($('score-sheet-holder').classList.contains('rotated')) {
        Object.values(this.reversedOrderedPlayers).forEach((player) => {
          $('score-sheet-holder').insertAdjacentElement('beforeend', $(`score-sheet-${player.id}`));
        });
      } else {
        Object.values(this.orderedPlayers).forEach((player) => {
          $('score-sheet-holder').insertAdjacentElement('beforeend', $(`score-sheet-${player.id}`));
        });
      }
    },

    updateComputedScoresheetData(pId) {
      let entries = pId == 0 ? this.gamedatas.astra : this.gamedatas.players[pId].scoresheet;
      let overviewPId = pId;
      if (this.isScenario8()) {
        pId = entries[0][0];
        overviewPId = entries[0][1];
      }

      entries.forEach((entry) => {
        if (entry.outsideScoresheet) return;

        // Dynamic slot on scoresheet
        if (entry.slot) {
          let id = `slot-${pId}-${entry.slot}`;
          if (!$(id)) {
            console.error('Not found slot:', id);
            return;
          }
          $(`slot-${pId}-${entry.slot}`).innerHTML = entry.v;
        }
        // Dynamic data on player panel
        if (entry.panel) {
          if (overviewPId != 0) {
            $(`${entry.panel}-status-${overviewPId}`).innerHTML = entry.v;
          }
        }
        // Dynamic data on overview
        if (entry.overview) {
          let lOverviewPId = entry.pId || overviewPId;
          if (lOverviewPId != 0) {
            this.updateOverviewEntry(entry, lOverviewPId);
          }
        }
        // SCORE
        if (entry.score && overviewPId != 0) {
          if (this.scoreCtrl[overviewPId]) this.scoreCtrl[overviewPId].toValue(entry.v);
          else $(`player_score_${overviewPId}`).innerHTML = entry.v;
        }
      });
    },

    //////////////////////////////////////
    //  ____
    // / ___|  ___ ___  _ __ ___  ___
    // \___ \ / __/ _ \| '__/ _ \/ __|
    //  ___) | (_| (_) | | |  __/\__ \
    // |____/ \___\___/|_|  \___||___/
    //////////////////////////////////////

    /*
     * Create a table with a nice overview of current situation for everyone
     */
    setupOverview() {
      if (this._overviewModal) {
        this._overviewModal.destroy();
      }

      this._overviewModal = new customgame.modal('showOverview', {
        class: 'welcometothemoon_popin',
        closeAction: 'hide',
        closeIcon: 'fa-times',
        openAnimation: true,
        openAnimationTarget: 'show-scores',
        contents: this.tplOverview(),
        // breakpoint: 0.9 * width,
        // scale: 0.8,
      });
      $('show-scores').addEventListener('click', () => this._overviewModal.show());
    },

    tplOverview() {
      const scenarioId = this.gamedatas.scenario;
      const data = SCENARIOS_DATA[scenarioId];
      if (!data.overview) {
        console.log('TODO: overview for this scenario');
        return '';
      }

      let overview = '';
      this._overviewEntries = {};
      data.overview.forEach((entry) => {
        this._overviewEntries[entry.name] = entry;
        let icons = '';
        entry.icon.split('/').forEach((icon) => (icons += this.formatIcon(icon)));
        overview += `<th id='overview-${entry.name}'>${icons}</th>`;
      });

      let rows = '';
      this.forEachPlayer((player) => {
        let tr = `<tr><td>${player.name}</td>`;
        data.overview.forEach((entry) => {
          tr += `<td id='overview-${entry.name}-${player.id}' class='overview-${entry.name}'></td>`;
        });
        tr += `<td id='overview-total-${player.id}' class='overview-total'></td></tr>`;
        rows += tr;
      });

      // ASTRA
      if (this.isSolo()) {
        let tr = `<tr><td>${_('ASTRA')}</td>`;
        data.overview.forEach((entry) => {
          tr += `<td id='overview-${entry.name}-astra' class='overview-${entry.name}'></td>`;
        });
        tr += `<td id='overview-total-astra' class='overview-total'></td></tr>`;
        rows += tr;
      }

      return `
      <table id='players-overview'>
        <thead>
          <tr>
            <th id="overview-user"><i class="fa fa-user"></i></th>
            ${overview}
            <th id="overview-total"><i class="fa fa-star"></i></th>
          </tr>
        </thead>
        <tbody id="player-overview-body">
          ${rows}
        </tbody>
      </table>
      `;
    },

    updateOverviewEntry(entry, pId) {
      let o = $(`overview-${entry.overview}-${pId}`);
      if (!o) {
        console.error(`Unfound overview entry: overview-${entry.overview}-${pId}`);
        return;
      }

      const config = this._overviewEntries[entry.overview];
      // Should only be used for total probably
      if (!config) {
        o.innerHTML = entry.v;
      }
      // Basic display with points + star icon
      else if (config.type == 'points') {
        let breakline = entry.subdetails ? '<br />' : '';
        let details = entry.details !== undefined ? ` <span class='details'>(${entry.details})</span>` : '';
        o.innerHTML = `${entry.v}<i class="fa fa-star"></i>${breakline}${details}`;
      }
      // Basic display with points + star icon or dash if 0
      else if (config.type == 'points-or-dash') {
        let details = entry.details ? ` (${entry.details})` : '';
        let score = entry.v > 0 ? `${entry.v}<i class="fa fa-star"></i>` : '-';
        o.innerHTML = `${score}${details}`;
      }
      // Display of X / Y
      else if (config.type == '/') {
        o.innerHTML = `<span>${entry.v}</span> / <span>${entry.max}</span>`;
      }
      // Checkmark
      else if (config.type == 'checkmark') {
        o.innerHTML = entry.checkmark ? this.tplScribbleCheckmark({ turn: -1, id: `overview-${config.name}-${pId}` }) : '-';
      }
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

      const astraBonusDesc = _(
        'Solo bonuses allows you to avoid giving a card to ASTRA. To do that, whenever you want, cross off an already circled Solo bonus. Then instead of giving ASTRA a Starship card, proceed to next round and discard remaining Starship cards.'
      );
      this.addCustomTooltip(
        'astra-bonus',
        `<h2>${_('SOLO BONUS:')}</h2> 
        ${adventureDatas.descSoloBonus}
        <br /> 
        ${astraBonusDesc} 
        `
      );
      if (adventureDatas.descMisc) {
        this.addCustomTooltip('astra-misc', `${adventureDatas.descMisc}`);
      }

      this.updateAstra();
    },

    updateAstra() {
      this.gamedatas.astra.forEach((entry) => {
        // Dynamic slot on scoresheet
        if (entry.slot) {
          $(entry.slot).innerHTML = entry.v;
        }
        // Dynamic data on overview
        if (entry.overview) {
          if (this.isScenario8() && entry.overview !== 'total') {
            return;
          }
          this.updateOverviewEntry(entry, 'astra');
        }
      });

      if (this.isScenario8()) {
        this.updateComputedScoresheetData(0);
      }
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
          name: _('The Launch'),
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
          name: _('The Journey'),
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
          name: _('The Colony'),
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
          name: _('The Mine'),
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
            'At the end of the game, if your number of crossed off astronauts is higher than or equal to the number of Astronaut cards given to ASTRA, then you are in the lead. Otherwise, you are second and you earn the corresponding points. You do not earn any points if you have not crossed off at least one astronaut. ASTRA does not receive any additional points for that.'
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
  });
});
