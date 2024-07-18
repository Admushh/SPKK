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
        foreach ($request->criteria as $criterion) {
            Criteria::create([
                'name' => $criterion['name'],
                'weight' => $criterion['weight'],
                'utility' => $criterion['utility'], // Pastikan ada nilai untuk kolom utility
            ]);
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

        DB::transaction(function() use ($validated) {
            foreach ($validated['values'] as $alternativeId => $criteriaValues) {
                foreach ($criteriaValues as $criteriaId => $value) {
                    AlternativeValue::updateOrCreate([
                        'alternative_id' => $alternativeId,
                        'criteria_id' => $criteriaId,
                    ], [
                        'value' => $value['value']
                    ]);
                }
            }
        });

        return redirect()->route('results');
    }

    public function results()
    {
        $criteria = Criteria::all();
        $alternatives = Alternative::all();
        $values = AlternativeValue::all();

        // Normalisasi bobot kriteria
        $totalWeight = $criteria->sum('weight');
        $criteria->each(function ($criterion) use ($totalWeight) {
            $criterion->normalized_weight = $criterion->weight / $totalWeight;
        });

        // Hitung skor untuk setiap alternatif
        $rankings = $alternatives->map(function($alternative) use ($criteria, $values) {
            $score = 0;
            foreach ($criteria as $criterion) {
                $value = $values->where('alternative_id', $alternative->id)
                                ->where('criteria_id', $criterion->id)
                                ->first();
                if ($value) {
                    $normalizedValue = $this->calculateUtility($value->value, $criterion);
                    $score += $normalizedValue * $criterion->normalized_weight;
                }
            }
            return (object) [
                'name' => $alternative->name,
                'score' => $score
            ];
        });

        // Sort rankings by score in descending order
        $rankings = $rankings->sortByDesc('score')->values();

        return view('results', ['rankings' => $rankings]);
    }

    private function calculateUtility($value, $criterion)
    {
        $criteriaValues = AlternativeValue::where('criteria_id', $criterion->id)->get();
        $minValue = $criteriaValues->min('value');
        $maxValue = $criteriaValues->max('value');

        if ($criterion->utility == 'cost') {
            return ($maxValue - $value) / ($maxValue - $minValue);
        } else { // benefit
            return ($value - $minValue) / ($maxValue - $minValue);
        }
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
