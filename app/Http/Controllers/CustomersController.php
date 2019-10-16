<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;
use App\Company;
use App\Events\NewCustomerHasRegisteredEvent;

class CustomersController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        //$customers = Customer::all();
        $customers = Customer::with('company')->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        $companies = Company::all();
        $customer = new Customer();

        return view('customers.create', compact('companies', 'customer'));
    }
    
    public function store(){
        $customer = Customer::create($this->validateRequst());

        event(new NewCustomerHasRegisteredEvent($customer));

        return redirect('customers');
    }

    public function show(Customer $customer){
        // $customer = Customer::find($customer);
        // $customer = Customer::where('id', $customer)->firstOrFail();

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer){
        $companies = Company::all();

        return view('customers.edit', compact('customer', 'companies'));
    }

    public function update(Customer $customer){
        $customer->update($this->validateRequst());

        return redirect('customers/'. $customer->id);
    }
    
    public function destroy(Customer $customer){
        $customer->delete();

        return redirect('customers');
    }

    private function validateRequst(){
        return request()->validate([
            'name' => 'required|min:3',
            'email' => 'required|email',
            'active' => 'required',
            'company_id' => 'required'
        ]);
    }
}
