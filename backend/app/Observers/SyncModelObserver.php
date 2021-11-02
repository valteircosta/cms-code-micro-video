<?php

namespace App\Observers;

use Bschmitt\Amqp\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SyncModelObserver
{
    public function created(Model $model)
    {
        $modelName = $this->getModelName($model);
        $data = $model->toArray();
        //Pegando nome do método (created,update,deleted etc ) com uso da constante mágica __FUNCTION__
        $action = __FUNCTION__;
        //Formando routingKey
        $routingKey = "model.{$modelName}.${action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            //O laravel pode mandar email  e etc
            //Vamos usar o helper report que captura e grava o erro no storage/log
            //Construimos uma nova exception e passamos a anterior como parâmetro no construtor para ser tratada pelo report na cadeia
            $id = $model->id;
            $this->reportException([
                'modelName' => $modelName,
                'id' => $id,
                'action' => $action,
                'exception' => $exception
            ]);
        }
    }

    public function updated(Model $model)
    {
        $modelName = $this->getModelName($model);
        $data = $model->toArray();
        $action = __FUNCTION__;
        $routingKey = "model.{$modelName}.${action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            $id = $model->id;
            $this->reportException([
                'modelName' => $modelName,
                'id' => $id,
                'action' => $action,
                'exception' => $exception
            ]);
        }
    }

    public function deleted(Model $model)
    {
        $modelName = $this->getModelName($model);
        $data = ['id' => $model->id];
        $action = __FUNCTION__;
        $routingKey = "model.{$modelName}.${action}";

        try {
            $this->publish($routingKey, $data);
        } catch (\Exception $exception) {
            $id = $model->id;
            $this->reportException([
                'modelName' => $modelName,
                'id' => $id,
                'action' => $action,
                'exception' => $exception
            ]);
        }
    }

    public function restored(Model $model)
    {
        //
    }

    public function forceDeleted(Model $model)
    {
        //
    }
    protected function getModelName(Model $model)
    {
        //Usando reflection obtem o nome da model
        $shortName = (new \ReflectionClass($model))->getShortName();
        //Faz a conversão para o padrão SnakeCase Ex.: CastMember -> cast_member
        return Str::snake($shortName);
    }
    protected function publish($routingKey, array $Data)
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
    public function reportException(array $params)
    {
        list(
            'modelName' => $modelName,
            'id' => $id,
            'action' => $action,
            'exception' => $exception
        ) = $params;
        $myexception  =  new \Exception("The model $modelName with ID $id not synced on $action", 0, $exception);
        report($myexception);
    }
}
