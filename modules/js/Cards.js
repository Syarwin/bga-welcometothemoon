define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  return declare('welcometothemoon.cards', null, {
    setupCards() {
      let neighbourObjectives = this.gamedatas.cards.NOCards;
      Object.keys(neighbourObjectives).forEach((cardId) => {
        let card = neighbourObjectives[cardId];
        this.addCard(card);

        if (card.pId && card.pId2) {
          // Switch pid1 and pid2 and create again
          card.uid = card.id + 'd';
          let tmp = card.pId2;
          card.pId2 = card.pId;
          card.pId = tmp;
          this.addCard(card);
        }
      });

      let eventCard = this.gamedatas.cards.event;
      if (eventCard) {
        this.addCard(eventCard);
      }

      this._fakeCardCounter = -2;
      this._handModals = {};
      this.orderedPlayers.forEach((player, i) => {
        this._handModals[player.id] = new customgame.modal('showCards' + player.id, {
          class: 'welcometothemoon_popin_cards',
          closeIcon: 'fa-times',
          title: _('Cards of ') + `<span style='color:#${player.color}'>${player.name}</span>`,
          closeAction: 'hide',
          verticalAlign: 'flex-start',
          contentsTpl: `<div class='modal-cards-holder' id='cards-${player.id}'></div>`,
        });
        this.onClick(`civ-cards-indicator-${player.id}`, () => this._handModals[player.id].show(), false);

        Object.values(player.playedCiv).forEach((card) => {
          this.addCard(card, `cards-${player.id}`);
        });

        Object.values(player.handCiv).forEach((card) => {
          this.addCard(card, `cards-${player.id}`);
        });

        Object.values(player.playedObj).forEach((card) => {
          this.addCard(card, `private-objectives-${player.id}`);
        });

        Object.values(player.handObj).forEach((card) => {
          this.addCard(card, `private-objectives-${player.id}`);
        });
      });

      this._civDeckCounters = {};
      for (let i = 1; i <= 4; i++) {
        let v = this.gamedatas.cards[`deck_civ_${i}`];
        this._civDeckCounters[i] = this.createCounter(`civ-deck-counter-${i}`, v);
      }

      this._eventDeckCounter = null;
      if ($('counter-deck-event')) {
        this._eventDeckCounter = this.createCounter('counter-deck-event', this.gamedatas.cards.deck_event);
      }
    },

    updateCivCounters() {
      for (let i = 1; i <= 4; i++) {
        let v = this.gamedatas.cards[`deck_civ_${i}`];
        this._civDeckCounters[i].toValue(v);
      }
      if (this._eventDeckCounter) {
        this._eventDeckCounter.toValue(this.gamedatas.cards.deck_event);
      }
    },

    notif_newCards(n) {
      debug('Notif: newCards (event)');

      // Gaining new objective
      if (n.args.card) {
        let card = n.args.card;
        this.addCard(card, this.getVisibleTitleContainer());
        this.slide(`card-${card.id}`, `private-objectives-${this.player_id}`);
      }
      // Adding/removing CIV cards
      else {
        for (let i = 1; i <= 4; i++) {
          let deck = `deck_civ_${i}`;
          this.gamedatas.cards[deck] = n.args[deck];
        }
        this.updateCivCounters();
      }
    },

    updateHand() {
      let pId = this.player_id;
      let player = this.gamedatas.players[pId];
      this.empty(`cards-${pId}`);
      Object.values(player.playedCiv).forEach((card) => {
        this.addCard(card, `cards-${player.id}`);
      });

      Object.values(player.handCiv).forEach((card) => {
        this.addCard(card, `cards-${player.id}`);
      });
    },

    addCard(card, location = null) {
      card.uid = card.uid || card.id;
      if (card.uid == -1) card.uid = this._fakeCardCounter--;

      if ($('card-' + card.uid)) return;

      let o = this.place('tplCard', card, location == null ? this.getCardContainer(card) : location);
      let tooltipDesc = this.getCardTooltip(card);
      if (tooltipDesc != null) {
        this.addCustomTooltip(o.id, tooltipDesc.map((t) => this.formatString(t)).join('<br/>'));
      }

      return o;
    },

    getCardTooltip(card) {
      if (card.id < 0) {
        if (card.type == 'civCard') {
          return [this.fsr(_('Civ Card of level ${lvl}'), { lvl: card.level })];
        }
        return [_('TODO')];
      }

      return [`<h4>${_(card.title)}</h4>${_(card.desc)}`];
    },

    tplCard(card) {
      let uid = card.uid || card.id;

      // CIV CARD
      if (card.type == 'civCard') {
        let effect = '';
        if (card.id >= 0) {
          effect = card.effectType == 'immediate' ? _('Immediate') : _('End Game');
        }
        return `<div id="card-${uid}" data-type="${card.type}" class="welcometothemoon-card ${card.id < 0 ? 'fake' : ''}">
          <div class='card-inner' data-id="${card.id}" data-level="${card.level}">
            <div class='card-title'>${_(card.title || '')}</div>
            <div class='card-desc'>${_(card.desc || '')}</div>
            <div class='card-effect'>${effect}</div>
          </div>
        </div>`;
      }
      // EVENT CARD
      else if (card.type == 'EventCard') {
        return `<div id="card-${uid}" data-type="${card.type}" class="welcometothemoon-card">
          <div class='card-inner' data-id="${card.id}">
            <div class='card-title'>${_(card.title)}</div>
            <div class='card-desc'>${_(card.desc)}</div>
          </div>
        </div>`;
      }
      // Neighbour objectives
      else if (card.type == 'NOCard') {
        let pId1 = card.pId,
          pId2 = card.pId2;
        if (pId1 && pId2) {
          return `<div id="card-${uid}" class="nocard-wrapper">
            <div class='nocard-indicator'>
              <span class='nocard-indicator-value' id='card-${uid}-${pId1}-value' style="color:#${this.getPlayerColor(
                pId1
              )}"></span>
              <span class='welcometothemoon-icon icon-medal' id='card-${uid}-${pId1}-medal'></span>
            </div>
            <div data-type="${card.type}" class="welcometothemoon-card icon-only">
              <div class='card-inner' data-id="${card.id}"></div>
            </div>
            <div class='nocard-indicator'>
              <span class='nocard-indicator-value' id='card-${uid}-${pId2}-value' style="color:#${this.getPlayerColor(
                pId2
              )}"></span>
              <span class='welcometothemoon-icon icon-medal' id='card-${uid}-${pId2}-medal'></span>
            </div>
          </div>`;
        } else {
          return (
            `<div id="card-${uid}" class="nocard-wrapper">
                <div data-type="${card.type}" class="welcometothemoon-card">
                  <div class='card-inner' data-id="${card.id}"></div>
                </div>
                ` +
            this.orderedPlayers
              .map(
                (player) => `
                <div class='nocard-indicator'>
                <span class='nocard-indicator-value' id='card-${uid}-${player.id}-value' style="color:#${this.getPlayerColor(
                  player.id
                )}"></span>
                <span class='welcometothemoon-icon icon-medal' id='card-${uid}-${player.id}-medal'></span>
              </div>`
              )
              .join('') +
            `
            </div>`
          );
        }
      }
      // Private objectives
      else if (card.type == 'POCard') {
        let pId1 = card.pId;
        return `<div id="card-${uid}" class="pocard-wrapper">
          <div class='pocard-indicator'>
            <span class='pocard-indicator-value' id='card-${uid}-${pId1}-value'></span>
            <span class='welcometothemoon-icon icon-medal' id='card-${uid}-${pId1}-medal'>0</span>
          </div>
          <div data-type="${card.type}" class="welcometothemoon-card">
            <div class='card-inner' data-id="${card.id}"></div>
          </div>
        </div>`;
      }
    },

    getCardContainer(card) {
      let t = card.location.split('_');
      if (card.location == 'trash') {
        return this.getVisibleTitleContainer();
      }
      if (card.type == 'NOCard' && card.location == 'NOCards') {
        let pId1 = card.pId,
          pId2 = card.pId2;

        if (!pId1 && !pId2) {
          return $('shared-obj');
        }

        // pId2 is sitting at the right of pId1
        if (this.getDeltaPlayer(pId1, 1) == pId2) {
          return $(`next-objectives-${pId1}`);
        } else {
          return $(`prev-objectives-${pId1}`);
        }
      }
      if (card.type == 'EventCard') {
        return $('event-card-holder');
      }
      if (card.location == 'tochoose_obj') {
        return $('pending-cards');
      }

      console.error('Trying to get container of a card', card);
      return 'game_play_area';
    },

    notif_takeCivCard(n) {
      debug('Notif: take civ card', n);
      if ($('welcometothemoon-choose-card-footer')) $('welcometothemoon-choose-card-footer').remove();

      let pId = n.args.player_id;
      let card = n.args.card;

      let oCard = null;
      // Private notif
      if (card.id < 0) {
        this.addCard(card, 'welcometothemoon-main-container');
        oCard = $(`card-${card.uid}`);
      }
      // Public notif
      else {
        if (!$(`card-${card.id}`)) this.addCard(card, 'welcometothemoon-main-container');
        oCard = $(`card-${card.id}`);
      }

      this._civDeckCounters[card.level].incValue(-1);
      this.slide(oCard, `civ-cards-indicator-${pId}`).then(() => {
        dojo.place(oCard, `cards-${pId}`);
        let counter = card.location == 'playedCivCards' ? 'immediateCiv' : 'endgameCiv';
        this.gamedatas.players[pId][counter]++;
        this._playerCounters[pId][counter].incValue(1);
      });
    },

    notif_destroyCard(n) {
      debug('Notif: destroying a private objective card', n);
      this.slide(`card-${n.args.cardId}`, this.getVisibleTitleContainer(), {
        destroy: true,
      });
    },

    notif_revealCards(n) {
      debug('Notif: revealing cards', n);
      this.gamedatas.players = n.args.playersData;
      this.forEachPlayer((player) => {
        this.empty(`cards-${player.id}`);
        Object.values(player.playedCiv).forEach((card) => {
          this.addCard(card, `cards-${player.id}`);
        });

        Object.values(player.playedObj).forEach((card) => {
          this.addCard(card, `private-objectives-${player.id}`);
        });
      });

      this.updatePlayersCounters();
    },

    notif_newEventCard(n) {
      debug('Notif: revealing new event card', n);
      let card = n.args.card;
      this.empty('event-card-holder');
      this.addCard(card);
      this._eventDeckCounter.incValue(-1);
      this.gamedatas.cards.deck_event--;
      this.zoomOnEventCard(true);
    },

    notif_peekNextEvent(n) {
      debug('Notif: peeking next event card', n);
      if (this.isFastMode()) return;

      let card = n.args.card;
      this.addCard(card);
      let oCard = $(`card-${card.id}`);
      this.zoomOnEventCard(true, oCard);
      this.wait(200).then(oCard.remove());
    },

    zoomOnEventCard(autoClose = false, oCard = null) {
      oCard = oCard || $('event-card-holder').querySelector('.welcometothemoon-card');
      if (!oCard) return;

      dojo.place("<div id='card-overlay'></div>", 'ebd-body');
      let duplicate = oCard.cloneNode(true);
      duplicate.id = duplicate.id + 'duplicate';
      $('card-overlay').appendChild(duplicate);
      $('card-overlay').offsetHeight;
      $('card-overlay').classList.add('active');

      let close = () => {
        $('card-overlay').classList.remove('active');
        this.wait(700).then(() => $('card-overlay').remove());
      };

      if (autoClose) this.wait(2500).then(close);
      else $('card-overlay').addEventListener('click', close);
    },

    notif_newObjectiveCard(n) {
      debug('Notif: adding new common objective card', n);

      let card = n.args.card;
      if (!$(`card-${card.id}`)) {
        this.addCard(card, this.getVisibleTitleContainer());
      }

      this.slide(`card-${card.id}`, 'shared-obj').then(() => {
        $('pending-cards').innerHTML = '';
      });
    },
  });
});
