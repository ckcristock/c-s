<?php

namespace App\Http\Controllers;

use App\Models\ActividadProducto;
use App\Models\CategoryVariable;
use App\Models\CategoriaNueva;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\SubcategoryVariable;
use App\Models\Tax;
use App\Models\Unit;
use App\Models\VariableProduct;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ProductNewController extends Controller
{

    use ApiResponser;

    public function paginate(Request $request)
    {
        return $this->success(
            Product::with('subcategory', 'category', 'unit', 'packaging', 'tax')
                ->when($request->company_id, function ($q, $fill) {
                    $q->where("company_id", $fill);
                })
                ->when($request->categoria, function ($q, $fill) {
                    $q->where("Id_Categoria", $fill);
                })
                ->when($request->subcategoria, function ($q, $fill) {
                    $q->where("Id_Subcategoria", $fill);
                })
                ->when($request->nombre, function ($q, $fill) {
                    $q->where("Nombre_Comercial", 'like', "%$fill%");
                })
                ->when($request->estado, function ($q, $fill) {
                    $q->where("Estado", '=', $fill);
                })
                ->when($request->imagen, function ($q, $fill) {
                    $q->where('Imagen', (($fill == 'con') ? "!=" : "="), null);
                })
                ->orderBy('Nombre_Comercial')
                ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
        );
    }

    public function getMaterials()
    {
        //*Funcion que obtiene los productos de la categoría materia prima y subcategoria materiales para llamar en materiales comerciales de apu y para mostrar en la parametrización de apu materiales corte agua y corte láser

        $product = Product::where('Id_Categoria', 1)
            ->where('Id_Subcategoria', 1)
            ->get(['Id_Producto as id', 'Id_Producto as value', 'Nombre_Comercial as text']);
        return $this->success($product);
    }

    public function getDataCreate()
    {
        $unit = Unit::get(['name As text', 'id AS value']);
        $packaging = Packaging::get(['id as value', 'name as text']);
        $categories = CategoriaNueva::with(['subcategories' => function ($q) {
            $q->select('Id_Subcategoria', 'Id_Categoria_Nueva', 'Id_Subcategoria as value', 'Nombre as text');
        }])->get(['Id_Categoria_Nueva', 'Id_Categoria_Nueva as value', 'Nombre as text']);
        $taxes = Tax::get(['Id_Impuesto as value', 'Valor as text']);
        return $this->success([
            'categories' => $categories,
            'packagings' => $packaging,
            'units' => $unit,
            'taxes' => $taxes
        ]);
    }

    public function getVariablesCat($id)
    {
        return $this->success(CategoriaNueva::find($id)->categoryVariables);
    }

    public function getVariablesSubCat($id)
    {
        return $this->success(Subcategory::find($id)->subcategoryVariables);
    }

    public function index()
    {
    }

    public function listarProductos(Request $request)
    {
        return $this->success(
            Product::where('Estado', 'Activo')
                ->with('unit', 'packaging', 'tax')
                ->when($request->categoria, function ($q, $fill) {
                    $q->where("Id_Categoria", $fill);
                })
                ->when($request->subcategoria, function ($q, $fill) {
                    $q->where("Id_Subcategoria", $fill);
                })
                ->get()
        );
    }

    public function store(Request $request)
    {
        //dd('holi');
        $data = $request->except(['category_variables', 'subcategory_variables']);
        $category_variables = $request->category_variables;
        $subcategory_variables = $request->subcategory_variables;
        if (!$request->Id_Producto && $request->Imagen) {
            $base64 = saveBase64($request->Imagen, 'fotos_productos/', true, '.' . explode("/", $request->typeFile)[1]);
            $data['Imagen'] = URL::to('/') . '/api/image?path=' . $base64;
        } else {
            $actualData = Product::find($request->Id_Producto);
            if ($request->Imagen != $actualData->Imagen) {
                $base64 = saveBase64($request->Imagen, 'fotos_productos/', true, '.' . explode("/", $request->typeFile)[1]);
                $data['Imagen'] = URL::to('/') . '/api/image?path=' . $base64;
            }
        }
        $product = Product::updateOrCreate(["Id_Producto" => $data["Id_Producto"]], $data);
        if ($product->wasRecentlyCreated) {
            $this->createActivity(
                $product->Id_Producto,
                "El producto '" . $product->Nombre_Comercial . "' fue ingresado al sistema",
            );
            foreach ($data as $key => $campo) {
                $this->newDataActivity($campo, $key, $product->Id_Producto);
            }
        } else {
            foreach ($data as $key => $campo) {
                if ($campo !== $actualData[$key]) {
                    $this->changeDataActivity($campo, $key, $product->Id_Producto, $actualData[$key]);
                }
            }
        }
        $all_variables = array_merge($category_variables, $subcategory_variables);
        foreach ($all_variables as $value) {
            if ($value['subcategory_variables_id']) {
                $label = SubcategoryVariable::find($value['subcategory_variables_id'])->label;
            } else {
                $label = CategoryVariable::find($value['category_variables_id'])->label;
            }
            if ($value['id']) {
                $actualDataVariable = VariableProduct::find($value['id']);
            }
            $value['product_id'] = $product->Id_Producto;
            $variable = VariableProduct::updateOrCreate(['id' => $value['id']], $value);
            if ($variable->wasRecentlyCreated) {
                $this->newDataActivity($variable->valor, $label, $product->Id_Producto);
            } else {
                if ($value['valor'] != $actualDataVariable['valor']) {
                    $this->changeDataActivity($variable->valor, $label, $product->Id_Producto, $actualDataVariable->valor);
                }
            }
        }
    }

    function newDataActivity($campo, $key, $id)
    {
        switch ($key) {
            case 'Id_Producto':
                break;
            case 'typeFile':
                break;
            case 'Presentacion':
                $key = 'presentacion';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'impuesto_id':
                $key = 'impuesto';
                $campo = Tax::find($campo)->Valor;
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Nombre_Comercial':
                $key = 'nombre';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Referencia':
                $key = 'referencia';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Unidad_Medida':
                $key = 'unidad de medida';
                $campo = Unit::find($campo)->name;
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Precio':
                $key = 'precio';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Cantidad':
            case 'Codigo_Barras':
                $key = 'código de barras';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Imagen':
                $key = 'imagen';
                if ($campo) {
                    $this->createActivity(
                        $id,
                        "El campo '" . $key . "' fue ingresado."
                    );
                }
                break;
            case 'Id_Categoria':
                $key = 'categoría';
                $campo = CategoriaNueva::find($campo)->Nombre;
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Referencia':
                $key = 'referencia';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Estado':
                $key = 'estado';
            case 'Id_Subcategoria':
                $key = 'subcategoría';
                $campo = Subcategory::find($campo)->Nombre;
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Embalaje_id':
                $key = 'embalaje';
                $campo = Packaging::find($campo)->name;
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            default:
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
        }
    }

    function changeDataActivity($campo, $key, $id, $oldData)
    {
        switch ($key) {
            case 'Id_Producto':
                break;
            case 'typeFile':
                break;
            case 'Presentacion':
                $key = 'presentacion';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Nombre_Comercial':
                $key = 'nombre';

                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'impuesto_id':
                $key = 'impuesto';
                $campo = Tax::find($campo)->Valor ?? '';
                $oldData = Tax::find($oldData)->Valor ?? '';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Precio':
                $key = 'precio';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Unidad_Medida':
                $key = 'unidad de medida';
                $campo = Unit::find($campo)->name;
                $oldData = Unit::find($oldData)->name;
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Cantidad':
            case 'Codigo_Barras':
                $key = 'código de barras';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Referencia':
                $key = 'referencia';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Imagen':
                $key = 'imagen';
                if ($campo) {
                    $this->createActivity(
                        $id,
                        (isset($oldData))
                            ? "El campo '" . $key . "' fue modificado."
                            : "El campo '" . $key . "' fue ingresado."
                    );
                }
                break;
            case 'Id_Categoria':
                $key = 'categoría';
                $campo = CategoriaNueva::find($campo)->Nombre;
                $oldData = CategoriaNueva::find($oldData)->Nombre;
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Referencia':
                $key = 'referencia';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Estado':
                $key = 'estado';
            case 'Id_Subcategoria':
                $key = 'subcategoría';
                $campo = Subcategory::find($campo)->Nombre;
                $oldData = Subcategory::find($oldData)->Nombre;
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Embalaje_id':
                $key = 'embalaje';
                $campo = Packaging::find($campo)->name;
                $oldData = Packaging::find($oldData)->name ?? '';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            default:
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
        }
    }

    function createActivity($id, $details)
    {
        //dd($details);
        ActividadProducto::create([
            "Id_Producto" => $id,
            "Person_Id" => auth()->user()->person_id,
            "Detalles" => $details
        ]);
    }

    public function show($id)
    {
        return $this->success(
            Product::with('subcategory', 'category', 'unit', 'activity', 'variables', 'packaging', 'tax')
                ->find($id)
        );
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }
}
