<?php

namespace App\Http\Controllers;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getLanguageAll()
    {
        $languages = Language::all();

        try {
            if ($languages) {
                return response()->json([
                    'success' => true,
                    'message' => 'Language succesfully retrieved',
                    'data' => $languages
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Language not exists',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage()
            ], 500);
        }
    }

    public function getLanguageById(Request $request, $id)
    {
        $language = Language::where('id', $id)->first();

        try {
            if ($language->id == $id) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Language found',
                    'data'      => [
                        'language'  => $language
                    ]
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Language not found',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage()
            ], 500);
        }
    }

    public function insertLanguage(Request $request)
    {
        $this->validate($request, ['language' => 'required|unique:languages']);

        try {
            $language = Language::create([
                'language'  => $request->input('language')
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'Language successfully added',
                'data'      => $language
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage(),
            ], 500);
        }
    }

    public function updateLanguage(Request $request, $id)
    {
        $language = Language::where('id', $id)->first();

        try {
            if ($language) {
                $this->validate($request, ['language' => 'required']);

                $language->language = $request->input('language');
                $language->save();

                return response()->json([
                    'success'   => true,
                    'message'   => 'Language succesfully updated',
                    'data'      => $language
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Language not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage()
            ], 500);
        }
    }

    public function deleteLanguage(Request $request, $id)
    {
        $language = Language::where('id', $id)->first();
        
        try {
            if ($language){
                $language->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Language succesfully deleted'
                ], 200);
            } else {
                return response()->json([
                    'success'   => false,
                    'message'   => 'Language not found',
                ], 404);
            }
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Internal server error:'. $e->getMessage(),
            ], 500);
        }
    }
}
