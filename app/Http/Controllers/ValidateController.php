<?php
namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use ObzoraNMS\Interfaces\ValidationFixer;
use ObzoraNMS\Validator;

class ValidateController extends Controller
{
    public function index(): View
    {
        return view('validate.index');
    }

    public function runValidation(): JsonResponse
    {
        $validator = new Validator();
        $validator->validate();

        return response()->json($validator->toArray());
    }

    public function runFixer(Request $request): JsonResponse
    {
        $this->validate($request, [
            'fixer' => [
                'starts_with:ObzoraNMS\Validations',
                function ($attribute, $value, $fail) {
                    if (! class_exists($value) || ! in_array(ValidationFixer::class, class_implements($value))) {
                        $fail(trans('validation.results.invalid_fixer'));
                    }
                },
            ],
        ]);
        $fixer = $request->get('fixer');

        return response()->json([
            'result' => (new $fixer)->fix(),
        ]);
    }
}
