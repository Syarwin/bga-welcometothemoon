<?php

namespace Bga\Games\WelcomeToTheMoon\Models\Scoresheets;

use Bga\Games\WelcomeToTheMoon\Core\Globals;
use Bga\Games\WelcomeToTheMoon\Core\Notifications;
use Bga\Games\WelcomeToTheMoon\Managers\Players;
use Bga\Games\WelcomeToTheMoon\Models\Scoresheet;

include_once dirname(__FILE__) . "/../../Material/Scenario2.php";


class Scoresheet2 extends Scoresheet
{
  protected int $scenario = 2;
  protected array $datas = DATAS2;
}
