define(['dojo', 'dojo/_base/declare', g_gamethemeurl + 'modules/js/data.js'], (dojo, declare) => {
  const PLAYER_COUNTERS = ['immediateCiv', 'endgameCiv'];

  const SCORE_CATEGORIES = ['planet', 'tracks', 'lifepods', 'meteors', 'civ', 'objectives', 'total'];
  const SCORE_MULTIPLE_ENTRIES = ['civ', 'objectives'];

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
        // Panels
        //        this.place('tplPlayerPanel', player, `overall_player_board_${player.id}`);
        $(`overall_player_board_${player.id}`).addEventListener('click', () => this.goToPlayerBoard(player.id));
      });

      //      this.setupPlayersCounters();
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
        data.sections.forEach((section) => {
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
      });

      this.goToPlayerBoard(this.orderedPlayers[0].id);
    },

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

    updateComputedScoresheetData(pId) {
      this.gamedatas.players[pId].scoresheet.forEach((entry) => {
        $(`slot-${pId}-${entry.slot}`).innerHTML = '';

        // "v" for value
        if (entry.v !== undefined) {
          $(`slot-${pId}-${entry.slot}`).innerHTML = entry.v;
        }
        // "s" for scribble (useless ??)
        else if (entry.s !== undefined) {
          console.error('TODO');
        }
      });
    },

    ////////////////////////////////////////////////////
    //   ____                  _
    //  / ___|___  _   _ _ __ | |_ ___ _ __ ___
    // | |   / _ \| | | | '_ \| __/ _ \ '__/ __|
    // | |__| (_) | |_| | | | | ||  __/ |  \__ \
    //  \____\___/ \__,_|_| |_|\__\___|_|  |___/
    //
    ////////////////////////////////////////////////////
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
