<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\AlternativeValue;
use Illuminate\Support\Facades\DB;

class SmartController extends Controller
{
    public function index()
    {
        $criteria = Criteria::all();
        $alternatives = Alternative::all();
        return view('welcome', compact('criteria', 'alternatives'));
    }

    public function storeCriteria(Request $request)
    {
        $validated = $request->validate([
            'criteria' => 'required|array',
            'criteria.*.name' => 'required|string|max:255',
            'criteria.*.weight' => 'required|numeric|min:0|max:1',
            'criteria.*.type' => 'required|string|in:benefit,cost' // Added type (benefit or cost)
        ]);

        foreach ($validated['criteria'] as $criterion) {
            Criteria::create($criterion);
        }
        return redirect('/');
    }

    public function storeAlternative(Request $request)
    {
        $validated = $request->validate([
            'alternatives' => 'required|array',
            'alternatives.*.name' => 'required|string|max:255',
        ]);

        foreach ($validated['alternatives'] as $alternative) {
            Alternative::create($alternative);
        }
        return redirect('/');
    }

    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'values' => 'required|array',
            'values.*.*.value' => 'required|numeric',
        ]);

        $normalizedValues = $this->normalizeValues($validated['values']);

        DB::transaction(function() use ($normalizedValues) {
            foreach ($normalizedValues as $alternativeId => $criteriaValues) {
                foreach ($criteriaValues as $criteriaId => $normalizedValue) {
                    AlternativeValue::updateOrCreate([
                        'alternative_id' => $alternativeId,
                        'criteria_id' => $criteriaId,
                    ], [
                        'value' => $normalizedValue
                    ]);
                }
            }
        });

        return redirect()->route('results');
    }

    private function normalizeValues($values)
    {
        $criteria = Criteria::all();
        $minMaxValues = [];

        foreach ($values as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $value) {
                if (!isset($minMaxValues[$criteriaId])) {
                    $minMaxValues[$criteriaId] = [
                        'min' => $value['value'],
                        'max' => $value['value']
                    ];
                } else {
                    $minMaxValues[$criteriaId]['min'] = min($minMaxValues[$criteriaId]['min'], $value['value']);
                    $minMaxValues[$criteriaId]['max'] = max($minMaxValues[$criteriaId]['max'], $value['value']);
                }
            }
        }

        $normalizedValues = [];
        foreach ($values as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $value) {
                $min = $minMaxValues[$criteriaId]['min'];
                $max = $minMaxValues[$criteriaId]['max'];
                $criterion = $criteria->where('id', $criteriaId)->first();

                if ($criterion->type == 'benefit') {
                    // Normalization formula for benefit criteria
                    $normalizedValue = ($value['value'] - $min) / ($max - $min);
                } else {
                    // Normalization formula for cost criteria
                    $normalizedValue = ($max - $value['value']) / ($max - $min);
                }

                $normalizedValues[$alternativeId][$criteriaId] = $normalizedValue;
            }
        }

        return $normalizedValues;
    }

    public function results()
    {
        $criteria = Criteria::all();
        $alternatives = Alternative::all();
        $values = AlternativeValue::all();
    
        $rankings = $alternatives->map(function($alternative) use ($criteria, $values) {
            $score = 0;
            foreach ($criteria as $criterion) {
                $value = $values->where('alternative_id', $alternative->id)->where('criteria_id', $criterion->id)->first();
                if ($value) {
                    $score += $value->value * $criterion->weight;
                }
            }
            return (object) [
                'name' => $alternative->name,
                'score' => $score
            ];
        });
    
        $rankings = $rankings->sortByDesc('score');
    
        return view('results', compact('rankings'));
    }
    
    public function deleteCriteria($id)
    {
        Criteria::destroy($id);
        return redirect('/');
    }

    public function deleteAlternative($id)
    {
        Alternative::destroy($id);
        return redirect('/');
    }

    public function deleteValue($id)
    {
        AlternativeValue::destroy($id);
        return redirect('/');
    }
}
