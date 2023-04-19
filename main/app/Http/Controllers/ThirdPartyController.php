<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Models\ThirdPartyField;
use App\Models\ThirdPartyPerson;
use App\Http\Services\HttpResponse;
use App\Http\Services\QueryBaseDatos;
use App\Http\Services\consulta;
use App\Models\CompensationFund;
use App\Models\Person;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rules\Exists;

class ThirdPartyController extends Controller
{
    use ApiResponser;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $response = ThirdParty::with('municipality')
        ->when(Request()->get('nit'), function ($q, $fill) {
            $q->where('nit', 'like', '%' . $fill . '%');
        })
        ->when($request->name, function ($q, $fill) {
            $q->where(DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname))'), 'like', '%' . $fill . '%');
            /* $q->where('social_reason', 'like', '%' . $fill . '%'); */
        })
        ->when($request->third_party_type, function ($q, $fill) {
            if ($fill == 'Todos') {
                return null;
            } else if ($fill == 'Cliente') {
                $q->where('is_client', 1);
            } else if ($fill == 'Proveedor') {
                $q->where('is_supplier', 1);
            }
        })
        ->when(Request()->get('email'), function ($q, $fill) {
            $q->where('email', 'like', '%' . $fill . '%');
        })
        ->when(Request()->get('cod_dian_address'), function ($q, $fill) {
            $q->where('cod_dian_address', 'like', '%' . $fill . '%');
        })
        ->when(Request()->get('phone'), function ($q, $fill) {
            $q->where('landline', 'like', '%' . $fill . '%')
                ->orwhere('cell_phone', 'like', '%' . $fill . '%');
        })
        ->when(Request()->get('municipio'), function ($q, $fill) {
            $q->whereHas('municipality', function ($q) {
                $q->where('name', 'like', '%' . \Request()->get('municipio') . '%');
            });
        })
        ->select("*", DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as name'))
        ->orderBy('state', 'asc')
        ->orderBy('name', 'asc')
        ->paginate(request()->get('pageSize', 10), ['*'], 'page', request()->get('page', 1));
        return $this->success(
            $response
        );
    }

    public function thirdParties()
    {
        return $this->success(
            ThirdParty::select(
                DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as text'),
                'id as value',
                'retefuente_percentage'
            )
                ->when(Request()->get('name'), function ($q, $fill) {
                    $q->where(DB::raw('concat(IFNULL(social_reason, " "), IFNULL(first_name,"")," ",IFNULL(first_surname,"") )'), 'like', '%' . $fill . '%');
                })
                ->where('state', 'Activo')
                ->when(Request()->get('get_full'), function ($q, $fill) {
                    $q->orWhere('state', 'Inactivo');
                })
                ->orderBy('text')
                ->get()
        );
    }

    public function thirdPartyProvider()
    {
        return $this->success(
            ThirdParty::select(
                DB::raw('id as value')
            )->name("text")
                ->when(Request()->get('name'), function ($q, $fill) {
                    $q->where(DB::raw('concat(IFNULL(social_reason, " "), IFNULL(first_name,"")," ",IFNULL(first_surname,"") )'), 'like', '%' . $fill . '%');
                })
                ->where('state', 'Activo')
                ->where('is_supplier', 1)
                ->get()
        );
    }

    public function thirdPartyClient()
    {
        return $this->success(
            ThirdParty::select(
                DB::raw('IFNULL(social_reason,concat(first_name," ",first_surname)) as text'),
                'id as value',
                'retefuente_percentage',
                'nit'
            )->when(Request()->get('name'), function ($q, $fill) {
                $q->where(DB::raw('concat(IFNULL(social_reason, " "), IFNULL(first_name,"")," ",IFNULL(first_surname,"") )'), 'like', '%' . $fill . '%');
            })
                ->where('state', 'Activo')
                ->where('is_client', 1)
                ->orderBy('text')
                ->get()
        );
    }

    public function getFields()
    {
        return $this->success(
            ThirdPartyField::where('state', '=', 'Activo')
                ->get()
        );
    }

    public function filtrarPhp()
    {
        $match = (isset($_REQUEST['coincidencia']) ? $_REQUEST['coincidencia'] : '');

        $condicion = '';

        if ($match != '') {
            $condicion .= ' WHERE
		T.Nit LIKE "%' . $match . '%" OR T.Nombre_Tercero LIKE "%' . $match . '%"';
        }

        $http_response = new HttpResponse();

        $query = '
		SELECT
			T.*
		FROM (SELECT
				id AS Nit,
				id AS Id,
				CONCAT(id," - ", CONCAT_WS(" ", first_name, first_surname)) AS Nombre_Tercero,
				CONCAT(id," - ", CONCAT_WS(" ", first_name, first_surname)) AS Nombre,
				"Funcionario" as Tipo
			FROM people
				UNION
			SELECT
				Id_Cliente AS Nit,
				Id_Cliente AS Id,
				CONCAT(Id_Cliente," - ",Nombre) AS Nombre_Tercero,CONCAT(Id_Cliente," - ",Nombre) AS Nombre, "Cliente" as Tipo
			FROM Cliente
				UNION
			SELECT
				Id_Proveedor AS Nit,
				Id_Proveedor AS Id,
				CONCAT(Id_Proveedor," - ",IF((Primer_Nombre IS NULL OR Primer_Nombre = ""), Nombre, CONCAT_WS(" ", Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido))) AS Nombre_Tercero, CONCAT(Id_Proveedor," - ",IF((Primer_Nombre IS NULL OR Primer_Nombre = ""), Nombre, CONCAT_WS(" ", Primer_Nombre, Segundo_Nombre, Primer_Apellido, Segundo_Apellido))) AS Nombre, "Proveedor" as Tipo
			FROM Proveedor

			) T ' . $condicion;


        $queryObj = new QueryBaseDatos($query);
        $matches = $queryObj->ExecuteQuery('Multiple');

        return json_encode($matches);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->except(["person"]);
        if ($data['image']) {
            $typeImage = '.' . $request->typeImage;
            $data["image"] = URL::to('/') . '/api/image?path=' . saveBase64($data["image"], 'third_parties/', true, $typeImage);
        }
        if ($data['rut']) {
            $typeRut = '.' . $request->typeRut;
            $base64 = saveBase64File($data["rut"], 'thirdPartiesRut/', false, $typeRut);
            $data["rut"] = URL::to('/') . '/api/file?path=' . $base64;
        }
        $people = request()->get('person');
        try {
            if (in_array('Cliente', $data['third_party_type'])) {
                $data['is_client'] = true;
            }
            if (in_array('Proveedor', $data['third_party_type'])){
                $data['is_supplier'] = true;
            }
            $thirdParty =  ThirdParty::create($data);
            foreach ($people as $person) {
                $person["third_party_id"] = $thirdParty->id;
                ThirdPartyPerson::create($person);
            }
            return $this->success('Guardado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), $th->getLine(), $th->getFile(), 500);
        }
    }

    public function changeState(Request $request)
    {
        try {
            $third = ThirdParty::find(request()->get('id'));
            $third->update($request->all());
            return $this->success('Proceso Correcto');
        } catch (\Throwable $th) {
            return $this->error($th->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show($id)
    {
        $third_party_query = ThirdParty::with('country', 'document_type_', 'municipality', 'department')
            ->name()
            ->find($id);
        $third_party = ThirdParty::find($id);
        $third_party_fields = ThirdPartyField::get();
        $quotations2 = $third_party->quotations()->get();
        $groupedQuotations = $quotations2->groupBy(function ($item) {
            return $item->created_at->month;
        });
        $quotations = $third_party->quotations()->paginate(Request()->get('pageSizeQuotation', 10), ['*'], 'pageQuotation', Request()->get('pageQuotation', 1));
        $business = $third_party->business()->paginate(Request()->get('pageSizeBusiness', 10), ['*'], 'pageBusiness', Request()->get('pageBusiness', 1));
        $budgets = $third_party->budgets()->paginate(Request()->get('pageSizeBudgets', 10), ['*'], 'pageBudgets', Request()->get('pageBudgets', 1));
        $people = $third_party->thirdPartyPerson()->paginate(Request()->get('pageSizePeople', 10), ['*'], 'pagePeople', Request()->get('pagePeople', 1));
        $quotations_total = $third_party->quotations()->get();
        $business_total = $third_party->business()->get();
        $budgets_total = $third_party->budgets()->get();
        return $this->success(
            [
                "third_party_query" => $third_party_query,
                "quotations" => $quotations,
                "business" => $business,
                "budgets" => $budgets,
                "people" => $people,
                "third_party_fields" => $third_party_fields,
                "chart_quotations" => $groupedQuotations,
                "quotations_total" => $quotations_total,
                "business_total" => $business_total,
                "budgets_total" => $budgets_total,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->success(
            ThirdParty::with('thirdPartyPerson')
                ->find($id)
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = ThirdParty::find($id);
        $data = $request->except(["person"]);
        if ($data["image"] != $validator["image"]) {
            $typeImage = '.' . $request->typeImage;
            $data["image"] = URL::to('/') . '/api/image?path=' . saveBase64($data["image"], 'third_parties/', true, $typeImage);
        }
        if ($data["rut"] != $validator["rut"]) {
            $typeRut = '.' . $request->typeRut;
            $base64 = saveBase64File($data["rut"], 'thirdPartiesRut/', false, $typeRut);
            $data["rut"] = URL::to('/') . '/api/file?path=' . $base64;
        }
        $people = request()->get('person');
        try {
            if (in_array('Cliente', $data['third_party_type'])) {
                $data['is_client'] = true;
            } else {
                $data['is_client'] = false;
            }
            if (in_array('Proveedor', $data['third_party_type'])){
                $data['is_supplier'] = true;
            } else {
                $data['is_supplier'] = false;
            }
            $thirdParty = ThirdParty::find($id)
                ->update($data);
            foreach ($people as $person) {
                if (isset($person["id"])) {
                    $thirdPerson = ThirdPartyPerson::find($person["id"]);
                    $thirdPerson->update($person);
                } else {
                    $person["third_party_id"] = $id;
                    ThirdPartyPerson::create($person);
                }
            }
            return $this->success('Actualizado con éxito');
        } catch (\Throwable $th) {
            return $this->errorResponse($th->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function buscarProveedor()
    {
        $query = 'SELECT PR.id as Id_Proveedor,
            IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname, second_name, second_surname))  as NombreProveedor
            FROM third_parties PR
            WHERE PR.is_client = 1 ';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $proveedorbucar = $oCon->getData();
        unset($oCon);


        return json_encode($proveedorbucar);
    }

    public function nitBuscar()
    {
        /* $third = ThirdParty::where('state', 'Activo')
            ->select(
                'id',
                DB::raw('IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) as Nombre'),
                DB::raw('"Cliente" AS Tipo')
            )->get();
        $people = Person::where('status', 'Activo')
            ->select(
                'id',
                DB::raw('CONCAT(id, " - ", first_name," ", first_surname) AS Nombre'),
                DB::raw('"Funcionario" AS Tipo')
            )->get();
        $compensation_funds = CompensationFund::where('status', 'Activo')
            ->select(
                'nit as id',
                DB::raw('CONCAT_WS(" ", nit, name) AS Nombre'),
                DB::raw('"Caja_Compensacion" AS Tipo')
            )->get(); */

        $query = ThirdParty::where('state', 'Activo')
            ->select(
                'id',
                DB::raw('IFNULL(social_reason COLLATE utf8mb4_spanish_ci, CONCAT_WS(" ", first_name, first_surname)) as Nombre'),
                DB::raw('"Cliente" AS Tipo')
            )
            ->union(
                Person::where('status', 'Activo')
                    ->select(
                        'id',
                        DB::raw('CONCAT(id, " - ", first_name," ", first_surname) AS Nombre'),
                        DB::raw('"Funcionario" AS Tipo')
                    )
            )
            ->union(
                CompensationFund::where('status', 'Activo')
                    ->select(
                        'nit as id',
                        DB::raw('CONCAT_WS(" ", nit, name) AS Nombre'),
                        DB::raw('"Caja_Compensacion" AS Tipo')
                    )
            )
            ->get();


        return json_encode($query);
    }

    public function porTipo()
    {
        $tipo = isset($_REQUEST['Tipo']) ? $_REQUEST['Tipo'] : false;

        if ($tipo) {
            $query = $this->query($tipo);

            $oCon = new consulta();
            $oCon->setQuery($query);
            $oCon->setTipo('Multiple');
            $clientes = $oCon->getData();

            $res = $clientes;
            return json_encode($res);
        }
    }

    public function listaCliente()
    {
        $query = '(SELECT C.Id_Cliente, CONCAT(C.Id_Cliente," - ", C.Nombre) as Nombre
                FROM Cliente C WHERE C.Estado != "Inactivo") UNION (SELECT F.id AS Id_Cliente, CONCAT(CONCAT_WS(" ",F.first_name,F.first_surname)," - ",F.id) AS Nombre FROM people F)';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $clientes = $oCon->getData();
        unset($oCon);

        return json_encode($clientes);
    }

    function query($tipo)
    {
        if ($tipo == 'Funcionario') {
            $select = 'SELECT id AS Id_Cliente,
                         CONCAT(id, " - ",first_name," ",first_surname) AS Nombre
                         FROM people';
        } else if ($tipo == 'Cliente') {
            $select = 'SELECT id as Id_Cliente, IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) AS Nombre
            FROM third_parties WHERE is_client = 1';
        } else if ($tipo == 'Proveedor') {
            $select = 'SELECT id AS Id_Cliente ,
                        IFNULL(social_reason, CONCAT_WS(" ", first_name, first_surname)) AS Nombre FROM third_parties WHERE is_supplier = 1 ';
        }
        return $select;
    }

    public function listaProveedores()
    {
        $query = 'SELECT Id_Proveedor, IF(Nombre = "" OR Nombre IS NULL,CONCAT_WS(" ",Id_Proveedor,"-",Primer_Nombre,Segundo_Nombre,Primer_Apellido,Segundo_Apellido),CONCAT_WS(" ",Id_Proveedor,"-",Nombre)) AS NombreProveedor
	    FROM Proveedor UNION (SELECT F.id AS Id_Proveedor, CONCAT(CONCAT_WS(" ",F.first_name,F.first_surname)," - ",F.id) AS NombreProveedor FROM people F)';

        $oCon = new consulta();
        $oCon->setQuery($query);
        $oCon->setTipo('Multiple');
        $proveedores = $oCon->getData();
        unset($oCon);

        return json_encode($proveedores);
    }
}
