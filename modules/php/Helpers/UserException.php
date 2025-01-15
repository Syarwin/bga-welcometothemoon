<?php

namespace Bga\Games\WelcomeToTheMoon\Helpers;

use Bga\Games\WelcomeToTheMoon\Game;

class UserException extends \BgaUserException
{
  public function __construct($str)
  {
    parent::__construct(Game::get()::translate($str));
  }
}
