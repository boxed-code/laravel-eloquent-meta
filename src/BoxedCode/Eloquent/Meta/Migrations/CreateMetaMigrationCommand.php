<?php

/*
 * This file is part of Mailable.
 *
 * (c) Oliver Green <oliver@mailable.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BoxedCode\Eloquent\Meta\Migrations;

use Illuminate\Database\Console\Migrations\MigrateMakeCommand;

class CreateMetaMigrationCommand extends MigrateMakeCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'make:meta-migration {model_name? : The name of the model to create a meta table for.}
        {--path= : The location where the migration file should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new meta migration file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $file_name = $this->makeMigration();

        $this->composer->dumpAutoloads();

        $this->info("Migration [$file_name] created successfully.");
    }

    /**
     * Assemble variables.
     *
     * @return void
     */
    protected function makeVars()
    {
        $name = $this->input->getArgument('model_name');

        return [
            'path'        => $this->getMigrationPath(),
            'name'        => $name,
            'table_name'  => $name ? $name . '_meta' : 'meta',
            'file_name'   => $name ? $name . '_meta_migration' : 'meta_migration',
        ];
    }

    /**
     * Replace the stubs placeholders.
     *
     * @param  string $template   [description]
     * @param  string $name       [description]
     * @param  string $table_name [description]
     * @return string
     */
    protected function replacePlaceholders($template, $name, $table_name)
    {
        $replacements = [
            'DummyClass' => ucfirst($name) . 'MetaMigration',
            'DummyTable' => $table_name,
        ];

        return str_replace(array_keys($replacements), $replacements, $template);
    }

    /**
     * Get the stub.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR);
    }

    /**
     * Assemble and write the migration file.
     *
     * @return string
     */
    protected function makeMigration()
    {
        extract($this->makeVars());

        $migration = $this->replacePlaceholders($this->getStub(), $name, $table_name);

        file_put_contents($this->getPath($file_name, $path), $migration);

        return $file_name;
    }

    /**
     * Get the full path name to the migration.
     *
     * @param  string  $name
     * @param  string  $path
     * @return string
     */
    protected function getPath($name, $path)
    {
        return $path.'/'.$this->getDatePrefix().'_'.$name.'.php';
    }

    /**
     * Get the date prefix for the migration.
     *
     * @return string
     */
    protected function getDatePrefix()
    {
        return date('Y_m_d_His');
    }
}
