<?php

namespace MSAR\InteractiveModel\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InteractiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interactive with application models';

    protected $model;
    protected $modelWithSingleData;
    protected $attributes = [];

    protected $fileSystem;

    protected $availableModels;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $fileSystem)
    {
        parent::__construct();
        $this->fileSystem = $fileSystem;

        $this->availableModels = $this->getAvailableModels();
    }

    public function getAvailableModels(): \Illuminate\Support\Collection
    {
        $models = [];
        $files = $this->fileSystem->files(app_path('Models'));
        foreach ($files as $file) {
            $model = str_replace('.php', '', $file);
            $model = str_replace(app_path('Models/'), '', $model);
            $models[$model] = "App\\Models\\".$model;
        }
        return collect($models);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Ask what command
        $this->runTheCommand();
        return 0;
    }

    public function runTheCommand()
    {
        $modelName = $this->choice('Which model do you want to interact?', $this->availableModels->toArray());
        $this->model = $this->availableModels[$modelName];
        $this->model = new $this->model;

        $this->info('You are interacting with '.$modelName);
        $this->interactWithModel();

        $this->info('-----------------------------------------------------');
        if( $this->confirm('Do you want to interact with another model?') ) {
            $this->runTheCommand();
        }
    }

    public function interactWithModel()
    {
        $method = $this->choice('What do you want to do?', ['Create', 'Read', 'Update', 'Delete']);
        switch ($method) {
            case 'Create':
                $this->create();
                break;
            case 'Read':
                $this->read();
                break;
            case 'Update':
                $this->update();
                break;
            case 'Delete':
                $this->delete();
                break;
        }
    }

    public function create()
    {
        $this->info('Creating a new '.$this->model->getTable());
        try {
            $this->model->create($this->getAttributes());
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    public function getSearchableColumns()
    {
        $searchableColumns = [];
        $columns = $this->ask('What columns do you want to search? (comma separated)');
        if( empty($columns) ) {
            return $searchableColumns;
        }
        $columns = array_map('trim', explode(',', $columns));
        foreach ($columns as $column) {
            $searchableColumns[$column] = $this->ask('What is the value for '.$column.'?');
        }
        return $searchableColumns;
    }

    public function read()
    {
        $this->info('Reading data of '.$this->model->getTable());
        $instance = $this->model;
        try {
            // ask for limit or single record
            $limit = $this->askWithCompletion('How many records do you want to read?', ['single', 'multiple']);
            if( $limit == 'single' ) {
                $this->info('Reading a single record');
                $searchableColumns = $this->getSearchableColumns();
                if( count($searchableColumns) > 0 ) {
                    $instance->where($searchableColumns);
                }
                $result = $instance->first();
                $this->modelWithSingleData = $result;
            } else {
                $this->info('Reading '.$limit.' records');
                if( !is_numeric($limit) ){
                    $limit = 10;
                }
                $searchableColumns = $this->getSearchableColumns();
                if (count($searchableColumns) > 0) {
                    $instance->where($searchableColumns);
                }
                $result = $instance->limit($limit)->get();
            }

            $this->info('Result:');
            dump($result->toArray());

            $this->interactWithItems($result);
            
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    public function interactWithItems($items)
    {
        $method = $this->choice('What do you want to do?', ['Read', 'Update', 'Delete']);
        switch ($method) {
            case 'Read':
                $this->readItems($items);
                break;
            case 'Update':
                $this->updateItems($items);
                break;
            case 'Delete':
                $this->deleteItems($items);
                break;
        }
    }

    

    public function update()
    {
        $this->info('Updating a '.$this->model->getTable());
        try {
            $result = $this->model->where($this->getSearchableColumns())->first();            
            $this->info('Updating the record');
            $result->update($this->getAttributes());
            $this->info('Record updated');
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    public function delete()
    {
        $this->info('Deleting a '.$this->model->getTable());
        try {
            $result = $this->model->where($this->getSearchableColumns())->first();
            $this->info('Deleting the record');
            // confirm before deleting
            if( $this->confirm('Are you sure you want to delete this record?') ) {
                $result->delete();
                $this->info('Record deleted');
            }
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    public function getAttributes()
    {
        $attributes = $keys = [];
        $onlyFillable = $this->confirm('Do you want to fill only fillable attributes?', true);
        if($onlyFillable) {
            $keys = $this->model->getFillable();
        } else {
            $keys = $this->ask('Enter the attributes you want to fill separated by comma', '');
            // explode and trim the keys
            $keys = array_map('trim', explode(',', $keys));
        }
        foreach ($keys as $attribute) {
            $attributes[$attribute] = $this->ask($attribute);
        }
        return $attributes;
    }
}
