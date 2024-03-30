<?php

namespace App\Http\Controllers;

use App\Exports\FinancesExport;
use App\Order;
use App\Restorant;
use App\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\Stripe;
use App\Status;

class FinanceController extends Controller
{
    private function getResources()
    {
        $restorants = Restorant::where(['active' => 1])->get();
        $drivers = User::role('driver')->where(['active' => 1])->get();
        $clients = User::role('client')->where(['active' => 1])->get();

        $orders = Order::orderBy('created_at', 'desc');

        //Get client's orders
        if (auth()->user()->hasRole('client')) {
            $orders = $orders->where(['client_id' => auth()->user()->id]);
        } elseif (auth()->user()->hasRole('driver')) {
            $orders = $orders->where(['driver_id' => auth()->user()->id]);
            //Get owner's restorant orders
        } elseif (auth()->user()->hasRole('owner')) {
            $orders = $orders->where(['restorant_id' => auth()->user()->restorant->id]);
        }

        //FILTER BT RESTORANT
        if (isset($_GET['restorant_id'])) {
            $orders = $orders->where(['restorant_id' => $_GET['restorant_id']]);
        }
        //If restorant owner, get his restorant orders only
        if (auth()->user()->hasRole('owner')) {
            //Current restorant id
            $restorant_id = auth()->user()->restorant->id;
            $orders = $orders->where(['restorant_id' => $restorant_id]);
        }

        //BY CLIENT
        if (isset($_GET['client_id'])) {
            $orders = $orders->where(['client_id' => $_GET['client_id']]);
        }

        //BY DRIVER
        if (isset($_GET['driver_id'])) {
            $orders = $orders->where(['driver_id' => $_GET['driver_id']]);
        }

        //BY DATE FROM
        if (isset($_GET['fromDate']) && strlen($_GET['fromDate']) > 3) {
            $orders = $orders->whereDate('created_at', '>=', $_GET['fromDate']);
        }

        //BY DATE TO
        if (isset($_GET['toDate']) && strlen($_GET['toDate']) > 3) {
            $orders = $orders->whereDate('created_at', '<=', $_GET['toDate']);
        }

        return ['orders' => $orders, 'restorants' => $restorants, 'drivers' => $drivers, 'clients' => $clients];
    }

