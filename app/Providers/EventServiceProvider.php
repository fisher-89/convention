<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
//        Registered::class => [
//            SendEmailVerificationNotification::class,
//        ],

        //抽奖开始事件
        'App\Events\DrawStart' => [],
        // 抽奖停止
        'App\Events\DrawStop' => [],
        // 抽奖继续
        'App\Events\DrawContinue'=>[],

        // 中奖 弃奖
        'App\Events\WinnerAbandon'=>[],

        // 配置提交
        'App\Events\ConfigurationSave'=>[],
        // 配置修改
        'App\Events\ConfigurationUpdate'=>[],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
