define(['dojo', 'dojo/_base/declare'], (dojo, declare) => {
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
        debug(container, o.parentNode);
        if (o.parentNode != $(container)) {
          dojo.place(o, container);
        }
        o.dataset.state = scribble.state;

        return scribble.id;
      });
      document.querySelectorAll('.wttm-scribble[id^="scribble-"]').forEach((oScribble) => {
        if (!scribbleIds.includes(oScribble.getAttribute('data-id'))) {
          this.destroy(oScribble);
        }
      });
    },

    addScribble(scribble, animation) {
      if ($(`scribble-${scribble.id}`)) return;

      let container = this.getScribbleContainer(scribble);
      var scribbleTpl = 'tplScribble';

      // Number
      if ([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 100].includes(scribble.type)) {
        scribbleTpl = 'tplScribbleNumber';
      }
      // if (scribble.type == 'pool') scribbleTpl = 'scribbleCircle';
      // if (scribble.type == 'estate-fence') scribbleTpl = 'scribbleLine';
      // if (scribble.type == 'top-fence') scribbleTpl = 'scribbleLineHor';

      this.place(scribbleTpl, scribble, container);
      if (animation) {
        //        playSound('welcometo_scribble');
        $(`scribble-${scribble.id}`).classList.add('animate');
      }
    },

    notif_addScribble(args) {
      debug('Notif: add scribble', args);
      this.addScribble(args.scribble, true);
      return this.wait(800);
    },

    getScribbleContainer(scribble) {
      let t = scribble.location.split('-');
      let pId = scribble.id.split('-')[0];

      if (t[0] == 'slot') {
        return $(`slot-${pId}-${t[1]}`);
      }

      console.error('Trying to get container of a scribble', scribble);
      return 'game_play_area';
    },

    tplScribble(scribble) {
      return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 533.33331 533.33331" class="wttm-scribble" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">
      <path
         clip-path="url(#scribble-clip-path)"
         fill="none" stroke-miterlimit="0" stroke-width="70"
         class="scribble-path"
         d="M 207.55019,87.389555 C 76.569696,311.99391 57.909342,399.75673 38.554216,493.49396 122.0427,330.8991 181.21334,138.53703 294.29718,30.200802 229.2164,163.7014 169.67116,281.85814 115.02007,442.73091 174.01295,312.46209 233.33432,199.24878 308.43372,114.37751 281.67061,154.27485 201.50136,359.17532 169.63854,487.71083 234.96652,374.61846 264.42725,235.62198 365.62247,148.43373 l -114.3775,286.58633 c 47.33601,-82.46318 79.17712,-192.04244 142.00803,-247.38955 4.53817,74.75234 -48.6109,151.43239 -71.96787,226.18473 37.9056,-68.3347 65.46371,-150.46604 113.73493,-204.97991 -13.56619,75.03628 -37.51108,129.31518 -57.18875,192.12851 l 93.81526,-152.28915 -53.97591,170.28112 73.25302,-104.09638 c 4.77407,41.49757 -20.97985,75.72064 -35.98394,110.52208"
       />
      </svg>
      `;
    },

    tplScribbleNumber(scribble) {
      let number = scribble.type;
      if (number == 100) number = 'X';
      return `<div class="wttm-scribble scribble-number" data-turn="${scribble.turn}" id="scribble-${scribble.id}" data-id="${scribble.id}">${number}</div>`;
    },
  });
});
