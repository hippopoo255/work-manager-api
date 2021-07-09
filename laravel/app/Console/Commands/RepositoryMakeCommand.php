<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand as Command;
use Illuminate\Support\Facades\Artisan;

class RepositoryMakeCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
//  protected $signature = 'command:name';
  protected $name = 'make:repository';
  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a new Repository class';
  protected $type = 'Repository';

  public function handle()
  {
    Artisan::call('make:repositoryInterface', ['name' => $this->getNameInput() . 'Interface']);
    $this->info($this->getNameInput().' Interface created successfully.');
    parent::handle();
  }

  /**
   * Generator側がクラス生成に使うnamespaceを決めてあげる
   * @param string $rootNamespace
   * @return string
   */
  protected function getDefaultNamespace($rootNamespace)
  {
    return $rootNamespace . '\Repositories';
  }

  
  /**
   * 生成に使うスタブファイルを取得する
   * @return string
   */
  protected function getStub()
  {
    return __DIR__.'/stubs/repository.stub';
  }

  /**
   * 指定された名前でクラスを構築する
   *
   * @param  string  $name
   * @return string
   */
  protected function buildClass($name)
  {
    $modelName = str_replace($this->type, '', $this->getNameInput());
    // 引数やオプションなどを使い'置換前'=>'置換後'のような配列を作る
    $replace = [
      'DummyModel' => $modelName,
    ];

    return str_replace(
      array_keys($replace),
      array_values($replace),
      parent::buildClass($name)
    );
  }
}
