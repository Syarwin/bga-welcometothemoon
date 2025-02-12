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

    setupConstructionCards() {
      let gamedatas = this.gamedatas;
      this._isStandard = gamedatas.standard; // Standard = playing with three stack
      debug('Seting up the construction cards', this._isStandard ? 'Standard mode' : 'Only one card by stack');

      // Adjust stack size for flip animation
      if (this._isStandard) dojo.addClass('construction-cards-container-resizable', 'standard');

      gamedatas.constructionCards.forEach((stack, i) => {
        stack.forEach((card, j) => {
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
    highlightCombination(combination) {
      document.querySelectorAll('.construction-cards-stack').forEach((o) => o.classList.add('unselectable'));
      combination.stacks.forEach((stackId, i) => {
        $(`construction-cards-stack-${stackId}`).classList.add('selected');

        // Solo mode => flip the second stack
        if (i > 0) {
          $(`construction-cards-stack-${stackId}`).classList.add('flipped');
        }
      });
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
      // Clear previous choice
      document.querySelectorAll('.construction-cards-stack').forEach((o) => {
        o.classList.remove('selected', 'flipped');
        o.classList.add('unselectable');
      });

      // Make them selectable
      let selectableStacks = [];
      args.combinations.forEach((combination) => {
        let stackId = combination.stacks[0];
        if (selectableStacks.includes(stackId)) return;
        selectableStacks.push(stackId);

        let o = $(`construction-cards-stack-${stackId}`);
        o.classList.remove('unselectable');
        this.onClick(o, () => {
          // Standard mode => return stack id
          if (this._isStandard) this.takeAtomicAction('actChooseCards', [combination]);
          else {
            this.clientState('chooseCardsSecondStack', _('You must choose another stack for the action'), {
              combinations: args.combinations,
              stackId,
            });
          }
        });
      });

      if (args.combinations.length == 0) {
        this.addDangerActionButton('btnSystemError', _('System Error'), () => this.takeAtomicAction('actSystemError', []));
      }
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

    // /*
    //  * Expert/solo mode => need two stacks
    //  */
    // onClickStackNonStandard(stackId) {
    //   // Click again on same card => unselect
    //   if (this._selectedStackForNonStandard == stackId) {
    //     this.unselectFirstStack();
    //     return;
    //   }

    //   // First stack => ask for a second one
    //   if (this._selectedStackForNonStandard == null) {
    //     this._selectedStackForNonStandard = stackId;
    //     // Compute new possible choices for stacks
    //     this.makeStacksSelectable(this.getSelectableSecondStacks(stackId), true);
    //     this.addActionButton('buttonUnselect', _('Unselect'), () => this.unselectFirstStack(), null, false, 'gray');
    //     dojo.addClass('construction-cards-stack-' + stackId, 'selected');
    //   }

    //   // Second stack => return both stacks
    //   else {
    //     this._callback([this._selectedStackForNonStandard, stackId]);
    //   }
    // },

    // // Get the available choices for second stack depending on the first stack selected
    // getSelectableSecondStacks(stackId) {
    //   return this._possibleChoices.reduce((stacks, choice) => {
    //     if (choice[0] == stackId) stacks.push(choice[1]);
    //     return stacks;
    //   }, []);
    // },

    // // Unselect first stack
    // unselectFirstStack() {
    //   dojo.destroy('buttonUnselect');
    //   dojo.removeClass('construction-cards-stack-' + this._selectedStackForNonStandard, 'selected');
    //   this.initSelectableStacks();
    // },

    //////////////////////////////////////
    /////////////  New turn  /////////////
    //////////////////////////////////////
    async notif_newTurn(args) {
      debug('Notif: new turn', args);
      $('game_play_area').dataset.turn = args.turn;

      await args.cards.forEach(async (card) => {
        card.stackId = card.location.split('-')[1];
        let cardsInStack = $(`construction-cards-stack-${card.stackId}`).querySelector('.construction-card-holder:last-of-type');
        let oldCard = cardsInStack;

        //// STANDARD MODE : FLIP CARD ////
        if (this._isStandard) {
          // New card
          let o = $(`construction-card-${card.id}`);
          if (o) o.remove();
          $(`construction-cards-stack-${card.stackId}`).insertAdjacentHTML('beforeend', this.tplConstructionCard(card));
          $(`construction-card-${card.id}`).style.zIndex = 100 - args.turn;

          // Flip card animation
          if (oldCard) await this.flipCard(oldCard, args.turn);

          // First card in this stack ? => slide from left
          if (!oldCard) await this.slideFromLeft(newCard);
        }
        //// NON STANDARD MODE : SLIDE LEFT ////
        else {
          // Compute x position to make it slide out the left border of window
          let stack = $('construction-cards-stack-' + card.stackId);
          this.slideToLeftAndDestroy(oldCard);

          setTimeout(() => {
            // Remove flipped class if needed
            dojo.addClass(stack, 'notransition');
            dojo.removeClass(stack, 'flipped');
            stack.offsetHeight;
            dojo.removeClass(stack, 'notransition');

            // Create a new card and put it to the left (hidden)
            var newCard = dojo.place(this.format_block('jstpl_constructionCard', card), stack);
            dojo.style(newCard, 'z-index', 100 - turn);
            this.slideFromLeft(newCard);
          }, 800);
        }
      });
    },

    slideFromLeft(elem) {
      let stack = elem.parentNode;
      let x = elem.offsetWidth + stack.offsetWidth + stack.offsetLeft + 30;
      dojo.addClass(elem, 'notransition');
      dojo.style(elem, 'opacity', '0');
      dojo.style(elem, 'left', -x + 'px');
      elem.offsetHeight;
      dojo.removeClass(elem, 'notransition');

      dojo.style(elem, 'opacity', '1');
      dojo.style(elem, 'left', '0px');

      return this.wait(800);
    },

    slideToLeftAndDestroy(elem) {
      if (elem == null) return;

      let stack = elem.parentNode;
      let x = elem.offsetWidth + stack.offsetWidth + stack.offsetLeft + 30;

      dojo.style(elem, 'left', -x + 'px');
      setTimeout(() => {
        dojo.destroy(elem);
      }, 800);
    },

    discard() {
      dojo.query('.construction-card-holder').forEach((elem) => this.slideToLeftAndDestroy(elem));
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
