<?php

namespace App\Notifications;

use Akaunting\Money\Currency;
use Akaunting\Money\Money;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\OneSignal\OneSignalChannel;
use NotificationChannels\OneSignal\OneSignalMessage;
use NotificationChannels\OneSignal\OneSignalWebButton;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;
use App\NotificationChannels\Expo\ExpoChannel;
use App\NotificationChannels\Expo\ExpoMessage;

class OrderNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    protected $order;
    protected $status;
    protected $user;

    public function __construct($order, $status = '1',$user=null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notificationClasses = ['database'];
        
        //Mail notification on vendor email
        if($this->order->restorant->getConfig('enable_email_order_notification',false)){
            array_push($notificationClasses, 'mail');
        }else if(config('settings.send_order_email_to_vendor',false)){
            array_push($notificationClasses, 'mail');
        }

        if (config('settings.onesignal_app_id')) {
            array_push($notificationClasses, OneSignalChannel::class);
        }
        if (config('settings.twilio_account_sid') && config('settings.send_sms_notifications')) {
            if( $this->order->client&&strlen($this->order->client->phone)>4){
                array_push($notificationClasses, TwilioChannel::class);
            }
        }
        if($this->user!=null&&strlen($this->user->expotoken)>3){
            array_push($notificationClasses,ExpoChannel::class);  
        }
        return $notificationClasses;
    }

    public function toExpo($notifiable)
    {
        $messages=$this->getMessages();
        $greeting=$messages[0];
        $line=$messages[1];
        try {
            return ExpoMessage::create()
            ->title($greeting)
            ->body($line)
            ->badge(1);
        } catch (\Throwable $th) {
            //throw $th;
        }
        
    }

    public function toTwilio($notifiable)
    {
        if ($this->status.'' == '1') {
            //Created
             $line = $this->order->delivery_method.'' == '3'? __('You have just received an order on table').' '.$this->order->table->name :  __('You have just received an order');
        } elseif ($this->status.'' == '3') {
            //Accepted
            $line = __('Your order has been accepted. We are now working on it!');
        } elseif ($this->status.'' == '4') {
            //Assigned to driver
            $line = __('There is new order assigned to you.');
        } elseif ($this->status.'' == '5') {
            //Prepared
            $line = $this->order->delivery_method && $this->order->delivery_method.'' == '1' ? __('Your order is ready for delivery. Expect us soon.') : __('Your order is ready for pickup. We are expecting you.');
        } elseif ($this->status.'' == '9') {
            //Rejected
            $line = __('Unfortunately your order is rejected. There where issues with the order and we need to reject it. Pls contact us for more info.');
        }

        return (new TwilioSmsMessage())->content($line);
    }

    private function getMessages(){
        if ($this->status.'' == '1') {
            //Created
            $greeting = __('There is new order');
            $line = $this->order->delivery_method.'' == '3'? __('You have just received an order on table').' '.$this->order->table->name :  __('You have just received an order');
        } elseif ($this->status.'' == '3') {
            //Accepted
            $greeting = __('Your order has been accepted');
            $line = __('We are now working on it!');
        } elseif ($this->status.'' == '4') {
            //Assigned to driver
            $greeting = __('There is new order for you.');
            $line = __('There is new order assigned to you.');
        } elseif ($this->status.'' == '5') {
            //Prepared
            $greeting = __('Your order is ready.');
            $line = $this->order->delivery_method && $this->order->delivery_method.'' == '1' ? __('Your order is ready for delivery. Expect us soon.') : __('Your order is ready for pickup. We are expecting you.');
        } elseif ($this->status.'' == '9') {
            //Rejected
            $greeting = __('Order rejected');
            $line = __('Unfortunately your order is rejected. There where issues with the order and we need to reject it. Pls contact us for more info.');
        }
        return [$greeting." #".$this->order->id,$line];
    }

    public function toOneSignal($notifiable)
    {
        $messages=$this->getMessages();
        $greeting=$messages[0];
        $line=$messages[1];

        $url = url('/orders/'.$this->order->id);

        //Inders in the db

        return OneSignalMessage::create()
            ->subject($greeting)
            ->body($line)
            ->url($url)
            ->webButton(
                OneSignalWebButton::create('link-1')
                    ->text(__('View Order'))
                    ->icon('https://upload.wikimedia.org/wikipedia/commons/4/4f/Laravel_logo.png')
                    ->url($url)
            );
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //Change currency
        \App\Services\ConfChanger::switchCurrency( $this->order->restorant);

        if ($this->status.'' == '1') {
            //Created
            $greeting = __('There is new order');
            $line = $this->order->delivery_method.'' == '3'? __('You have just received an order on table').' '.$this->order->table->name :  __('You have just received an order');
        } elseif ($this->status.'' == '3') {
            //Accepted
            $greeting = __('Your order has been accepted');
            $line = __('We are now working on it!');
        } elseif ($this->status.'' == '4') {
            //Assigned to driver
            $greeting = __('There is new order for you.');
            $line = __('There is new order assigned to you.');
        } elseif ($this->status.'' == '5') {
            //Prepared
            $greeting = __('Your order is ready.');
            $line = $this->order->delivery_method && $this->order->delivery_method.'' == '1' ? __('Your order is ready for delivery. Expect us soon.') : __('Your order is ready for pickup. We are expecting you.');
        } elseif ($this->status.'' == '9') {
            //Rejected
            $greeting = __('Order rejected');
            $line = __('Unfortunately your order is rejected. There where issues with the order and we need to reject it. Pls contact us for more info.');
        }

        $message = (new MailMessage)
            ->greeting($greeting)
            ->subject(__('Order notification').' #'.$this->order->id)
            ->line($line)
            ->action(__('View Order'), url('/orders/'.$this->order->id));

        //Add order details
        $message->line(__('Order items'));
        $message->line(__('________________'));
        foreach ($this->order->items as $key => $item) {
            $lineprice = $item->pivot->qty.' X '.$item->name.$item->pivot->variant_name.' ( '.money($item->pivot->variant_price, config('settings.cashier_currency'), config('settings.do_convertion')).' ) = '.money($item->pivot->qty * $item->pivot->variant_price, config('settings.cashier_currency'), true);
            $message->line($lineprice);
        }
        $message->line(__('________________'));
        $message->line(__('Sub Total').': '.money($this->order->order_price, config('settings.cashier_currency'), config('settings.do_convertion')));

        if ($this->order->delivery_method && $this->order->delivery_method.'' == '1') {
            $message->line(__('Delivery').': '.money($this->order->delivery_price, config('settings.cashier_currency'), config('settings.do_convertion')));
        }

        if ($this->order->discount>0) {
            $message->line(__('Discount').': '.money($this->order->discount, config('settings.cashier_currency'), config('settings.do_convertion')));
        }

        $message->line(__('Total').': '.money($this->order->order_price_with_discount+$this->order->delivery_price, config('settings.cashier_currency'), config('settings.do_convertion')));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [ ];
    }

    public function toDatabase($notifiable)
    {
        if ($this->status.'' == '1') {
            //Created
            $greeting = __('There is new order');
            $line = __('You have just received an order');
        } elseif ($this->status.'' == '3') {
            //Accepted
            $greeting = __('Your order has been accepted');
            $line = __('order').'#'.$this->order->id.' '.__('We are now working on it!');
        } elseif ($this->status.'' == '4') {
            //Assigned to driver
            $greeting = __('There is new order for you.');
            $line = __('There is new order assigned to you.');
        } elseif ($this->status.'' == '5') {
            //Prepared
            $greeting = __('Your order is ready.');
            $line = $this->order->delivery_method && $this->order->delivery_method.'' == '1' ? __('Your order is ready for delivery. Expect us soon.') : __('Your order is ready for pickup. We are expecting you.');
        } elseif ($this->status.'' == '9') {
            //Rejected
            $greeting = __('Order rejected');
            $line = __('Unfortunately your order is rejected. There where issues with the order and we need to reject it. Pls contact us for more info.');
        }

        return [
            'title'=>$greeting,
            'body' =>$line,
        ];
    }
}
