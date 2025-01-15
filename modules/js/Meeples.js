define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  function isVisible(elem) {
    return !!(elem.offsetWidth || elem.offsetHeight || elem.getClientRects().length);
  }

  return declare('welcometothemoon.meeples', null, {
    setupMeeples() {
      // This function is refreshUI compatible
      let meepleIds = this.gamedatas.meeples.map((meeple) => {
        if (!$(`meeple-${meeple.id}`)) {
          this.addMeeple(meeple);
        }

        let o = $(`meeple-${meeple.id}`);
        if (!o) return null;

        let container = this.getMeepleContainer(meeple);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
        }
        o.dataset.state = meeple.state;

        return meeple.id;
      });
      document.querySelectorAll('.welcometothemoon-meeple[id^="meeple-"]').forEach((oMeeple) => {
        if (!meepleIds.includes(parseInt(oMeeple.getAttribute('data-id'))) && oMeeple.getAttribute('data-type') != 'cylinder') {
          this.destroy(oMeeple);
        }
      });

      if (!$(`meeple-firstPlayer`)) {
        this.addMeeple({ id: 'firstPlayer', type: 'first-player' }, $(`firstPlayer-${this.gamedatas.firstPlayer}`));
      }

      // if (!$('meeple-flux')) {
      //   this.addMeeple({ id: 'flux', type: 'flux' }, $(`corporation-${this.player_id}`));
      // }

      this.updatePlayersCounters();
    },

    addMeeple(meeple, location = null) {
      if ($('meeple-' + meeple.id)) return;

      let o = this.place('tplMeeple', meeple, location == null ? this.getMeepleContainer(meeple) : location);
      let tooltipDesc = this.getMeepleTooltip(meeple);
      if (tooltipDesc != null) {
        this.addCustomTooltip(o.id, tooltipDesc.map((t) => this.formatString(t)).join('<br/>'));
      }

      return o;
    },

    getMeepleTooltip(meeple) {
      let type = meeple.type;
      if (type == 'lifepod') {
        return [_('Lifepod')];
      }
      if (type == 'meteor') {
        return [_('Meteor')];
      }
      if (type == 'first-player') {
        return [_('Station Commander')];
      }
      if (type == 'flux') {
        return [_('Flux token')];
      }
      return null;
    },

    tplMeeple(meeple) {
      let type = meeple.type.charAt(0).toLowerCase() + meeple.type.substr(1);
      if (['water', 'tech', 'rover', 'biomass', 'civ'].includes(type)) {
        type = 'tracker-' + type;
      }
      let color = '';
      return `<div class="welcometothemoon-meeple welcometothemoon-icon icon-${type}" id="meeple-${meeple.id}" data-id="${meeple.id}" data-type="${type}" data-state="${meeple.state}"></div>`;
    },

    getMeepleContainer(meeple) {
      let t = meeple.location.split('_');
      if (meeple.location == 'trash') {
        return this.getVisibleTitleContainer();
      }
      // Flux token
      if (meeple.type == 'flux') {
        return $(`corporation-${meeple.pId}`).querySelector(`.column-${meeple.x}`);
      }
      // Things on the planet
      if (meeple.location == 'planet') {
        return this.getPlanetCell(meeple.pId, meeple.x, meeple.y);
      }
      // Rover in reserve
      if (meeple.type == 'rover-meeple' && meeple.location == 'corporation') {
        return $(`rover-reserve-${meeple.pId}`);
      }
      // Meteor in reserve
      if (meeple.type == 'meteor' && meeple.location == 'corporation') {
        return $(`meteor-reserve-${meeple.pId}`);
      }
      // Things on tracks
      if (meeple.location == 'corporation' && $(`corporation-${meeple.pId}-${meeple.x}-${meeple.y}`)) {
        return $(`corporation-${meeple.pId}-${meeple.x}-${meeple.y}`);
      }
      // Lifepod in reserve
      if (meeple.type == 'lifepod' && meeple.location == 'corporation') {
        return $(`lifepod-reserve-${meeple.pId}`);
      }

      console.error('Trying to get container of a meeple', meeple);
      return 'game_play_area';
    },

    /**
     * Wrap the sliding animations
     */
    slideResources(meeples, configFn, syncNotif = true) {
      let fakeId = -1; // Used for virtual meeple that will get destroyed after animation (eg SCORE)
      let needUpdateForActionCards = false;
      let workerMoved = false;
      let promises = meeples.map((resource, i) => {
        // Get config for this slide
        let config = typeof configFn === 'function' ? configFn(resource, i) : Object.assign({}, configFn);
        if (resource.destroy) {
          resource.id = fakeId--;
          config.destroy = true;
        }

        // Default delay if not specified
        let delay = config.delay ? config.delay : 100 * i;
        config.delay = 0;

        // Make sure we are looking at other's board
        if (this._focusedPlayer != null && this._focusedPlayer != resource.pId) {
          this.goToPlayerBoard(resource.pId);
        }

        // Use meepleContainer if target not specified
        let target = config.target ? config.target : this.getMeepleContainer(resource);
        if (!isVisible(target)) {
          config.to = $(`overall_player_board_${resource.pId}`);
        }

        // Rename if meeple is existing => due to private flow, some meeples might receive the same id
        let oMeeple = $(`meeple-${resource.id}`);
        if (config.renameIfExisting && oMeeple) {
          let newId = +oMeeple.dataset.id + 1;
          oMeeple.dataset.id = newId;
          oMeeple.id = `meeple-${newId}`;
        }

        if (oMeeple && oMeeple.parentNode == $(target)) {
          return this.wait(800);
        }

        // Slide it
        let slideIt = () => {
          // Create meeple if needed
          if (!$('meeple-' + resource.id)) {
            this.addMeeple(resource);
          }

          // Slide it
          return this.slide('meeple-' + resource.id, target, config);
        };

        if (this.isFastMode()) {
          slideIt();
          return null;
        } else {
          return this.wait(delay - 10).then(slideIt);
        }
      });

      let endCallback = () => {
        if (workerMoved) {
          this.updateWorkerCounters();
        }
        if (needUpdateForActionCards) {
          this.updateActionCardsSummaries();
        }
        if (syncNotif) {
          this.notifqueue.setSynchronousDuration(this.isFastMode() ? 0 : 10);
        }
      };

      if (this.isFastMode()) {
        endCallback();
        return Promise.resolve();
      } else
        return Promise.all(promises)
          .then(() => this.wait(10))
          .then(endCallback);
    },

    notif_silentKill(n) {
      debug('Silent kill', n);
      debug('TODO');
    },

    notif_destroyedMeeples(n) {
      debug('Notif: destroying meeples', n);
      let target = this.getVisibleTitleContainer();
      this.slideResources(n.args.meeples, {
        destroy: true,
        target,
      });
    },

    notif_addMeeples(n) {
      debug('Notif: adding & sliding meeples', n);
      this.slideResources(n.args.meeples, {
        from: this.getVisibleTitleContainer(),
        renameIfExisting: true,
      });
    },

    notif_slideMeeple(n) {
      debug('Notif: sliding meeple', n);
      this.slideResources([n.args.meeple]);
    },
    notif_slideMeeples(n) {
      debug('Notif: sliding meeples', n);
      this.slideResources(n.args.meeples);
    },

    notif_changeFirstPlayer(n) {
      debug('Notif: change of first player', n);
      this.slide($(`meeple-firstPlayer`), $(`firstPlayer-${n.args.player_id}`));
    },

    notif_chooseFluxTrack(n) {
      debug('Notif: change flux track', n);
      if (n.args.isSame) {
        this.wait(1000).then(() => this.notifqueue.setSynchronousDuration(this.isFastMode() ? 0 : 10));
      } else {
        this.slideResources([n.args.meeple]);
      }
    },
  });
});
