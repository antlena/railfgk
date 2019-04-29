<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\BookRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Models\Book;
use App\Services\BookService;
use App\Mail\Book\BookSaved;

/**
 * BookController
 * 
 * @package App\Http\Controllers\API
 */
class BookController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return ResourceCollection
     */
    public function index() : ResourceCollection
    {
        return BookResource::collection(Book::paginate());
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param BookRequest $request
     * @return JsonResponse
     */
    public function store(BookRequest $request) : JsonResponse
    {
        $id = BookService::save($request->all());

        $book = Book::updateOrCreate(['service_id' => $id], $request->all());

        Mail::queue(new BookSaved($book));

        return new JsonResponse([
            'success' => true,
            'message' => trans('book.saved')
        ]);                   
    }

    /**
     * Display the specified resource.
     * 
     * @param Book $book
     * @return BookResource
     */
    public function show(Book $book) : BookResource
    {   
        return new BookResource($book);
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param Book $book
     */
    public function destroy(Book $book) : JsonResponse
    {
        $book->delete();

        return new JsonResponse([
            'success' => true,
            'message' => trans('book.deleted')
        ]);       
    }
    
}
