<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Mmal\OpenapiValidator\Validator;
use Symfony\Component\Yaml\Yaml;
use Artisan;

abstract class TestCase extends BaseTestCase
{
  use CreatesApplication;

  /**
   * @var User
   */
  protected $user;
  /**
   * @var Validator
   */
  static $openApiValidator;

  static public function setUpBeforeClass(): void
  {
    parent::setUpBeforeClass();
    self::$openApiValidator = new Validator(Yaml::parse(file_get_contents(__DIR__ . '/../openapi.yaml')));
  }

  // static public function tearDownAfterClass(): void
  // {
  //   parent::tearDownAfterClass();
  //   Artisan::call('migrate:reset');
  // }

  protected function setUp(): void
  {
    parent::setUp();
    $this->user = User::orderBy('id')->first();
  }
}
