<?php

namespace App\Http\Controllers;

use App\Http\Requests\PreserveDate;
use App\Http\Requests\StoreOrder;
use App\Http\Requests\UpdateOrder;
use App\Models\Config;
use App\Models\Order;
use App\Models\Set;
use App\Models\TimeRange;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->showOrders();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrder $request)
    {
        $request->validated();

        $order = new Order();

        $order->document_id = Order::createDocumentId();
        $order->owner_id = User::where('username', $request->username)->first()->id;

        $order->status_code = Order::code_created;
        $order->total_price = $this->calculateTotalPrice($request->sets);

        $order->save();

        $this->saveSets($order, $request->sets);

        // return Inertia::render('Student/Order/Show', ['re_order'=> $order, 'finish'=> true]);
        // return redirect()->back()->with('success', $order->fresh());
        return $order->fresh();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        return $order;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrder $request, $id)
    {
        $order = Order::find($id);
        $user = Auth::user();

        if (is_null($order))
            abort(404);

        $request->validated();

        if ($request->status_code === Order::code_canceled) {
            $order->status_code = $request->status_code;
            $order->save();
            $order->sets()->delete();
            return redirect()->back()->with('success', $order->fresh());
        }

        $order->document_id = $request->document_id;
        $order->owner_id = User::where('username', $request->owner_username)->first()->id;
        $order->status_code = $request->status_code;

        if (!is_null($request->payment_id))
            $order->payment_id = $request->payment_id;

        if (!is_null($request->sets))
            $order->total_price = $this->calculateTotalPrice($request->sets);

        $order->save();

        if (!is_null($request->sets)) {
            $order->sets()->delete();
            $this->saveSets($order, $request->sets);
        }

        return redirect()->back()->with('success', $order->fresh());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->delete();

        return response()->noContent();
    }

    public function searchOrder(Request $request)
    {
        $search = $request->search;

        if (!is_null($search)) {
            $find_owner = User::where('username', $search);
            $find_document = Order::where('document_id', $search);
            $find_payment_id = Order::where('payment_id', $search);

            if ($find_owner->count() === 0 && $find_document->count() === 0 && $find_payment_id->count() === 0)
                $result = [];

            if ($find_owner->count() > 0) {
                $result = $find_owner->first()->orders()->get();

                if ($result->count() === 0) {
                    $set = $find_owner->first()->set()->first();
                    if (!is_null($set))
                        $result = $set->order()->get();
                }
            }

            if ($find_document->count() > 0) {
                $result = $find_document->get();
            }

            if ($find_payment_id->count() > 0) {
                $result = $find_payment_id->get();
            }

            return $result;
        }

        abort(404);
    }

    public function returnOrder(Request $request)
    {
        $stu_id = $request->stu_id;

        if ($stu_id !== null) {
            $stu = User::where('username', $stu_id)->firstOr(fn() => abort(404));

            $set = $stu->set()->get()->first();

            if ($set->returned) {
                return response()
                    ->json([
                        'error' => 'duplicate',
                        'message' => __('validation.student_returned')
                    ], 400);
            }

            $set->returned = true;
            $set->save();

            $order = $set->order()->get();

            return $order;
        }
        return abort(404);
    }

    public function preserveDate(PreserveDate $request)
    {
        $request->validated();

        $order = Order::where('document_id', $request->order_id)->first();

        $order->forceFill([
            'preserve' => $request['preserve_date'],
        ])->save();

        return $order->fresh();
    }

    public function paidOrder(Request $request)
    {
        //
    }

    public function cancelOrder(Request $request)
    {
        //
    }

    public static function showOrders()
    {
        if (Auth::check()) {
            if (Auth::user()->role === User::ADMIN) {
                return Order::all();
            }
            return [
                'own' => User::find(Auth::id())->orders,
                'set' => User::find(Auth::id())->set,
            ];
        }
        return [];
    }

    private function saveSets($order, $sets)
    {
        foreach ($sets as $set) {
            $username = $set['set_owner'];
            $user = User::where('username', $username)->first();
            $ordered_set = new Set();
            $ordered_set->student_id = $user->id;
            $ordered_set->order_id = $order->id;
            $ordered_set->color_item = $set['accessory'];
            $ordered_set->size_item = $set['cloth'];
            $ordered_set->save();
        }
    }

    private function calculateTotalPrice($sets)
    {
        $total_price = 0;
        foreach ($sets as $set) {
            $user = User::where('username', $set['set_owner'])->first();
            if ($user->isBachelor()) {
                $total_price += Config::getBachelorSetPriceValue();
            } else if ($user->isMaster()) {
                $total_price += Config::getMasterSetPriceValue();
            } else {
                // who are you?
            }
        }
        return $total_price;
    }
}
