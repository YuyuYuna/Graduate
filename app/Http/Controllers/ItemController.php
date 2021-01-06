<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Item::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validateForm($request);

        $item = new Item();
        $item->type = $request->type;
        $item->name = $request->name;
        $item->spec = $request->property;
        $item->quantity = $request->total_amount;

        $item->save();

        return $item->fresh();
    }

    private function validateForm($request)
    {
        $request->validate([
            'type' => [
                'required',
                Rule::in([
                    Item::BACHELOR,
                    Item::MASTER,
                    Item::DOCTOR
                ])
            ],
            'name' => 'required',
            'property' => 'required',
            'total_amount' => 'required|numeric',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $item = Item::find($id);
        return $item;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validateForm($request);

        $item = Item::find($id);
        $item->type = $request->type;
        $item->name = $request->name;
        $item->spec = $request->property;
        $item->quantity = $request->total_amount;

        $item->save();
        return $item->fresh();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = Item::find($id);
        $item->delete();

        return response()->noContent();
    }
}