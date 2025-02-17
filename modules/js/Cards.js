define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  var isDebug = window.location.host == 'studio.boardgamearena.com' || window.location.hash.indexOf('debug') > -1;
  var debug = isDebug ? console.info.bind(window.console) : function () {};

  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  return declare('welcometothemoon.cards', null, {
    ////////////////////////////////////////////////////////////////////
    //   ____                _                   _   _
    //  / ___|___  _ __  ___| |_ _ __ _   _  ___| |_(_) ___  _ __
    // | |   / _ \| '_ \/ __| __| '__| | | |/ __| __| |/ _ \| '_ \
    // | |__| (_) | | | \__ \ |_| |  | |_| | (__| |_| | (_) | | | |
    //  \____\___/|_| |_|___/\__|_|   \__,_|\___|\__|_|\___/|_| |_|
    ////////////////////////////////////////////////////////////////////

    /*****************************************
     ********* Constructions cards ***********
     *****************************************
     * create the layout for the cards
     * handle the clicks event to ask user to select card
     * animate cards at the beggining of a new turn
     */

    initConstructionCards() {
      this._isStandard = this.gamedatas.standard; // Standard = playing with three stack
      debug('Seting up the construction cards', this._isStandard ? 'Standard mode' : 'Only one card by stack');

      // Adjust stack size for flip animation
      if (this._isStandard) $('construction-cards-container-resizable').classList.add('standard');

      this.setupConstructionCards();
    },

    setupConstructionCards() {
      this.gamedatas.constructionCards.forEach((stack, i) => {
        stack.forEach((card, j) => {
          if ($(`construction-card-${card.id}`)) return;

          $(`construction-cards-stack-${i}`).insertAdjacentHTML('beforeend', this.tplConstructionCard(card));
          let oCard = $(`construction-card-${card.id}`);
          oCard.style.zIndex = 100;

          // Flip first card
          if (j == 0 && this._isStandard) {
            this.flipCard(oCard, 1);
          }
        });
      });
    },

    tplConstructionCard(card) {
      return `<div id="construction-card-${card.id}" class="construction-card-holder" data-action="${card.action}" data-number="${card.number}">
  <div class="construction-card-back">
    <div class="action"></div>
  </div>
  <div class="construction-card-front">
    <div class="top-right-corner"></div>
    <div class="bottom-left-corner"></div>
    <div class="number"></div>
  </div>
</div>`;
    },

    // Hightlight selected stack(s)
    highlightCombination(combination, overwriteJoker = true) {
      document.querySelectorAll('.construction-cards-stack').forEach((o) => o.classList.add('unselectable'));
      combination.stacks.forEach((stackId, i) => {
        $(`construction-cards-stack-${stackId}`).classList.add('selected');

        // Solo mode => flip the second stack
        if (i > 0) {
          $(`construction-cards-stack-${stackId}`).classList.add('flipped');
        }
      });

      if (!overwriteJoker) return;
      let stackAction = combination.stacks[this._isStandard ? 0 : 1];
      let cardAction = $(`construction-cards-stack-${stackAction}`).querySelector('.construction-card-holder');
      if (cardAction.dataset.action != combination.action) {
        cardAction.dataset.joker = combination.action;
      }
    },

    notif_chooseCards(args) {
      debug('Notif: choose cards', args);
      return this.wait(800);
    },

    // Flip card and add tooltip
    async flipCard(card, turn) {
      // Flip animation
      card.classList.add('flipped');
      await this.wait(1000);
      card.style.zIndex = turn;

      // Add tooltip
      let action = card.dataset.action;
      this.addCustomTooltip(card.id, 'TODO: tooltip ' + action);
    },

    ////////////////////////////////////
    ////////  Selecting a stack ////////
    ////////////////////////////////////

    onEnteringStateChooseCards(args) {
      args.useJoker = args.useJoker || false;

      // Clear previous choice
      document.querySelectorAll('.construction-cards-stack').forEach((o) => {
        o.classList.remove('selected', 'flipped');
        o.classList.add('unselectable');
      });

      // Make them selectable
      let selectableStacks = {};
      let combinations = args.useJoker || false ? args.jokerCombinations : args.combinations;
      combinations.forEach((combination) => {
        let stackId = combination.stacks[0];
        if (selectableStacks[stackId]) {
          selectableStacks[stackId].push(combination.action);
          return;
        }
        selectableStacks[stackId] = [combination.action];

        let o = $(`construction-cards-stack-${stackId}`);
        o.classList.remove('unselectable');
        this.onClick(o, () => {
          // Standard mode
          if (this._isStandard) {
            console.log(selectableStacks[stackId]);
            // Basic case => combination selection is over
            if (selectableStacks[stackId].length == 1) {
              this.takeAtomicAction('actChooseCards', [combination]);
            }
            // Joker card or joker bonus => need to choose the action
            else {
              this.clientState('chooseCardsJokerAction', _('Which action do you want to use the joker for?'), {
                combinations,
                combination,
                useJoker: args.useJoker,
              });
            }
          }
          // Solo mode => we need to select another stack
          else {
            this.clientState('chooseCardsSecondStack', _('You must choose another stack for the action'), {
              combinations,
              stackId,
            });
          }
        });
      });

      if (combinations.length == 0) {
        this.addDangerActionButton('btnSystemError', _('System Error'), () => this.takeAtomicAction('actSystemError', []));
      }

      if (args.jokerCombinations) {
        this.addSecondaryActionButton('btnUseJoker', _('Use joker'), () => {
          args.useJoker = !args.useJoker;
          this.clearPossible();
          this.onEnteringStateChooseCards(args);
        });

        $('btnUseJoker').classList.toggle('selected', args.useJoker);
      }
    },

    ///////////////////////////////////////////////////
    ////////  Joker action : select the action ////////
    ///////////////////////////////////////////////////

    onEnteringStateChooseCardsJokerAction(args) {
      this.highlightCombination(args.combination, false);
      args.combinations.forEach((combination, i) => {
        if (JSON.stringify(combination.stacks) != JSON.stringify(args.combination.stacks)) return;

        this.addPrimaryActionButton(`btnAction${i}`, this.formatIcon(combination.action), () =>
          this.takeAtomicAction('actChooseCards', [combination, args.useJoker])
        );
        $(`btnAction${i}`).classList.add('btnAction');
      });

      this.addCancelStateBtn(_('Reset cards selection'));
    },

    ////////////////////////////////////////////////////////
    ////////  Non-standard mode : select two stacks ////////
    ////////////////////////////////////////////////////////

    onEnteringStateChooseCardsSecondStack(args) {
      this.addCancelStateBtn(_('Unselect'));

      document.querySelectorAll('.construction-cards-stack').forEach((o) => {
        if (o.id == `construction-cards-stack-${args.stackId}`) {
          o.classList.add('selected');
          this.onClick(o, () => this.clearClientState());
        } else {
          o.classList.add('flipped', 'unslectable');
        }
      });

      let selectableStacks = [];
      args.combinations.forEach((combination) => {
        if (combination.stacks[0] != args.stackId) return;
        let stackId = combination.stacks[1];
        if (selectableStacks.includes(stackId)) return;
        selectableStacks.push(stackId);

        let o = $(`construction-cards-stack-${stackId}`);
        o.classList.remove('unselectable');
        this.onClick(o, () => this.takeAtomicAction('actChooseCards', [combination]));
      });
    },

    //////////////////////////////////////
    /////////////  New turn  /////////////
    //////////////////////////////////////
    async notif_newTurn(args) {
      debug('Notif: new turn', args);
      $('game_play_area').dataset.turn = args.turn;

      if (!this._isStandard) {
        await Promise.all(
          args.cards.map(async (card) => {
            card.stackId = card.location.split('-')[1];
            let cardsInStack = $(`construction-cards-stack-${card.stackId}`).querySelector(
              '.construction-card-holder:last-of-type'
            );
            let oldCard = cardsInStack;

            // Compute x position to make it slide out the left border of window
            await this.slideToLeftAndDestroy(oldCard);

            // Remove flipped class if needed
            let stack = $(`construction-cards-stack-${card.stackId}`);
            stack.classList.add('notransition');
            stack.classList.remove('flipped');
            stack.offsetHeight;
            stack.classList.remove('notransition');

            return true;
          })
        );
      }

      await Promise.all(
        args.cards.map((card) => {
          card.stackId = card.location.split('-')[1];
          let cardsInStack = $(`construction-cards-stack-${card.stackId}`).querySelector(
            '.construction-card-holder:last-of-type'
          );
          let oldCard = cardsInStack;
          let stack = $(`construction-cards-stack-${card.stackId}`);

          //// STANDARD MODE : FLIP CARD ////
          if (this._isStandard) {
            // Remove if existing
            let o = $(`construction-card-${card.id}`);
            if (o) o.remove();

            // New card
            stack.insertAdjacentHTML('beforeend', this.tplConstructionCard(card));
            $(`construction-card-${card.id}`).style.zIndex = 100 - args.turn;

            // Flip card animation
            if (oldCard) return this.flipCard(oldCard, args.turn);

            // First card in this stack ? => slide from left
            if (!oldCard) return this.slideFromLeft(newCard);
          }
          //// NON STANDARD MODE : SLIDE LEFT ////
          else {
            // Create a new card and put it to the left (hidden)
            stack.insertAdjacentHTML('beforeend', this.tplConstructionCard(card));
            let newCard = $(`construction-card-${card.id}`);
            newCard.style.zIndex = 100 - args.turn;
            return this.slideFromLeft(newCard);
          }
        })
      );
    },

    slideFromLeft(elem) {
      let stack = elem.parentNode;
      let x = elem.offsetWidth + stack.offsetWidth + stack.offsetLeft + 30;
      elem.classList.add('notransition');
      elem.style.opacity = 0;
      elem.style.left = -x + 'px';
      elem.offsetHeight;
      elem.classList.remove('notransition');

      elem.style.opacity = 1;
      elem.style.left = '0px';

      return this.wait(800);
    },

    async slideToLeftAndDestroy(elem) {
      if (elem == null) return;

      let stack = elem.parentNode;
      let x = elem.offsetWidth + stack.offsetWidth + stack.offsetLeft + 30;

      elem.style.left = -x + 'px';
      await this.wait(800);
      elem.remove();
      return true;
    },

    ///////////////////////////////////
    //      _        _
    //     / \   ___| |_ _ __ __ _
    //    / _ \ / __| __| '__/ _` |
    //   / ___ \\__ \ |_| | | (_| |
    //  /_/   \_\___/\__|_|  \__,_|
    ///////////////////////////////////

    onEnteringStateGiveCardAstra(args) {
      args.stacks.forEach((stackId) => {
        let o = $(`construction-cards-stack-${stackId}`);
        this.onClick(o, () => this.takeAtomicAction('actGiveCardToAstra', [stackId]));
      });

      if (args.mayUseSoloBonus) {
        this.addPrimaryActionButton('actUseSoloBonus', _('Use one solo bonus instead'), () =>
          this.takeAtomicAction('actUseSoloBonus', [])
        );
      }
    },

    notif_giveCardToAstra(args) {
      let o = $(`construction-card-${args.card.id}`);
      o.classList.add('notransition');
      o.offsetHeight;
      return this.slide(o, $('astra-container').querySelector('.astra-scores'), {
        destroy: true,
      });
    },

    ////////////////////////////////
    //  ____  _
    // |  _ \| | __ _ _ __  ___
    // | |_) | |/ _` | '_ \/ __|
    // |  __/| | (_| | | | \__ \
    // |_|   |_|\__,_|_| |_|___/
    ////////////////////////////////

    setupPlanCards(gamedatas) {
      debug('Seting up the plan cards');

      // Display the cards
      this.gamedatas.planCards.forEach((plan) => {
        $('plan-cards-container-resizable').insertAdjacentHTML('beforeend', this.tplPlanCard(plan));

        let desc = plan.desc.map((t) => _(t)).join('<br />');
        this.addCustomTooltip(`plan-card-${plan.id}`, `<h3>Plan Card n°${plan.id}</h3>${desc}`);
      });
    },

    tplPlanCard(card) {
      let id = card.id;
      return `<div id="plan-card-${id}" data-id="${id}" class="plan-card-holder">
    <div class="plan-card-front">
      <div id="plan-card-${id}-validation" class="plan-validation"></div>

      <div id="plan-card-${id}-0" class="plan-validation-0">
        <div class="stamp"></div>
      </div>

      <div id="plan-card-${id}-1" class="plan-validation-1">
        <div class="stamp"></div>
      </div>
    </div>
  </div>`;
    },

    onEnteringStateAccomplishMission(args) {
      $('plan-cards-container-resizable')
        .querySelectorAll('.plan-card-holder')
        .forEach((o) => o.classList.add('unselectable'));

      args.plans.forEach((planId) => {
        let o = $(`plan-card-${planId}`);
        o.classList.remove('unselectable');
        this.onClick(o, () => this.takeAtomicAction('actAccomplishMission', [planId]));
      });
    },

    async notif_accomplishMission(args) {
      return Promise.all([this.notif_addScribbles(args), this.wait(200).then(() => this.updatePlansValidationMarks())]);
    },

    updatePlansValidationMarks() {
      $('plan-cards-container')
        .querySelectorAll('.plan-card-holder')
        .forEach((oPlan) => {
          delete oPlan.dataset.validation;

          // Is this plan validated ?
          let validationScribble = oPlan.querySelector('.wttm-scribble.scribble-squiggle');

          // Compute first/second players
          let high = [],
            low = [],
            validationStatus = validationScribble ? 1 : -1;
          if (validationScribble) {
            oPlan.querySelectorAll('.wttm-scribble.scribble-checkmark').forEach((oScribble) => {
              let pId = parseInt(oScribble.dataset.id.split('-')[0]);
              let name = this.gamedatas.players[pId].name;
              let firstValidation = oScribble.dataset.turn == validationScribble.dataset.turn;
              if (pId == this.player_id) {
                validationStatus = firstValidation ? 0 : 1;
              }

              if (firstValidation) high.push(name);
              else low.push(name);
            });
          }
          oPlan.dataset.validation = validationStatus;

          let textHigh = _('First fulfillment: ') + (high.length > 0 ? high.join(',') : _('no one yet'));
          this.addCustomTooltip(oPlan.querySelector('.plan-validation-0').id, textHigh, '');

          let textLow = _('Later fulfillment: ') + (low.length > 0 ? low.join(',') : _('no one yet'));
          this.addCustomTooltip(oPlan.querySelector('.plan-validation-1').id, textLow, '');
        });
    },
  });
});
