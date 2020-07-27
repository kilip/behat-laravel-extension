<?php


namespace Tests\DummyPackage;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DummyAction extends Controller
{
    public function __invoke(Request $request)
    {
        return response("Hello World\n"."Foo value: ".config('foo'));
    }
}