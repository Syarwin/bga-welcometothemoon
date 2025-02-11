const SCENARIOS_DATA = {"1":{"id":"scenario-1","name":"The Launch","jpgUrl":".\/scenarios\/scenario-1.jpg","sections":[{"id":"numbers","name":"Numbers","modes":["show","add"],"elts":[{"id":1,"x":372,"y":137},{"id":2,"x":422,"y":137},{"id":3,"x":475,"y":137},{"id":4,"x":372,"y":241},{"id":5,"x":425,"y":242},{"id":6,"x":477,"y":241},{"id":7,"x":305,"y":344},{"id":8,"x":357,"y":345},{"id":9,"x":426,"y":346},{"id":10,"x":481,"y":344},{"id":11,"x":540,"y":341},{"id":12,"x":303,"y":449},{"id":13,"x":354,"y":450},{"id":14,"x":432,"y":450},{"id":15,"x":483,"y":449},{"id":16,"x":533,"y":449},{"id":17,"x":321,"y":551},{"id":18,"x":372,"y":552},{"id":19,"x":422,"y":553},{"id":20,"x":471,"y":553},{"id":21,"x":522,"y":552},{"id":22,"x":293,"y":654},{"id":23,"x":347,"y":657},{"id":24,"x":397,"y":660},{"id":25,"x":448,"y":658},{"id":26,"x":500,"y":656},{"id":27,"x":551,"y":653},{"id":28,"x":174,"y":756},{"id":29,"x":224,"y":760},{"id":30,"x":275,"y":762},{"id":31,"x":327,"y":764},{"id":32,"x":378,"y":764},{"id":33,"x":452,"y":763},{"id":34,"x":511,"y":763},{"id":35,"x":584,"y":762},{"id":36,"x":637,"y":761},{"id":37,"x":691,"y":757},{"id":38,"x":212,"y":865},{"id":39,"x":262,"y":869},{"id":40,"x":337,"y":871},{"id":41,"x":386,"y":871},{"id":42,"x":462,"y":871},{"id":43,"x":511,"y":871},{"id":44,"x":581,"y":867},{"id":45,"x":632,"y":864},{"id":46,"x":211,"y":963},{"id":47,"x":263,"y":968},{"id":48,"x":336,"y":970},{"id":49,"x":388,"y":971},{"id":50,"x":459,"y":970},{"id":51,"x":511,"y":970},{"id":52,"x":581,"y":966},{"id":53,"x":632,"y":963}],"eltClass":"slot-number"},{"id":"bonuses","name":"Bonuses","modes":["show","add"],"eltClass":"slot-bonus","elts":[{"id":54,"x":388,"y":99},{"id":55,"x":429,"y":99},{"id":56,"x":468,"y":98},{"id":57,"x":376,"y":200},{"id":58,"x":432,"y":204},{"id":59,"x":493,"y":201},{"id":60,"x":313,"y":303},{"id":61,"x":361,"y":301},{"id":62,"x":450,"y":305},{"id":63,"x":513,"y":303},{"id":64,"x":299,"y":405},{"id":65,"x":344,"y":404},{"id":66,"x":461,"y":404},{"id":67,"x":518,"y":404},{"id":68,"x":330,"y":513},{"id":69,"x":392,"y":517},{"id":70,"x":467,"y":509},{"id":71,"x":524,"y":508},{"id":72,"x":320,"y":614},{"id":73,"x":384,"y":619},{"id":74,"x":456,"y":618},{"id":75,"x":524,"y":615},{"id":76,"x":185,"y":717},{"id":77,"x":243,"y":720},{"id":78,"x":317,"y":721},{"id":79,"x":377,"y":721},{"id":80,"x":476,"y":723},{"id":81,"x":589,"y":720},{"id":82,"x":637,"y":719},{"id":83,"x":685,"y":716},{"id":84,"x":220,"y":830},{"id":85,"x":263,"y":831},{"id":86,"x":341,"y":829},{"id":87,"x":390,"y":829},{"id":88,"x":457,"y":830},{"id":89,"x":504,"y":832},{"id":90,"x":578,"y":825},{"id":91,"x":625,"y":823},{"id":92,"x":214,"y":926},{"id":93,"x":262,"y":928},{"id":94,"x":339,"y":931},{"id":95,"x":389,"y":932},{"id":96,"x":458,"y":934},{"id":97,"x":509,"y":931},{"id":98,"x":585,"y":928},{"id":99,"x":629,"y":923}]},{"id":"rockets","name":"Rockets","modes":["show","add"],"eltClass":"slot-rocket","elts":[{"id":100,"x":863,"y":1051},{"id":101,"x":895,"y":1052},{"id":102,"x":926,"y":1053},{"id":103,"x":956,"y":1052},{"id":104,"x":988,"y":1051},{"id":105,"x":881,"y":996},{"id":106,"x":913,"y":996},{"id":107,"x":942,"y":996},{"id":108,"x":975,"y":996},{"id":109,"x":895,"y":936},{"id":110,"x":927,"y":936},{"id":111,"x":957,"y":935},{"id":112,"x":895,"y":875},{"id":113,"x":927,"y":875},{"id":114,"x":957,"y":873},{"id":115,"x":895,"y":811},{"id":116,"x":928,"y":812},{"id":117,"x":957,"y":810},{"id":118,"x":895,"y":754},{"id":119,"x":926,"y":754},{"id":120,"x":958,"y":754},{"id":121,"x":896,"y":697},{"id":122,"x":928,"y":697},{"id":123,"x":957,"y":698},{"id":124,"x":895,"y":637},{"id":125,"x":927,"y":638},{"id":126,"x":956,"y":637},{"id":127,"x":914,"y":575},{"id":128,"x":943,"y":577},{"id":129,"x":914,"y":519},{"id":130,"x":943,"y":519},{"id":131,"x":890,"y":160},{"id":132,"x":921,"y":159},{"id":133,"x":952,"y":159},{"id":134,"x":981,"y":159},{"id":135,"x":891,"y":113},{"id":136,"x":921,"y":113},{"id":137,"x":952,"y":113},{"id":138,"x":982,"y":112}]},{"id":"scores","name":"Scores","modes":["show","add"],"eltClass":"slot-score","elts":[{"id":139,"x":1033,"y":1046},{"id":140,"x":1034,"y":988},{"id":141,"x":1033,"y":929},{"id":142,"x":1033,"y":868},{"id":143,"x":1033,"y":810},{"id":144,"x":1032,"y":753},{"id":145,"x":1032,"y":693},{"id":146,"x":1032,"y":634},{"id":147,"x":1032,"y":573},{"id":148,"x":1031,"y":515},{"id":149,"x":1032,"y":455},{"id":150,"x":1033,"y":320},{"id":151,"x":1033,"y":130}]},{"id":"arrows","name":"Arrows","modes":["show","add"],"eltClass":"slot-arrow","elts":[{"id":152,"x":463,"y":199},{"id":153,"x":540,"y":298},{"id":154,"x":421,"y":513},{"id":155,"x":415,"y":615},{"id":156,"x":277,"y":714},{"id":157,"x":297,"y":826},{"id":158,"x":535,"y":827}]},{"id":"errors","name":"Errors","modes":["show","add"],"eltClass":"slot-error","elts":[{"id":159,"x":729,"y":334},{"id":160,"x":762,"y":286},{"id":161,"x":796,"y":336},{"id":162,"x":828,"y":285},{"id":163,"x":862,"y":337},{"id":164,"x":896,"y":286},{"id":165,"x":929,"y":336},{"id":166,"x":962,"y":286}]},{"id":"plans","name":"Plans","modes":["show","add"],"eltClass":"slot-plan","elts":[{"id":167,"x":768,"y":28},{"id":168,"x":826,"y":28},{"id":169,"x":883,"y":28}]}]},"2":{"id":"scenario-2","name":"The Journey","jpgUrl":".\/scenarios\/scenario-2.jpg","sections":[{"id":"numbers","name":"Numbers","modes":["show","add","move"],"eltClass":"slot-number","elts":[{"id":1,"x":220,"y":684},{"id":2,"x":284,"y":662},{"id":3,"x":352,"y":654},{"id":4,"x":421,"y":681},{"id":5,"x":468,"y":741},{"id":6,"x":483,"y":825},{"id":7,"x":462,"y":910},{"id":8,"x":400,"y":966},{"id":9,"x":324,"y":987},{"id":10,"x":246,"y":987},{"id":11,"x":172,"y":966},{"id":12,"x":100,"y":925},{"id":13,"x":57,"y":865},{"id":14,"x":35,"y":797},{"id":15,"x":36,"y":713},{"id":16,"x":52,"y":645},{"id":17,"x":83,"y":587},{"id":18,"x":134,"y":540},{"id":19,"x":201,"y":503},{"id":20,"x":271,"y":479},{"id":21,"x":347,"y":460},{"id":22,"x":423,"y":439},{"id":23,"x":499,"y":418},{"id":24,"x":576,"y":393},{"id":25,"x":652,"y":371},{"id":26,"x":728,"y":342},{"id":27,"x":795,"y":321},{"id":28,"x":863,"y":290},{"id":29,"x":919,"y":251},{"id":30,"x":951,"y":187},{"id":31,"x":942,"y":94},{"id":32,"x":874,"y":49},{"id":33,"x":796,"y":53},{"id":34,"x":736,"y":96},{"id":35,"x":743,"y":182},{"id":36,"x":824,"y":209}]},{"id":"scores","name":"Scores","modes":["show","add","move"],"eltClass":"slot-score","elts":[{"id":37,"x":556,"y":1048},{"id":38,"x":640,"y":1049},{"id":39,"x":719,"y":1049},{"id":40,"x":800,"y":1049},{"id":41,"x":878,"y":1049},{"id":42,"x":955,"y":1048},{"id":43,"x":1037,"y":1047}]},{"id":"errors","name":"Errors","modes":["show","add","move"],"eltClass":"slot-error","elts":[{"id":44,"x":962,"y":850},{"id":45,"x":961,"y":902},{"id":46,"x":959,"y":954}]},{"id":"plans","name":"Plans","modes":["show","add","move"],"eltClass":"slot-plan","elts":[{"id":47,"x":554,"y":858},{"id":48,"x":553,"y":918},{"id":49,"x":554,"y":979}]},{"id":"robots","name":"Robots","modes":["show","add","move"],"eltClass":"slot-robot","elts":[{"id":50,"x":515,"y":794},{"id":51,"x":565,"y":735},{"id":52,"x":26,"y":605},{"id":53,"x":23,"y":527},{"id":54,"x":66,"y":462},{"id":55,"x":346,"y":414},{"id":56,"x":367,"y":325},{"id":57,"x":435,"y":279},{"id":58,"x":991,"y":87},{"id":59,"x":1027,"y":158},{"id":60,"x":1033,"y":229},{"id":61,"x":1018,"y":302}]},{"id":"waters","name":"Waters","modes":["show","add","move"],"eltClass":"slot-water","elts":[{"id":62,"x":288,"y":595},{"id":63,"x":510,"y":695},{"id":64,"x":484,"y":977},{"id":65,"x":130,"y":1025},{"id":66,"x":83,"y":749},{"id":67,"x":174,"y":595},{"id":68,"x":237,"y":558},{"id":69,"x":509,"y":483},{"id":70,"x":743,"y":408},{"id":71,"x":886,"y":177},{"id":72,"x":939,"y":17},{"id":73,"x":729,"y":245}]},{"id":"plants","name":"Plants","modes":["show","add","move"],"eltClass":"slot-plant","elts":[{"id":74,"x":663,"y":555},{"id":75,"x":704,"y":581},{"id":76,"x":667,"y":627},{"id":77,"x":605,"y":646},{"id":78,"x":598,"y":590},{"id":79,"x":135,"y":365},{"id":80,"x":141,"y":284},{"id":81,"x":199,"y":328},{"id":82,"x":517,"y":266},{"id":83,"x":507,"y":210},{"id":84,"x":573,"y":172},{"id":85,"x":580,"y":244},{"id":86,"x":952,"y":424},{"id":87,"x":944,"y":471},{"id":88,"x":888,"y":456},{"id":89,"x":848,"y":401}]},{"id":"walls","name":"Walls","modes":["show","add","move","rotate"],"eltClass":"slot-wall","elts":[{"id":90,"x":274,"y":666,"r":-15},{"id":91,"x":341,"y":651},{"id":92,"x":408,"y":661,"r":15},{"id":93,"x":472,"y":703,"r":44},{"id":94,"x":498,"y":775,"r":-106},{"id":95,"x":493,"y":866,"r":-75},{"id":96,"x":454,"y":939,"r":-35},{"id":97,"x":383,"y":977,"r":-15},{"id":98,"x":308,"y":986},{"id":99,"x":231,"y":973,"r":10},{"id":100,"x":162,"y":944,"r":35},{"id":101,"x":105,"y":892,"r":55},{"id":102,"x":66,"y":827,"r":70},{"id":103,"x":52,"y":748,"r":-85},{"id":104,"x":64,"y":672,"r":-70},{"id":105,"x":90,"y":611,"r":-65},{"id":106,"x":132,"y":560,"r":-40},{"id":107,"x":190,"y":515,"r":-25},{"id":108,"x":258,"y":484,"r":-20},{"id":109,"x":331,"y":465,"r":-15},{"id":110,"x":406,"y":445,"r":-15},{"id":111,"x":484,"y":423,"r":-15},{"id":112,"x":558,"y":401,"r":-15},{"id":113,"x":633,"y":378,"r":-20},{"id":114,"x":711,"y":350,"r":-15},{"id":115,"x":784,"y":329,"r":-20},{"id":116,"x":850,"y":301,"r":-20},{"id":117,"x":915,"y":268,"r":-30},{"id":118,"x":959,"y":217,"r":-60},{"id":119,"x":989,"y":135,"r":-100},{"id":120,"x":937,"y":62,"r":35},{"id":121,"x":856,"y":38},{"id":122,"x":784,"y":63,"r":-35},{"id":123,"x":746,"y":138,"r":-90},{"id":124,"x":806,"y":209,"r":10}]},{"id":"bigmultipliers","name":"Bigmultipliers","modes":["show","add","move"],"eltClass":"slot-bigmultiplier","elts":[{"id":125,"x":630,"y":691},{"id":126,"x":58,"y":375},{"id":127,"x":494,"y":306},{"id":128,"x":1009,"y":366}]},{"id":"smallmultipliers","name":"Smallmultipliers","modes":["show","add","move"],"eltClass":"slot-smallmultiplier","elts":[{"id":129,"x":626,"y":728},{"id":130,"x":54,"y":412},{"id":131,"x":491,"y":342},{"id":132,"x":1003,"y":405}]},{"id":"energymarkers","name":"Energymarkers","modes":["show","add","move"],"eltClass":"slot-energymarker","elts":[{"id":133,"x":153,"y":21},{"id":134,"x":153,"y":68},{"id":135,"x":148,"y":149},{"id":136,"x":213,"y":21},{"id":137,"x":211,"y":66},{"id":138,"x":211,"y":150},{"id":139,"x":278,"y":20},{"id":140,"x":276,"y":65},{"id":141,"x":273,"y":149},{"id":142,"x":338,"y":22},{"id":143,"x":337,"y":66},{"id":144,"x":336,"y":151},{"id":145,"x":397,"y":20},{"id":146,"x":399,"y":62},{"id":147,"x":398,"y":149},{"id":148,"x":459,"y":19},{"id":149,"x":459,"y":63},{"id":150,"x":459,"y":149}]},{"id":"othermarkers","name":"Othermarkers","modes":["show","add","move"],"eltClass":"slot-othermarker","elts":[{"id":151,"x":837,"y":586},{"id":152,"x":836,"y":624},{"id":153,"x":832,"y":706},{"id":154,"x":873,"y":587},{"id":155,"x":873,"y":625},{"id":156,"x":870,"y":705},{"id":157,"x":909,"y":587},{"id":158,"x":907,"y":624},{"id":159,"x":909,"y":707},{"id":160,"x":977,"y":583},{"id":161,"x":977,"y":623},{"id":162,"x":974,"y":706},{"id":163,"x":1012,"y":584},{"id":164,"x":1011,"y":621},{"id":165,"x":1012,"y":707},{"id":166,"x":1049,"y":584},{"id":167,"x":1049,"y":620},{"id":168,"x":1049,"y":706}]},{"id":"subscores","name":"Subscores","modes":["show","add","move"],"eltClass":"slot-subscore","elts":[{"id":169,"x":646,"y":867},{"id":170,"x":646,"y":910},{"id":171,"x":645,"y":956},{"id":172,"x":645,"y":1001}]}]}};