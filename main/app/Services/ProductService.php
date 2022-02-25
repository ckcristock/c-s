<?php

namespace App\Services;

use App\Models\InventaryDotation;
use App\Models\Product;
use App\Models\VariableProduct;
use Exception;
use Illuminate\Support\Facades\Http;

class ProductService
{

    static function saveProduct($data)
    {
        try {
            $data['Nombre_Comercial'] = $data["name"];
            $data['company_id'] = 1;
            $data['Orden_Compra'] = 1;
            $data['Ubicar'] = 1;
            $data['Tipo'] = "Material";

            $product = Product::create($data);
            $product->save();

            foreach ($data["dynamic"] as $d) {
               $d["product_id"] = $product->id;
                VariableProduct::create($d);
            }

            return $product;

        } catch (\Throwable $th) {
            echo json_encode(['message' => $th->getMessage(), $th->getLine(), $th->getFile()], 400);
        }
    }

    static function updateProduct($data, $dynamic){

        foreach ($dynamic as $d) {
            $d['product_id'] = $data["product_id"];
            VariableProduct::updateOrCreate(['id' => $d["id"]], $d);
        }
    }







    static function show($id)
    {

        return ApuPart::with([
            "city",
            "files",
            "indirect",
            "thirdparty" => function ($q) {
                $q->select('id', 'first_name', 'first_surname');
            },
            "machine" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "external" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "internal" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "other" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "cutwater" => function ($q) {
                $q->select("*")
                    ->with("material")
                    ->with('thickness');
            },
            "cutlaser" => function ($q) {
                $q->select("*")
                    ->with("cutLaserMaterial")
                    ->with("cutLaserMaterialValue");
            },
            "commercial" => function ($q) {
                $q->select("*")
                    ->with('unit')
                    ->with("material");
            },
        ])

            ->with([
                "person" => function ($q) {
                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                },
            ])
            ->with([
                "rawmaterial" => function ($q) {
                    $q->select("*")
                        ->with("geometry");
                },
                "rawmaterial.measures" => function ($q) {
                    $q->select("*");
                },
                "rawmaterial.material" => function ($q) {
                    $q->select("*");
                },
            ])
            ->where("id", $id)
            ->first();
    }
    static function find($name)
    {

        return ApuPart::with([
            "city",
            "files",
            "indirect",
            "thirdparty" => function ($q) {
                $q->select('id', 'first_name', 'first_surname');
            },
            "machine" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "external" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "internal" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "other" => function ($q) {
                $q->select("*")
                    ->with("unit");
            },
            "cutwater" => function ($q) {
                $q->select("*")
                    ->with("material")
                    ->with('thickness');
            },
            "cutlaser" => function ($q) {
                $q->select("*")
                    ->with("cutLaserMaterial")
                    ->with("cutLaserMaterialValue");
            },
            "commercial" => function ($q) {
                $q->select("*")
                    ->with('unit')
                    ->with("material");
            },
        ])

            ->with([
                "person" => function ($q) {
                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                },
            ])
            ->with([
                "rawmaterial" => function ($q) {
                    $q->select("*")
                        ->with("geometry");
                },
                "rawmaterial.measures" => function ($q) {
                    $q->select("*");
                },
                "rawmaterial.material" => function ($q) {
                    $q->select("*");
                },
            ])
            ->when($name, function ($q, $fill) {
                $q->where('name', 'like', "%$fill%");
            })
            ->get(['*', 'id as value', 'name as text']);
    }

    static function deleteMaterial($id)
    {
        $mat =  ApuPartRawMaterial::where("apu_part_id", $id)->get();

        foreach ($mat as $value) {

            ApuPartRawMaterialMeasure::where("apu_part_raw_material_id",  $value["id"])->delete();
        }

        ApuPartRawMaterial::where("apu_part_id", $id)->delete();
    }

    static public function paginate()
    {

        return ApuPart::select(["id", "third_party_id", "user_id", "person_id", "city_id", "name", "code", "unit_direct_cost", "line", "amount", "created_at", "state"])
            ->with([
                'user' => function ($q) {
                    $q->select("id", "person_id");
                },
                'user.person' => function ($q) {
                    $q->select("id", "first_name", "first_surname");
                },
                'city' => function ($q) {
                    $q->select("id", "name");
                },
                'thirdparty' => function ($q) {
                    $q->select("id", "first_name", "first_surname");
                },
                'person' => function ($q) {
                    $q->select("id", "first_name", "first_surname", 'passport_number', 'visa');
                }
            ])

            ->when(request()->get('name'), function ($q, $fill) {
                $q->where('name', 'like', '%' . $fill . '%');
            })
            ->when(request()->get('creation_date'), function ($q, $fill) {
                $q->where('created_at', 'like', '%' . $fill . '%');
            })

            // ->when( request()->get('state'), function($q, $fill)
            // {
            //     if (request()->get('state') == 'Todos') {
            //         return null;
            //     } else {
            //         $q->where('state','like','%'.$fill.'%');
            //     }
            // })
            ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
    }
}
