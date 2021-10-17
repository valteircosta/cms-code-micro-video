<?php

namespace App\Observers;

use App\Models\Category;
use Bschmitt\Amqp\Message;
use Illuminate\Database\Eloquent\Model;

class SyncModelObserver
{
    /**
     * Handle the category "created" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function created(Model $category)
    {
        // When created
        $message  = new Message(
            $category->toJson()
        );
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.category.created', $message);
    }

    /**
     * Handle the category "updated" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function updated(Category $category)
    {
        // When updated
        $message  = new Message(
            $category->toJson()
        );
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.category.updated', $message);
    }

    /**
     * Handle the category "deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function deleted(Category $category)
    {
         // When deleted
        $message  = new Message(json_encode(['id'=> $category->id]));
        // Sent a instance, its is a sincronized method
        \Amqp::publish('model.category.deleted', $message);
    }

    /**
     * Handle the category "restored" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function restored(Category $category)
    {
        //
    }

    /**
     * Handle the category "force deleted" event.
     *
     * @param  \App\Models\Category  $category
     * @return void
     */
    public function forceDeleted(Category $category)
    {
        //
    }
 protected function getModelName(Model $model)
 {

 }
    protected function publish($routingKey,array $Data)
    {
        $message  = new Message(
            json_encode($Data),
            [
                'content_type' => 'application/json',
                'delivery_mode' => 2 // 2=persistent, 1= none-persistent
            ]
        );
           // Sent a instance, its is a sincronized method
           \Amqp::publish(
               $routingKey,
                $message,
                [
                'exchange_type' => 'topic',
                'exchange' => 'amq.topic'
                ]
            );
    }

}
