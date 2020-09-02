<?php

use App\Models\Genre;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $genres = Genre::all();
        factory(\App\Models\Video::class, 100)
            ->create()
            ->each(function (Video $video) use ($genres) {
                $subGenres = $genres->random(5)->loads('categories');
                $categoriesId = [];
                //Usando operador spread PHP ... p/ obter [1,2,3,4,5]
                foreach ($subGenres as $genre) {
                    array_push($categoriesId, ...$genre->categories->pluck('id')->toArray());
                }
                $categoriesId = array_unique($categoriesId);
                $video->categories->attach($categoriesId);
                $subGenresId = $subGenres->pluck('id')->toArray();
                $video->genres->attach($subGenresId);
            });
    }
}
