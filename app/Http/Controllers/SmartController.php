<?php



namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Criteria;
use App\Models\Alternative;
use App\Models\AlternativeValue;

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
            Criteria::create($criterion);
        }
        return redirect('/');
    }

    public function storeAlternative(Request $request)
    {
        foreach ($request->alternatives as $alternative) {
            Alternative::create($alternative);
        }
        return redirect('/');
    }

    public function calculate(Request $request)
    {
        // Normalisasi data jika diperlukan
        $normalizedValues = $this->normalizeValues($request->values);

        // Simpan nilai yang sudah dinormalisasi ke dalam database
        foreach ($normalizedValues as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $normalizedValue) {
                AlternativeValue::create([
                    'alternative_id' => $alternativeId,
                    'criteria_id' => $criteriaId,
                    'value' => $normalizedValue
                ]);
            }
        }

        // Redirect to results page
        return redirect()->route('results');
    }

    private function normalizeValues($values)
    {
        // Ambil nilai maksimum dan minimum untuk setiap kriteria
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

        // Lakukan normalisasi min-max untuk setiap nilai kriteria
        $normalizedValues = [];
        foreach ($values as $alternativeId => $criteriaValues) {
            foreach ($criteriaValues as $criteriaId => $value) {
                $min = $minMaxValues[$criteriaId]['min'];
                $max = $minMaxValues[$criteriaId]['max'];
                $normalizedValue = ($value['value'] - $min) / ($max - $min);
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
    
        // Urutkan berdasarkan score secara descending
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