    public function adminFinances()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized action.');
        }

        $resources = $this->getResources();
        $resources['orders'] = $resources['orders']->where('payment_status', 'paid')->whereNotNull('payment_method');

        //With downloaod
        if (isset($_GET['report'])) {
            $items = [];
            foreach ($resources['orders']->get() as $key => $order) {
                $item = $this->formatColumnsForDownload($order);
                array_push($items, $item);
            }

            return Excel::download(new FinancesExport($items), 'finances_' . time() . '.xlsx');
        }

        //CARDS
        $cards = [
            ['title' => 'Orders', 'value' => 0],
            ['title' => 'Total', 'value' => 0, 'isMoney' => true],
            ['title' => 'Platform Fee', 'value' => 0, 'isMoney' => true],
            ['title' => 'Net', 'value' => 0, 'isMoney' => true],

            ['title' => 'Processor fee', 'value' => 0, 'isMoney' => true],
            ['title' => 'Deliveries', 'value' => 0],
            ['title' => 'Delivery income', 'value' => 0, 'isMoney' => true],
            ['title' => 'Platform profit', 'value' => 0, 'isMoney' => true],
        ];
        foreach ($resources['orders']->get() as $key => $order) {
            $cards[0]['value'] += 1;
            $cards[1]['value'] += $order->delivery_price + $order->order_price_with_discount;
            $cards[2]['value'] += $order->fee_value + $order->static_fee;
            $cards[3]['value'] += $order->order_price_with_discount - $order->fee_value - $order->static_fee;

            $cards[4]['value'] += $order->payment_processor_fee;
            $cards[5]['value'] += $order->delivery_method . '' == '1' ? 1 : 0;
            $cards[6]['value'] += $order->delivery_price;
            $cards[7]['value'] += $order->fee_value + $order->static_fee + $order->delivery_price - $order->payment_processor_fee;
        }

        $displayParam = [
            'cards' => $cards,
            'orders' => $resources['orders']->paginate(10),
            'restorants' => $resources['restorants'],
            'drivers' => $resources['drivers'],
            'clients' => $resources['clients'],
            'parameters' => count($_GET) != 0,
            'statuses' => Status::pluck('name', 'id')->toArray()
        ];

        return view('finances.index', $displayParam);
    }

    public function ownerFinances()
    {
        if (!auth()->user()->hasRole('owner')) {
            abort(403, 'Unauthorized action.');
        }

        //Find this owner restaurant
        $restaurant = auth()->user()->restorant;

        //Change currency
        \App\Services\ConfChanger::switchCurrency($restaurant);

        //Check if Owner has completed
        $stripe_details_submitted = __('No');
        if (auth()->user()->stripe_account) {
            //Set our key
            Stripe::setApiKey(config('settings.stripe_secret'));

            $stripe_details_submitted = Account::retrieve(
                auth()->user()->stripe_account,
                []
            )->details_submitted ? __('Yes') : __('No');
        }

        $resources = $this->getResources();

        $resources['orders'] = $resources['orders']->whereNotNull('payment_method')->where('payment_status', 'paid');

        //With downloaod
        if (isset($_GET['report'])) {
            $items = [];
            foreach ($resources['orders']->get() as $key => $order) {
                $item = $this->formatColumnsForDownload($order);
                array_push($items, $item);
            }

            return Excel::download(new FinancesExport($items), 'finances_' . time() . '.xlsx');
        }

        //CARDS
        $cards = [
            ['title' => 'Orders', 'value' => 0],
            ['title' => 'Total', 'value' => 0, 'isMoney' => true],
            ['title' => 'Platform Fee', 'value' => 0, 'isMoney' => true],
            ['title' => 'Net inc. Vat', 'value' => 0, 'isMoney' => true],

            ['title' => 'VAT', 'value' => 0, 'isMoney' => true],
            ['title' => 'Net', 'value' => 0, 'isMoney' => true],
            ['title' => 'Deliveries', 'value' => 0],
            ['title' => 'Delivery cost', 'value' => 0, 'isMoney' => true],
        ];
        foreach ($resources['orders']->get() as $key => $order) {
            $cards[0]['value'] += 1;
            $cards[1]['value'] += $order->delivery_price + $order->order_price_with_discount;
            $cards[2]['value'] += $order->fee_value + $order->static_fee;
            $cards[3]['value'] += $order->order_price_with_discount - $order->fee_value - $order->static_fee;

            $cards[4]['value'] += $order->vatvalue;
            $cards[5]['value'] += $order->order_price_with_discount - $order->vatvalue - $order->fee_value - $order->static_fee;
            $cards[6]['value'] += $order->delivery_method . '' == '1' ? 1 : 0;
            $cards[7]['value'] += $order->delivery_price;
        }

        $displayParam = [
            'cards' => $cards,
            'orders' => $resources['orders']->paginate(10),
            'restorants' => $resources['restorants'],
            'drivers' => $resources['drivers'],
            'clients' => $resources['clients'],
            'parameters' => count($_GET) != 0,
            'stripe_details_submitted' => $stripe_details_submitted,
            'showFeeTerms' => true,
            'showStripeConnect' => true,
            'restaurant' => $restaurant,
            'weHaveStripeConnect' => env('ENABLE_STRIPE_CONNECT', false),
            'statuses' => Status::pluck('name', 'id')->toArray()
        ];

        return view('finances.index', $displayParam);
    }

    public function formatColumnsForDownload(object $order): array
    {
        return [
            __('Order ID') => $order->id,
            __('Restaurant Name') => $order->restorant->name,
            __('Restaurant ID') => $order->restorant_id,
            __('Created At') => $order->created_at,
            __('Last Status') => __($order->status->pluck('alias')->last()),
            __('Client Name') => $order->client ? $order->client->name : "",
            __('Client ID') => $order->client_id,
            __('Address') => $order->address ? $order->address->address : '',
            __('Address ID') => $order->address_id,
            __('Driver Name') => $order->driver ? $order->driver->name : '',
            __('Driver ID') => $order->driver_id,
            __('Payment Method') => $order->payment_method,
            __('Stripe Payment ID') => $order->srtipe_payment_id,
            __('Restaurant Fee') => $order->fee,
            __('Order Fee') => $order->fee_value,
            __('Restaurant Static Fee') => $order->static_fee,
            __('Platform Fee') => $order->fee_value + $order->static_fee,
            __('Processor Fee') => $order->payment_processor_fee,
            __('Delivery') => $order->delivery_price,
            __('Net Price with VAT') => $order->order_price_with_discount,
            __('Discount') => $order->discount,
            __('VAT') => $order->vatvalue,
            __('Net Price') => $order->order_price_with_discount - $order->vatvalue,
            __('Order Total') => $order->delivery_price + $order->order_price_with_discount,
        ];
    }

    public function connect()
    {

        //Set our key
        Stripe::setApiKey(config('settings.stripe_secret'));

        if (!auth()->user()->stripe_account) {
            //Create account for client
            $account_id = Account::create([
                'type' => 'standard',
            ])->id;

            //Save this id in user object
            auth()->user()->stripe_account = $account_id;
            auth()->user()->update();
        } else {
            $account_id = auth()->user()->stripe_account;
        }

        //Set account
        $account_links = AccountLink::create([
            'account' => $account_id,
            'refresh_url' => route('finances.owner'),
            'return_url' => route('finances.owner'),
            'type' => 'account_onboarding',
        ]);

        return redirect()->away($account_links->url);
    }
}
