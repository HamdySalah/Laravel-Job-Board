<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use function PHPUnit\Framework\returnValueMap;


class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      return "Iam in the index method of JobApplicationController";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return "iam in the create method of JobApplicationController";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return "iam in the store method of JobApplicationController";

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
          return "iam in the show method of JobApplicationController";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return "iam in the edit method of JobApplicationController";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return "iam in the update method of JobApplicationController";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return "iam in the destroy method of JobApplicationController";
    }
}
