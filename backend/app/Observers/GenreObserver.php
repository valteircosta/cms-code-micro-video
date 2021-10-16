<?php

namespace App\Observers;

use App\Models\Genre;
use Bschmitt\Amqp\Message;

class GenreObserver
{
    /**
     * Handle the genre "created" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function created(Genre $genre)
    {

        $message  = new Message(
            $genre->toJson()
        );
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.genre.created', $message);
    }

    /**
     * Handle the genre "updated" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function updated(Genre $genre)
    {
        $message  = new Message(
            $genre->toJson()
        );
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.genre.update', $message);
    }

    /**
     * Handle the genre "deleted" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function deleted(Genre $genre)
    {
        $message  = new Message(
          json_encode(['id'=> $genre->id])
        );
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.genre.deleted', $message);
    }

    /**
     * Handle the genre "restored" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function restored(Genre $genre)
    {
        //
    }

    /**
     * Handle the genre "force deleted" event.
     *
     * @param  \App\Models\Genre  $genre
     * @return void
     */
    public function forceDeleted(Genre $genre)
    {
        //
    }
}
