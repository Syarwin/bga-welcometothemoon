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
      debug('Setting up the construction cards', this._isStandard ? 'Standard mode' : 'Only one card by stack');

      // Adjust stack size for flip animation
      if (this._isStandard) $('construction-cards-container-resizable').classList.add('standard');

      this.setupConstructionCards();
    },

    setupConstructionCards() {
      let cardIds = [];
      this.gamedatas.constructionCards.forEach((stack, i) => {
        stack.forEach((card, j) => {
          cardIds.push(`construction-card-${card.id}`);
          if ($(`construction-card-${card.id}`)) return;

          $(`construction-cards-stack-${i}`).insertAdjacentHTML('beforeend', this.tplConstructionCard(card));
          let oCard = $(`construction-card-${card.id}`);
          oCard.style.zIndex = 100;

          // Flip first card
          if (j == 0 && this._isStandard) {
            this.flipCard(oCard, 1);
          }
          // Solo : add the tooltip even if not flipped
          else if (this.isSolo()) {
            this.addConstructionCardTooltip(oCard);
          }
        });
      });

      document.querySelectorAll('.construction-card-holder').forEach((oCard) => {
        if (!cardIds.includes(oCard.id)) {
          this.destroy(oCard);
        }
      });

      this.updateDeckCount();
    },

    updateDeckCount(deckCount = null) {
      if (deckCount !== null) {
        this.gamedatas.deckCount = deckCount;
      }
      $('cards-count-status').innerHTML = this.gamedatas.deckCount;
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
      this.addConstructionCardTooltip(card);
    },

    addConstructionCardTooltip(card) {
      let action = card.dataset.action;
      const actionNames = {
        energy: _('Energy'),
        robot: _('Robot'),
        plant: _('Plant'),
        water: _('Water'),
        astronaut: _('Astronaut'),
        planning: _('Planning'),
      };
      let actionName = actionNames[action];

      let desc = `<div class='construction-card-tooltip'>
        <h3>${this.formatIcon(action)} ${actionName} ${this.formatIcon(action)}</h3> 
        ${this.getActionDesc(action)}
      </div>`;
      this.addCustomTooltip(card.id, desc);
    },

    getActionDesc(action) {
      const scenario = this.gamedatas.scenario;
      switch (scenario) {
        // Scenario 1
        case 1:
          return (
            _('In this Adventure, you won’t perform any action. That means that none of the 6 actions has any effect.') +
            _(
              'Each floor is tied to one of the action symbols (Astronaut, Water, Robot...). The actions only serve to show you where you can write down the number of the combination. So, each turn, you must write down a number on the floor tied to the action associated with that number.'
            )
          );
        // Scenario 2
        case 2:
          switch (action) {
            case 'energy':
              return (
                _(
                  'The energy allows you to turn on the engines of your rocket, in order to create a boost to correct your trajectory. For that purpose, circle one Energy symbol at the top of your sheet 2. At the beginning of the game, you start with one energy already circled.'
                ) +
                _(
                  'Each time you have 2 circled energies, you must immediately cross them off, then divide a zone on your trajectory by drawing a line between 2 spaces of your choice, numbered or not. This line shows the end of one zone, and the beginning of another one. This way you will get shorter zones that you must number independently from one another.'
                )
              );
            case 'robot':
              return (
                _(
                  'You must program the robots to retrieve the plants from the space stations. The stations are already connected to your trajectory by the robots, but you must activate them in order to retrieve the plants and earn points.'
                ) +
                _(
                  'No matter where you have written down the number of your combination, with this action, you can circle a robot sent towards any station. The first players to circle all the robots of a station, during the same turn, can circle the highest multiplier. The other players must then cross off this multiplier and will be able to get only the lowest multiplier.'
                )
              );
            case 'plant':
              return (
                _(
                  'You must organize the growing of the plants in microgravity in the space stations. These stations are connected to your trajectory by Robot symbols. With the Plant action, circle one plant in the station of your choice, as long as this station is connected to the zone where you have written down your number.'
                ) +
                _(
                  'At the beginning of the game, you can reach all stations from any space on your trajectory, because the 4 stations are connected to the one and only zone that makes up your trajectory. But gradually, through the use of the energies, you will split your trajectory in multiple separate zones. Thus, each station will be connected only to a specific zone, and a Plant action will only reach it if the number is written in this zone.'
                )
              );
            case 'water':
              return _(
                'It is important to stir the water tanks. If you have written down the number of your combination in a space with a water tank, then, with the Water action, you can circle that water tank.'
              );
            case 'astronaut':
              return _(
                'The Astronaut action allows you to modify the value of the number of the chosen combination, before you write it down (-2, -1, 0, +1, +2). Moreover, cross off 1 Astronaut symbol on the right side of your sheet. Whenever you have 2 crossed off Astronauts, circle a Wild Action symbol'
              );
            case 'planning':
              return _(
                'The Planning action allows you to fill in a space with an X in addition to the number of your combination. Moreover, cross off 1 Planning symbol on the right side of your sheet. Whenever you have 2 crossed off Planning, circle a Wild Action symbol. Note that you cannot cross off a Planning symbol without writing down an X in an empty space.'
              );
          }

        // Scenario 3
        case 3:
          switch (action) {
            case 'energy':
              return _(
                'Using the energy, you can improve the greenhouses, the water tanks or the network of parabolic antennas. To do that, cross off one box in the scoring area of your choice: the plants, the water or the antennas. You must first cross off the box with the lowest value, then the others from top to bottom.'
              );
            case 'robot':
              return (
                _(
                  'In order to dispatch the scientists and the equipment, you must create a network of pressurized tunnels connecting the buildings to one another. At the beginning of the game, you have already 2 tunnels built from the landing site of the rocket.'
                ) +
                _(
                  'With the Robot action, draw a line on a tunnel to connect two buildings. These buildings do not necessarily have to be numbered. The line must start from a building already connected to the network. The network can branch out in multiple paths.'
                ) +
                _(
                  'As soon as a building with a parabolic antenna is numbered AND connected to your tunnel network, circle its antenna. If you connect the observatory on the top right corner, immediately circle its 3 antennas.'
                )
              );
            case 'plant':
              return (
                _(
                  'You must build greenhouses in order to grow plants. For that purpose, circle one greenhouse in the same quarter where you have just written down the number of your combination.'
                ) +
                _(
                  'You must circle the greenhouses of a quarter from top to bottom. Each greenhouse will earn you 1 plant for the end of the game. The fourth greenhouse, which is larger, will earn you 2 plants.'
                )
              );
            case 'water':
              return _(
                'If you write down the number of your combination in a building with a water tank, then with the Water action, you can circle that water tank.'
              );
            case 'astronaut':
              return _(
                'The Astronaut action allows you to modify the value of the number of the chosen combination, before you write it down (-2, -1, 0, +1, +2). Moreover cross off 1 Astronaut symbol in the scoring area of your sheet.'
              );
            case 'planning':
              return _(
                'The Planning action allows you to fill in a space with an X in addition to the number of your combination. Moreover, you must immediately cross off the available box with the lowest value in the Planning scoring area at the bottom of your sheet. The Planning action is powerful to quickly establish your colony, but it will cost you some points at the end of the game.'
              );
          }
      }
    },

    ////////////////////////////////////
    ////////  Selecting a stack ////////
    ////////////////////////////////////

    onEnteringStateChooseCards(args) {
      if (args.noNode) return;

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
            // Basic case => combination selection is over
            if (selectableStacks[stackId].length == 1 && !args.useJoker) {
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
            this.clientState(
              'chooseCardsSecondStack',
              args.useJoker
                ? _('You must now choose another card for the action before choosing how to use the joker')
                : _('You must now choose another card for the action'),
              {
                combinations,
                stackId,
                useJoker: args.useJoker,
              }
            );
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

      let selectableStacks = {};
      let combinations = [];
      args.combinations.forEach((combination) => {
        if (combination.stacks[0] != args.stackId) return;
        combinations.push(combination);
        let stackId = combination.stacks[1];
        if (selectableStacks[stackId]) {
          selectableStacks[stackId].push(combination.action);
          return;
        }
        selectableStacks[stackId] = [combination.action];

        let o = $(`construction-cards-stack-${stackId}`);
        o.classList.remove('unselectable');
        this.onClick(o, () => {
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
        });
      });
      console.log(selectableStacks);
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
      this.updateDeckCount(args.deckCount);
      return true;
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
        this.addPrimaryActionButton(
          `btnCard${stackId}`,
          this.formatIcon(o.querySelector('.construction-card-holder').dataset.action),
          () => this.takeAtomicAction('actGiveCardToAstra', [stackId])
        );
        $(`btnCard${stackId}`).classList.add('btnAction');
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

    async notif_replaceSoloCard(args) {
      // Destroy old card
      let oldCard = $(`construction-card-${args.oldCard.id}`);
      let stack = oldCard.parentNode;
      await this.slideToLeftAndDestroy(oldCard);

      // Slide in new card
      stack.insertAdjacentHTML('beforeend', this.tplConstructionCard(args.newCard));
      let newCard = $(`construction-card-${args.newCard.id}`);
      newCard.style.zIndex = 100 - args.turn;
      this.updateDeckCount(args.deckCount);
      return this.slideFromLeft(newCard);
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
        this.addCustomTooltip(`plan-card-${plan.id}`, `${this.tplPlanCard(plan, true)}<h3>Plan Card n°${plan.id}</h3>${desc}`);
      });
    },

    tplPlanCard(card, tooltip = false) {
      let id = (tooltip ? 'tooltip-' : '') + card.id;
      return `<div id="plan-card-${id}" data-id="${card.id}" class="plan-card-holder">
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
        .forEach((oPlan, i) => {
          delete oPlan.dataset.validation;
          document.querySelectorAll(`.plan-status-${i + 1}`).forEach((o) => (o.innerHTML = ''));
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
              $(`plan-status-${i + 1}-${pId}`).innerHTML = firstValidation ? 1 : 2;
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
