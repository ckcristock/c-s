<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Models\BudgetItemSubitem;
use Illuminate\Support\Facades\DB;

class BudgetService
{

    static public function show($id)
    {
        # code...
        return Budget::with(
            [
                'items' => function ($q) {
                    $q->select('*')
                        ->with(
                            ['subitems' => function ($q) {
                                $q->select('*')
                                    ->with(
                                        ['indirect_costs' => function ($q) {
                                            $q->select('*');
                                        }, 'apuSet' => function ($q) {
                                            $q->select('id', 'name');
                                        }, 'apuPart' => function ($q) {
                                            $q->select('id', 'name');
                                        }/* ,''=> function($q) {
                                            $q->select('*');
                                        } */]
                                    );
                            }]
                        );
                },
                'indirectCosts' => function ($q) {
                    $q->select(
                        'id',
                        'indirect_cost_id',
                        'percentage',
                        'budget_id'
                    )
                        ->with(
                            ['indirectCost' => function ($q) {
                                $q->select('id', 'name');
                            }]
                        );
                },
                'destiny' => function ($q) {
                    $q->select('*');
                },
                'user' => function ($q) {
                    $q->select('id', 'usuario', 'person_id')
                        ->with(
                            ['person' => function ($q) {
                                $q->select('id', 'first_name', 'first_surname');
                            }]
                        );;
                },
                'customer' => function ($q) {
                    $q->select('id', 'nit')
                        ->selectRaw('IFNULL(social_reason, CONCAT_WS(" ",first_name, first_name) ) as name');
                }
            ]

        )->where('id', $id)->first();
    }

    static public function deleteItems($itemsTodelete)
    {
        $toDelete = BudgetItem::with('subitems')->whereIn('id', $itemsTodelete)->get();
        $toDelete->each(function ($item) {
            $item->subitems->each(function ($subitem) {
                DB::table('budget_item_subitem_indirect_costs')->where('budget_item_subitem_id', $subitem->id)->delete();
                $subitem->delete();
            });
          
            $item->delete();
        });
    }

    static public function deleteSubItems($subitemsTodelete)
    {
        $toDelete = BudgetItemSubitem::whereIn('id', $subitemsTodelete)->get();
        $toDelete->each(function ($subitem) {
            DB::table('budget_item_subitem_indirect_costs')->where('budget_item_subitem_id', $subitem->id)->delete();
            $subitem->delete();
        });
    }
}
