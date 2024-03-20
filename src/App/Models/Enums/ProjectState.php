<?php
namespace Src\App\Models;

enum ProjectState {
  case development;
  case production;
  case finished;
  case testing;
  

  public static function from(string $role): ProjectState {
    return match ($role) {
      'development' => Self::development,
      'production' => Self::production,
      'finished' => Self::finished,
      'testing' => Self::testing,
      default => Self::development,
    };
  }
};
