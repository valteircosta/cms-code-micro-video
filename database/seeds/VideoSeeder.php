<?php

use App\Models\Genre;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    private $allGenres;
    private $relations = [
        'genres_id' => [],
        'categories_id' => [],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dir = \Storage::getDriver()->getAdapter()->getPathPrefix();
        \File::deleteDirectory($dir, true);

        $self = $this;
        $this->allgenres = Genre::all();
        Model::reguard(); //Active mass assignament in the model
        factory(\App\Models\Video::class, 100)
            ->make()
            ->each(function (Video $video) use ($genres) {
            });
        Model::unguard(); //Return false mass assignament
    }
    public function fetchRelations()
    {
        $subGenres = $this->allGenres->random(5)->load('categories');
        $categoriesId = [];
        //Using operator spread PHP ... for get [1,2,3,4,5]
        foreach ($subGenres as $genre) {
            array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
        }
        $categoriesId = array_unique($categoriesId);
        $subGenresId = $subGenres->pluck('id')->toArray();
        $this->relations['categories_id'] = $categoriesId;
        $this->relations['genres_id'] = $subGenresId;
    }
}
