const SCENARIOS_DATA = {"1":{"id":"scenario-1","name":"The Launch","jpgUrl":".\/scenarios\/scenario-1.jpg","overview":[{"name":"numbers","type":"\/","icon":"slot-number"},{"name":"stack-A","type":"checkmark","icon":"stack-A"},{"name":"stack-B","type":"checkmark","icon":"stack-B"},{"name":"stack-C","type":"checkmark","icon":"stack-C"},{"name":"rockets","type":"\/","icon":"rocket"},{"name":"errors","type":"points","icon":"system-error"}],"sections":[{"id":"numbers","name":"Numbers","modes":["show","add"],"elts":[{"id":1,"x":372,"y":137},{"id":2,"x":422,"y":137},{"id":3,"x":475,"y":137},{"id":4,"x":372,"y":241},{"id":5,"x":425,"y":242},{"id":6,"x":477,"y":241},{"id":7,"x":305,"y":344},{"id":8,"x":357,"y":345},{"id":9,"x":426,"y":346},{"id":10,"x":481,"y":344},{"id":11,"x":540,"y":341},{"id":12,"x":303,"y":449},{"id":13,"x":354,"y":450},{"id":14,"x":432,"y":450},{"id":15,"x":483,"y":449},{"id":16,"x":533,"y":449},{"id":17,"x":321,"y":551},{"id":18,"x":372,"y":552},{"id":19,"x":422,"y":553},{"id":20,"x":471,"y":553},{"id":21,"x":522,"y":552},{"id":22,"x":293,"y":654},{"id":23,"x":347,"y":657},{"id":24,"x":397,"y":660},{"id":25,"x":448,"y":658},{"id":26,"x":500,"y":656},{"id":27,"x":551,"y":653},{"id":28,"x":174,"y":756},{"id":29,"x":224,"y":760},{"id":30,"x":275,"y":762},{"id":31,"x":327,"y":764},{"id":32,"x":378,"y":764},{"id":33,"x":452,"y":763},{"id":34,"x":511,"y":763},{"id":35,"x":584,"y":762},{"id":36,"x":637,"y":761},{"id":37,"x":691,"y":757},{"id":38,"x":212,"y":865},{"id":39,"x":262,"y":869},{"id":40,"x":337,"y":871},{"id":41,"x":386,"y":871},{"id":42,"x":462,"y":871},{"id":43,"x":511,"y":871},{"id":44,"x":581,"y":867},{"id":45,"x":632,"y":864},{"id":46,"x":211,"y":963},{"id":47,"x":263,"y":968},{"id":48,"x":336,"y":970},{"id":49,"x":388,"y":971},{"id":50,"x":459,"y":970},{"id":51,"x":511,"y":970},{"id":52,"x":581,"y":966},{"id":53,"x":632,"y":963}],"eltClass":"slot-number"},{"id":"bonuses","name":"Bonuses","modes":["show","move","add"],"eltClass":"slot-bonus","elts":[{"id":54,"x":388,"y":99},{"id":55,"x":429,"y":99},{"id":56,"x":468,"y":98},{"id":57,"x":376,"y":200},{"id":58,"x":432,"y":204},{"id":59,"x":494,"y":201},{"id":60,"x":313,"y":303},{"id":61,"x":361,"y":301},{"id":62,"x":450,"y":305},{"id":63,"x":513,"y":303},{"id":64,"x":299,"y":405},{"id":65,"x":345,"y":406},{"id":66,"x":461,"y":404},{"id":67,"x":518,"y":404},{"id":68,"x":330,"y":513},{"id":69,"x":391,"y":519},{"id":70,"x":469,"y":512},{"id":71,"x":524,"y":512},{"id":72,"x":320,"y":614},{"id":73,"x":385,"y":623},{"id":74,"x":459,"y":618},{"id":75,"x":525,"y":616},{"id":76,"x":185,"y":717},{"id":77,"x":245,"y":722},{"id":78,"x":319,"y":721},{"id":79,"x":377,"y":721},{"id":80,"x":476,"y":723},{"id":81,"x":593,"y":721},{"id":82,"x":637,"y":719},{"id":83,"x":685,"y":716},{"id":84,"x":220,"y":830},{"id":85,"x":265,"y":832},{"id":86,"x":343,"y":830},{"id":87,"x":390,"y":829},{"id":88,"x":457,"y":830},{"id":89,"x":503,"y":834},{"id":90,"x":580,"y":827},{"id":91,"x":627,"y":824},{"id":92,"x":216,"y":928},{"id":93,"x":263,"y":929},{"id":94,"x":339,"y":931},{"id":95,"x":389,"y":932},{"id":96,"x":458,"y":934},{"id":97,"x":509,"y":931},{"id":98,"x":585,"y":928},{"id":99,"x":629,"y":923}]},{"id":"rockets","name":"Rockets","modes":["show","add"],"eltClass":"slot-rocket","elts":[{"id":100,"x":863,"y":1051},{"id":101,"x":895,"y":1052},{"id":102,"x":926,"y":1053},{"id":103,"x":956,"y":1052},{"id":104,"x":988,"y":1051},{"id":105,"x":881,"y":996},{"id":106,"x":913,"y":996},{"id":107,"x":942,"y":996},{"id":108,"x":975,"y":996},{"id":109,"x":895,"y":936},{"id":110,"x":927,"y":936},{"id":111,"x":957,"y":935},{"id":112,"x":895,"y":875},{"id":113,"x":927,"y":875},{"id":114,"x":957,"y":873},{"id":115,"x":895,"y":811},{"id":116,"x":928,"y":812},{"id":117,"x":957,"y":810},{"id":118,"x":895,"y":754},{"id":119,"x":926,"y":754},{"id":120,"x":958,"y":754},{"id":121,"x":896,"y":697},{"id":122,"x":928,"y":697},{"id":123,"x":957,"y":698},{"id":124,"x":895,"y":637},{"id":125,"x":927,"y":638},{"id":126,"x":956,"y":637},{"id":127,"x":914,"y":575},{"id":128,"x":943,"y":577},{"id":129,"x":914,"y":519},{"id":130,"x":943,"y":519},{"id":131,"x":890,"y":160},{"id":132,"x":921,"y":159},{"id":133,"x":952,"y":159},{"id":134,"x":981,"y":159},{"id":135,"x":891,"y":113},{"id":136,"x":921,"y":113},{"id":137,"x":952,"y":113},{"id":138,"x":982,"y":112}]},{"id":"scores","name":"Scores","modes":["show","move","add"],"eltClass":"slot-score","elts":[{"id":139,"x":1033,"y":1046},{"id":140,"x":1034,"y":988},{"id":141,"x":1033,"y":929},{"id":142,"x":1033,"y":868},{"id":143,"x":1033,"y":810},{"id":144,"x":1032,"y":753},{"id":145,"x":1032,"y":693},{"id":146,"x":1032,"y":634},{"id":147,"x":1032,"y":573},{"id":148,"x":1031,"y":515},{"id":149,"x":1032,"y":455},{"id":150,"x":1033,"y":320},{"id":151,"x":1033,"y":130}]},{"id":"arrows","name":"Arrows","modes":["show","add"],"eltClass":"slot-arrow","elts":[{"id":152,"x":463,"y":199},{"id":153,"x":540,"y":298},{"id":154,"x":421,"y":513},{"id":155,"x":415,"y":615},{"id":156,"x":277,"y":714},{"id":157,"x":297,"y":826},{"id":158,"x":535,"y":827}]},{"id":"errors","name":"Errors","modes":["show","move","add"],"eltClass":"slot-error","elts":[{"id":159,"x":727,"y":334},{"id":160,"x":762,"y":286},{"id":161,"x":795,"y":336},{"id":162,"x":828,"y":285},{"id":163,"x":862,"y":335},{"id":164,"x":896,"y":286},{"id":165,"x":929,"y":334},{"id":166,"x":962,"y":286}]},{"id":"plans","name":"Plans","modes":["show","add"],"eltClass":"slot-plan","elts":[{"id":167,"x":768,"y":28},{"id":168,"x":826,"y":28},{"id":169,"x":883,"y":28}]}]},"2":{"id":"scenario-2","name":"The Journey","jpgUrl":".\/scenarios\/scenario-2.jpg","overview":[{"name":"numbers","type":"\/","icon":"slot-number"},{"name":"stack-A","type":"points-or-dash","icon":"stack-A"},{"name":"stack-B","type":"points-or-dash","icon":"stack-B"},{"name":"stack-C","type":"points-or-dash","icon":"stack-C"},{"name":"waters","type":"points","icon":"water"},{"name":"longest-section","type":"points","icon":"longest-section"},{"name":"most-sections","type":"points","icon":"most-sections"},{"name":"errors","type":"points","icon":"system-error"}],"sections":[{"id":"numbers","name":"Numbers","modes":["show","add","move"],"eltClass":"slot-number","elts":[{"id":1,"x":220,"y":684},{"id":2,"x":284,"y":662},{"id":3,"x":352,"y":654},{"id":4,"x":421,"y":681},{"id":5,"x":468,"y":741},{"id":6,"x":483,"y":825},{"id":7,"x":462,"y":910},{"id":8,"x":400,"y":966},{"id":9,"x":324,"y":987},{"id":10,"x":246,"y":987},{"id":11,"x":172,"y":966},{"id":12,"x":100,"y":925},{"id":13,"x":57,"y":865},{"id":14,"x":35,"y":797},{"id":15,"x":36,"y":713},{"id":16,"x":52,"y":645},{"id":17,"x":83,"y":587},{"id":18,"x":134,"y":540},{"id":19,"x":201,"y":503},{"id":20,"x":271,"y":479},{"id":21,"x":347,"y":460},{"id":22,"x":423,"y":439},{"id":23,"x":499,"y":418},{"id":24,"x":576,"y":393},{"id":25,"x":652,"y":371},{"id":26,"x":728,"y":342},{"id":27,"x":795,"y":321},{"id":28,"x":863,"y":290},{"id":29,"x":919,"y":251},{"id":30,"x":951,"y":187},{"id":31,"x":942,"y":94},{"id":32,"x":874,"y":49},{"id":33,"x":796,"y":53},{"id":34,"x":736,"y":96},{"id":35,"x":743,"y":182},{"id":36,"x":824,"y":209}]},{"id":"scores","name":"Scores","modes":["show","add","move"],"eltClass":"slot-score","elts":[{"id":37,"x":556,"y":1048},{"id":38,"x":640,"y":1049},{"id":39,"x":719,"y":1049},{"id":40,"x":800,"y":1049},{"id":41,"x":878,"y":1049},{"id":42,"x":955,"y":1048},{"id":43,"x":1037,"y":1047}]},{"id":"errors","name":"Errors","modes":["show","add","move"],"eltClass":"slot-error","elts":[{"id":44,"x":962,"y":850},{"id":45,"x":961,"y":902},{"id":46,"x":959,"y":954}]},{"id":"plans","name":"Plans","modes":["show","add","move"],"eltClass":"slot-plan","elts":[{"id":47,"x":554,"y":858},{"id":48,"x":553,"y":918},{"id":49,"x":554,"y":979}]},{"id":"robots","name":"Robots","modes":["show","add","move"],"eltClass":"slot-robot","elts":[{"id":50,"x":515,"y":794},{"id":51,"x":565,"y":735},{"id":52,"x":26,"y":605},{"id":53,"x":23,"y":527},{"id":54,"x":66,"y":462},{"id":55,"x":346,"y":414},{"id":56,"x":367,"y":325},{"id":57,"x":435,"y":279},{"id":58,"x":991,"y":87},{"id":59,"x":1027,"y":158},{"id":60,"x":1033,"y":229},{"id":61,"x":1018,"y":302}]},{"id":"waters","name":"Waters","modes":["show","add","move"],"eltClass":"slot-water","elts":[{"id":62,"x":288,"y":595},{"id":63,"x":510,"y":695},{"id":64,"x":484,"y":977},{"id":65,"x":130,"y":1025},{"id":66,"x":83,"y":749},{"id":67,"x":174,"y":595},{"id":68,"x":237,"y":558},{"id":69,"x":509,"y":483},{"id":70,"x":743,"y":408},{"id":71,"x":886,"y":177},{"id":72,"x":939,"y":17},{"id":73,"x":729,"y":245}]},{"id":"plants","name":"Plants","modes":["show","add","move"],"eltClass":"slot-plant","elts":[{"id":74,"x":663,"y":555},{"id":75,"x":704,"y":581},{"id":76,"x":667,"y":627},{"id":77,"x":605,"y":646},{"id":78,"x":598,"y":590},{"id":79,"x":135,"y":365},{"id":80,"x":141,"y":284},{"id":81,"x":199,"y":328},{"id":82,"x":517,"y":266},{"id":83,"x":507,"y":210},{"id":84,"x":573,"y":172},{"id":85,"x":580,"y":244},{"id":86,"x":952,"y":424},{"id":87,"x":944,"y":471},{"id":88,"x":888,"y":456},{"id":89,"x":848,"y":401}]},{"id":"walls","name":"Walls","modes":["show","add","move","rotate"],"eltClass":"slot-wall","elts":[{"id":90,"x":274,"y":666,"r":-15},{"id":91,"x":341,"y":651},{"id":92,"x":408,"y":661,"r":15},{"id":93,"x":472,"y":703,"r":44},{"id":94,"x":498,"y":775,"r":-106},{"id":95,"x":493,"y":866,"r":-75},{"id":96,"x":454,"y":939,"r":-35},{"id":97,"x":383,"y":977,"r":-15},{"id":98,"x":308,"y":986},{"id":99,"x":231,"y":973,"r":10},{"id":100,"x":162,"y":944,"r":35},{"id":101,"x":105,"y":892,"r":55},{"id":102,"x":66,"y":827,"r":70},{"id":103,"x":52,"y":748,"r":-85},{"id":104,"x":64,"y":672,"r":-70},{"id":105,"x":90,"y":611,"r":-65},{"id":106,"x":132,"y":560,"r":-40},{"id":107,"x":190,"y":515,"r":-25},{"id":108,"x":258,"y":484,"r":-20},{"id":109,"x":331,"y":465,"r":-15},{"id":110,"x":406,"y":445,"r":-15},{"id":111,"x":484,"y":423,"r":-15},{"id":112,"x":558,"y":401,"r":-15},{"id":113,"x":633,"y":378,"r":-20},{"id":114,"x":711,"y":350,"r":-15},{"id":115,"x":784,"y":329,"r":-20},{"id":116,"x":850,"y":301,"r":-20},{"id":117,"x":915,"y":268,"r":-30},{"id":118,"x":959,"y":217,"r":-60},{"id":119,"x":989,"y":135,"r":-100},{"id":120,"x":937,"y":62,"r":35},{"id":121,"x":856,"y":38},{"id":122,"x":784,"y":63,"r":-35},{"id":123,"x":746,"y":138,"r":-90},{"id":124,"x":806,"y":209,"r":10}]},{"id":"bigmultipliers","name":"Bigmultipliers","modes":["show","add","move"],"eltClass":"slot-bigmultiplier","elts":[{"id":125,"x":630,"y":691},{"id":126,"x":58,"y":375},{"id":127,"x":494,"y":306},{"id":128,"x":1009,"y":366}]},{"id":"smallmultipliers","name":"Smallmultipliers","modes":["show","add","move"],"eltClass":"slot-smallmultiplier","elts":[{"id":129,"x":626,"y":728},{"id":130,"x":54,"y":412},{"id":131,"x":491,"y":342},{"id":132,"x":1003,"y":405}]},{"id":"energymarkers","name":"Energymarkers","modes":["show","add","move"],"eltClass":"slot-energymarker","elts":[{"id":133,"x":153,"y":21},{"id":134,"x":153,"y":68},{"id":135,"x":148,"y":149},{"id":136,"x":213,"y":21},{"id":137,"x":211,"y":66},{"id":138,"x":211,"y":150},{"id":139,"x":278,"y":20},{"id":140,"x":276,"y":65},{"id":141,"x":273,"y":149},{"id":142,"x":338,"y":22},{"id":143,"x":337,"y":66},{"id":144,"x":336,"y":151},{"id":145,"x":397,"y":20},{"id":146,"x":399,"y":62},{"id":147,"x":398,"y":149},{"id":148,"x":459,"y":19},{"id":149,"x":459,"y":63},{"id":150,"x":459,"y":149}]},{"id":"othermarkers","name":"Othermarkers","modes":["show","add","move"],"eltClass":"slot-othermarker","elts":[{"id":151,"x":837,"y":586},{"id":152,"x":836,"y":624},{"id":153,"x":832,"y":706},{"id":154,"x":873,"y":587},{"id":155,"x":873,"y":625},{"id":156,"x":870,"y":705},{"id":157,"x":909,"y":587},{"id":158,"x":907,"y":624},{"id":159,"x":909,"y":707},{"id":160,"x":977,"y":583},{"id":161,"x":977,"y":623},{"id":162,"x":974,"y":706},{"id":163,"x":1012,"y":584},{"id":164,"x":1011,"y":621},{"id":165,"x":1012,"y":707},{"id":166,"x":1049,"y":584},{"id":167,"x":1049,"y":620},{"id":168,"x":1049,"y":706}]},{"id":"subscores","name":"Subscores","modes":["show","add","move"],"eltClass":"slot-subscore","elts":[{"id":169,"x":646,"y":867},{"id":170,"x":646,"y":910},{"id":171,"x":645,"y":956},{"id":172,"x":645,"y":1001}]}]},"3":{"id":"scenario-3","name":"The Colony","jpgUrl":".\/scenarios\/scenario-3.jpg","overview":[{"name":"numbers","type":"\/","icon":"slot-number"},{"name":"stack-A","type":"points-or-dash","icon":"stack-A"},{"name":"stack-B","type":"points-or-dash","icon":"stack-B"},{"name":"stack-C","type":"points-or-dash","icon":"stack-C"},{"name":"plants","type":"points","icon":"plant"},{"name":"waters","type":"points","icon":"water"},{"name":"antennas","type":"points","icon":"antenna"},{"name":"quarters","type":"points","icon":"quarter"},{"name":"astronaut","type":"points","icon":"astronaut"},{"name":"planning","type":"points","icon":"planning"},{"name":"errors","type":"points","icon":"system-error"}],"sections":[{"id":"numbers","name":"Numbers","modes":["show","add","move"],"eltClass":"slot-number","elts":[{"id":1,"x":244,"y":530},{"id":2,"x":243,"y":414},{"id":3,"x":245,"y":299},{"id":4,"x":245,"y":180},{"id":5,"x":245,"y":66},{"id":6,"x":361,"y":648},{"id":7,"x":359,"y":529},{"id":8,"x":361,"y":418},{"id":9,"x":359,"y":295},{"id":10,"x":362,"y":180},{"id":11,"x":360,"y":65},{"id":12,"x":478,"y":649},{"id":13,"x":479,"y":533},{"id":14,"x":478,"y":418},{"id":15,"x":480,"y":179},{"id":16,"x":478,"y":67},{"id":17,"x":595,"y":648},{"id":18,"x":593,"y":531},{"id":19,"x":593,"y":298},{"id":20,"x":593,"y":180},{"id":21,"x":594,"y":64},{"id":22,"x":709,"y":649},{"id":23,"x":709,"y":532},{"id":24,"x":708,"y":415},{"id":25,"x":708,"y":297},{"id":26,"x":709,"y":177},{"id":27,"x":710,"y":64},{"id":28,"x":827,"y":649},{"id":29,"x":827,"y":531},{"id":30,"x":826,"y":417},{"id":31,"x":826,"y":299},{"id":32,"x":828,"y":179}]},{"id":"scores","name":"Scores","modes":["show","add","move"],"eltClass":"slot-score","elts":[{"id":33,"x":54,"y":1049},{"id":34,"x":155,"y":1048},{"id":35,"x":260,"y":1049},{"id":36,"x":363,"y":1049},{"id":37,"x":504,"y":1050},{"id":38,"x":669,"y":1049},{"id":39,"x":790,"y":1049},{"id":40,"x":899,"y":1050},{"id":41,"x":1009,"y":1048}]},{"id":"errors","name":"Errors","modes":["show","add","move"],"eltClass":"slot-error","elts":[{"id":42,"x":902,"y":847},{"id":43,"x":903,"y":897},{"id":44,"x":902,"y":947}]},{"id":"plans","name":"Plans","modes":["show","add","move"],"eltClass":"slot-plan","elts":[{"id":45,"x":53,"y":832},{"id":46,"x":53,"y":901},{"id":47,"x":53,"y":969}]},{"id":"subscores","name":"Subscores","modes":["show","add","move"],"eltClass":"slot-subscore","elts":[{"id":48,"x":160,"y":997},{"id":49,"x":267,"y":997},{"id":50,"x":371,"y":996},{"id":51,"x":470,"y":929},{"id":52,"x":525,"y":929},{"id":53,"x":470,"y":974},{"id":54,"x":526,"y":973}]},{"id":"bigmultipliers","name":"Bigmultipliers","modes":["show","add","move"],"eltClass":"slot-bigmultiplier","elts":[{"id":55,"x":46,"y":113},{"id":56,"x":1024,"y":114},{"id":57,"x":47,"y":470},{"id":58,"x":1024,"y":468}]},{"id":"smallmultipliers","name":"Smallmultipliers","modes":["show","add","move"],"eltClass":"slot-smallmultiplier","elts":[{"id":59,"x":65,"y":151},{"id":60,"x":1020,"y":150},{"id":61,"x":66,"y":505},{"id":62,"x":1020,"y":506}]},{"id":"plants","name":"Plants","modes":["show","add","move"],"eltClass":"slot-plant","elts":[{"id":63,"x":145,"y":56},{"id":64,"x":144,"y":123},{"id":65,"x":144,"y":196},{"id":66,"x":143,"y":262},{"id":67,"x":143,"y":298},{"id":68,"x":929,"y":57},{"id":69,"x":928,"y":123},{"id":70,"x":928,"y":190},{"id":71,"x":928,"y":263},{"id":72,"x":928,"y":295},{"id":73,"x":144,"y":409},{"id":74,"x":143,"y":477},{"id":75,"x":143,"y":543},{"id":76,"x":142,"y":615},{"id":77,"x":143,"y":649},{"id":78,"x":929,"y":407},{"id":79,"x":928,"y":476},{"id":80,"x":927,"y":544},{"id":81,"x":929,"y":616},{"id":82,"x":928,"y":648}]},{"id":"planningmarkers","name":"Planningmarkers","modes":["show","add","move"],"eltClass":"slot-planningmarker","elts":[{"id":83,"x":782,"y":853},{"id":84,"x":819,"y":852},{"id":85,"x":782,"y":888},{"id":86,"x":819,"y":889},{"id":87,"x":783,"y":924},{"id":88,"x":819,"y":927},{"id":89,"x":783,"y":961},{"id":90,"x":818,"y":963},{"id":91,"x":782,"y":998}]},{"id":"astronautmarkers","name":"Astronautmarkers","modes":["show","add","move"],"eltClass":"slot-astronautmarker","elts":[{"id":92,"x":641,"y":832},{"id":93,"x":673,"y":832},{"id":94,"x":708,"y":831},{"id":95,"x":640,"y":877},{"id":96,"x":674,"y":877},{"id":97,"x":709,"y":878},{"id":98,"x":640,"y":925},{"id":99,"x":674,"y":926},{"id":100,"x":709,"y":925}]},{"id":"multmarkers","name":"Multmarkers","modes":["show","add","move"],"eltClass":"slot-multmarker","elts":[{"id":101,"x":165,"y":828},{"id":102,"x":166,"y":863},{"id":103,"x":165,"y":900},{"id":104,"x":166,"y":934},{"id":105,"x":270,"y":827},{"id":106,"x":270,"y":863},{"id":107,"x":271,"y":897},{"id":108,"x":271,"y":932},{"id":109,"x":373,"y":827},{"id":110,"x":376,"y":863},{"id":111,"x":375,"y":899},{"id":112,"x":373,"y":932}]},{"id":"waters","name":"Waters","modes":["show","add","move"],"eltClass":"slot-water","elts":[{"id":113,"x":280,"y":99},{"id":114,"x":512,"y":216},{"id":115,"x":743,"y":215},{"id":116,"x":858,"y":332},{"id":117,"x":395,"y":452},{"id":118,"x":512,"y":563},{"id":119,"x":744,"y":563},{"id":120,"x":628,"y":683}]},{"id":"antennas","name":"Antennas","modes":["show","add","move"],"eltClass":"slot-antenna","elts":[{"id":121,"x":316,"y":24},{"id":122,"x":554,"y":24},{"id":123,"x":441,"y":138},{"id":124,"x":668,"y":140},{"id":125,"x":202,"y":254},{"id":126,"x":557,"y":258},{"id":127,"x":323,"y":376},{"id":128,"x":671,"y":379},{"id":129,"x":209,"y":492},{"id":130,"x":557,"y":491},{"id":131,"x":790,"y":490},{"id":132,"x":324,"y":610},{"id":133,"x":672,"y":610},{"id":134,"x":787,"y":24},{"id":135,"x":875,"y":28},{"id":136,"x":874,"y":95}]},{"id":"tunnels","name":"Tunnels","modes":["show","add","move","rotate"],"eltClass":"slot-tunnel","elts":[{"id":137,"x":266,"y":468},{"id":138,"x":266,"y":350},{"id":139,"x":267,"y":235},{"id":140,"x":266,"y":118},{"id":141,"x":382,"y":584},{"id":142,"x":383,"y":468},{"id":143,"x":382,"y":349},{"id":144,"x":382,"y":232},{"id":145,"x":383,"y":116},{"id":146,"x":502,"y":585},{"id":147,"x":502,"y":469},{"id":148,"x":500,"y":352},{"id":149,"x":500,"y":230},{"id":150,"x":501,"y":117},{"id":151,"x":616,"y":584},{"id":152,"x":616,"y":469},{"id":153,"x":619,"y":354},{"id":154,"x":619,"y":235},{"id":155,"x":615,"y":116},{"id":156,"x":729,"y":586},{"id":157,"x":729,"y":468},{"id":158,"x":730,"y":351},{"id":159,"x":731,"y":232},{"id":160,"x":731,"y":115},{"id":161,"x":849,"y":583},{"id":162,"x":848,"y":469},{"id":163,"x":849,"y":354},{"id":164,"x":848,"y":232},{"id":165,"x":848,"y":113},{"id":166,"x":324,"y":527,"r":-90},{"id":167,"x":325,"y":415,"r":-90},{"id":168,"x":327,"y":295,"r":-90},{"id":169,"x":329,"y":173,"r":-90},{"id":170,"x":330,"y":58,"r":-90},{"id":171,"x":443,"y":645,"r":-90},{"id":172,"x":440,"y":528,"r":-90},{"id":173,"x":441,"y":414,"r":-90},{"id":174,"x":442,"y":294,"r":-90},{"id":175,"x":443,"y":175,"r":-90},{"id":176,"x":442,"y":61,"r":-90},{"id":177,"x":559,"y":645,"r":-90},{"id":178,"x":560,"y":527,"r":-90},{"id":179,"x":559,"y":413,"r":-90},{"id":180,"x":558,"y":291,"r":-90},{"id":181,"x":559,"y":172,"r":-90},{"id":182,"x":561,"y":60,"r":-90},{"id":183,"x":676,"y":646,"r":-90},{"id":184,"x":675,"y":529,"r":-90},{"id":185,"x":672,"y":413,"r":-90},{"id":186,"x":669,"y":293,"r":-90},{"id":187,"x":666,"y":173,"r":-90},{"id":188,"x":675,"y":60,"r":-90},{"id":189,"x":789,"y":646,"r":-90},{"id":190,"x":790,"y":529,"r":-90},{"id":191,"x":791,"y":412,"r":-90},{"id":192,"x":790,"y":294,"r":-90},{"id":193,"x":790,"y":176,"r":-90},{"id":194,"x":790,"y":59,"r":-90}]}]},"4":{"id":"scenario-4","name":"The Mine","jpgUrl":".\/scenarios\/scenario-4.jpg","overview":[{"name":"numbers","type":"\/","icon":"slot-number"},{"name":"stack-A","type":"points-or-dash","icon":"stack-A"},{"name":"stack-B","type":"points-or-dash","icon":"stack-B"},{"name":"stack-C","type":"points-or-dash","icon":"stack-C"},{"name":"plants","type":"points","icon":"plant"},{"name":"waters","type":"points","icon":"water"},{"name":"astronaut","type":"points","icon":"astronaut"},{"name":"planning","type":"points","icon":"planning"},{"name":"errors","type":"points","icon":"system-error"}],"sections":[{"id":"numbers","name":"Numbers","modes":["show","add","move"],"eltClass":"slot-number","elts":[{"id":1,"x":33,"y":767},{"id":2,"x":123,"y":768},{"id":3,"x":212,"y":766},{"id":4,"x":300,"y":766},{"id":5,"x":391,"y":765},{"id":6,"x":481,"y":765},{"id":7,"x":572,"y":765},{"id":8,"x":661,"y":765},{"id":9,"x":753,"y":765},{"id":10,"x":844,"y":766},{"id":11,"x":933,"y":766},{"id":12,"x":1024,"y":766},{"id":13,"x":30,"y":898},{"id":14,"x":122,"y":900},{"id":15,"x":212,"y":903},{"id":16,"x":302,"y":902},{"id":17,"x":392,"y":902},{"id":18,"x":482,"y":900},{"id":19,"x":574,"y":901},{"id":20,"x":663,"y":901},{"id":21,"x":751,"y":901},{"id":22,"x":844,"y":902},{"id":23,"x":933,"y":900},{"id":24,"x":1022,"y":902},{"id":25,"x":31,"y":1036},{"id":26,"x":122,"y":1037},{"id":27,"x":210,"y":1036},{"id":28,"x":302,"y":1037},{"id":29,"x":392,"y":1036},{"id":30,"x":484,"y":1037},{"id":31,"x":573,"y":1036},{"id":32,"x":663,"y":1037},{"id":33,"x":752,"y":1037},{"id":34,"x":843,"y":1036},{"id":35,"x":933,"y":1038},{"id":36,"x":1024,"y":1037}]},{"id":"scores","name":"Scores","modes":["show","add","move"],"eltClass":"slot-score","elts":[{"id":37,"x":51,"y":582},{"id":38,"x":166,"y":582},{"id":39,"x":328,"y":582},{"id":40,"x":483,"y":583},{"id":41,"x":645,"y":582},{"id":42,"x":806,"y":582},{"id":43,"x":940,"y":582},{"id":44,"x":1029,"y":582}]},{"id":"errors","name":"Errors","modes":["show","add","move"],"eltClass":"slot-error","elts":[{"id":45,"x":940,"y":106},{"id":46,"x":967,"y":133},{"id":47,"x":940,"y":161}]},{"id":"plans","name":"Plans","modes":["show","add","move"],"eltClass":"slot-plan","elts":[{"id":48,"x":50,"y":370},{"id":49,"x":49,"y":439},{"id":50,"x":50,"y":509}]},{"id":"subscores","name":"Subscores","modes":["show","add","move"],"eltClass":"slot-subscore","elts":[{"id":51,"x":138,"y":497},{"id":52,"x":215,"y":498},{"id":53,"x":296,"y":495},{"id":54,"x":376,"y":496},{"id":55,"x":455,"y":496},{"id":56,"x":533,"y":498},{"id":57,"x":616,"y":496},{"id":58,"x":693,"y":497},{"id":59,"x":775,"y":531},{"id":60,"x":851,"y":532},{"id":61,"x":913,"y":532},{"id":62,"x":974,"y":532},{"id":228,"x":182,"y":542},{"id":229,"x":344,"y":542},{"id":230,"x":499,"y":543},{"id":231,"x":660,"y":543}]},{"id":"robotmarkers","name":"Robotmarkers","modes":["show","add","move"],"eltClass":"slot-robotmarker","elts":[{"id":63,"x":193,"y":76},{"id":64,"x":358,"y":61},{"id":65,"x":359,"y":95},{"id":66,"x":515,"y":59},{"id":67,"x":514,"y":95},{"id":68,"x":675,"y":76},{"id":69,"x":212,"y":314},{"id":70,"x":213,"y":344},{"id":71,"x":213,"y":376},{"id":72,"x":373,"y":341},{"id":73,"x":373,"y":372},{"id":74,"x":530,"y":369},{"id":75,"x":691,"y":345},{"id":76,"x":689,"y":375},{"id":77,"x":849,"y":404},{"id":78,"x":970,"y":406}]},{"id":"energymarkers","name":"Energymarkers","modes":["show","add","move"],"eltClass":"slot-energymarker","elts":[{"id":79,"x":148,"y":78},{"id":80,"x":306,"y":84},{"id":81,"x":462,"y":85},{"id":82,"x":625,"y":78},{"id":83,"x":801,"y":63},{"id":84,"x":837,"y":67},{"id":85,"x":213,"y":280},{"id":86,"x":372,"y":306},{"id":87,"x":530,"y":305},{"id":88,"x":533,"y":335},{"id":89,"x":692,"y":279},{"id":90,"x":690,"y":314},{"id":91,"x":848,"y":370},{"id":92,"x":968,"y":368}]},{"id":"waters","name":"Waters","modes":["show","add","move"],"eltClass":"slot-water","elts":[{"id":93,"x":291,"y":214},{"id":94,"x":316,"y":240},{"id":95,"x":289,"y":265},{"id":96,"x":314,"y":291},{"id":97,"x":290,"y":317},{"id":98,"x":315,"y":346},{"id":99,"x":289,"y":371},{"id":100,"x":315,"y":396},{"id":101,"x":290,"y":427},{"id":102,"x":314,"y":454}]},{"id":"plants","name":"Plants","modes":["show","add","move"],"eltClass":"slot-plant","elts":[{"id":103,"x":121,"y":211},{"id":104,"x":151,"y":225},{"id":105,"x":121,"y":250},{"id":106,"x":150,"y":263},{"id":107,"x":119,"y":290},{"id":108,"x":150,"y":303},{"id":109,"x":119,"y":329},{"id":110,"x":150,"y":346},{"id":111,"x":121,"y":368},{"id":112,"x":150,"y":386},{"id":113,"x":120,"y":408},{"id":114,"x":149,"y":423},{"id":115,"x":120,"y":449},{"id":116,"x":151,"y":467}]},{"id":"pearls","name":"Pearls","modes":["show","add","move"],"eltClass":"slot-pearl","elts":[{"id":117,"x":445,"y":214},{"id":118,"x":471,"y":225},{"id":119,"x":444,"y":253},{"id":120,"x":472,"y":264},{"id":121,"x":444,"y":292},{"id":122,"x":472,"y":303},{"id":123,"x":443,"y":332},{"id":124,"x":471,"y":344},{"id":125,"x":444,"y":372},{"id":126,"x":472,"y":384},{"id":127,"x":444,"y":411},{"id":128,"x":471,"y":422},{"id":129,"x":443,"y":451},{"id":130,"x":472,"y":463}]},{"id":"rubies","name":"Rubies","modes":["show","add","move"],"eltClass":"slot-ruby","elts":[{"id":131,"x":601,"y":214},{"id":132,"x":631,"y":223},{"id":133,"x":599,"y":248},{"id":134,"x":628,"y":257},{"id":135,"x":600,"y":281},{"id":136,"x":629,"y":293},{"id":137,"x":600,"y":316},{"id":138,"x":628,"y":327},{"id":139,"x":600,"y":351},{"id":140,"x":629,"y":361},{"id":141,"x":600,"y":384},{"id":142,"x":630,"y":395},{"id":143,"x":600,"y":417},{"id":144,"x":630,"y":428},{"id":145,"x":601,"y":453},{"id":146,"x":629,"y":461}]},{"id":"bonuses","name":"Bonuses","modes":["show","add","move"],"eltClass":"slot-bonus","elts":[{"id":147,"x":166,"y":138},{"id":148,"x":197,"y":138},{"id":149,"x":326,"y":147},{"id":150,"x":358,"y":146},{"id":151,"x":482,"y":146},{"id":152,"x":515,"y":146},{"id":153,"x":644,"y":139},{"id":154,"x":676,"y":137},{"id":155,"x":793,"y":144},{"id":156,"x":820,"y":115},{"id":157,"x":845,"y":144}]},{"id":"resources","name":"Resources","modes":["show","add","move"],"eltClass":"slot-resource","elts":[{"id":158,"x":18,"y":747},{"id":159,"x":36,"y":844},{"id":160,"x":19,"y":885},{"id":161,"x":38,"y":979},{"id":162,"x":18,"y":1017},{"id":163,"x":106,"y":747},{"id":164,"x":128,"y":842},{"id":165,"x":126,"y":979},{"id":166,"x":107,"y":1023},{"id":167,"x":215,"y":842},{"id":168,"x":199,"y":883},{"id":169,"x":200,"y":1022},{"id":170,"x":283,"y":748},{"id":171,"x":307,"y":843},{"id":172,"x":307,"y":977},{"id":173,"x":288,"y":1023},{"id":174,"x":380,"y":885},{"id":175,"x":397,"y":978},{"id":176,"x":376,"y":1014},{"id":177,"x":466,"y":746},{"id":178,"x":484,"y":841},{"id":179,"x":467,"y":1017},{"id":180,"x":560,"y":747},{"id":181,"x":559,"y":881},{"id":182,"x":578,"y":974},{"id":183,"x":650,"y":745},{"id":184,"x":667,"y":838},{"id":185,"x":649,"y":881},{"id":186,"x":670,"y":979},{"id":187,"x":738,"y":743},{"id":188,"x":757,"y":837},{"id":189,"x":758,"y":974},{"id":190,"x":737,"y":1023},{"id":191,"x":833,"y":746},{"id":192,"x":848,"y":838},{"id":193,"x":829,"y":881},{"id":194,"x":852,"y":973},{"id":195,"x":918,"y":745},{"id":196,"x":936,"y":839},{"id":197,"x":935,"y":976},{"id":198,"x":919,"y":1017},{"id":199,"x":1010,"y":745},{"id":200,"x":1029,"y":838},{"id":201,"x":1013,"y":882},{"id":202,"x":1031,"y":979},{"id":203,"x":1006,"y":1018}]},{"id":"astronautmarkers","name":"Astronautmarkers","modes":["show","add","move"],"eltClass":"slot-astronautmarker","elts":[{"id":204,"x":766,"y":278},{"id":205,"x":789,"y":307},{"id":206,"x":767,"y":337},{"id":207,"x":788,"y":366},{"id":208,"x":767,"y":396},{"id":209,"x":789,"y":425},{"id":210,"x":766,"y":451},{"id":211,"x":788,"y":482}]},{"id":"planningmarkers","name":"Planningmarkers","modes":["show","add","move"],"eltClass":"slot-planningmarker","elts":[{"id":212,"x":918,"y":279},{"id":213,"x":918,"y":321},{"id":214,"x":917,"y":360},{"id":215,"x":917,"y":399},{"id":216,"x":917,"y":441},{"id":217,"x":917,"y":482}]},{"id":"factorymults","name":"Factorymults","modes":["show","add","move"],"eltClass":"slot-factorymult","elts":[{"id":218,"x":213,"y":234},{"id":219,"x":370,"y":233},{"id":220,"x":530,"y":234},{"id":221,"x":690,"y":234},{"id":222,"x":845,"y":287},{"id":223,"x":969,"y":287}]},{"id":"factorybonuses","name":"Factorybonuses","modes":["show","add","move"],"eltClass":"slot-factorybonus","elts":[{"id":224,"x":174,"y":337},{"id":225,"x":334,"y":331},{"id":226,"x":492,"y":374},{"id":227,"x":653,"y":390}]},{"id":"extractors","name":"Extractors","modes":["show","add","move"],"eltClass":"slot-extractor","elts":[{"id":232,"x":30,"y":635},{"id":233,"x":117,"y":645},{"id":234,"x":207,"y":648},{"id":235,"x":294,"y":646},{"id":236,"x":379,"y":643},{"id":237,"x":476,"y":643},{"id":238,"x":566,"y":648},{"id":239,"x":655,"y":650},{"id":240,"x":753,"y":645},{"id":241,"x":839,"y":648},{"id":242,"x":928,"y":643},{"id":243,"x":1022,"y":638}]}]}};