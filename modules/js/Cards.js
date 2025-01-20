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

    // // Clear everything
    // clearPossible() {
    //   this._callback = null;
    //   this._possibleChoices = null;
    //   this._selectableStacks = null;
    //   this._highlighted = null;
    //   dojo.query('.construction-cards-stack').removeClass('unselectable selectable');
    // },

    // // Hightlight selected stack(s)
    // highlight(stack, callback) {
    //   this._callback = callback;
    //   this._highlighted = stack;
    //   dojo.query('.construction-cards-stack').addClass('unselectable');
    //   if (this._isStandard) {
    //     dojo.addClass('construction-cards-stack-' + stack, 'selected');
    //   } else {
    //     dojo.addClass('construction-cards-stack-' + stack[0], 'selected');
    //     dojo.addClass('construction-cards-stack-' + stack[1], 'selected flipped');
    //   }
    // },

    // Flip card and add tooltip
    flipCard(card, turn) {
      card.classList.add('flipped');
      setTimeout(() => {
        card.style.zIndex = turn;

        let action = card.dataset.action;
        this.addCustomTooltip(card.id, 'TODO: tooltip ' + action);
      }, 1000);
    },

    ////////////////////////////////////
    ////////  Selecting a stack ////////
    ////////////////////////////////////
    promptPlayer(possibleChoices, callback) {
      this._callback = callback;
      this._possibleChoices = possibleChoices;
      this.initSelectableStacks();
    },

    initSelectableStacks() {
      this._selectedStackForNonStandard = null;
      let stacks = this._possibleChoices.map((choice) => (this._isStandard ? choice : choice[0]));
      this.makeStacksSelectable(stacks);
    },

    makeStacksSelectable(stacks, flipped) {
      dojo.query('.construction-cards-stack').removeClass('selected flipped'); // TODO : add in the clearPossible function instead ?
      dojo.query('.construction-cards-stack').addClass('unselectable');
      this._selectableStacks = stacks;
      stacks.forEach((stackId) =>
        dojo
          .query('#construction-cards-stack-' + stackId)
          .removeClass('unselectable')
          .addClass('selectable' + (flipped ? ' flipped' : ''))
      );
    },

    onClickStack(stackId) {
      debug('Clicked on a stack', stackId);
      // Check if selectable
      if (
        (!this._selectableStacks || !this._selectableStacks.includes(stackId)) &&
        this._selectedStackForNonStandard != stackId
      ) {
        // Clicked on a selected card => callback to restart turn
        if (
          this._highlighted != null &&
          this._callback != null &&
          ((this._isStandard && this._highlighted == stackId) || (!this._isStandard && this._highlighted.includes(stackId)))
        )
          this._callback();
        return;
      }

      // Standard mode => return stack id
      if (this._isStandard) this._callback(stackId);
      else this.onClickStackNonStandard(stackId);
    },

    //////////////////////////////////////
    /////////////  New turn  /////////////
    //////////////////////////////////////
    newTurn(cards, turn) {
      // Clear everything
      dojo.query('.construction-cards-stack').removeClass('selected selectable unselectable');

      // Marks ?
      let markedStack = Math.floor(Math.random() * 3);
      debug(markedStack, marks[turn]);
      cards.forEach((card) => {
        // Add small mark
        card.mark = card.stackId == markedStack ? marks[turn] : 0;

        let cardsInStack = dojo.query('#construction-cards-stack-' + card.stackId + ' .construction-card-holder:last-of-type');
        // NULL only happens in EXPERT MODE
        let oldCard = cardsInStack.length == 0 ? null : cardsInStack[0];

        //// STANDARD MODE : FLIP CARD ////
        if (this._isStandard) {
          // Flip card animation
          if (oldCard) this.flipCard(oldCard, turn);

          // New card
          if ($('construction-card-' + card.id)) dojo.destroy('construction-card-' + card.id);
          var newCard = dojo.place(this.format_block('jstpl_constructionCard', card), 'construction-cards-stack-' + card.stackId);
          dojo.style('construction-card-' + card.id, 'z-index', 100 - turn);

          // First card in this stack ? => slide from left
          if (!oldCard) this.slideFromLeft(newCard);
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

    giveCard(stack, pId) {
      let oldCard = dojo.query('#construction-cards-stack-' + stack + ' .construction-card-holder:last-of-type')[0];
      dojo.addClass(oldCard, 'notransition');
      this.slideToObjectAndDestroy(oldCard, 'overall_player_board_' + pId, 1000);
    },

    ////////////////////////////////////////////////////////
    ////////  Non-standard mode : select two stacks ////////
    ////////////////////////////////////////////////////////

    /*
     * Expert/solo mode => need two stacks
     */
    onClickStackNonStandard(stackId) {
      // Click again on same card => unselect
      if (this._selectedStackForNonStandard == stackId) {
        this.unselectFirstStack();
        return;
      }

      // First stack => ask for a second one
      if (this._selectedStackForNonStandard == null) {
        this._selectedStackForNonStandard = stackId;
        // Compute new possible choices for stacks
        this.makeStacksSelectable(this.getSelectableSecondStacks(stackId), true);
        this.addActionButton('buttonUnselect', _('Unselect'), () => this.unselectFirstStack(), null, false, 'gray');
        dojo.addClass('construction-cards-stack-' + stackId, 'selected');
      }

      // Second stack => return both stacks
      else {
        this._callback([this._selectedStackForNonStandard, stackId]);
      }
    },

    // Get the available choices for second stack depending on the first stack selected
    getSelectableSecondStacks(stackId) {
      return this._possibleChoices.reduce((stacks, choice) => {
        if (choice[0] == stackId) stacks.push(choice[1]);
        return stacks;
      }, []);
    },

    // Unselect first stack
    unselectFirstStack() {
      dojo.destroy('buttonUnselect');
      dojo.removeClass('construction-cards-stack-' + this._selectedStackForNonStandard, 'selected');
      this.initSelectableStacks();
    },
  });
});
