/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * Welcome To The Moon implementation : © Timothée Pecatte <tim.pecatte@gmail.com>, Vincent Toper <vincent.toper@gmail.com>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * welcometothemoon.js
 *
 * Welcome To The Moon user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
var debug = isDebug ? console.info.bind(window.console) : function () {};

define([
  'dojo',
  'dojo/_base/declare',
  'ebg/core/gamegui',
  'ebg/counter',
  g_gamethemeurl + 'modules/js/Core/game.js',
  g_gamethemeurl + 'modules/js/Core/modal.js',
  g_gamethemeurl + 'modules/js/Players.js',
  g_gamethemeurl + 'modules/js/Cards.js',
  g_gamethemeurl + 'modules/js/data.js',
  g_gamethemeurl + 'modules/js/Scribbles.js',
], function (dojo, declare) {
  //  return declare('bgagame.welcometothemoon', [customgame.game, welcometothemoon.players, welcometothemoon.meeples, welcometothemoon.cards], {
  return declare(
    'bgagame.welcometothemoon',
    [customgame.game, welcometothemoon.players, welcometothemoon.cards, welcometothemoon.scribbles],
    {
      constructor() {
        this._activeStates = [];
        this._notifications = [
          'chooseCards',
          'addScribble',
          'addScribbles',
          'resolveSabotage',
          'accomplishMission',
          'giveCardToAstra',
          'replaceSoloCard',
          'newTurn',
          'endGameTriggered',
          'endGameMessage',
          'clearTurn',
          'refreshUI',
          'midMessage',
        ];

        // Fix mobile viewport (remove CSS zoom)
        this.default_viewport = 'width=740';
        this.cardStatuses = {};
      },
      notif_midMessage() {
        return this.wait(1000);
      },

      getSettingsSections() {
        return {
          layout: _('Layout'),
          gameFlow: _('Game Flow'),
          other: _('Other'),
        };
      },

      getSettingsConfig() {
        return {
          ////////////////////
          ///    LAYOUT    ///
          playerBoardsLayout: {
            default: 0,
            name: _('Player boards layout'),
            attribute: 'player-boards-layout',
            type: 'select',
            values: {
              0: _('Individual view (tabbed layout)'),
              1: _('Multiple view'),
            },
            section: 'layout',
          },

          singleColumn: {
            default: (isMobile) => (isMobile ? 1 : 0),
            name: _('Single column layout'),
            attribute: 'single-column',
            type: 'switch',
            section: 'layout',
          },

          mergedRow: {
            default: (isMobile) => (isMobile ? 1 : 0),
            name: _('Single row for cards'),
            attribute: 'merged',
            type: 'switch',
            section: 'layout',
          },

          ratio: {
            default: [20, 90],
            name: _('Size ratios'),
            type: 'multislider',
            sliderConfig: {
              step: 1,
              margin: 40,
              padding: 5,
              range: {
                min: [0],
                max: [100],
              },
            },
            section: 'layout',
          },

          fitToWidth: {
            default: 1,
            name: _('Fit to width'),
            type: 'switch',
            section: 'layout',
            attribute: 'fitwidth',
          },

          scoresheetZoom: {
            default: 100,
            name: _('Scoresheet scale'),
            type: 'slider',
            sliderConfig: {
              step: 1,
              padding: 5,
              range: {
                min: [0],
                max: [105],
              },
            },
            section: 'layout',
          },

          cardsOrder: {
            default: 1,
            name: _('Plans at bottom'),
            type: 'switch',
            section: 'layout',
            attribute: 'cardsOrder',
          },

          //////////////////////
          /// BOARD / PANELS ///

          //////////////////////
          ///// GAME FLOW //////
          confirmMode: { type: 'pref', prefId: 103, section: 'gameFlow' },
          confirmUndoableMode: {
            type: 'pref',
            prefId: 104,
            section: 'gameFlow',
          },
          restartButtons: {
            default: 1,
            name: _('Restart turn buttons'),
            type: 'select',
            attribute: 'undoButtons',
            values: {
              0: _('Only "Restart turn" button'),
              1: _('"Restart turn" and "Undo last step" buttons'),
              2: _('Only "Undo last step" button'),
            },
            section: 'gameFlow',
          },

          //////////////////////
          /////// OTHER ////////
        };
      },

      /**
       * Setup:
       *	This method set up the game user interface according to current game situation specified in parameters
       *	The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
       *
       * Params :
       *	- mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
       */
      setup(gamedatas) {
        debug('SETUP', gamedatas);
        // Create a new div for "subtitle"
        dojo.place("<div id='pagesubtitle'></div>", 'maintitlebar_content');

        // Prevent loading all the scoresheets
        for (let scenarioId = 1; scenarioId <= 8; scenarioId++) {
          this.dontPreloadImage(`scenario-${scenarioId}.jpg`);
        }

        // Central area
        $('game_play_area').insertAdjacentHTML(
          'beforeend',
          `
      <div id="astra-container"></div>
      <div id="welcometo-container">
        <div id="construction-cards-container">
          <div id="construction-cards-container-sticky">
            <div id="construction-cards-container-resizable">
              <div id="construction-cards-stack-0" class="construction-cards-stack"></div>
              <div id="construction-cards-stack-1" class="construction-cards-stack"></div>
              <div id="construction-cards-stack-2" class="construction-cards-stack"></div>
            </div>
          </div>
        </div>
      
        <div id="player-score-sheets-container">
          <div id="player-score-sheets-container-resizable">
            <div id="score-sheet-wrapper" class="score-sheet-wrapper">
              <div class='slideshow-left'>
                <div class="arrow"></div>
              </div>
              <div id="score-sheet-holder" class="score-sheet-holder"></div>
              <div class='slideshow-right'>
                <div class="arrow"></div>
              </div>
            </div>
          </div>
        </div>
      
        <div id="plan-cards-container">
          <div id="plan-cards-container-sticky">
            <div id="plan-cards-container-resizable">
            </div>
          </div>
        </div>
      </div>

      <svg style="display:none" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="map-marker-question" role="img" xmlns="http://www.w3.org/2000/svg">
        <symbol id="help-marker-svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="white" d="M256 8C119 8 8 119.08 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 422a46 46 0 1 1 46-46 46.05 46.05 0 0 1-46 46zm40-131.33V300a12 12 0 0 1-12 12h-56a12 12 0 0 1-12-12v-4c0-41.06 31.13-57.47 54.65-70.66 20.17-11.31 32.54-19 32.54-34 0-19.82-25.27-33-45.7-33-27.19 0-39.44 13.14-57.3 35.79a12 12 0 0 1-16.67 2.13L148.82 170a12 12 0 0 1-2.71-16.26C173.4 113 208.16 90 262.66 90c56.34 0 116.53 44 116.53 102 0 77-83.19 78.21-83.19 106.67z" opacity="1"></path><path class="fa-primary" fill="currentColor" d="M256 338a46 46 0 1 0 46 46 46 46 0 0 0-46-46zm6.66-248c-54.5 0-89.26 23-116.55 63.76a12 12 0 0 0 2.71 16.24l34.7 26.31a12 12 0 0 0 16.67-2.13c17.86-22.65 30.11-35.79 57.3-35.79 20.43 0 45.7 13.14 45.7 33 0 15-12.37 22.66-32.54 34C247.13 238.53 216 254.94 216 296v4a12 12 0 0 0 12 12h56a12 12 0 0 0 12-12v-1.33c0-28.46 83.19-29.67 83.19-106.67 0-58-60.19-102-116.53-102z"></path></g>
        </symbol>
      </svg>

      `
        );

        $('game_play_area').dataset.turn = gamedatas.turn;

        this.setupInfoPanel();
        this.setupPlayers();
        this.initConstructionCards();
        this.setupPlanCards();
        if (gamedatas.scenario) this.setupScenario(gamedatas.scenario);
        this.addScribbleClipPaths();
        this.setupScribbles();

        this.inherited(arguments);
        // Create a new div for "anytime" buttons
        dojo.place("<div id='anytimeActions' style='display:inline-block'></div>", $('customActions'), 'after');
      },

      setupScenario(scenario) {
        this.empty('score-sheet-holder');
        this.gamedatas.scenario = scenario;
        this.setupScoreSheets();
      },

      notif_endGameTriggered(args) {
        return this.wait(800);
      },

      notif_endGameMessage(args) {
        return this.wait(1200);
      },

      /**
       * update cached information from Notifications.php module
       */
      updateInfosFromNotif(infos) {
        debug('Updating some cached infos', infos);

        // Scoresheet data
        if (infos.scoresheet) {
          Object.entries(infos.scoresheet).forEach(([pId, data]) => {
            this.gamedatas.players[pId].scoresheet = data;
            this.updateComputedScoresheetData(pId);
          });
        }

        // Aastra data
        if (infos.astra) {
          this.gamedatas.astra = infos.astra;
          this.updateAstra();
        }
      },

      /////////////////////////////////////////////////////////////////
      //  _____       _             ___
      // | ____|_ __ | |_ ___ _ __ / / |    ___  __ ___   _____
      // |  _| | '_ \| __/ _ \ '__/ /| |   / _ \/ _` \ \ / / _ \
      // | |___| | | | ||  __/ | / / | |__|  __/ (_| |\ V /  __/
      // |_____|_| |_|\__\___|_|/_/  |_____\___|\__,_| \_/ \___|
      /////////////////////////////////////////////////////////////////

      clearPossible() {
        dojo.empty('pagesubtitle');

        let toRemove = [];
        toRemove.forEach((eltId) => {
          if ($(eltId)) $(eltId).remove();
        });

        // Remove joker marker
        document.querySelectorAll('.construction-card-holder').forEach((o) => delete o.dataset.joker);

        this.inherited(arguments);
      },

      onUpdateActionButtons(stateName, args) {
        //        this.addPrimaryActionButton('test', 'test', () => this.testNotif());
        this.inherited(arguments);
      },

      testNotif() {},

      onEnteringState(stateName, args) {
        debug('Entering state: ' + stateName, args);
        if (this.isFastMode() && ![].includes(stateName)) return;

        if (this._focusedPlayer != null && this._focusedPlayer != this.player_id && !this.isSpectator) {
          this.goToPlayerBoard(this.player_id);
        }

        if (args.args && args.args.descSuffix) {
          this.changePageTitle(args.args.descSuffix);
        }

        if (args.args && args.args.optionalAction) {
          let base = args.args.descSuffix ? args.args.descSuffix : '';
          this.changePageTitle(base + 'skippable');
        }

        if (this._activeStates.includes(stateName) && !this.isCurrentPlayerActive()) return;

        if (args.args && args.args.optionalAction && !args.args.automaticAction) {
          this.addSecondaryActionButton(
            'btnPassAction',
            _('Pass'),
            () => this.takeAction('actPassOptionalAction'),
            'restartAction'
          );
        }

        // Undo last steps
        if (args.args && args.args.previousSteps) {
          args.args.previousSteps.forEach((stepId) => {
            let logEntry = $('logs').querySelector(`.log.notif_newUndoableStep[data-step="${stepId}"]`);
            if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));

            logEntry = document.querySelector(`.chatwindowlogs_zone .log.notif_newUndoableStep[data-step="${stepId}"]`);
            if (logEntry) this.onClick(logEntry, () => this.undoToStep(stepId));
          });
        }

        // Restart turn button
        if (args.args && args.args.previousEngineChoices && args.args.previousEngineChoices >= 1 && !args.args.automaticAction) {
          if (args.args && args.args.previousSteps) {
            let lastStep = Math.max(...args.args.previousSteps);
            if (lastStep > 0)
              this.addDangerActionButton(
                'btnUndoLastStep',
                _('Undo last step'),
                () => this.undoToStep(lastStep),
                'restartAction'
              );
          }

          // Restart whole turn
          this.addDangerActionButton(
            'btnRestartTurn',
            _('Restart turn'),
            () => {
              this.stopActionTimer();
              this.takeAction('actRestart');
            },
            'restartAction'
          );
        }

        // Highlight stacks
        if (args.args && args.args.selectedCombination) {
          this.highlightCombination(args.args.selectedCombination);
        }

        if (this.isCurrentPlayerActive() && args.args) {
          // Anytime buttons
          // if (args.args.anytimeActions) {
          //   args.args.anytimeActions.forEach((action, i) => {
          //     let msg = action.desc;
          //     msg = msg.log ? this.fsr(msg.log, msg.args) : _(msg);
          //     msg = this.formatString(msg);
          //     // if (action.source && action.source != '') {
          //     //   msg += ' (' + _(action.source) + ')';
          //     // }
          //     this.addPrimaryActionButton(
          //       'btnAnytimeAction' + i,
          //       msg,
          //       () => this.askConfirmation(action.irreversibleAction, () => this.takeAction('actAnytimeAction', { id: i }, false)),
          //       'anytimeActions'
          //     );
          //   });
          // }
        }

        // Call appropriate method
        var methodName = 'onEnteringState' + stateName.charAt(0).toUpperCase() + stateName.slice(1);
        if (this[methodName] !== undefined) this[methodName](args.args);
      },

      /////////////////////////////
      //  _   _           _
      // | | | |_ __   __| | ___
      // | | | | '_ \ / _` |/ _ \
      // | |_| | | | | (_| | (_) |
      //  \___/|_| |_|\__,_|\___/
      /////////////////////////////

      onAddingNewUndoableStepToLog(notif) {
        if (!$(`log_${notif.logId}`)) return;
        let stepId = notif.msg.args.stepId;
        $(`log_${notif.logId}`).dataset.step = stepId;
        if ($(`dockedlog_${notif.mobileLogId}`)) $(`dockedlog_${notif.mobileLogId}`).dataset.step = stepId;

        if (this.gamedatas && this.gamedatas.gamestate) {
          let state = this.gamedatas.gamestate;
          if (state.private_state) state = state.private_state;

          if (state.args && state.args.previousSteps && state.args.previousSteps.includes(parseInt(stepId))) {
            this.onClick($(`log_${notif.logId}`), () => this.undoToStep(stepId));

            if ($(`dockedlog_${notif.mobileLogId}`))
              this.onClick($(`dockedlog_${notif.mobileLogId}`), () => this.undoToStep(stepId));
          }
        }
      },

      undoToStep(stepId) {
        this.stopActionTimer();
        this.checkAction('actRestart');
        this.takeAction('actUndoToStep', { stepId }, false);
      },

      notif_clearTurn(args) {
        this.cancelLogs(args.notifIds);
      },

      notif_mediumMessage(args) {
        return this.wait(1200);
      },

      notif_refreshUI(args) {
        debug('Notif: refresh UI', args);
        this.clearPossible();
        ['scribbles', 'players', 'constructionCards'].forEach((value) => {
          this.gamedatas[value] = args.datas[value];
        });
        this.setupScribbles();
        this.setupConstructionCards();

        // this.forEachPlayer((player) => {
        //   this._scoreCounters[player.id].toValue(player.newScore);
        //   this._playerCounters[player.id]['income'].toValue(player.income);
        // });
      },

      ////////////////////////////////////////
      //  _____             _
      // | ____|_ __   __ _(_)_ __   ___
      // |  _| | '_ \ / _` | | '_ \ / _ \
      // | |___| | | | (_| | | | | |  __/
      // |_____|_| |_|\__, |_|_| |_|\___|
      //              |___/
      ////////////////////////////////////////
      onEnteringStateSetupEngine(args) {
        if (!this.isCurrentPlayerActive() && !this.isSpectator) {
          this.addSecondaryActionButton('btnCancel', _('Cancel'), () => this.takeAction('actCancel', {}, false));
        }
      },

      onUpdateActivitySetupEngine(args, status) {
        if (status) {
          if ($('btnCancel')) $('btnCancel').remove();
        } else {
          this.clearPossible();
          this.addSecondaryActionButton('btnCancel', _('Cancel'), () => this.takeAction('actCancel', {}, false));
        }
      },

      addActionChoiceBtn(choice, disabled = false) {
        if ($('btnChoice' + choice.id)) return;

        let desc = this.translate(choice.description);
        desc = this.formatString(desc);

        // Add source if any
        let source = _(choice.source ? choice.source : '');
        if (source != '') {
          desc += ` (${source})`;
        }

        this.addSecondaryActionButton(
          'btnChoice' + choice.id,
          desc,
          disabled
            ? () => {}
            : () => {
                this.askConfirmation(choice.irreversibleAction, () =>
                  this.takeAction('actChooseAction', {
                    choiceId: choice.id,
                  })
                );
              }
        );
        if (disabled) {
          $(`btnChoice${choice.id}`).classList.add('disabled');
        }
        if (choice.description.args && choice.description.args.bonus_pentagon) {
          $(`btnChoice${choice.id}`).classList.add('withbonus');
        }
      },

      onEnteringStateResolveChoice(args) {
        if (args.noNode) return;

        Object.values(args.choices).forEach((choice) => this.addActionChoiceBtn(choice, false));
        Object.values(args.allChoices).forEach((choice) => this.addActionChoiceBtn(choice, true));
      },

      onEnteringStateImpossibleAction(args) {
        this.addActionChoiceBtn(
          {
            choiceId: 0,
            description: args.desc,
          },
          true
        );
      },

      addConfirmTurn(args, action) {
        this.addPrimaryActionButton('btnConfirmTurn', _('Confirm'), () => {
          this.stopActionTimer();
          this.takeAction(action);
        });

        const OPTION_CONFIRM = 103;
        let n = args.previousEngineChoices;
        let timer = Math.min(10 + 2 * n, 20);
        this.startActionTimer('btnConfirmTurn', timer, this.prefs[OPTION_CONFIRM].value);
      },

      onEnteringStateConfirmTurn(args) {
        this.addConfirmTurn(args, 'actConfirmTurn');
      },

      askConfirmation(warning, callback) {
        if (warning === false || this.prefs[104].value == 0) {
          callback();
        } else {
          let msg =
            warning === true
              ? _(
                  "If you take this action, you won't be able to undo past this step because you will either draw card(s) from the deck or the discard, or someone else is going to make a choice"
                )
              : warning;
          this.confirmationDialog(msg, () => {
            callback();
          });
        }
      },

      // Generic call for Atomic Action that encode args as a JSON to be decoded by backend
      takeAtomicAction(action, args = [], warning = false) {
        if (!this.checkAction(action)) return false;

        this.askConfirmation(warning, () =>
          this.takeAction('actTakeAtomicAction', { actionName: action, actionArgs: JSON.stringify(args) }, false)
        );
      },

      ///////////////////////////////////////
      //  _____  __  __           _
      // | ____|/ _|/ _| ___  ___| |_ ___
      // |  _| | |_| |_ / _ \/ __| __/ __|
      // | |___|  _|  _|  __/ (__| |_\__ \
      // |_____|_| |_|  \___|\___|\__|___/
      ///////////////////////////////////////

      onEnteringStateWriteNumber(args) {
        let numbersBySlots = {};

        Object.entries(args.numbers).forEach(([number, slots]) => {
          slots.forEach((slotId) => {
            // Init click listener
            if (!numbersBySlots[slotId]) {
              numbersBySlots[slotId] = [];
              this.onClick(`slot-${this.player_id}-${slotId}`, () => {
                const numbers = numbersBySlots[slotId];

                //  Only one choice possible => take action
                if (numbers.length == 1) {
                  // Only one number, we can call the callback directly
                  this.takeAtomicAction('actWriteNumber', [slotId, numbers[0]]);
                }
                // Several numbers possible => modal
                else {
                  // Open a modal to ask the number to write
                  let dial = new customgame.modal('chooseNumber', {
                    class: 'welcometothemoon_popin',
                    closeIcon: 'fa-times',
                    title: _('Choose the number you want to write'),
                    openAnimation: true,
                    openAnimationTarget: `slot-${this.player_id}-${slotId}`,
                  });

                  numbers.forEach((number) => {
                    $('popin_chooseNumber_contents').insertAdjacentHTML(
                      'beforeend',
                      `<div id="number-choice-${number}" class='number-choice' data-number='${number}'></div>`
                    );

                    this.onClick(`number-choice-${number}`, () => {
                      dial.destroy();
                      this.takeAtomicAction('actWriteNumber', [slotId, number]);
                    });
                  });
                  dial.show();
                }
              });
            }

            // Add that number as a possibility for that slot
            numbersBySlots[slotId].push(number);
          });
        });
      },

      /**
       * launchActionOnSlotClick : make slots selectable and launch action when clicking on one
       * @param {*} slots
       * @param {*} action
       * @param callback
       */
      launchActionOnSlotClick(slots, action, callback = null) {
        slots.forEach((slotId) => {
          this.onClick(`slot-${this.player_id}-${slotId}`, () => {
            if (action !== null) {
              this.takeAtomicAction(action, [slotId]);
            } else {
              callback(slotId);
            }
          });
        });
      },

      // GENERIC PICK ONE SLOT
      onEnteringStatePickOneSlot(args) {
        this.launchActionOnSlotClick(args.slots, args.action);
      },

      onEnteringStateStirWaterTanks(args) {
        this.onClick(`slot-${this.player_id}-${args.slot}`, () => {
          this.takeAtomicAction('actStirWaterTanks');
        });
        this.addPrimaryActionButton('btnStir', _('Stir'), () => {
          this.takeAtomicAction('actStirWaterTanks');
        });
      },

      ////////////////////////////////////////////////////////////
      // _____                          _   _   _
      // |  ___|__  _ __ _ __ ___   __ _| |_| |_(_)_ __   __ _
      // | |_ / _ \| '__| '_ ` _ \ / _` | __| __| | '_ \ / _` |
      // |  _| (_) | |  | | | | | | (_| | |_| |_| | | | | (_| |
      // |_|  \___/|_|  |_| |_| |_|\__,_|\__|\__|_|_| |_|\__, |
      //                                                 |___/
      ////////////////////////////////////////////////////////////

      /**
       * Replace some expressions by corresponding html formating
       */
      formatIcon(name, n = null, lowerCase = true) {
        let type = lowerCase ? name.toLowerCase() : name;

        // SVG ICONS
        const glyphs = {
          arrow: 1,
          rocket: 1,
          sabotage: 1,
          x: 1,
          reshuffle: 1,
        };
        if (glyphs[type]) {
          let icon = `<i class='svgicon-${type}'>`;
          let nGlyphs = glyphs[type];
          if (nGlyphs > 1) {
            for (let i = 1; i <= nGlyphs; i++) {
              icon += `<span class="path${i}"></span>`;
            }
          }
          icon += '</i>';
          return icon;
        }

        const NO_TEXT_ICONS = [];
        let noText = NO_TEXT_ICONS.includes(name);
        let text = n == null ? '' : `<span>${n}</span>`;
        return `${noText ? text : ''}<div class="icon-container icon-container-${type}">
            <div class="welcometothemoon-icon icon-${type}">${noText ? '' : text}</div>
          </div>`;
      },

      formatString(str) {
        const ICONS = ['ARROW', 'ROCKET', 'SABOTAGE', 'X', 'SYSTEM-ERROR', 'RESHUFFLE'];

        ICONS.forEach((name) => {
          const regex = new RegExp('<' + name + ':([^>]+)>', 'g');
          str = str.replaceAll(regex, this.formatIcon(name, '<span>$1</span>'));
          str = str.replaceAll(new RegExp('<' + name + '>', 'g'), this.formatIcon(name));
        });
        str = str.replace(/\*\*([^\*]+)\*\*/g, '<b>$1</b>');

        return str;
      },

      /**
       * Format log strings
       *  @Override
       */
      format_string_recursive(log, args) {
        try {
          if (log && args && !args.processed) {
            args.processed = true;

            log = this.formatString(_(log));

            if (args.action) {
              args.action_icon = this.formatIcon(args.action);

              // Specific formatting of chooseCombination
              if (args.number) {
                args.action = '';
                args.number = `<span class='card-number'>${args.number}</span>`;
              }
            } else if (args.number) {
              args.number = `<span class='log-number'>${args.number}</span>`;
            }

            if (args.stack) {
              args.stack = `<span class='log-stack stack-${args.stack}'>${args.stack}</span>`;
            }
          }
        } catch (e) {
          console.error(log, args, 'Exception thrown', e.stack);
        }

        return this.inherited(arguments);
      },

      //////////////////////////////////////////////////////
      //  ___        __         ____                  _
      // |_ _|_ __  / _| ___   |  _ \ __ _ _ __   ___| |
      //  | || '_ \| |_ / _ \  | |_) / _` | '_ \ / _ \ |
      //  | || | | |  _| (_) | |  __/ (_| | | | |  __/ |
      // |___|_| |_|_|  \___/  |_|   \__,_|_| |_|\___|_|
      //////////////////////////////////////////////////////

      setupInfoPanel() {
        dojo.place(this.tplInfoPanel(), 'player_boards', 'first');
        let chk = $('help-mode-chk');
        dojo.connect(chk, 'onchange', () => this.toggleHelpMode(chk.checked));
        this.addTooltip('help-mode-switch', '', _('Toggle help/safe mode.'));

        this._settingsModal = new customgame.modal('showSettings', {
          class: 'welcometothemoon_popin',
          closeIcon: 'fa-times',
          title: _('Settings'),
          closeAction: 'hide',
          verticalAlign: 'flex-start',
          contentsTpl: `<div id='welcometothemoon-settings'>
             <div id='welcometothemoon-settings-header'></div>
             <div id="settings-controls-container"></div>
           </div>`,
        });
      },

      tplInfoPanel() {
        return `
  <div class='player-board' id="player_board_config">
    <div id="player_config" class="player_board_content">
      <div class="player_config_row" id="scenario-name"></div>
      
      <div class="player_config_row">
        <div id="cards-count">
          <div id="cards-count-status"></div>
        </div>

        <div id="show-scores">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <g class="fa-group">
              <path class="fa-secondary" fill="currentColor" d="M0 192v272a48 48 0 0 0 48 48h352a48 48 0 0 0 48-48V192zm324.13 141.91a11.92 11.92 0 0 1-3.53 6.89L281 379.4l9.4 54.6a12 12 0 0 1-17.4 12.6l-49-25.8-48.9 25.8a12 12 0 0 1-17.4-12.6l9.4-54.6-39.6-38.6a12 12 0 0 1 6.6-20.5l54.7-8 24.5-49.6a12 12 0 0 1 21.5 0l24.5 49.6 54.7 8a12 12 0 0 1 10.13 13.61zM304 128h32a16 16 0 0 0 16-16V16a16 16 0 0 0-16-16h-32a16 16 0 0 0-16 16v96a16 16 0 0 0 16 16zm-192 0h32a16 16 0 0 0 16-16V16a16 16 0 0 0-16-16h-32a16 16 0 0 0-16 16v96a16 16 0 0 0 16 16z" opacity="0.4"></path>
              <path class="fa-primary" fill="currentColor" d="M314 320.3l-54.7-8-24.5-49.6a12 12 0 0 0-21.5 0l-24.5 49.6-54.7 8a12 12 0 0 0-6.6 20.5l39.6 38.6-9.4 54.6a12 12 0 0 0 17.4 12.6l48.9-25.8 49 25.8a12 12 0 0 0 17.4-12.6l-9.4-54.6 39.6-38.6a12 12 0 0 0-6.6-20.5zM400 64h-48v48a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16V64H160v48a16 16 0 0 1-16 16h-32a16 16 0 0 1-16-16V64H48a48 48 0 0 0-48 48v80h448v-80a48 48 0 0 0-48-48z"></path>
            </g>
          </svg>
        </div>

        <div id="help-mode-switch">
          <input type="checkbox" class="checkbox" id="help-mode-chk" />
          <label class="label" for="help-mode-chk">
            <div class="ball"></div>
          </label><svg aria-hidden="true" focusable="false" data-prefix="fad" data-icon="question-circle" class="svg-inline--fa fa-question-circle fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><g class="fa-group"><path class="fa-secondary" fill="currentColor" d="M256 8C119 8 8 119.08 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 422a46 46 0 1 1 46-46 46.05 46.05 0 0 1-46 46zm40-131.33V300a12 12 0 0 1-12 12h-56a12 12 0 0 1-12-12v-4c0-41.06 31.13-57.47 54.65-70.66 20.17-11.31 32.54-19 32.54-34 0-19.82-25.27-33-45.7-33-27.19 0-39.44 13.14-57.3 35.79a12 12 0 0 1-16.67 2.13L148.82 170a12 12 0 0 1-2.71-16.26C173.4 113 208.16 90 262.66 90c56.34 0 116.53 44 116.53 102 0 77-83.19 78.21-83.19 106.67z" opacity="0.4"></path><path class="fa-primary" fill="currentColor" d="M256 338a46 46 0 1 0 46 46 46 46 0 0 0-46-46zm6.66-248c-54.5 0-89.26 23-116.55 63.76a12 12 0 0 0 2.71 16.24l34.7 26.31a12 12 0 0 0 16.67-2.13c17.86-22.65 30.11-35.79 57.3-35.79 20.43 0 45.7 13.14 45.7 33 0 15-12.37 22.66-32.54 34C247.13 238.53 216 254.94 216 296v4a12 12 0 0 0 12 12h56a12 12 0 0 0 12-12v-1.33c0-28.46 83.19-29.67 83.19-106.67 0-58-60.19-102-116.53-102z"></path></g></svg>
        </div>

        <div id="show-settings">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">
            <g>
              <path class="fa-secondary" fill="currentColor" d="M638.41 387a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4L602 335a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6 12.36 12.36 0 0 0-15.1 5.4l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 44.9c-29.6-38.5 14.3-82.4 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79zm136.8-343.8a12.34 12.34 0 0 0-12.2-10.3h-16.5a86.33 86.33 0 0 0-15.9-27.4l8.2-14.3a12.42 12.42 0 0 0-2.8-15.7 110.5 110.5 0 0 0-32.1-18.6A12.36 12.36 0 0 0 552 7.19l-8.2 14.3a88.86 88.86 0 0 0-31.7 0l-8.2-14.3a12.36 12.36 0 0 0-15.1-5.4 111.83 111.83 0 0 0-32.1 18.6 12.3 12.3 0 0 0-2.8 15.7l8.2 14.3a86.33 86.33 0 0 0-15.9 27.4h-16.5a12.43 12.43 0 0 0-12.2 10.4 112.66 112.66 0 0 0 0 37.1 12.34 12.34 0 0 0 12.2 10.3h16.5a86.33 86.33 0 0 0 15.9 27.4l-8.2 14.3a12.42 12.42 0 0 0 2.8 15.7 110.5 110.5 0 0 0 32.1 18.6 12.36 12.36 0 0 0 15.1-5.4l8.2-14.3a88.86 88.86 0 0 0 31.7 0l8.2 14.3a12.36 12.36 0 0 0 15.1 5.4 111.83 111.83 0 0 0 32.1-18.6 12.3 12.3 0 0 0 2.8-15.7l-8.2-14.3a86.33 86.33 0 0 0 15.9-27.4h16.5a12.43 12.43 0 0 0 12.2-10.4 112.66 112.66 0 0 0 .01-37.1zm-136.8 45c-29.6-38.5 14.3-82.5 52.8-52.8 29.59 38.49-14.3 82.39-52.8 52.79z" opacity="0.4"></path>
              <path class="fa-primary" fill="currentColor" d="M420 303.79L386.31 287a173.78 173.78 0 0 0 0-63.5l33.7-16.8c10.1-5.9 14-18.2 10-29.1-8.9-24.2-25.9-46.4-42.1-65.8a23.93 23.93 0 0 0-30.3-5.3l-29.1 16.8a173.66 173.66 0 0 0-54.9-31.7V58a24 24 0 0 0-20-23.6 228.06 228.06 0 0 0-76 .1A23.82 23.82 0 0 0 158 58v33.7a171.78 171.78 0 0 0-54.9 31.7L74 106.59a23.91 23.91 0 0 0-30.3 5.3c-16.2 19.4-33.3 41.6-42.2 65.8a23.84 23.84 0 0 0 10.5 29l33.3 16.9a173.24 173.24 0 0 0 0 63.4L12 303.79a24.13 24.13 0 0 0-10.5 29.1c8.9 24.1 26 46.3 42.2 65.7a23.93 23.93 0 0 0 30.3 5.3l29.1-16.7a173.66 173.66 0 0 0 54.9 31.7v33.6a24 24 0 0 0 20 23.6 224.88 224.88 0 0 0 75.9 0 23.93 23.93 0 0 0 19.7-23.6v-33.6a171.78 171.78 0 0 0 54.9-31.7l29.1 16.8a23.91 23.91 0 0 0 30.3-5.3c16.2-19.4 33.7-41.6 42.6-65.8a24 24 0 0 0-10.5-29.1zm-151.3 4.3c-77 59.2-164.9-28.7-105.7-105.7 77-59.2 164.91 28.7 105.71 105.7z"></path>
            </g>
          </svg>
        </div>
      </div>
    </div>
  </div>
   `;
      },

      onChangeSingleColumnSetting(val) {
        this.updateLayout();
      },
      onChangeMergedRowSetting(val) {
        this.updateLayout();
      },
      onChangeRatioSetting(val) {
        this.updateLayout();
      },
      onChangeScoresheetZoomSetting(val) {
        this.updateLayout();
      },
      onChangeFitToWidthSetting(val) {
        this.updateLayout();
      },
      onChangePlayerBoardsLayoutSetting(val) {
        this.updateLayout();
      },

      onLoadingComplete() {
        this.updateLayout();
        this.inherited(arguments);
      },

      onScreenWidthChange() {
        if (this.settings) this.updateLayout();
      },

      updateLayout() {
        if (!this.settings || !this.settings.ratio) return;

        if (this.settings.singleColumn == 0) {
          this.resizeHorizontal();
        } else {
          this.resizeVertical();
        }
      },

      resizeHorizontal() {
        const box = $('welcometo-container').getBoundingClientRect();
        const ratio = this.settings.ratio;
        const firstHandle = this._isStandard ? ratio[0] : 0.6 * ratio[0];

        const sheetWidth = 1128;
        const sheetZoom = this.settings.fitToWidth == 1 ? 1 : this.settings.scoresheetZoom / 100;
        const sheetRatio = (ratio[1] - firstHandle) / 100;
        const newSheetWidth = sheetZoom * sheetRatio * box['width'];
        const sheetScale = newSheetWidth / sheetWidth;
        const tabbed = this.settings.playerBoardsLayout == 0;
        $('player-score-sheets-container-resizable').style.transform = `scale(${sheetScale})`;
        $('player-score-sheets-container').style.width = `${newSheetWidth}px`;
        $('player-score-sheets-container').style.height = `${(tabbed ? 1 : this.getPlayers().length) * newSheetWidth}px`;

        const cardsWidth = this._isStandard ? 420 : 208;
        const cardsHeight = 963;
        const cardsRatio = firstHandle / 100;
        const newCardsWidth = cardsRatio * box['width'] - 30;
        const cardsScale = newCardsWidth / cardsWidth;
        $('construction-cards-container-resizable').style.transform = `scale(${cardsScale})`;
        $('construction-cards-container-resizable').style.width = `${cardsWidth}px`;
        $('construction-cards-container-sticky').style.height = `${cardsHeight * cardsScale}px`;
        $('construction-cards-container-sticky').style.width = `${newCardsWidth}px`;
        $('construction-cards-container').style.width = `${newCardsWidth}px`;

        const plansWidth = 245;
        const plansHeight = 964;
        const plansRatio = 1 - sheetRatio - cardsRatio;
        const newPlansWidth = plansRatio * box['width'] - 10;
        const plansScale = newPlansWidth / plansWidth;
        $('plan-cards-container-resizable').style.transform = `scale(${plansScale})`;
        $('plan-cards-container-resizable').style.width = `${plansWidth}px`;
        $('plan-cards-container-sticky').style.height = `${plansHeight * plansScale}px`;
        $('plan-cards-container').style.width = `${newPlansWidth - 20}px`;
      },

      resizeVertical() {
        const box = $('welcometo-container').getBoundingClientRect();

        const sheetWidth = 1128;
        const sheetScale = box['width'] / sheetWidth;
        const newSheetWidth = box['width'];
        const tabbed = this.settings.playerBoardsLayout == 0;
        $('player-score-sheets-container-resizable').style.transform = `scale(${sheetScale})`;
        $('player-score-sheets-container').style.width = `${newSheetWidth}px`;
        $('player-score-sheets-container').style.height = `${(tabbed ? 1 : this.getPlayers().length) * newSheetWidth}px`;

        const cardsWidth = this._isStandard ? 1289 : 900;
        const plansWidth = 654;
        const cardsHeight = 312;

        let cardsScale = 1,
          plansScale = 1;
        if (this.settings.mergedRow == 1) {
          const totalWidth = cardsWidth + plansWidth;
          cardsScale = (box['width'] - 40) / totalWidth;
          plansScale = cardsScale;
        } else {
          cardsScale = (box['width'] - 20) / cardsWidth;
          plansScale = (0.7 * (box['width'] - 20)) / plansWidth;
        }

        const newCardsWidth = cardsWidth * cardsScale;
        const newCardsHeight = cardsHeight * cardsScale;
        $('construction-cards-container-resizable').style.transform = `scale(${cardsScale})`;
        $('construction-cards-container-resizable').style.width = `${cardsWidth}px`;
        $('construction-cards-container-sticky').style.height = `${newCardsHeight}px`;
        $('construction-cards-container-sticky').style.width = `${newCardsWidth}px`;
        $('construction-cards-container').style.width = `${newCardsWidth}px`;

        const newPlansWidth = plansWidth * plansScale;
        const newPlansHeight = cardsHeight * plansScale;
        $('plan-cards-container-resizable').style.transform = `scale(${plansScale})`;
        $('plan-cards-container-resizable').style.width = `${plansWidth}px`;
        $('plan-cards-container-sticky').style.height = `${newPlansHeight}px`;
        $('plan-cards-container-sticky').style.width = `${newPlansWidth}px`;
        $('plan-cards-container').style.width = this.settings.mergedRow == 0 ? `${box['width'] - 20}px` : `${newPlansWidth}px`;
      },

      ///////////////////////////////////////////////////////////
      //  ____                     _                         _
      // / ___|  ___ ___  _ __ ___| |__   ___   __ _ _ __ __| |
      // \___ \ / __/ _ \| '__/ _ \ '_ \ / _ \ / _` | '__/ _` |
      //  ___) | (_| (_) | | |  __/ |_) | (_) | (_| | | | (_| |
      // |____/ \___\___/|_|  \___|_.__/ \___/ \__,_|_|  \__,_|
      ///////////////////////////////////////////////////////////

      setupScoreBoard() {
        this._scoreboardModal = new customgame.modal('showScoreboard', {
          class: 'welcometothemoon_popin',
          closeIcon: 'fa-times',
          closeAction: 'hide',
          verticalAlign: 'flex-start',
          contentsTpl: ``,
          scale: 0.95,
          breakpoint: 1400,
        });

        $('open-scoreboard').addEventListener('click', () => this._scoreboardModal.show());
      },
    }
  );
});
