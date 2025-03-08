define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
  const NUMBER_X = 100;
  const SCRIBBLE_ARROW = 301;
  const SCRIBBLE_CIRCLE = 302;
  const SCRIBBLE_CHECKMARK = 303;
  const SCRIBBLE_LINE = 304;

  const BGA_URL = dojoConfig.packages.reduce((r, p) => (p.name == 'bgagame' ? p.location : r), null);
  return declare('welcometothemoon.scribbles', null, {
    setupScribbles() {
      // This function is refreshUI compatible
      let scribbleIds = this.gamedatas.scribbles.map((scribble) => {
        if (!$(`scribble-${scribble.id}`)) {
          this.addScribble(scribble);
        }

        let o = $(`scribble-${scribble.id}`);
        if (!o) return null;

        let container = this.getScribbleContainer(scribble);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
        }
        o.dataset.state = scribble.state;

        return scribble.id;
      });
      $('overall-content')
        .querySelectorAll('.wttm-scribble[id^="scribble-"]')
        .forEach((oScribble) => {
          if (!scribbleIds.includes(oScribble.getAttribute('data-id'))) {
            this.destroy(oScribble);
          }
        });
      this.updatePlansValidationMarks();
    },

    addScribble(scribble, animation) {
      if ($(`scribble-${scribble.id}`)) return;

      let container = this.getScribbleContainer(scribble);
      var scribbleTpl = 'tplScribbleSquiggle';

      // Number
      if ([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, NUMBER_X].includes(scribble.type)) {
        scribbleTpl = 'tplScribbleNumber';
      }
      if (scribble.type == SCRIBBLE_ARROW) scribbleTpl = 'tplScribbleArrow';
      if (scribble.type == SCRIBBLE_CIRCLE) scribbleTpl = 'tplScribbleCircle';
      if (scribble.type == SCRIBBLE_CHECKMARK) scribbleTpl = 'tplScribbleCheckmark';
      if (scribble.type == SCRIBBLE_LINE) scribbleTpl = 'tplScribbleLine';

      this.place(scribbleTpl, scribble, container);
      if (animation && !this.isFastMode()) {
        if (this.prefs && this.prefs[106] && this.prefs[106].value == 1) {
          this.playSound('welcometo_scribble');
        }
        $(`scribble-${scribble.id}`).classList.add('animate');
      }
    },

    async notif_addScribble(args) {
      debug('Notif: add scribble', args);
      this.addScribble(args.scribble, true);

      let duration = args.duration || 900;
      await this.wait(duration);

      if (args.plansUpdateNeeded) {
        this.updatePlansValidationMarks();
      }

      return true;
    },

    notif_addScribbles(args) {
      debug('Notif: add scribbles', args);
      args.scribbles.forEach((scribble) => {
        this.addScribble(scribble, true);
      });
      return this.wait(1200);
    },

    notif_resolveSabotage(args) {
      return this.notif_addScribbles(args);
    },

    getScribbleContainer(scribble) {
      let t = scribble.location.split('-');
      let pId = scribble.id.split('-')[0];

      if (t[0] == 'slot') {
        return $(`slot-${pId}-${t[1]}`);
      }
      if (t[0] == 'plan') {
        if (pId != this.player_id) {
          return $(`plan-card-${t[1]}`);
        } else {
          return $(`plan-card-${t[1]}-validation`);
        }
      }
      if ($(scribble.location)) {
        return $(scribble.location);
      }

      console.error('Trying to get container of a scribble', scribble);
      return 'game_play_area';
    },

    //////////////////////////////////////////////////////
    //  _____                    _       _
    // |_   _|__ _ __ ___  _ __ | | __ _| |_ ___  ___
    //   | |/ _ \ '_ ` _ \| '_ \| |/ _` | __/ _ \/ __|
    //   | |  __/ | | | | | |_) | | (_| | ||  __/\__ \
    //   |_|\___|_| |_| |_| .__/|_|\__,_|\__\___||___/
    //                    |_|
    //////////////////////////////////////////////////////

    // tplScribble(scribble) {
    //   return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 533.33331 533.33331" class="wttm-scribble" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
    //   <path
    //      clip-path="url(#scribble-clip-path)"
    //      fill="none" stroke-miterlimit="0" stroke-width="70"
    //      class="scribble-path"
    //      d="M 207.55019,87.389555 C 76.569696,311.99391 57.909342,399.75673 38.554216,493.49396 122.0427,330.8991 181.21334,138.53703 294.29718,30.200802 229.2164,163.7014 169.67116,281.85814 115.02007,442.73091 174.01295,312.46209 233.33432,199.24878 308.43372,114.37751 281.67061,154.27485 201.50136,359.17532 169.63854,487.71083 234.96652,374.61846 264.42725,235.62198 365.62247,148.43373 l -114.3775,286.58633 c 47.33601,-82.46318 79.17712,-192.04244 142.00803,-247.38955 4.53817,74.75234 -48.6109,151.43239 -71.96787,226.18473 37.9056,-68.3347 65.46371,-150.46604 113.73493,-204.97991 -13.56619,75.03628 -37.51108,129.31518 -57.18875,192.12851 l 93.81526,-152.28915 -53.97591,170.28112 73.25302,-104.09638 c 4.77407,41.49757 -20.97985,75.72064 -35.98394,110.52208"
    //    />
    //   </svg>
    //   `;
    // },

    tplScribbleNumber(scribble) {
      let number = scribble.type;
      if (number == 100) number = 'X';
      return `<div class="wttm-scribble scribble-number" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">${number}</div>`;
    },

    tplScribbleSquiggle(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 533.33331 533.33331" class="wttm-scribble scribble-squiggle" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-squiggle-clip-path)" class="scribble-path"
         d="M 207.55019,87.389555 C 76.569696,311.99391 57.909342,399.75673 38.554216,493.49396 122.0427,330.8991 181.21334,138.53703 294.29718,30.200802 229.2164,163.7014 169.67116,281.85814 115.02007,442.73091 174.01295,312.46209 233.33432,199.24878 308.43372,114.37751 281.67061,154.27485 201.50136,359.17532 169.63854,487.71083 234.96652,374.61846 264.42725,235.62198 365.62247,148.43373 l -114.3775,286.58633 c 47.33601,-82.46318 79.17712,-192.04244 142.00803,-247.38955 4.53817,74.75234 -48.6109,151.43239 -71.96787,226.18473 37.9056,-68.3347 65.46371,-150.46604 113.73493,-204.97991 -13.56619,75.03628 -37.51108,129.31518 -57.18875,192.12851 l 93.81526,-152.28915 -53.97591,170.28112 73.25302,-104.09638 c 4.77407,41.49757 -20.97985,75.72064 -35.98394,110.52208" />
      </svg>`;
    },

    tplScribbleCircle(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" class="wttm-scribble scribble-circle" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-circle-clip-path)" class="scribble-path"
         d="M 5.8675652,223.7513 C 29.8029,62.897383 152.50238,26.040296 211.90954,28.739843 c 145.70033,0 166.10898,124.593187 166.10898,187.254677 C 356.7476,340.94531 245.80325,372.53033 204.45444,372.53033 59.105463,369.44535 43.507538,280.41825 24.118564,208.05762" />
      </svg>`;
    },
    tplScribbleCirclethin(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 522.23163 525.4444" class="wttm-scribble scribble-circleThin" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-circleThin-clip-path)" class="scribble-path"
         d="M 26.429579,293.67823 A 234.53815,232.61044 0 0 1 228.07832,32.689965 234.53815,232.61044 0 0 1 491.4228,232.43086 234.53815,232.61044 0 0 1 290.27933,493.80237 234.53815,232.61044 0 0 1 26.548924,294.56297 L 258.95581,263.27576 Z" />
      </svg>`;
    },

    tplScribbleLine(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.874146 238.29044" class="wttm-scribble scribble-line" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-line-clip-path)" class="scribble-path"
         d="M 11.407509,0.58584784 C -3.6095602,79.522236 12.506111,162.69193 5.411485,238.21108" />
      </svg>`;
    },

    tplScribbleCheckmark(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 157.24748 176.96564" class="wttm-scribble scribble-checkmark" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-checkmark-clip-path)" class="scribble-path"
         d="M 0.44462372,70.92492 C 23.1763,98.831672 45.100537,126.89671 59.050848,156.52496 76.947648,104.07125 120.73978,58.261417 152.92656,9.6548999" />
      </svg>`;
    },

    tplScribbleArrow(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" class="wttm-scribble scribble-arrow" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
        <path clip-path="url(#scribble-arrow-clip-path)" class="scribble-path"
         d="M 4.6711468,37.770392 30.106729,8.6026853 37.269727,17.074382 5.4537347,37.774811 8.4858451,10.903111 33.453563,41.036228 Z" />
      </svg>`;
    },

    addScribbleClipPaths() {
      $('game_play_area').insertAdjacentHTML(
        'beforeend',
        `
      <audio id="audiosrc_o_welcometo_scribble" src="${BGA_URL}/img/sounds/scribble.ogg" autobuffer></audio>
      <audio id="audiosrc_welcometo_scribble" src="${BGA_URL}/img/sounds/scribble.mp3" autobuffer></audio>



  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 533.33331 533.33331" width="0" height="0">
      <clipPath id="scribble-squiggle-clip-path">
          <path d="m 32.923611,508.08566 c -3.525347,-1.37744 -7.275347,-3.22284 -8.333333,-4.10089 -2.500036,-2.07484 -2.493339,-13.22218 0.01999,-33.26993 2.828011,-22.55787 5.432635,-33.85848 18.285135,-79.33333 2.6775,-9.47356 7.507377,-23.96985 11.139537,-33.434 5.001867,-13.03312 5.198376,-13.51892 9.667383,-23.89933 2.210018,-5.13333 8.345854,-19.53333 13.63519,-32 9.72597,-22.92356 41.297687,-89.05874 48.374667,-101.33333 2.11404,-3.66667 4.94242,-7.56667 6.2853,-8.66667 2.49272,-2.04186 4.12886,-4.76201 12.02981,-19.99999 15.94965,-30.76096 43.05165,-71.37473 60.5651,-90.76004 4.71314,-5.21687 4.8135,-5.25389 8.20978,-3.02857 4.54054,2.97509 3.72253,7.0516 -4.08867,20.375379 -9.68825,16.525521 -15.85743,30.806451 -14.06897,32.568011 1.1476,1.13032 2.02904,0.83888 3.49258,-1.15478 1.07668,-1.46667 4.45655,-5.59476 7.51082,-9.17353 3.05425,-3.57878 11.382,-15.06358 18.50607,-25.5218 7.1241,-10.45821 21.10384,-30.00872 31.06611,-43.44556 9.96227,-13.43685 18.11321,-25.43989 18.11321,-26.67342 0,-2.879882 8.00017,-9.905978 15.33921,-13.471563 l 5.70978,-2.7740193 4.80852,2.9718303 c 8.71185,5.384223 16.80916,17.227152 16.80916,24.584692 0,3.17056 -16.11668,35.60902 -23.27027,46.8367 -2.33619,3.66667 -7.74531,13.26667 -12.02028,21.33334 -4.27497,8.06666 -9.50924,17.90059 -11.63168,21.85316 -2.12245,3.95257 -5.3444,8.35491 -7.15988,9.78297 -3.47498,2.73341 -16.00816,23.10097 -22.114,35.93726 -1.98134,4.16538 -5.44777,9.50436 -7.70317,11.86443 -4.35263,4.55461 -5.11264,7.68358 -2.50072,10.2955 2.54297,2.54298 3.39775,1.98682 7.27492,-4.73333 2.00971,-3.48333 7.585,-12.21513 12.38955,-19.40398 4.80454,-7.18887 11.22642,-16.97032 14.27086,-21.73659 14.55005,-22.77908 32.62409,-44.27258 40.47943,-48.1378 3.29188,-1.619771 6.40705,-2.969771 6.92261,-3.000001 0.51556,-0.0302 5.1534,3.695041 10.30632,8.278381 9.83107,8.74438 11.16851,11.84132 7.73665,17.91475 -0.90585,1.60311 -3.68486,6.39969 -6.17557,10.65907 -2.49072,4.25937 -9.29688,16.74773 -15.1248,27.75191 -5.82793,11.00418 -11.63813,21.2076 -12.91157,22.67426 -1.27344,1.46667 -3.03844,5.20688 -3.92222,8.31157 -0.89544,3.14571 -2.82188,6.29518 -4.35118,7.11364 -1.51308,0.80978 -5.30247,6.95112 -8.44616,13.68842 -3.136,6.72084 -8.1379,17.46313 -11.11534,23.87177 -2.97742,6.40864 -7.33277,14.32459 -9.67853,17.591 -2.66509,3.71108 -3.94565,6.77116 -3.41368,8.15747 0.49211,1.28241 -0.84194,5.55066 -3.16165,10.11553 -2.20713,4.34336 -4.01297,8.83834 -4.01297,9.98886 0,1.15051 -1.70151,3.68256 -3.78111,5.62679 -4.78809,4.47636 -9.42012,16.7552 -10.31623,27.34684 -0.6875,8.12605 -0.67461,8.16365 1.70228,4.96181 1.31728,-1.77445 6.79744,-11.67445 12.17815,-22 24.15278,-46.34907 39.65166,-73.12447 64.74716,-111.8553 9.05894,-13.981 24.97242,-36.16569 27.43866,-38.25173 0.4335,-0.36667 3.09011,-3.51667 5.90357,-7 2.81348,-3.48333 5.74588,-6.33333 6.51647,-6.33333 0.77059,0 2.34831,-0.84026 3.50605,-1.86722 1.15775,-1.02697 4.41819,-2.82277 7.24544,-3.99065 5.1285,-2.11849 5.14914,-2.11415 8.86828,1.86721 2.05031,2.19487 4.9517,3.99066 6.44752,3.99066 1.49584,0 3.08044,0.58369 3.52136,1.2971 0.44091,0.7134 3.2217,1.94875 6.17954,2.74522 6.84277,1.84256 10.40825,8.6986 7.99474,15.37304 -0.81688,2.25901 -1.48522,5.63097 -1.48522,7.49324 0,1.86226 -1.52966,5.64001 -3.39924,8.39498 -3.58103,5.27695 -3.36854,7.56703 0.47916,5.16411 3.7567,-2.34611 11.21369,-0.39963 16.82674,4.39224 2.882,2.46037 6.29659,4.4734 7.58798,4.4734 1.29138,0 2.7511,0.65227 3.24381,1.44949 0.49271,0.79722 2.0885,1.1376 3.54622,0.7564 2.53536,-0.66301 4.40048,-0.12773 13.98455,4.01361 7.40003,3.1976 12.01679,8.18219 16.94888,18.29924 2.63004,5.39492 4.78189,10.71175 4.78189,11.81518 0,2.56058 2.02076,4.00846 8.66667,6.2097 6.56744,2.17524 22.0482,11.68239 24.39133,14.97936 0.94856,1.33471 3.3681,5.58806 5.37673,9.45188 3.62408,6.97132 3.63602,7.08131 1.55826,14.35847 -2.49133,8.72568 -6.08099,17.11657 -8.53519,19.9512 -0.9756,1.12684 -2.09562,4.5988 -2.48893,7.71546 -0.67472,5.34655 -0.52677,5.66667 2.61895,5.66667 1.83374,0 5.11701,1.05 7.29615,2.33333 3.7734,2.22223 4.02446,2.22223 5.27198,0 0.72044,-1.28333 1.93745,-2.33333 2.70448,-2.33333 3.01069,0 9.29825,5.79875 11.18573,10.31613 2.795,6.68939 2.68995,12.72087 -0.43576,25.0172 -1.49131,5.86667 -3.12843,13.36667 -3.63804,16.66667 -1.22296,7.91902 -4.63264,21.42521 -6.12099,24.24604 -0.69233,1.31212 -0.68921,3.55026 0.008,5.38273 2.4392,6.41559 -4.8344,18.57733 -13.79859,23.07179 -2.04566,1.02565 -2.90014,3.07996 -3.42019,8.22273 -0.42,4.15328 -1.73388,8.01837 -3.33333,9.8057 -3.54668,3.96332 -11.09199,8.62655 -15.86819,9.80702 -2.14212,0.52944 -5.29981,2.06784 -7.01709,3.41865 -4.40942,3.46844 -18.73147,2.34541 -21.96779,-1.72255 -2.21213,-2.78057 -2.21493,-2.78064 -7.16608,-0.17356 -2.72419,1.43447 -6.45808,4.85811 -8.29752,7.60811 -1.83944,2.75 -3.86471,5 -4.5006,5 -2.47082,0 -0.88,-6.2532 2.88323,-11.33333 6.73002,-9.08514 9.98078,-18 6.56362,-18 -1.71637,0 -6.85896,5.43806 -12.34625,13.05564 -4.82841,6.70289 -10.2574,10.34968 -17.37176,11.66904 -4.8365,0.89693 -5.09058,0.77598 -4.66666,-2.22139 0.2466,-1.74348 1.79834,-3.76224 3.44834,-4.48613 1.94075,-0.85146 3.01668,-2.49918 3.04726,-4.66667 0.026,-1.84277 0.90472,-4.85049 1.95274,-6.68383 1.04803,-1.83333 1.92678,-4.08333 1.95275,-5 0.0649,-2.29123 -3.26837,-2.09786 -5.26283,0.30532 -1.2717,1.53231 -2.24729,1.64515 -4.37562,0.5061 -1.54218,-0.82535 -5.48196,-1.14103 -9.01628,-0.72244 -7.1702,0.84921 -10.814,-0.99623 -13.95318,-7.06673 -1.0853,-2.09874 -4.38188,-6.5988 -7.32572,-10.00015 -2.94436,-3.40136 -5.35296,-7.40644 -5.35296,-8.90019 0,-1.49374 2.50535,-7.7719 5.56744,-13.95146 4.22058,-8.51747 5.19363,-11.60938 4.02223,-12.78078 -0.84988,-0.84988 -1.74717,-1.28522 -1.99401,-0.96744 -6.14226,7.90755 -15.25902,23.23878 -23.92075,40.22636 -8.8907,17.43667 -11.89852,22.1292 -16.1657,25.22033 -6.68191,4.84035 -12.84254,5.1591 -12.84254,0.66447 0,-2.9786 -6.21858,-12.94171 -9.24526,-14.81229 -2.72283,-1.6828 -1.37758,-8.03345 5.36377,-25.32108 10.61128,-27.2118 33.37821,-81.09316 47.96121,-113.50744 5.60068,-12.44888 7.98925,-19.69026 6.86551,-20.81401 -2.07295,-2.07294 -8.06167,1.30032 -10.41092,5.86416 -1.24306,2.41484 -5.0631,8.59061 -8.489,13.72395 -3.4259,5.13333 -9.69109,15.93333 -13.92265,24 -4.23158,8.06666 -9.0207,16.46666 -10.64251,18.66666 -1.6218,2.2 -5.00004,8.8 -7.5072,14.66667 -2.50715,5.86667 -6.93475,14.86666 -9.83909,20 -2.90436,5.13333 -6.87392,12.93333 -8.82124,17.33333 -1.94732,4.4 -6.26535,13.7 -9.5956,20.66667 -3.33026,6.96666 -9.22624,19.77221 -13.10218,28.45677 -7.75837,17.38368 -15.06497,28.50986 -19.48925,29.6774 -5.73212,1.51266 -15.12558,-8.39415 -15.12558,-15.9522 0,-3.36455 10.54104,-34.65245 18.70337,-55.51531 2.1518,-5.5 7.13725,-19 11.07877,-30 6.35083,-17.72393 17.69943,-47.28327 20.98795,-54.66666 7.71549,-17.32284 25.2299,-60.06272 25.2299,-61.56774 0,-6.29778 -10.25589,4.3232 -18.89514,19.56774 -2.28574,4.03334 -6.19019,10.93334 -8.67658,15.33333 -2.48637,4.4 -6.92017,12.2 -9.85289,17.33334 -7.63499,13.36406 -31.9433,62.95463 -38.56084,78.66666 -3.08862,7.33333 -6.43573,15.13333 -7.43802,17.33333 -3.06978,6.73808 -9.90044,23.94282 -9.90528,24.94894 -0.003,0.52192 -3.85646,10.12192 -8.5643,21.33333 -4.70785,11.21142 -12.53337,30.5844 -17.39004,43.05106 -9.95247,25.54715 -18.07398,39.33333 -23.17147,39.33333 -8.60503,0 -20.22398,-10.1903 -20.19635,-17.71306 0.0293,-7.99276 11.90093,-50.0466 16.75384,-59.34875 1.03436,-1.98266 1.88435,-4.64792 1.88887,-5.92278 0.0333,-9.39496 39.7863,-115.40656 63.62865,-169.68206 20.98357,-47.76772 22.31416,-51.33333 19.15622,-51.33333 -3.40946,0 -37.11002,58.04048 -41.4189,71.33333 -0.95084,2.93333 -4.38771,10.65573 -7.63747,17.16089 -3.24976,6.50515 -8.62623,18.20515 -11.94769,26 -5.89833,13.84225 -8.21263,19.11253 -14.47476,32.96314 -1.68863,3.73488 -3.97508,9.83768 -5.08101,13.56178 -1.10594,3.72408 -3.85715,10.76013 -6.11382,15.63565 -2.25665,4.87552 -4.10301,9.18204 -4.10301,9.57005 0,0.38802 -0.96117,2.89615 -2.13595,5.57364 -1.17477,2.6775 -2.64892,6.06818 -3.27588,7.53484 -13.84848,32.39608 -22.26306,47.11556 -31.92347,55.84316 -8.10036,7.31819 -11.33474,6.72243 -22.67436,-4.17649 l -9.363897,-9 1.55327,-4.66667 c 0.85429,-2.56667 3.91754,-10.66667 6.807207,-18 2.88966,-7.33333 8.44829,-21.43333 12.35251,-31.33333 3.90421,-9.9 10.21816,-26.4 14.031,-36.66667 3.81283,-10.26666 9.33064,-23.76666 12.26177,-29.99999 2.93113,-6.23334 7.30041,-16.13334 9.70949,-22 2.40908,-5.86667 8.56852,-19.96667 13.68766,-31.33334 5.11913,-11.36666 13.31553,-29.66666 18.21422,-40.66666 4.89871,-11 18.75344,-39.32663 30.78831,-62.94805 12.03486,-23.62143 21.31641,-43.29734 20.62566,-43.72425 -0.69074,-0.4269 -4.12126,3.18763 -7.62338,8.0323 -6.8623,9.49298 -26.17026,43.67166 -28.98376,51.30667 -0.94583,2.56667 -2.93996,6.76666 -4.43143,9.33333 -1.49145,2.56667 -3.62197,7.36667 -4.73446,10.66667 -1.11251,3.3 -6.33103,14.99116 -11.59672,25.98036 -5.2657,10.9892 -10.64396,22.6892 -11.95171,26 -1.30773,3.3108 -3.86681,9.31963 -5.68685,13.35297 -3.57047,7.91244 -13.99788,32.92126 -17.78984,42.66666 -1.28404,3.3 -3.08895,7.2 -4.01091,8.66667 -0.92196,1.46667 -3.27462,7.44999 -5.22815,13.29628 -1.95353,5.84628 -4.65111,13.04628 -5.99462,16 -1.34351,2.9537 -3.42425,8.37038 -4.62386,12.03705 -2.13899,6.53789 -12.65726,32.16631 -18.60547,45.33333 -1.65642,3.66667 -5.825467,13.26667 -9.264557,21.33333 -3.43909,8.06667 -10.0392,22.16667 -14.666898,31.33334 -23.943206,47.42729 -25.20749,49.25058 -34.131583,49.22301 -2.566666,-0.008 -7.551041,-1.14142 -11.076387,-2.51886 z M 473.02321,416.00707 c 0.21294,-1.10576 -0.4935,-3.19887 -1.56987,-4.65135 -1.81184,-2.44493 -2.12115,-2.47929 -4.16869,-0.46309 -1.79802,1.77047 -1.92111,2.72067 -0.6582,5.08044 1.70989,3.19494 5.78385,3.21661 6.39676,0.034 z m 33.3171,-92.62556 c -0.48904,-2.55826 -0.0784,-3.33333 1.76587,-3.33333 3.31661,0 3.92305,-1.45329 2.99438,-7.17593 -0.80165,-4.94004 -5.00424,-8.84508 -7.11822,-6.61426 -0.58558,0.61794 -0.63922,3.52352 -0.1192,6.45686 0.52001,2.93333 0.36884,6.30385 -0.33594,7.49004 -1.48929,2.50661 -0.30874,6.50996 1.91971,6.50996 0.91369,0 1.27379,-1.34352 0.89341,-3.33334 z M 212.06763,229.81728 c 1.79613,-3.427 4.87767,-9.277 6.84785,-13 3.8189,-7.21644 4.52899,-10.10243 2.4857,-10.10243 -2.10734,0 -7.46894,8.20283 -10.69079,16.35605 -1.6712,4.22916 -3.56472,8.01458 -4.20781,8.41202 -1.80754,1.11712 -1.38074,4.56526 0.56505,4.56526 0.95388,0 3.20388,-2.8039 5,-6.2309 z"  />
      </clipPath>
  </svg>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400" width="0" height="0">
        <clipPath id="scribble-circle-clip-path">
            <path d="m 189,383.43727 c -3.72055,-0.87034 -9.70598,-1.11996 -18.5,-0.77151 -12.81927,0.50793 -13.14597,0.46651 -23.5,-2.97954 -14.6569,-4.87815 -23.23391,-8.24518 -27.14949,-10.65793 l -3.35051,-2.06457 5.5,0.60379 c 3.025,0.33209 6.54309,0.2065 7.81798,-0.27908 2.14427,-0.81671 1.91944,-1.06284 -3,-3.28418 -2.92489,-1.32072 -9.66495,-5.01197 -14.97792,-8.20278 C 106.52709,352.61066 101.85663,350 101.46126,350 c -0.39537,0 -2.462195,-1.575 -4.59294,-3.5 -4.641228,-4.19307 -2.401026,-4.90643 2.844206,-0.9057 1.870724,1.42687 4.532284,2.71846 5.914584,2.8702 1.3823,0.15173 3.04419,0.81096 3.69308,1.46494 0.90913,0.91625 0.77831,1.0362 -0.57019,0.52282 -3.26215,-1.24192 -1.77145,0.34489 3,3.19342 2.6125,1.55965 5.44826,2.88668 6.30168,2.94897 1.97158,0.1439 11.51481,3.73255 15.44832,5.80921 1.65,0.8711 3.5625,1.58659 4.25,1.58998 0.6875,0.003 1.25,0.41999 1.25,0.92579 0,2.3585 22.90348,7.70138 36,8.39801 5.225,0.27793 8.63182,0.27163 7.57072,-0.014 -1.0611,-0.28564 -2.55574,-1.14581 -3.32143,-1.91149 C 178.48361,370.62647 176.59699,370 175.0568,370 c -1.54019,0 -7.24555,-1.17511 -12.67858,-2.61135 -5.43302,-1.43625 -13.27738,-3.39574 -17.43191,-4.35443 -12.96395,-2.99154 -17.8445,-5.01362 -22.04962,-9.13548 C 120.52158,351.57065 117.95923,350 116.53633,350 c -1.31059,0 -3.71987,-1.125 -5.35396,-2.5 -1.6341,-1.375 -3.39487,-2.5 -3.91284,-2.5 -1.11951,0 -11.813888,-8.12986 -18.549801,-14.10154 -3.618063,-3.20757 -5.061442,-3.95675 -5.936756,-3.08143 -0.875314,0.87531 -0.661057,1.70674 0.881259,3.41973 1.120759,1.24478 2.319671,3.06115 2.66425,4.03638 0.516273,1.46115 0.308082,1.60272 -1.183232,0.8046 C 82.994053,334.92645 80.415208,332 81.551869,332 c 0.438218,0 -0.0986,-1.0125 -1.192937,-2.25 -2.489683,-2.8154 -6.091998,-8.69502 -4.288749,-7 0.731399,0.6875 1.695462,1.25 2.142362,1.25 1.544578,0 -4.529289,-8.52488 -10.939099,-15.3534 -3.538241,-3.76937 -7.236061,-8.71937 -8.217378,-11 -1.856322,-4.31419 -7.328507,-11.63266 -13.19475,-17.6466 -4.638412,-4.75519 -8.231101,-9.98248 -8.395607,-12.21542 -0.07486,-1.01612 -1.138883,-3.20382 -2.364496,-4.86155 -1.225614,-1.65773 -2.087274,-4.00608 -1.914802,-5.21854 0.263574,-1.85291 0.758895,-1.44693 3.105741,2.54551 1.535684,2.6125 3.223184,4.75 3.75,4.75 C 40.568969,265 41,265.675 41,266.5 c 0,0.825 0.458573,1.5 1.019052,1.5 0.595433,0 0.761141,-1.14317 0.398624,-2.75 -0.833338,-3.6937 -5.855509,-11.8027 -7.14691,-11.53969 -1.835698,0.37387 -4.683263,-3.73735 -5.895538,-8.51177 -1.015728,-4.00035 -0.988706,-4.39042 0.192259,-2.77536 1.206512,1.65 1.299857,1.22047 0.765386,-3.522 -0.334789,-2.97065 -0.996645,-6.1591 -1.47079,-7.08544 -1.412127,-2.75888 -0.973113,-7.12735 1.020083,-10.15048 1.818184,-2.75768 1.684361,-7.32155 -0.309271,-10.54731 -1.251402,-2.02481 1.038366,-1.16459 3.013343,1.13205 C 35.883605,216.08441 41,228.76412 41,233.10141 c 0,0.95983 1.090318,3.04091 2.422928,4.62463 1.332611,1.58371 3.357611,5.65404 4.5,9.04518 C 50.175021,253.45645 50.632361,256.5259 49,254 c -1.640703,-2.53881 -1.123737,-0.16434 1.983352,9.1097 3.982248,11.88622 6.051526,15.71023 10.807193,19.97164 2.198197,1.96974 4.909845,5.34474 6.025884,7.5 C 68.932468,292.7366 72.840075,297.425 76.5,301 c 3.659925,3.575 7.524082,8.17823 8.587016,10.22939 1.062933,2.05117 3.234533,4.40265 4.825776,5.22551 C 92.793206,317.94442 105,329.723 105,331.01284 c 0,1.27984 3.659,3.98716 5.38873,3.98716 0.95219,0 3.77964,1.56406 6.28323,3.47569 2.50359,1.91163 6.18909,3.76787 8.19001,4.12498 2.00092,0.3571 6.56303,2.09326 10.13803,3.85812 3.575,1.76487 8.6375,4.01976 11.25,5.01089 2.6125,0.99112 4.75,2.17934 4.75,2.6405 0,2.06164 6.43667,4.10222 11.4156,3.61902 4.16639,-0.40434 6.07745,0.001 10.5844,2.24669 6.36334,3.17024 15.38613,4.27373 37.5,4.58626 12.26009,0.17327 14.93209,-0.0949 21.5,-2.15798 4.125,-1.29572 7.63577,-1.95942 7.80171,-1.47489 0.52625,1.53662 7.26744,-0.87686 20.91612,-7.48838 7.26981,-3.52155 13.88546,-6.14663 14.70144,-5.83351 0.92221,0.35389 3.14238,-1.13744 5.86731,-3.94119 2.41105,-2.48078 8.1704,-7.34224 12.79857,-10.80323 10.46267,-7.82411 29.46607,-26.74416 37.52755,-37.36297 23.7582,-31.29496 36.39287,-67.0435 36.38328,-102.94289 -0.006,-20.76506 -1.57912,-29.86333 -9.04852,-52.31783 -0.59657,-1.7934 -2.78978,-6.0684 -4.87379,-9.5 -2.08402,-3.43161 -5.0115,-8.65263 -6.5055,-11.60228 -1.49401,-2.94965 -3.52728,-5.57505 -4.51838,-5.83423 -1.80602,-0.47228 -2.83446,-2.09722 -5.02005,-7.93172 -0.64465,-1.72092 -2.04686,-3.52092 -3.11601,-4 -1.06915,-0.47908 -3.63377,-3.12105 -5.69915,-5.87105 -2.06539,-2.75 -5.22703,-5.739657 -7.02588,-6.643683 -4.52341,-2.273282 -14.97454,-12.211699 -11.95585,-11.36932 1.85124,0.516595 1.62507,0.07156 -1.23285,-2.42591 -1.925,-1.682212 -4.625,-3.356073 -6,-3.719691 C 299.22524,70.34316 292.9521,66.990583 288.70944,63.704025 282.2989,58.738141 262.61991,51.787615 243.97848,47.905264 233.74587,45.774173 229.94932,45.516386 209,45.530219 188.51274,45.543748 184.08978,45.836527 174.5,47.813956 154.58432,51.920604 138.10226,58.00734 122.21232,67.12352 107.93349,75.315388 100.94723,80.869109 86,95.910477 55.683987,126.41742 37.824357,156.61896 31.543019,188 30.607254,192.675 28.977263,200.67516 27.920815,205.77814 26.864367,210.88112 26,216.16862 26,217.52814 26,218.88766 25.580324,220 25.067388,220 c -0.512937,0 -1.218262,0.9 -1.567388,2 -1.08816,3.42849 -4.856779,2.5077 -7.162465,-1.75 C 10.30223,209.10514 9.5631603,191.72753 14.242931,171 17.321804,157.36312 20.96924,146.83783 28.327813,130.3557 32.757487,120.43387 34.779159,117.16539 39.374864,112.49567 42.468689,109.35201 45,106.40318 45,105.94271 45,105.07415 62.466976,84.032064 73.93362,71.087006 95.264367,47.00605 105.63035,38.472819 124.5,29.461002 c 15.42529,-7.366853 25.96075,-9.512144 49.5,-14.702545 14.99443,-3.30627 42.50318,-3.297849 59.5,-0.862474 2.78109,0.398486 8.84807,1.702693 13.55127,2.505758 9.66329,1.64999 36.77604,10.975377 47.25989,16.254958 30.96343,15.592931 56.8004,41.707409 73.0586,73.843301 9.0313,17.85124 13.86341,34.18861 18.63352,63 1.55511,9.39285 0.80854,36.42734 -1.35213,48.96281 -5.14424,29.84506 -16.55987,58.83454 -32.63436,82.87345 -5.94427,8.88946 -25.77965,31.5618 -28.34,32.39333 -0.38882,0.12628 -3.06266,1.96694 -5.94187,4.09037 -6.68938,4.93344 -31.67787,21.45798 -36.81461,24.34498 -2.15617,1.21184 -3.92031,2.74662 -3.92031,3.41062 0,0.664 -1.9125,1.99422 -4.25,2.95603 -6.50638,2.67718 -19.06399,6.48776 -19.6032,5.94854 -0.26407,-0.26407 0.15737,-0.49381 0.93653,-0.51054 0.99081,-0.0213 0.87487,-0.34562 -0.38569,-1.07899 -1.16438,-0.67742 -2.22629,-0.70753 -3,-0.0851 -0.6587,0.52993 -2.77264,1.29325 -4.69764,1.69625 -1.925,0.40301 -6.2,1.98539 -9.5,3.51641 -4.99934,2.31942 -7.91794,2.89678 -17.5,3.46187 -7.16234,0.4224 -12.3354,1.21414 -13.71482,2.09907 -2.6282,1.68606 -8.69715,1.63319 -16.28518,-0.14187 z M 187.5,371 c -0.33992,-0.55 -1.46492,-1 -2.5,-1 -1.03508,0 -2.16008,0.45 -2.5,1 -0.36155,0.585 0.67595,1 2.5,1 1.82405,0 2.86155,-0.415 2.5,-1 z m -54.99563,-1.00706 c 0.34231,-0.55389 -0.10054,-1.28448 -0.98412,-1.62354 -1.91464,-0.73472 -2.98171,0.0749 -2.06137,1.56407 0.83038,1.34358 2.23491,1.37101 3.04549,0.0595 z M 110.5,372.15589 C 95.674302,365.01678 73,350.26777 73,347.76317 c 0,-1.96633 3.11408,-0.16634 10,5.78018 C 91.012613,360.46286 98.864906,365 102.82767,365 105.51422,365 123,374.60017 123,376.07515 c 0,1.69369 -2.41825,0.93547 -12.5,-3.91926 z m 2.25,-8.84522 c -0.9625,-0.25152 -1.75,-0.92311 -1.75,-1.4924 0,-1.17754 4.94796,-0.21672 6.45,1.25249 0.98917,0.96756 -1.44092,1.0916 -4.7,0.23991 z m 181.677,-6.83214 c 1.05985,-0.80183 2.17182,-1.21305 2.47104,-0.91382 0.62769,0.62769 -1.9121,2.41268 -3.39804,2.38817 -0.55,-0.009 -0.13285,-0.67253 0.927,-1.47435 z M 156.5,356 c -0.7149,-1.15673 1.88811,-1.15673 5.5,0 2.24559,0.71916 2.12439,0.81078 -1.19098,0.90032 C 158.77898,356.95514 156.83992,356.55 156.5,356 Z M 320,338.20578 c 0,-0.52849 0.7269,-1.56417 1.61534,-2.3015 1.39754,-1.15986 1.56608,-1.08641 1.25,0.54469 C 322.4887,338.39259 320,339.91847 320,338.20578 Z M 54.119931,327.99961 C 52.352133,325.79983 51.323549,324 51.834188,324 c 1.110928,0 6.556447,6.9427 5.933853,7.56529 -0.238664,0.23867 -1.880313,-1.36589 -3.64811,-3.56568 z M 329,330 c 0,-0.55 0.47656,-1 1.05902,-1 0.58246,0 0.7809,0.45 0.44098,1 -0.33992,0.55 -0.81648,1 -1.05902,1 C 329.19844,331 329,330.55 329,330 Z M 68.763434,320.47524 c -2.816468,-2.06642 -5.362621,-4.97353 -6.28094,-7.17138 -2.058376,-4.92639 -6.376991,-10.4871 -7.571331,-9.74896 -0.978468,0.60473 -5.462371,-8.33703 -4.608725,-9.19067 0.258254,-0.25826 1.420855,0.73983 2.583558,2.21797 C 54.048698,298.06033 55,298.8839 55,298.41235 c 0,-0.47155 0.76034,0.25527 1.689645,1.61514 0.929304,1.35988 2.240039,2.8104 2.912743,3.22337 0.672703,0.41297 3.11566,3.56297 5.428792,7 2.313133,3.43703 5.132178,6.98625 6.264546,7.88717 1.132367,0.90092 2.128867,2.58842 2.214443,3.75 0.08558,1.16158 0.118297,2.10081 0.07271,2.08718 -0.04559,-0.0136 -2.214336,-1.58862 -4.819447,-3.49997 z M 44.954163,287.91435 c -0.613912,-1.1471 -0.927843,-2.274 -0.697624,-2.50422 0.230219,-0.23022 0.932031,0.70832 1.559582,2.08564 1.378071,3.02454 0.707091,3.35038 -0.861958,0.41858 z m 4.146228,-0.20983 c -1.264918,-2.10631 -1.243608,-2.12762 0.478332,-0.47833 0.991226,0.94941 1.58698,1.94144 1.323898,2.20452 -0.263082,0.26308 -1.074085,-0.5137 -1.80223,-1.72619 z m 9.164814,-11.17968 c -1.628161,-2.51379 -1.821709,-3.29189 -0.713015,-2.86644 1.641421,0.62987 5.006401,6.34969 3.72349,6.32922 -0.426624,-0.007 -1.781338,-1.56506 -3.010475,-3.46278 z m -2.91346,-7.25649 c -0.165075,-2.67028 -0.08147,-2.65222 1.458787,0.31498 0.805961,1.55264 0.776783,2.08332 -0.114546,2.08327 -0.657792,-3e-5 -1.262701,-1.07925 -1.344241,-2.39825 z M 23.157895,266 c 0,-1.375 0.226973,-1.9375 0.504386,-1.25 0.277412,0.6875 0.277412,1.8125 0,2.5 -0.277413,0.6875 -0.504386,0.125 -0.504386,-1.25 z m 28.156528,-6.75 c -0.342544,-0.9625 -0.811791,-2.2 -1.042772,-2.75 -0.230981,-0.55 0.288405,-0.12649 1.154192,0.94113 1.572088,1.93858 2.046844,3.55887 1.042772,3.55887 -0.292261,0 -0.811648,-0.7875 -1.154192,-1.75 z m -31.623792,-3.93522 c -2.270743,-7.22547 -2.890866,-10.55657 -1.844885,-9.91011 1.155459,0.71411 4.659815,13.4231 3.908445,14.17447 -0.302732,0.30273 -1.231335,-1.61623 -2.06356,-4.26436 z M 16.232003,238 c 0,-1.925 0.205795,-2.7125 0.457323,-1.75 0.251527,0.9625 0.251527,2.5375 0,3.5 -0.251528,0.9625 -0.457323,0.175 -0.457323,-1.75 z"  />
        </clipPath>
    </svg>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 522.23163 525.4444" width="0" height="0">
        <clipPath id="scribble-circleThin-clip-path">
            <path d="m 240.18226,502.50155 c -4.96073,-1.16045 -12.94131,-1.49328 -24.66667,-1.02868 -17.09236,0.67724 -17.52796,0.62201 -31.33333,-3.97272 -18.94132,-6.30409 -30.9209,-10.96459 -35.78061,-13.91994 l -4.04863,-2.46212 10.91462,0.2632 c 6.00305,0.14474 16.74346,1.53069 23.86759,3.07986 24.00096,5.21911 55.57706,6.01196 81.04703,2.03503 15.39446,-2.40372 34.33237,-6.21844 36.33504,-7.31908 1.28426,-0.70581 8.16162,-3.46599 15.28301,-6.13372 7.12139,-2.66773 19.36713,-8.06387 27.21278,-11.9914 7.92563,-3.96757 15.17763,-6.79069 16.3189,-6.35275 1.32585,0.50879 4.1263,-1.34392 7.89904,-5.22577 3.21473,-3.30771 10.89386,-9.78965 17.06476,-14.40431 13.95022,-10.43214 39.28809,-35.65887 50.03673,-49.81729 23.76514,-31.3041 39.87724,-68.10701 46.56388,-106.36023 2.85797,-16.35003 2.10574,-55.97984 -1.35983,-71.63976 -7.96149,-35.97562 -23.54869,-65.89074 -47.54191,-91.24289 -31.06201,-32.82131 -64.43491,-50.51212 -115.14574,-61.03819 -12.88349,-2.67423 -18.47617,-3.04114 -46,-3.01782 -27.30143,0.0231 -33.22059,0.41571 -45.99999,3.05085 -26.55424,5.47553 -48.53032,13.59117 -69.71691,25.74608 -19.03844,10.92249 -28.35345,18.32745 -48.28309,38.38261 -40.418191,40.67274 -64.215121,80.91197 -72.613954,122.78603 -8.975699,44.7501 -9.965467,47.99999 -14.618626,47.99999 -6.9943838,0 -13.2047429,-20.7166 -12.4264269,-41.45223 0.91048,-24.25671 8.0747099,-50.50772 22.7627629,-83.40683 5.906231,-13.2291 8.601794,-17.58708 14.729403,-23.81337 4.1251,-4.19155 7.50018,-8.10007 7.50018,-8.68561 0,-1.17848 15.20436,-19.56925 34.66666,-41.93183 31.257191,-35.91513 45.071841,-47.55342 71.333331,-60.09545 20.56706,-9.82247 34.36668,-13.92125 66,-19.603389 10.82627,-1.9446703 71.33892,-2.8218203 79.33333,-1.1499703 3.66667,0.7668103 11.79743,2.2702603 18.06836,3.3410103 12.88439,2.199989 49.03472,14.633839 63.01318,21.673279 41.28457,20.79057 75.73387,55.60988 97.41147,98.45773 12.04173,23.80166 18.48454,45.58482 24.84469,84 2.07348,12.5238 1.07805,48.56978 -1.80284,65.28374 -9.64868,55.97831 -34.32604,106.47872 -68.76867,140.73006 -16.2749,16.18452 -37.44582,31.51366 -73.7275,53.38356 -15.68803,9.45644 -17.53975,10.23509 -24.66667,10.37235 -10.40301,0.20033 -24.77257,3.40369 -29.31978,6.53616 -6.33915,4.36688 -11.22764,5.59132 -25.71891,6.44192 -8.44006,0.4954 -15.17278,1.56912 -16.95309,2.70364 -3.5092,2.23628 -11.61334,2.16098 -21.71357,-0.20175 z m -75.3275,-17.92577 c 0.45641,-0.73852 -0.13406,-1.71264 -1.31216,-2.16472 -2.55286,-0.97963 -3.97562,0.0999 -2.7485,2.08543 1.10718,1.79144 2.97988,1.82801 4.06066,0.0793 z m -29.33916,2.88393 C 115.748,477.9409 85.515599,458.27555 85.515599,454.93609 c 0,-2.62178 4.15211,-0.22179 13.33333,7.7069 10.683491,9.22602 21.153211,15.27554 26.436891,15.27554 3.58207,0 26.89644,12.80022 26.89644,14.76686 0,2.25825 -3.22433,1.24729 -16.66666,-5.22568 z m 3,-11.79362 c -1.28334,-0.33536 -2.33334,-1.23082 -2.33334,-1.98987 0,-1.57005 6.59728,-0.28896 8.6,1.66999 1.3189,1.29008 -1.92122,1.45546 -6.26666,0.31988 z M 60.342179,428.58467 c -2.35707,-2.93304 -3.72851,-5.33281 -3.04766,-5.33281 1.48124,0 8.74193,9.25693 7.9118,10.08705 -0.31822,0.31823 -2.50708,-1.82118 -4.86414,-4.75424 z M 19.059462,345.91853 c 0,-1.83333 0.302631,-2.58333 0.672515,-1.66667 0.369882,0.91667 0.369882,2.41667 0,3.33334 -0.369884,0.91666 -0.672515,0.16666 -0.672515,-1.66667 z m -4.623018,-14.24696 c -3.027658,-9.63396 -3.854488,-14.07542 -2.459847,-13.21348 1.540612,0.95215 6.213086,17.89747 5.21126,18.89929 -0.403643,0.40364 -1.64178,-2.15497 -2.751413,-5.68581 z M 9.8249402,308.5852 c 0,-2.56667 0.2743928,-3.61667 0.6097638,-2.33333 0.335369,1.28333 0.335369,3.38333 0,4.66666 -0.335371,1.28334 -0.6097638,0.23334 -0.6097638,-2.33333 z"  />
        </clipPath>
    </svg>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 17.874146 238.29044" width="0" height="0">
        <clipPath id="scribble-line-clip-path">
            <path d="M 4.6524392,2.2505752 C 5.9885742,0.36061298 12.89342,-0.73569533 14.693417,0.57012866 c 2.51067,1.82138154 4.12406,10.53973334 2.56988,13.88696434 -4.910112,10.574888 -7.4489658,63.25442 -4.960648,102.930067 1.17946,18.80621 1.424158,35.32691 0.977971,66.027 -0.663698,45.66586 -1.416339,52.6515 -5.8777408,54.55447 -3.026311,1.29082 -7.928712,-1.54088 -7.347445,-4.244 1.753674,-8.15533 1.817118,-64.84854 0.102779,-91.84677 -0.766625,-12.0732 1.336684,-135.1609412 4.494226,-139.6272848 z"  />
        </clipPath>
    </svg>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 157.24748 176.96564" width="0" height="0">
        <clipPath id="scribble-checkmark-clip-path">
            <path d="M 56.56434,173.7067 C 39.610117,150.24872 22.812357,126.55237 8.5450169,101.34135 c -4.284737,-9.454278 5.1453541,3.38731 6.6377751,6.35156 3.580931,8.11017 9.945618,14.10371 14.684456,21.61118 9.695701,14.49773 19.862617,28.67038 30.026242,42.84138 7.307519,-13.49023 15.220872,-26.66717 22.402676,-40.20436 -3.3707,2.97486 -7.913734,12.3903 -11.468296,18.07648 -3.683443,6.43776 -7.366887,12.87552 -11.05033,19.31328 C 40.824595,144.22382 23.301762,117.98042 7.6946368,90.664262 5.3028218,85.299598 -1.8397088,72.513866 0.44462372,70.92492 4.6482676,75.387507 7.2394161,81.705374 12.083982,85.316981 c -2.2703197,-4.349916 2.699993,-9.83683 2.068097,-1.943628 2.214444,7.676523 11.532528,12.275176 14.742154,20.473627 4.843521,6.10561 9.257973,15.71194 14.334892,19.79852 -3.364373,-7.92953 -7.870672,-15.68849 -12.71537,-22.85907 -6.11421,-11.203108 -13.134136,-21.998164 -18.194932,-33.746141 -1.247547,-5.217956 9.062637,13.3608 4.3826,1.709227 -2.195758,-3.576446 -2.137956,-11.365133 0.749881,-3.750927 2.287496,2.812095 4.828715,7.494342 5.09157,9.181473 3.672537,2.577276 7.360563,9.175202 9.715426,14.430015 3.025608,3.399029 4.66175,12.402493 6.40441,13.425033 1.845503,-1.24456 1.699648,8.45456 5.459184,5.45941 -2.412254,-7.05548 -7.503481,-13.253192 -9.060985,-20.492931 6.933282,1.996846 7.769484,13.057341 13.154361,18.060991 2.705504,5.12757 5.827852,10.01648 8.87034,14.94686 5.577875,-7.70424 11.568945,-15.06749 16.428771,-23.274878 9.785898,-14.535875 21.804995,-27.38221 31.867349,-41.731244 7.45644,-10.232898 15.3301,-20.217721 22.92323,-30.298787 1.59503,-6.257713 6.67431,-10.842493 12.62402,-12.222238 C 143.04156,8.2055697 150.47173,2.8535759 155.52094,0 c 0.68525,6.315642 -8.89845,13.731193 -9.33435,18.364899 4.61369,-4.008901 7.04295,-0.739287 3.17202,3.507122 -3.0053,3.486805 -1.17931,6.998546 1.13076,1.583648 1.50798,-4.401894 10.37675,-8.822352 5.09934,-1.03915 -5.91903,12.653821 -14.69433,23.611868 -20.79082,36.154025 -5.06785,9.533364 -12.93374,17.171697 -18.49754,26.419054 -19.376604,28.733352 -36.445835,58.920202 -53.67882,88.956732 -1.985556,4.60542 -3.552387,3.46964 -6.05719,-0.23963 z m 30.07528,-48.90762 c -5.165082,2.94518 -3.760965,8.05727 0,0 z"  />
        </clipPath>
    </svg>

  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 44 44" width="0" height="0">
        <clipPath id="scribble-arrow-clip-path">
            <path d="M 16.120844,41.397667 C 8.7623089,40.670212 2.5887952,39.922129 2.4019251,39.735258 2.0940194,39.427353 4.670804,5.5522215 5.2718962,2.0058144 5.4193194,1.1360264 7.9716497,3.577864 11.379632,7.8491404 l 5.854481,7.3375116 4.132943,-3.866264 c 2.273119,-2.1264444 5.272747,-4.8551005 6.665839,-6.0636798 l 2.532895,-2.1974168 5.434666,7.1262056 5.434666,7.126206 -6.217561,5.718566 C 31.797902,26.17548 29,29.147445 29,29.634636 c 0,0.48719 1.02668,2.006202 2.281511,3.375582 1.254831,1.36938 3.558241,4.177282 5.118689,6.239782 L 39.237378,43 34.368689,42.860155 C 31.69091,42.783241 23.47938,42.125121 16.120844,41.397667 Z"  />
        </clipPath>
  </svg>
`
      );
    },
  });
});
