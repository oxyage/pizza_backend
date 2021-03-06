<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Http\Response;
use App\Cart;
use App\Order;


class CartController extends Controller
{

    public function add($customer_id, Request $request)
    {

        $find_pizza = Cart::all()
            ->where('customer_id',$customer_id)
            ->where('pizza_id', $request->pizza_id);

        if($find_pizza->count() > 0)
        {
            $current_count = $find_pizza->first()->count;

            $current_count += $request->count;

            if(Cart::edit($customer_id, $request->get('pizza_id'), $current_count))
                return response('', 202);
            else
                return response("Invalid data", 400);
        }
        else
        {
            if(Cart::edit($customer_id, $request->get('pizza_id'), 1))
                return response('', 201);
            else
                return response("Invalid data", 400);



        }



    }

	public function edit($customer_id, Request $request)
    {
        //todo: validation request

        if($request->get('count') <= 0)
        {
            Cart::removeFromCart($customer_id, $request->get('pizza_id'));
            return response('', 204);
        }

        if(Cart::edit($customer_id, $request->get('pizza_id'), $request->get('count')))
            return response('', 202);
        else
            return response("Invalid data", 400);
    }



	public function show($customer_id)
	{
	    $customer_cart = OrderController::getPizzasByCustomerCart($customer_id);
        $summary = OrderController::getCostAndWeightByOrder($customer_cart);

        return ["summary"=>$summary, "customer_cart"=>$customer_cart];
                #return Cart::all()->where('customer_id', $customer_id);
	}

    public function clear($customer_id)
    {
        //todo: validation request
        if(Cart::clear($customer_id))
            return response('', 204);

        else
            return response('Invalid `customer_id`', 400);

    }

}
