<?php

namespace App\Http\Controllers;

use App\Models\ActividadProducto;
use App\Models\CategoryVariable;
use App\Models\NewCategory;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\Unit;
use App\Models\VariableProduct;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class ProductNewController extends Controller
{

    use ApiResponser;

    public function paginate(Request $request)
    {
        return $this->success(
            Product::with('subcategory', 'category', 'unit')
                ->when($request->company_id, function ($q, $fill) {
                    $q->where("p.company_id", $fill);
                })
                ->when($request->categoria, function ($q, $fill) {
                    $q->where("p.Id_Categoria", '=', $fill);
                })
                ->when($request->subcategoria, function ($q, $fill) {
                    $q->where("p.Id_Subcategoria", '=', $fill);
                })
                ->when($request->nombre, function ($q, $fill) {
                    $q->where("p.Nombre_Comercial", 'like', "%$fill%");
                })
                ->when($request->estado, function ($q, $fill) {
                    $q->where("p.Estado", '=', $fill);
                })
                ->when($request->imagen, function ($q, $fill) {
                    $q->where('p.Imagen', (($fill == 'con') ? "!=" : "="), null);
                })->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1))
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
        $categories = NewCategory::with(['subcategories' => function ($q) {
            $q->select('Id_Subcategoria', 'Id_Categoria_Nueva', 'Id_Subcategoria as value', 'Nombre as text');
        }])->get(['Id_Categoria_Nueva', 'Id_Categoria_Nueva as value', 'Nombre as text']);

        return $this->success([
            'categories' => $categories,
            'packagings' => $packaging,
            'units' => $unit
        ]);
    }

    public function getVariablesCat($id)
    {
        return $this->success(NewCategory::find($id)->categoryVariables);
    }

    public function getVariablesSubCat($id)
    {
        return $this->success(Subcategory::find($id)->subcategoryVariables);
    }

    public function index()
    {
    }

    public function store(Request $request)
    {
        //dd('holi');
        $data = $request->except(['category_variables', 'subcategory_variables']);
        $category_variables = $request->category_variables;
        $subcategory_variables = $request->subcategory_variables;
        if (!$request->Id_Producto) {
            $base64 = saveBase64($request->Imagen, 'fotos_productos/', true, '.' . explode("/", $request->typeFile)[1]);
            $data['Imagen'] = URL::to('/') . '/api/image?path=' . $base64;
        } else {
            $actualData = Product::find($request->Id_Producto);
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
        //!Hay que borrar las variables creadas y registrar cambios jeje
        $all_variables = array_merge($category_variables, $subcategory_variables);
        foreach ($all_variables as $value) {
            $value['product_id'] = $product->Id_Producto;
            VariableProduct::create($value);
        }
    }

    function newDataActivity($campo, $key, $id)
    {
        switch ($key) {
            case 'Id_Producto':
                break;
            case 'Presentacion':
                $key = 'presentacion';
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
            case 'Unidad_Medida':
                $key = 'unidad de medida';
                $campo = Unit::find($campo)->name;
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
                $campo = '';
                $this->createActivity(
                    $id,
                    "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Id_Categoria':
                $key = 'categoría';
                $campo = NewCategory::find($campo)->Nombre;
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
                break;
        }
    }

    function changeDataActivity($campo, $key, $id, $oldData)
    {
        switch ($key) {
            case 'Id_Producto':
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
            case 'Unidad_Medida':
                $key = 'unidad de medida';
                $campo = Unit::find($campo)->name;
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
            case 'Imagen':
                $key = 'imagen';
                $campo = '';
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            case 'Id_Categoria':
                $key = 'categoría';
                $campo = NewCategory::find($campo)->Nombre;
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
                $this->createActivity(
                    $id,
                    (isset($oldData))
                        ? "El campo '" . $key . "' fue modificado de '" . $oldData . "' a '" . $campo . "'."
                        : "El campo '" . $key . "' fue ingresado con el valor '" . $campo . "'."
                );
                break;
            default:
                break;
        }
    }

    function createActivity($id, $details)
    {
        ActividadProducto::create([
            "Id_Producto" => $id,
            "Person_Id" => 1,
            "Detalles" => $details
        ]);
    }

    public function show($id)
    {
        return $this->success(
            Product::with('subcategory', 'category', 'unit', 'activity', 'variables', 'packaging')
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
