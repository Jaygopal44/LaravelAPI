<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

/**
 * @OA\Info(
 *      title="Ecommerce Product API",
 *      version="1.0.0",
 *      description="Ecommerce Product API",
 *      @OA\Contact(
 *          email="jaygopal00786@gmail.com",
 *          name="Jaygopal Gain"
 *      ),
 *      @OA\License(
 *          name="MIT License",
 *          url="https://opensource.org/licenses/MIT"
 *      )
 * )
 */

/**
 * @OA\Tag(
 *     name="Products",
 *     description="API Endpoints for Products"
 * )
 */
class ProductController extends Controller
{


/**
 * @OA\Post(
 *     path="/api/insert",
 *     summary="Insert a new product",
 *     tags={"Insert"},
 *     @OA\RequestBody(
 *         @OA\JsonContent(),
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name", "description", "price", "quantity", "image"},
 *                 @OA\Property(property="name", type="string"),
 *                 @OA\Property(property="description", type="text"),
 *                 @OA\Property(property="price", type="integer"),
 *                 @OA\Property(property="quantity", type="integer"),
 *                 @OA\Property(property="image", type="string", format="binary")
 *             )
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product inserted successfully"
 *
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */


    public function insert(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = Product::create($validatedData);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = 'image_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $product->image = $filename;
            $product->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Data inserted successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Display all products",
     *     operationId="displayProducts",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function display()
    {
        $products = Product::all();
        return response()->json([
            'success' => true,
            'message' => 'All the data displayed successfully',
            'data' => $products,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="View a product",
     *     operationId="viewProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function view($id)
    {
        $product = Product::find($id);
        return response()->json([
            'success' => true,
            'message' => 'Data shown successfully',
            'data' => $product,
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     operationId="updateProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price", type="number"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="quantity", type="integer"),
     *             @OA\Property(property="image", type="string", format="binary")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');
        $product->quantity = $request->input('quantity');

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('upload/' . $product->image))) {
                unlink(public_path('upload/' . $product->image));
            }

            $file = $request->file('image');
            $filename = 'image_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload'), $filename);
            $product->image = $filename;
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Data updated successfully',
            'data' => $product
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     operationId="deleteProduct",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid input"
     *     )
     * )
     */
    public function del($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && file_exists(public_path('upload/' . $product->image))) {
            unlink(public_path('upload/' . $product->image));
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted Successfully',
        ], 200);
    }
}
